<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Services\FlaskService;

class DashboardController extends Controller
{
    public function __construct(private FlaskService $flaskService) {}

    public function index()
    {
        $today = today();

        $stats = [
            'total_students'  => Student::active()->count(),
            'total_classes'   => ClassRoom::active()->count(),
            'sessions_today'  => Session::whereDate('date', $today)->count(),
            'open_sessions'   => Session::open()->whereDate('date', $today)->count(),
            'present_today'   => Attendance::whereDate('checked_in_at', $today)->count(),
            'late_today'      => Attendance::whereDate('checked_in_at', $today)->where('status', 'late')->count(),
            'flask_online'    => $this->flaskService->healthCheck(),
        ];

        $recentSessions = Session::with(['classRoom', 'attendances'])
            ->whereDate('date', $today)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $recentAttendances = Attendance::with(['student', 'session'])
            ->whereDate('checked_in_at', $today)
            ->orderByDesc('checked_in_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentSessions', 'recentAttendances'));
    }
}
