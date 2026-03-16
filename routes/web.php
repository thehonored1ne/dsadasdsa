<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherImportController;
use App\Http\Controllers\ChairController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AssignmentController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:program_chair'])->group(function () {
    Route::get('/dashboard/chair', [ChairController::class, 'index'])->name('chair.dashboard');
    Route::get('/chair/upload', [ChairController::class, 'upload'])->name('chair.upload');
    Route::get('/chair/assignments', [ChairController::class, 'assignments'])->name('chair.assignments');
    Route::get('/chair/report', [ChairController::class, 'report'])->name('chair.report');
    Route::post('/import-teachers', [TeacherImportController::class, 'store'])->name('import.teachers');

    Route::post('/chair/upload/teachers', [ChairController::class, 'uploadTeachers'])->name('chair.upload.teachers');
    Route::post('/chair/upload/subjects', [ChairController::class, 'uploadSubjects'])->name('chair.upload.subjects');
    Route::post('/chair/upload/schedules', [ChairController::class, 'uploadSchedules'])->name('chair.upload.schedules');

    Route::post('/chair/assignments/generate', [AssignmentController::class, 'generate'])->name('chair.assignments.generate');
    Route::post('/chair/assignments/override', [AssignmentController::class, 'override'])->name('chair.assignments.override');

    Route::get('/chair/report/export-csv', [ChairController::class, 'exportCsv'])->name('chair.report.csv');
    Route::get('/chair/report/export-pdf', [ChairController::class, 'exportPdf'])->name('chair.report.pdf');
});

Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/dashboard/teacher', [TeacherController::class, 'index'])->name('teacher.dashboard');
    Route::get('/teacher/export-schedule', [TeacherController::class, 'exportSchedule'])->name('teacher.export.schedule');

    Route::get('/teacher/profile', [TeacherController::class, 'editProfile'])->name('teacher.profile');
    Route::patch('/teacher/profile', [TeacherController::class, 'updateProfile'])->name('teacher.profile.update');
});

require __DIR__.'/auth.php';