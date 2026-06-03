<?php

namespace App\Http\Controllers;

use App\Models\Session;

class AttendancePageController extends Controller
{
    public function index()
    {
        $openSessions = Session::with('classRoom')
            ->open()
            ->whereDate('date', today())
            ->orderBy('start_time')
            ->get();

        return view('attendance.index', compact('openSessions'));
    }
}
