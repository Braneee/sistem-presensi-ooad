<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceDetectRequest;
use App\Models\Session;
use App\Services\FlaskService;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class AttendanceApiController extends Controller
{
    public function __construct(
        private FlaskService $flaskService,
        private AttendanceService $attendanceService
    ) {}

    /**
     * POST /api/attendance/detect
     * Endpoint utama presensi via face recognition (dipanggil dari browser/webcam)
     */
    public function detect(AttendanceDetectRequest $request): JsonResponse
    {
        try {
            // 1. Ambil dan validasi session
            $session = Session::with('classRoom')
                ->findOrFail($request->session_id);

            if (! $session->isOpen()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi presensi sudah ditutup.',
                    'code'    => 'SESSION_CLOSED',
                ], 422);
            }

            // 2. Kirim ke Flask untuk face recognition
            $flaskResult = $this->flaskService->recognize($request->image);

            // 3. Cek hasil Flask
            if (! ($flaskResult['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => $flaskResult['message'] ?? 'Wajah tidak dikenali.',
                    'code'    => $flaskResult['code'] ?? 'FACE_NOT_RECOGNIZED',
                ], 422);
            }

            $studentId       = (int) $flaskResult['student_id'];
            $similarityScore = (float) $flaskResult['similarity'];

            // 4. Simpan captured photo
            $photoPath = $this->saveCapturedPhoto($request->image, $studentId);

            // 5. Catat attendance
            $attendance = $this->attendanceService->record(
                $session,
                $studentId,
                $similarityScore,
                $photoPath,
                $request->ip() ?? '0.0.0.0'
            );

            $attendance->load('student');

            Log::info('Attendance recorded', [
                'student_id' => $studentId,
                'session_id' => $session->id,
                'status'     => $attendance->status,
                'similarity' => $similarityScore,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Presensi berhasil! Selamat datang, {$attendance->student->name}.",
                'data'    => [
                    'student' => [
                        'nim'  => $attendance->student->nim,
                        'name' => $attendance->student->name,
                    ],
                    'status'      => $attendance->status,
                    'checked_in'  => $attendance->checked_in_at->format('H:i:s'),
                    'similarity'  => number_format($similarityScore * 100, 1) . '%',
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak ditemukan.',
                'code'    => 'SESSION_NOT_FOUND',
            ], 404);

        } catch (Exception $e) {
            // Handle duplicate
            if (str_contains($e->getMessage(), 'sudah melakukan presensi')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'code'    => 'DUPLICATE_ATTENDANCE',
                ], 409);
            }

            // Handle Flask down
            if (str_contains($e->getMessage(), 'tidak dapat dijangkau')
                || str_contains($e->getMessage(), 'tidak tersedia')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'code'    => 'AI_ENGINE_DOWN',
                ], 503);
            }

            Log::error('Attendance detect error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
                'code'    => 'INTERNAL_ERROR',
            ], 500);
        }
    }

    /**
     * GET /api/dashboard/stats
     */
    public function dashboardStats(): JsonResponse
    {
        $today = today();

        return response()->json([
            'success' => true,
            'data'    => [
                'sessions_today' => Session::whereDate('date', $today)->count(),
                'open_sessions'  => Session::open()->whereDate('date', $today)->count(),
                'total_present'  => \App\Models\Attendance::whereDate('checked_in_at', $today)->count(),
                'total_students' => \App\Models\Student::active()->count(),
                'flask_status'   => $this->flaskService->healthCheck() ? 'online' : 'offline',
            ],
        ]);
    }

    /**
     * GET /api/sessions/{session}/stats
     */
    public function sessionStats(Session $session): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->attendanceService->getSessionStats($session),
        ]);
    }

    // ─── Private ──────────────────────────────────────────────────────────────

    private function saveCapturedPhoto(string $base64Image, int $studentId): string
    {
        try {
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
            $decoded   = base64_decode($imageData);
            $filename  = 'captures/' . $studentId . '_' . time() . '.jpg';

            Storage::disk('public')->put($filename, $decoded);

            return $filename;
        } catch (Exception $e) {
            Log::warning('Failed to save captured photo: ' . $e->getMessage());
            return '';
        }
    }
}
