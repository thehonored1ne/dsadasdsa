<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeacherProfile;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\Availability;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Assignment;
use App\Helpers\FileImportHelper;
use Barryvdh\DomPDF\Facade\Pdf;

class ChairController extends Controller
{
    public function index()
    {
        $totalTeachers = TeacherProfile::count();
        $totalSubjects = Subject::count();
        $totalAssignments = Assignment::count();
        $overloadedCount = Assignment::where('is_overloaded', true)->count();
        $recentAssignments = Assignment::with([
            'teacherProfile.user',
            'subject'
        ])->latest()->take(5)->get();

        return view('chair.dashboard', compact(
            'totalTeachers',
            'totalSubjects',
            'totalAssignments',
            'overloadedCount',
            'recentAssignments'
        ));
    }

    public function upload()
    {
        return view('chair.upload');
    }

    public function assignments()
    {
        $assignments = Assignment::with([
            'teacherProfile.user',
            'subject',
            'schedule'
        ])->get();

        $teachers = TeacherProfile::with('user')->get();

        return view('chair.assignments', compact('assignments', 'teachers'));
    }

    public function report()
    {
        $assignments = Assignment::with([
            'teacherProfile.user',
            'subject',
            'schedule'
        ])->get();

        $teacherSummary = $this->getTeacherSummary();

        return view('chair.report', compact('assignments', 'teacherSummary'));
    }

    public function exportCsv()
    {
        $assignments = Assignment::with([
            'teacherProfile.user',
            'subject',
            'schedule'
        ])->get();

        $teacherSummary = $this->getTeacherSummary();
        $filename = 'load_assignment_report_' . now()->format('Y_m_d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($assignments, $teacherSummary) {
            $file = fopen('php://output', 'w');

            // Teacher Summary Section
            fputcsv($file, ['TEACHER LOAD SUMMARY']);
            fputcsv($file, ['Teacher', 'Assigned Subjects', 'Total Units', 'Max Units', 'Status']);
            foreach ($teacherSummary as $summary) {
                fputcsv($file, [
                    $summary['name'],
                    $summary['subject_count'],
                    $summary['total_units'],
                    $summary['max_units'],
                    $summary['is_overloaded'] ? 'Overloaded' : 'OK',
                ]);
            }

            fputcsv($file, []); // empty row separator

            // Assignments Section
            fputcsv($file, ['ASSIGNMENT DETAILS']);
            fputcsv($file, [
                'Teacher Name',
                'Subject Code',
                'Subject Name',
                'Units',
                'Schedule',
                'Room',
                'Rationale',
                'Status'
            ]);

            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $assignment->teacherProfile->user->name,
                    $assignment->subject->code,
                    $assignment->subject->name,
                    $assignment->total_units,
                    $assignment->schedule->day . ' ' . $assignment->schedule->time_start . ' - ' . $assignment->schedule->time_end,
                    $assignment->schedule->room,
                    str_replace('_', ' ', $assignment->rationale),
                    $assignment->is_overloaded ? 'Overloaded' : 'OK',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $assignments = Assignment::with([
            'teacherProfile.user',
            'subject',
            'schedule'
        ])->get();

        $teacherSummary = $this->getTeacherSummary();

        $pdf = Pdf::loadView('chair.report_pdf', compact('assignments', 'teacherSummary'));

        return $pdf->download('load_assignment_report_' . now()->format('Y_m_d') . '.pdf');
    }

    public function uploadTeachers(Request $request)
    {
        $request->validate([
            'teachers_csv' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        $rows = FileImportHelper::toArray($request->file('teachers_csv'));

        foreach ($rows as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('teacher123'),
                    'role' => 'teacher',
                ]
            );

            TeacherProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'expertise_areas' => $data['expertise_areas'],
                    'max_units' => $data['max_units'] ?? 21,
                ]
            );

            if (!empty($data['available_days'])) {
                $days = explode('|', $data['available_days']);
                $timeStarts = explode('|', $data['time_start']);
                $timeEnds = explode('|', $data['time_end']);

                Availability::where('teacher_profile_id', $user->teacherProfile->id)->delete();

                foreach ($days as $index => $day) {
                    Availability::create([
                        'teacher_profile_id' => $user->teacherProfile->id,
                        'day' => trim($day),
                        'time_start' => trim($timeStarts[$index]),
                        'time_end' => trim($timeEnds[$index]),
                    ]);
                }
            }
        }

        return back()->with('success', 'Teachers uploaded successfully.');
    }

    public function uploadSubjects(Request $request)
    {
        $request->validate([
            'subjects_csv' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        $rows = FileImportHelper::toArray($request->file('subjects_csv'));

        foreach ($rows as $data) {
            Subject::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'units' => $data['units'],
                    'prerequisites' => $data['prerequisites'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Subjects uploaded successfully.');
    }

    public function uploadSchedules(Request $request)
    {
        $request->validate([
            'schedules_csv' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        $rows = FileImportHelper::toArray($request->file('schedules_csv'));

        foreach ($rows as $data) {
            Schedule::create([
                'day' => $data['day'],
                'time_start' => $data['time_start'],
                'time_end' => $data['time_end'],
                'room' => $data['room'],
            ]);
        }

        return back()->with('success', 'Schedules uploaded successfully.');
    }

    private function getTeacherSummary(): array
    {
        $teachers = TeacherProfile::with(['user', 'assignments'])->get();
        $summary = [];

        foreach ($teachers as $teacher) {
            $totalUnits = $teacher->assignments->sum('total_units');
            $summary[] = [
                'name' => $teacher->user->name,
                'subject_count' => $teacher->assignments->count(),
                'total_units' => $totalUnits,
                'max_units' => $teacher->max_units,
                'is_overloaded' => $totalUnits > $teacher->max_units,
            ];
        }

        return $summary;
    }
}