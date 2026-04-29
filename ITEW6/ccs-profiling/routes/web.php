<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ReportController;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware(['auth', 'verified'])->group(function () {

    // ── Shared ─────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Faculty-only (MUST be before resource routes!) ─────────────
    Route::middleware('role:Faculty')->group(function () {
        Route::get('/faculty/profile', [FacultyController::class, 'myProfile'])->name('faculty.my-profile');
        Route::get('/faculty/profile/edit', [FacultyController::class, 'editMyProfile'])->name('faculty.edit-profile');
        Route::put('/faculty/profile', [FacultyController::class, 'updateMyProfile'])->name('faculty.update-profile');
        Route::get('/schedules/my', [ScheduleController::class, 'mySchedules'])->name('schedules.my');
        Route::get('/reports/faculty', [ReportController::class, 'facultyReports'])->name('reports.faculty');
        Route::get('/reports/faculty/download', [ReportController::class, 'facultyDownload'])->name('reports.faculty.download');
    });

    // ── Student-only ───────────────────────────────────────────────
    Route::middleware('role:Student')->group(function () {
        Route::get('/student/profile', [StudentController::class, 'myProfile'])->name('student.my-profile');
        Route::get('/student/profile/edit', [StudentController::class, 'editMyProfile'])->name('student.edit-profile');
        Route::put('/student/profile', [StudentController::class, 'updateMyProfile'])->name('student.update-profile');
        Route::get('/reports/student', [ReportController::class, 'studentReports'])->name('reports.student');
        Route::get('/reports/student/download', [ReportController::class, 'studentDownload'])->name('reports.student.download');
    });

    // ── Admin-only (resource routes last to avoid wildcard conflicts)
    Route::middleware('role:Admin')->group(function () {
        Route::resource('students', StudentController::class);
        Route::resource('faculty', FacultyController::class);
        Route::get('/queries/advanced', [App\Http\Controllers\StudentQueryController::class, 'advanced'])->name('queries.advanced');
        Route::get('/queries/basketball', [QueryController::class, 'basketball'])->name('queries.basketball');
        Route::get('/queries/programming', [QueryController::class, 'programming'])->name('queries.programming');
        Route::get('/queries/skill/{skill}', [QueryController::class, 'custom'])->name('queries.custom');
        Route::resource('events', EventController::class);
        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::post('/admin/seed', [AdminController::class, 'seedDatabase'])->name('admin.seed');
        Route::delete('/admin/logs/{log}', [AdminController::class, 'destroyLog'])->name('admin.logs.destroy');
        Route::get('/reports/admin', [ReportController::class, 'adminReports'])->name('reports.admin');
        Route::get('/reports/admin/download', [ReportController::class, 'adminDownload'])->name('reports.admin.download');

        // Instruction Module
        Route::get('/instruction', [App\Http\Controllers\InstructionController::class, 'index'])->name('instruction.index');
        Route::post('/instruction/curriculum', [App\Http\Controllers\InstructionController::class, 'storeCurriculum'])->name('instruction.curriculum.store');
        Route::delete('/instruction/curriculum/{curriculum}', [App\Http\Controllers\InstructionController::class, 'destroyCurriculum'])->name('instruction.curriculum.destroy');
        Route::get('/instruction/curriculum/{curriculum}', [App\Http\Controllers\InstructionController::class, 'showCurriculum'])->name('instruction.curriculum.show');
        Route::post('/instruction/curriculum/{curriculum}/subject', [App\Http\Controllers\InstructionController::class, 'storeSubject'])->name('instruction.subject.store');
        Route::delete('/instruction/subject/{subject}', [App\Http\Controllers\InstructionController::class, 'destroySubject'])->name('instruction.subject.destroy');
        Route::get('/instruction/subject/{subject}/syllabus', [App\Http\Controllers\InstructionController::class, 'showSyllabus'])->name('instruction.syllabus.show');
        Route::post('/instruction/subject/{subject}/syllabus', [App\Http\Controllers\InstructionController::class, 'storeSyllabus'])->name('instruction.syllabus.store');
        Route::post('/instruction/syllabus/{syllabus}/lesson', [App\Http\Controllers\InstructionController::class, 'storeLesson'])->name('instruction.lesson.store');
        Route::delete('/instruction/lesson/{lesson}', [App\Http\Controllers\InstructionController::class, 'destroyLesson'])->name('instruction.lesson.destroy');
    });

});

require __DIR__.'/auth.php';
