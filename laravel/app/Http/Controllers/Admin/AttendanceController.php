<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Session;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['student.classRoom', 'session'])
            ->orderByDesc('checked_in_at');

        if ($request->session_id) {
            $query->where('session_id', $request->session_id);
        }

        if ($request->date) {
            $query->whereDate('checked_in_at', $request->date);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $attendances = $query->paginate(25)->withQueryString();
        $sessions    = Session::orderByDesc('date')->take(50)->get();

        return view('admin.attendance.index', compact('attendances', 'sessions'));
    }
}
