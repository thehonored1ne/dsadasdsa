<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\TeacherProfile;
use App\Services\AssignmentService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function generate(Request $request)
    {
        $service = new AssignmentService();
        $service->generate(auth()->id());

        return redirect()->route('chair.assignments')->with('success', 'Schedule generated successfully.');
    }

    public function override(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'teacher_profile_id' => 'required|exists:teacher_profiles,id',
        ]);

        $assignment = Assignment::findOrFail($request->assignment_id);
        $teacher = TeacherProfile::findOrFail($request->teacher_profile_id);

        $currentUnits = $teacher->assignments()->where('id', '!=', $assignment->id)->sum('total_units');
        $newTotal = $currentUnits + $assignment->subject->units;

        $assignment->update([
            'teacher_profile_id' => $request->teacher_profile_id,
            'rationale' => 'manual_override',
            'is_overloaded' => $newTotal > $teacher->max_units,
        ]);

        return back()->with('success', 'Assignment overridden successfully.');
    }
}