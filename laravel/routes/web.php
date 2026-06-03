<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\FaceController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AttendancePageController;

// ─── Public: Halaman Presensi (tanpa login) ──────────────────────────────────
Route::get('/', fn() => redirect()->route('attendance.index'));
Route::get('/attendance', [AttendancePageController::class, 'index'])->name('attendance.index');

// ─── Admin Auth ───────────────────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ─── Admin Protected Routes ───────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Students CRUD
    Route::resource('students', StudentController::class);

    // Sessions CRUD
    Route::resource('sessions', SessionController::class);
    Route::patch('/sessions/{session}/close',  [SessionController::class, 'close'])->name('sessions.close');
    Route::patch('/sessions/{session}/reopen', [SessionController::class, 'reopen'])->name('sessions.reopen');
    Route::get('/sessions/{session}/monitor',  [SessionController::class, 'monitor'])->name('sessions.monitor');

    // Face Registration
    Route::get('/faces',                    [FaceController::class, 'index'])->name('faces.index');
    Route::get('/faces/register/{student}', [FaceController::class, 'register'])->name('faces.register');
    Route::delete('/faces/{face}',          [FaceController::class, 'destroy'])->name('faces.destroy');

    // Attendance History
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');

    // Reports
    Route::get('/reports',                      [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/pdf/{session}', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/export/csv/{session}', [ReportController::class, 'exportCsv'])->name('reports.csv');
});
