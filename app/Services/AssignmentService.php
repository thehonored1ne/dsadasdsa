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
        $scheduleIndex = 0;

        $assignedSubjectCodes = Assignment::whereIn('rationale', ['expertise_match', 'availability'])
            ->with('subject')
            ->get()
            ->pluck('subject.code')
            ->toArray();

        $teacherScheduleMap = [];

        // Instantiate TFIDFService
        $tfidfService = new TFIDFService;

        foreach ($subjects as $subject) {
            // Check prerequisites
            if (! empty($subject->prerequisites)) {
                $prereqs = array_map('trim', explode(',', $subject->prerequisites));
                foreach ($prereqs as $prereq) {
                    if (! empty($prereq) && ! in_array($prereq, $assignedSubjectCodes)) {
                        continue 2;
                    }
                }
            }

            // Pick a schedule slot
            $schedule = $schedules->get($scheduleIndex);
            if (! $schedule) {
                break;
            }
            $scheduleIndex++;

            // Step 1: Score teachers with TF-IDF and filter matches > 0.3
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

            // Sort by highest score first
            usort($scoredTeachers, function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            // Step 2: Among expertise matches find available teachers (overlap check)
            $availableExpertise = collect($scoredTeachers)->filter(function ($item) use ($schedule, $teacherScheduleMap) {
                return $this->isAvailable($item['teacher'], $schedule) &&
                    $this->hasNoConflict($item['teacher']->id, $schedule->id, $teacherScheduleMap);
            });

            // Step 3: Fallback to availability only (overlap check)
            $availableOnly = $teachers->filter(function ($teacher) use ($schedule, $teacherScheduleMap) {
                return $this->isAvailable($teacher, $schedule) &&
                    $this->hasNoConflict($teacher->id, $schedule->id, $teacherScheduleMap);
            });

            // Step 4: Pick best teacher
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

            if (! $selectedTeacher) {
                continue;
            }

            // Step 5: Calculate total units
            $currentUnits = $selectedTeacher->assignments()->sum('total_units');
            $newTotal = $currentUnits + $subject->units;
            $isOverloaded = $newTotal > $selectedTeacher->max_units;

            // Step 6: Create assignment
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

            // Send notification to teacher
            NotificationService::send(
                $selectedTeacher->user->id,
                'New Subject Assigned',
                "You have been assigned to teach {$subject->name} ({$subject->code}) on {$schedule->day} {$schedule->time_start} - {$schedule->time_end} at {$schedule->room}."
            );

            // Send overload notification
            if ($isOverloaded) {
                NotificationService::send(
                    $selectedTeacher->user->id,
                    'Overload Warning',
                    "Your total units have exceeded the maximum limit after being assigned {$subject->name}. Please contact your Program Chair."
                );
            }

            // Track conflict map
            $teacherScheduleMap[$selectedTeacher->id][] = $schedule->id;

            // Track assigned subject codes for prerequisites
            $assignedSubjectCodes[] = $subject->code;

            $results[] = $assignment;
        }

        return $results;
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
