<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\TeacherProfile;
use Illuminate\Support\Facades\Auth;
use App\Models\Availability;


class TeacherController extends Controller
{
    public function index()
    {
        $teacherProfile = TeacherProfile::where('user_id', Auth::id())->first();

        if (!$teacherProfile) {
            return view('teacher.dashboard', [
                'assignments' => collect(),
                'totalSubjects' => 0,
                'totalUnits' => 0,
                'isOverloaded' => false,
            ]);
        }

        $assignments = Assignment::with(['subject', 'schedule'])
            ->where('teacher_profile_id', $teacherProfile->id)
            ->get();

        $totalUnits = $assignments->sum('total_units');
        $totalSubjects = $assignments->count();
        $isOverloaded = $totalUnits > $teacherProfile->max_units;

        return view('teacher.dashboard', compact(
            'assignments',
            'totalSubjects',
            'totalUnits',
            'isOverloaded'
        ));
    }

public function exportSchedule()
{
    $teacherProfile = TeacherProfile::where('user_id', Auth::id())->first();

    if (!$teacherProfile) {
        return back()->with('error', 'No profile found.');
    }

    $assignments = Assignment::with(['subject', 'schedule'])
        ->where('teacher_profile_id', $teacherProfile->id)
        ->get();

    $filename = 'my_schedule_' . now()->format('Y_m_d') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=$filename",
    ];

    $callback = function () use ($assignments, $teacherProfile) {
        $file = fopen('php://output', 'w');

        // Teacher info header
        fputcsv($file, ['Teacher', Auth::user()->name]);
        fputcsv($file, ['Total Units', $assignments->sum('total_units')]);
        fputcsv($file, ['Max Units', $teacherProfile->max_units]);
        fputcsv($file, ['Status', $assignments->sum('total_units') > $teacherProfile->max_units ? 'Overloaded' : 'Normal']);
        fputcsv($file, []); // empty row

        // Schedule details
        fputcsv($file, ['Subject Code', 'Subject Name', 'Units', 'Day', 'Time', 'Room', 'Rationale']);

        foreach ($assignments as $assignment) {
            fputcsv($file, [
                $assignment->subject->code,
                $assignment->subject->name,
                $assignment->total_units,
                $assignment->schedule->day,
                $assignment->schedule->time_start . ' - ' . $assignment->schedule->time_end,
                $assignment->schedule->room,
                str_replace('_', ' ', $assignment->rationale),
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

public function editProfile()
{
    $teacherProfile = TeacherProfile::where('user_id', Auth::id())
        ->with('availabilities')
        ->first();

    return view('teacher.profile', compact('teacherProfile'));
}

public function updateProfile(Request $request)
{
    $request->validate([
        'expertise_areas' => 'required|string',
        'max_units' => 'required|integer|min:1|max:30',
    ]);

    $teacherProfile = TeacherProfile::where('user_id', Auth::id())->first();

    $teacherProfile->update([
        'expertise_areas' => $request->expertise_areas,
        'max_units' => $request->max_units,
    ]);

    // Update availabilities if provided
    if ($request->has('days')) {
        Availability::where('teacher_profile_id', $teacherProfile->id)->delete();

        foreach ($request->days as $index => $day) {
            if (!empty($day)) {
                Availability::create([
                    'teacher_profile_id' => $teacherProfile->id,
                    'day' => $day,
                    'time_start' => $request->time_starts[$index],
                    'time_end' => $request->time_ends[$index],
                ]);
            }
        }
    }

    return back()->with('success', 'Profile updated successfully.');
}
}