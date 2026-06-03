<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSessionRequest;
use App\Models\Session;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;

class SessionApiController extends Controller
{
    public function __construct(private AttendanceService $attendanceService) {}

    /**
     * GET /api/sessions/{session}/attendance
     * Digunakan untuk polling realtime di halaman monitor
     */
    public function getAttendance(Session $session): JsonResponse
    {
        $attendances = Attendance::with('student')
            ->where('session_id', $session->id)
            ->orderByDesc('checked_in_at')
            ->get()
            ->map(fn($a) => [
                'id'         => $a->id,
                'nim'        => $a->student->nim,
                'name'       => $a->student->name,
                'status'     => $a->status,
                'checked_in' => $a->checked_in_at->format('H:i:s'),
                'similarity' => number_format($a->similarity_score * 100, 1) . '%',
            ]);

        return response()->json([
            'success'     => true,
            'attendances' => $attendances,
            'stats'       => $this->attendanceService->getSessionStats($session),
            'session'     => [
                'id'     => $session->id,
                'status' => $session->status,
                'title'  => $session->title,
            ],
        ]);
    }

    /**
     * PATCH /api/sessions/{session}/close
     */
    public function close(Session $session): JsonResponse
    {
        $session->update(['status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'Sesi berhasil ditutup.',
        ]);
    }

    public function index(): JsonResponse
    {
        $sessions = Session::with('classRoom')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $sessions]);
    }

    public function store(StoreSessionRequest $request): JsonResponse
    {
        $session = Session::create([
            ...$request->validated(),
            'code'       => 'SES-' . strtoupper(\Str::random(6)),
            'created_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $session], 201);
    }

    public function show(Session $session): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $session->load('classRoom', 'attendances.student')]);
    }

    public function update(StoreSessionRequest $request, Session $session): JsonResponse
    {
        $session->update($request->validated());
        return response()->json(['success' => true, 'data' => $session]);
    }

    public function destroy(Session $session): JsonResponse
    {
        $session->delete();
        return response()->json(['success' => true, 'message' => 'Sesi dihapus.']);
    }
}
