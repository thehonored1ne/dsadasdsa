<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TeacherImportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'teacher_csv' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('teacher_csv');
        $rows = array_map('str_getcsv', file($file->getPathname()));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('teacher123'),
                    'role' => 'teacher',
                ]
            );
        }

        return back()->with('success', 'Teachers imported successfully.');
    }
}