<?php

namespace App\Http\Controllers;

use App\Helpers\FileImportHelper;
use App\Models\Assignment;
use App\Models\AuditLog;
use App\Models\Availability;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'subject',
        ])->latest()->take(5)->get();

        // Chart 1 — Assignments by rationale
        $expertiseCount = Assignment::where('rationale', 'expertise_match')->count();
        $availabilityCount = Assignment::where('rationale', 'availability')->count();
        $overrideCount = Assignment::where('rationale', 'manual_override')->count();

        // Chart 2 — Units per teacher
        $teachers = TeacherProfile::with(['user', 'assignments'])->get();
        $teacherNames = $teachers->map(fn ($t) => $t->user->name)->toArray();
        $teacherUnits = $teachers->map(fn ($t) => $t->assignments->sum('total_units'))->toArray();
        $teacherMaxUnits = $teachers->map(fn ($t) => $t->max_units)->toArray();

        // Chart 3 — Assignments per day
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $assignmentsPerDay = [];
        foreach ($days as $day) {
            $assignmentsPerDay[] = Assignment::whereHas('schedule', function ($q) use ($day) {
                $q->where('day', $day);
            })->count();
        }

        return view('chair.dashboard', compact(
            'totalTeachers',
            'totalSubjects',
            'totalAssignments',
            'overloadedCount',
            'recentAssignments',
            'expertiseCount',
            'availabilityCount',
            'overrideCount',
            'teacherNames',
            'teacherUnits',
            'teacherMaxUnits',
            'days',
            'assignmentsPerDay'
        ));
    }

    public function upload()
    {
        return view('chair.upload');
    }

    public function assignments(Request $request)
    {
        $query = Assignment::with([
            'teacherProfile.user',
            'subject',
            'schedule',
        ]);

        // Search by teacher name or subject
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('teacherProfile.user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%");
                })->orWhereHas('subject', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%")
                        ->orWhere('code', 'like', "%$search%");
                });
            });
        }

        // Filter by rationale
        if ($request->filled('rationale')) {
            $query->where('rationale', $request->rationale);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_overloaded', $request->status === 'overloaded');
        }

        // Filter by day
        if ($request->filled('day')) {
            $query->whereHas('schedule', function ($q) use ($request) {
                $q->where('day', $request->day);
            });
        }

        $assignments = $query->get();
        $teachers = TeacherProfile::with('user')->get();

        return view('chair.assignments', compact('assignments', 'teachers'));
    }

    public function report()
    {
        $assignments = Assignment::with([
            'teacherProfile.user',
            'subject',
            'schedule',
        ])->get();

        $teacherSummary = $this->getTeacherSummary();

        return view('chair.report', compact('assignments', 'teacherSummary'));
    }

    public function exportCsv()
    {
        $assignments = Assignment::with([
            'teacherProfile.user',
            'subject',
            'schedule',
        ])->get();

        $teacherSummary = $this->getTeacherSummary();
        $filename = 'load_assignment_report_'.now()->format('Y_m_d').'.csv';

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
                'Status',
            ]);

            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $assignment->teacherProfile->user->name,
                    $assignment->subject->code,
                    $assignment->subject->name,
                    $assignment->total_units,
                    $assignment->schedule->day.' '.$assignment->schedule->time_start.' - '.$assignment->schedule->time_end,
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
            'schedule',
        ])->get();

        $teacherSummary = $this->getTeacherSummary();

        $pdf = Pdf::loadView('chair.report_pdf', compact('assignments', 'teacherSummary'));

        return $pdf->download('load_assignment_report_'.now()->format('Y_m_d').'.pdf');
    }

    public function uploadTeachers(Request $request)
    {
        $request->validate([
            'teachers_csv' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        try {
            $rows = FileImportHelper::toArray($request->file('teachers_csv'));

            if (empty($rows)) {
                return back()->with('upload_error', 'The uploaded file is empty or formatted incorrectly.');
            }

            foreach ($rows as $index => $data) {
                if (! isset($data['email'], $data['name'], $data['expertise_areas'])) {
                    return back()->with('upload_error', 'Missing required fields on row '.($index + 2).". Ensure 'email', 'name', and 'expertise_areas' exist.");
                }

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

                if (! empty($data['available_days'])) {
                    $days = explode('|', $data['available_days']);
                    $timeStarts = explode('|', $data['time_start'] ?? '');
                    $timeEnds = explode('|', $data['time_end'] ?? '');

                    Availability::where('teacher_profile_id', $user->teacherProfile->id)->delete();

                    foreach ($days as $i => $day) {
                        if (! empty(trim($day))) {
                            Availability::create([
                                'teacher_profile_id' => $user->teacherProfile->id,
                                'day' => trim($day),
                                'time_start' => trim($timeStarts[$i] ?? '08:00'),
                                'time_end' => trim($timeEnds[$i] ?? '17:00'),
                            ]);
                        }
                    }
                }
            }

            return back()->with('success', 'Teachers uploaded successfully.');

        } catch (\Exception $e) {
            return back()->with('upload_error', 'Error processing file: '.$e->getMessage());
        }
    }

    public function uploadSubjects(Request $request)
    {
        $request->validate([
            'subjects_csv' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        try {
            $rows = FileImportHelper::toArray($request->file('subjects_csv'));

            if (empty($rows)) {
                return back()->with('upload_error', 'The uploaded file is empty or formatted incorrectly.');
            }

            foreach ($rows as $index => $data) {
                if (! isset($data['code'], $data['name'], $data['units'])) {
                    return back()->with('upload_error', 'Missing required fields on row '.($index + 2).". Ensure 'code', 'name', and 'units' exist.");
                }

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

        } catch (\Exception $e) {
            return back()->with('upload_error', 'Error processing file: '.$e->getMessage());
        }
    }

    public function uploadSchedules(Request $request)
    {
        $request->validate([
            'schedules_csv' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        try {
            $rows = FileImportHelper::toArray($request->file('schedules_csv'));

            if (empty($rows)) {
                return back()->with('upload_error', 'The uploaded file is empty or formatted incorrectly.');
            }

            foreach ($rows as $index => $data) {
                if (! isset($data['day'], $data['time_start'], $data['time_end'], $data['room'])) {
                    return back()->with('upload_error', 'Missing required fields on row '.($index + 2).". Ensure 'day', 'time_start', 'time_end', and 'room' exist.");
                }

                Schedule::create([
                    'day' => $data['day'],
                    'time_start' => $data['time_start'],
                    'time_end' => $data['time_end'],
                    'room' => $data['room'],
                ]);
            }

            return back()->with('success', 'Schedules uploaded successfully.');

        } catch (\Exception $e) {
            return back()->with('upload_error', 'Error processing file: '.$e->getMessage());
        }
    }


    public function downloadTemplate(string $type, string $format)
    {
        $templates = [
            'teachers' => [
                'headers' => ['name', 'email', 'expertise_areas', 'max_units', 'available_days', 'time_start', 'time_end'],
                'sample' => ['John Cruz', 'john@school.edu', 'Programming|Web Development', '21', 'Monday|Tuesday', '08:00|10:00', '09:00|11:00'],
            ],
            'subjects' => [
                'headers' => ['code', 'name', 'units', 'prerequisites'],
                'sample' => ['CS101', 'Introduction to Programming', '3', ''],
            ],
            'schedules' => [
                'headers' => ['day', 'time_start', 'time_end', 'room'],
                'sample' => ['Monday', '08:00', '09:00', 'Room 101'],
            ],
        ];

        if (!isset($templates[$type])) {
            abort(404);
        }

        $template = $templates[$type];

        if ($format === 'csv') {
            return $this->downloadCsvTemplate($type, $template);
        } elseif ($format === 'excel') {
            return $this->downloadExcelTemplate($type, $template);
        }

        abort(404);
    }

    private function downloadCsvTemplate(string $type, array $template)
    {
        $filename = "{$type}_template.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($template) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $template['headers']);
            fputcsv($file, $template['sample']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function downloadExcelTemplate(string $type, array $template)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(ucfirst($type));

        // Column letters
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        // Write headers with styling
        foreach ($template['headers'] as $colIndex => $header) {
            $col = $columns[$colIndex];
            $sheet->setCellValue("{$col}1", $header);

            $sheet->getStyle("{$col}1")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'],
                ],
            ]);

            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Write sample row
        foreach ($template['sample'] as $colIndex => $value) {
            $col = $columns[$colIndex];
            $sheet->setCellValue("{$col}2", $value);
        }

        $filename = "{$type}_template.xlsx";
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
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

    public function auditLog()
    {
        $logs = AuditLog::with('user')
            ->latest()
            ->paginate(20);

        return view('chair.audit_log', compact('logs'));
    }
}
