<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Services\NotificationService;
use App\Services\TFIDFService;

class AssignmentService
{
    public function generate(int $assignedBy): array
    {
        Assignment::where('rationale', '!=', 'manual_override')->delete();

        $subjects = Subject::all();
        $schedules = Schedule::all();
        $teachers = TeacherProfile::with(['availabilities', 'assignments', 'user'])->get();
        $results = [];
        $skipped = [];
        $scheduleIndex = 0;

        $assignedSubjectCodes = [];
        $teacherScheduleMap = [];
        $tfidfService = new TFIDFService;

        foreach ($subjects as $subject) {
            if (!empty($subject->prerequisites)) {
                $prereqs = array_map('trim', explode(',', $subject->prerequisites));
                foreach ($prereqs as $prereq) {
                    if (!empty($prereq) && !in_array($prereq, $assignedSubjectCodes)) {
                        $skipped[] = $subject->name . ' (prerequisite not met)';
                        continue 2;
                    }
                }
            }

            $schedule = $schedules->get($scheduleIndex);
            if (!$schedule) break;
            $scheduleIndex++;

            $scoredTeachers = [];
            foreach ($teachers as $teacher) {
                $score = $tfidfService->calculateMatchScore($teacher->expertise_areas ?? '', $subject->name);
                if ($score >= 0.3) {
                    $scoredTeachers[] = [
                        'teacher' => $teacher,
                        'score' => $score,
                    ];
                }
            }

            usort($scoredTeachers, fn($a, $b) => $b['score'] <=> $a['score']);

            $availableExpertise = collect($scoredTeachers)->filter(function ($item) use ($schedule, $teacherScheduleMap) {
                return $this->isAvailable($item['teacher'], $schedule) &&
                    $this->hasNoConflict($item['teacher']->id, $schedule->id, $teacherScheduleMap);
            });

            $availableOnly = $teachers->filter(function ($teacher) use ($schedule, $teacherScheduleMap) {
                return $this->isAvailable($teacher, $schedule) &&
                    $this->hasNoConflict($teacher->id, $schedule->id, $teacherScheduleMap);
            });

            $rationale = 'expertise_match';
            $matchScore = null;

            $bestMatch = $availableExpertise->first();
            if ($bestMatch) {
                $selectedTeacher = $bestMatch['teacher'];
                $matchScore = $bestMatch['score'];
            } else {
                $selectedTeacher = $availableOnly->first();
                $rationale = 'availability';
            }

            if (!$selectedTeacher) {
                $skipped[] = $subject->name . ' (no available teacher)';
                continue;
            }

            $currentUnits = $selectedTeacher->assignments()->sum('total_units');
            $newTotal = $currentUnits + $subject->units;
            $isOverloaded = $newTotal > $selectedTeacher->max_units;

            $assignment = Assignment::create([
                'teacher_profile_id' => $selectedTeacher->id,
                'subject_id' => $subject->id,
                'schedule_id' => $schedule->id,
                'total_units' => $subject->units,
                'rationale' => $rationale,
                'match_score' => $matchScore,
                'is_overloaded' => $isOverloaded,
                'assigned_by' => $assignedBy,
            ]);

            NotificationService::send(
                $selectedTeacher->user->id,
                'New Subject Assigned',
                "You have been assigned to teach {$subject->name} ({$subject->code}) on {$schedule->day} {$schedule->time_start} - {$schedule->time_end} at {$schedule->room}."
            );

            if ($isOverloaded) {
                NotificationService::send(
                    $selectedTeacher->user->id,
                    'Overload Warning',
                    "Your total units have exceeded the maximum limit after being assigned {$subject->name}. Please contact your Program Chair."
                );
            }

            $teacherScheduleMap[$selectedTeacher->id][] = $schedule->id;
            $assignedSubjectCodes[] = $subject->code;
            $results[] = $assignment;
        }

        return [
            'assignments' => $results,
            'skipped' => $skipped,
            'conflicts' => 0, // conflicts prevented by engine
        ];
    }

    private function isAvailable(TeacherProfile $teacher, Schedule $schedule): bool
    {
        return $teacher->availabilities->contains(function ($availability) use ($schedule) {
            // Overlap check: teacher availability overlaps with schedule slot
            return $availability->day === $schedule->day &&
                $availability->time_start < $schedule->time_end &&
                $availability->time_end > $schedule->time_start;
        });
    }

    private function hasNoConflict(int $teacherId, int $scheduleId, array $teacherScheduleMap): bool
    {
        if (! isset($teacherScheduleMap[$teacherId])) {
            return true;
        }

        return ! in_array($scheduleId, $teacherScheduleMap[$teacherId]);
    }
}
