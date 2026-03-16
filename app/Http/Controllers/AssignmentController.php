<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\TeacherProfile;
use App\Services\AssignmentService;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function generate(Request $request)
    {
        $service = new AssignmentService();
        $result = $service->generate(auth()->id());

        $totalAssignments = count($result['assignments']);
        $totalSkipped = count($result['skipped']);
        $conflicts = $result['conflicts'];

        AuditLogService::log(
            auth()->id(),
            'generated',
            'Assignment',
            null,
            [
                'total_assignments' => $totalAssignments,
                'skipped' => $totalSkipped,
                'conflicts' => $conflicts,
            ]
        );

        $message = "Schedule generated successfully. {$totalAssignments} assignments created with {$conflicts} conflicts.";

        if ($totalSkipped > 0) {
            $message .= " {$totalSkipped} subject(s) skipped.";
        }

        return redirect()->route('chair.assignments')->with('success', $message);
    }

public function override(Request $request)
{
    $request->validate([
        'teacher_profile_id' => 'required|exists:teacher_profiles,id',
    ]);

    $teacher = TeacherProfile::with('user')->findOrFail($request->teacher_profile_id);

    // Handle unassigned subject assignment
    if ($request->filled('subject_id') && !$request->filled('assignment_id')) {
        $subject = \App\Models\Subject::findOrFail($request->subject_id);
        $schedule = \App\Models\Schedule::first(); // get next available schedule

        $currentUnits = $teacher->assignments()->sum('total_units');
        $newTotal = $currentUnits + $subject->units;

        $assignment = Assignment::create([
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'schedule_id' => $schedule->id,
            'total_units' => $subject->units,
            'rationale' => 'manual_override',
            'match_score' => null,
            'is_overloaded' => $newTotal > $teacher->max_units,
            'assigned_by' => auth()->id(),
        ]);

        NotificationService::send(
            $teacher->user->id,
            'Subject Assigned',
            "You have been manually assigned to teach {$subject->name} ({$subject->code})."
        );

        AuditLogService::log(
            auth()->id(),
            'overridden',
            'Assignment',
            $assignment->id,
            [
                'subject' => $subject->name,
                'from_teacher' => 'Unassigned',
                'to_teacher' => $teacher->user->name,
            ]
        );

        return back()->with('success', "{$subject->name} assigned to {$teacher->user->name} successfully.");
    }

    // Handle existing assignment override
    $request->validate([
        'assignment_id' => 'required|exists:assignments,id',
    ]);

    $assignment = Assignment::findOrFail($request->assignment_id);
    $oldTeacher = $assignment->teacherProfile->user->name;
    $newTeacher = $teacher->user->name;

    $currentUnits = $teacher->assignments()->where('id', '!=', $assignment->id)->sum('total_units');
    $newTotal = $currentUnits + $assignment->subject->units;

    $assignment->update([
        'teacher_profile_id' => $request->teacher_profile_id,
        'rationale' => 'manual_override',
        'is_overloaded' => $newTotal > $teacher->max_units,
    ]);

    NotificationService::send(
        $teacher->user->id,
        'Assignment Updated',
        "You have been manually assigned to teach {$assignment->subject->name} ({$assignment->subject->code})."
    );

    AuditLogService::log(
        auth()->id(),
        'overridden',
        'Assignment',
        $assignment->id,
        [
            'subject' => $assignment->subject->name,
            'from_teacher' => $oldTeacher,
            'to_teacher' => $newTeacher,
        ]
    );

    return back()->with('success', 'Assignment overridden successfully.');
}
}