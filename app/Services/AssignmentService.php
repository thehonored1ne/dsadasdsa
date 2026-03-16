<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\TeacherProfile;

class AssignmentService
{
    public function generate(int $assignedBy): array
    {
        // Clear previous auto-assignments
        Assignment::where('rationale', '!=', 'manual_override')->delete();

        $subjects = Subject::all();
        $schedules = Schedule::all();
        $teachers = TeacherProfile::with(['availabilities', 'assignments'])->get();
        $results = [];
        $scheduleIndex = 0;

        // Track assigned subjects for prerequisites check
        $assignedSubjectCodes = Assignment::whereIn('rationale', ['expertise_match', 'availability'])
            ->with('subject')
            ->get()
            ->pluck('subject.code')
            ->toArray();

        // Track teacher schedule conflicts: [teacher_id => [schedule_id, ...]]
        $teacherScheduleMap = [];

        foreach ($subjects as $subject) {
            // Check prerequisites
            if (!empty($subject->prerequisites)) {
                $prereqs = array_map('trim', explode(',', $subject->prerequisites));
                foreach ($prereqs as $prereq) {
                    if (!in_array($prereq, $assignedSubjectCodes)) {
                        // Skip subject — prerequisite not yet assigned
                        continue 2;
                    }
                }
            }

            // Pick a schedule slot
            $schedule = $schedules->get($scheduleIndex);
            if (!$schedule) break;
            $scheduleIndex++;

            // Step 1: Expertise match
            $expertiseMatches = $teachers->filter(function ($teacher) use ($subject) {
                $expertiseList = array_map('trim', explode(',', strtolower($teacher->expertise_areas)));
                $subjectName = strtolower($subject->name);
                $subjectCode = strtolower($subject->code);

                foreach ($expertiseList as $expertise) {
                    if (str_contains($subjectName, $expertise) || str_contains($subjectCode, $expertise)) {
                        return true;
                    }
                }
                return false;
            });

            // Step 2: Filter by availability
            $availableExpertise = $expertiseMatches->filter(function ($teacher) use ($schedule, $teacherScheduleMap) {
                return $this->isAvailable($teacher, $schedule) &&
                    $this->hasNoConflict($teacher->id, $schedule->id, $teacherScheduleMap);
            });

            // Step 3: Fallback to availability only
            $availableOnly = $teachers->filter(function ($teacher) use ($schedule, $teacherScheduleMap) {
                return $this->isAvailable($teacher, $schedule) &&
                    $this->hasNoConflict($teacher->id, $schedule->id, $teacherScheduleMap);
            });

            // Step 4: Pick best teacher
            $rationale = 'expertise_match';
            $selectedTeacher = $availableExpertise->first();

            if (!$selectedTeacher) {
                $selectedTeacher = $availableOnly->first();
                $rationale = 'availability';
            }

            if (!$selectedTeacher) continue;

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
                'is_overloaded' => $isOverloaded,
                'assigned_by' => $assignedBy,
            ]);

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
            return $availability->day === $schedule->day &&
                $availability->time_start <= $schedule->time_start &&
                $availability->time_end >= $schedule->time_end;
        });
    }

    private function hasNoConflict(int $teacherId, int $scheduleId, array $teacherScheduleMap): bool
    {
        if (!isset($teacherScheduleMap[$teacherId])) {
            return true;
        }
        return !in_array($scheduleId, $teacherScheduleMap[$teacherId]);
    }
}