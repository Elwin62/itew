<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ScheduleController;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware(['auth', 'verified'])->group(function () {

    // ── Shared ─────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Faculty-only (MUST be before resource routes!) ─────────────
    Route::middleware('role:Faculty')->group(function () {
        Route::get('/faculty/profile', [FacultyController::class, 'myProfile'])->name('faculty.my-profile');
        Route::get('/schedules/my', [ScheduleController::class, 'mySchedules'])->name('schedules.my');
    });

    // ── Student-only ───────────────────────────────────────────────
    Route::middleware('role:Student')->group(function () {
        Route::get('/student/profile', [StudentController::class, 'myProfile'])->name('student.my-profile');
    });

    // ── Admin-only (resource routes last to avoid wildcard conflicts)
    Route::middleware('role:Admin')->group(function () {
        Route::resource('students', StudentController::class);
        Route::resource('faculty', FacultyController::class);
        Route::get('/queries/basketball', [QueryController::class, 'basketball'])->name('queries.basketball');
        Route::get('/queries/programming', [QueryController::class, 'programming'])->name('queries.programming');
        Route::get('/queries/skill/{skill}', [QueryController::class, 'custom'])->name('queries.custom');
        Route::resource('events', EventController::class);
        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::post('/admin/seed', [AdminController::class, 'seedDatabase'])->name('admin.seed');
        Route::delete('/admin/logs/{log}', [AdminController::class, 'destroyLog'])->name('admin.logs.destroy');
    });

});

require __DIR__.'/auth.php';
