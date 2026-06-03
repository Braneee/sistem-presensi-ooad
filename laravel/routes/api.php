<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\SessionApiController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\FaceApiController;

// ─── Public API (browser → Laravel, CSRF + rate limit) ───────────────────────
Route::post('/attendance/detect', [AttendanceApiController::class, 'detect'])
    ->middleware(['throttle:30,1'])
    ->name('api.attendance.detect');

// ─── Session Monitor polling (no auth required for live monitor) ──────────────
Route::get('/sessions/{session}/attendance', [SessionApiController::class, 'getAttendance'])
    ->name('api.sessions.attendance');

Route::get('/sessions/{session}/stats', [AttendanceApiController::class, 'sessionStats'])
    ->name('api.sessions.stats');

// ─── Admin API (Sanctum authenticated) ───────────────────────────────────────
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard/stats', [AttendanceApiController::class, 'dashboardStats']);

    Route::apiResource('sessions', SessionApiController::class);
    Route::patch('/sessions/{session}/close', [SessionApiController::class, 'close']);

    Route::apiResource('students', StudentApiController::class);

    Route::post('/faces/register', [FaceApiController::class, 'register']);
    Route::delete('/faces/{face}', [FaceApiController::class, 'destroy']);
});
