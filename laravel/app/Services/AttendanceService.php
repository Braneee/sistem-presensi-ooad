<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Session;
use Carbon\Carbon;
use Exception;

class AttendanceService
{
    /**
     * Catat kehadiran setelah face recognition sukses
     */
    public function record(
        Session $session,
        int $studentId,
        float $similarityScore,
        string $capturedPhoto,
        string $ipAddress
    ): Attendance {

        // 1. Validasi session masih open
        if (! $session->isOpen()) {
            throw new Exception('Sesi presensi sudah ditutup.');
        }

        // 2. Cegah duplicate attendance
        $existing = Attendance::where('session_id', $session->id)
            ->where('student_id', $studentId)
            ->first();

        if ($existing) {
            throw new Exception('Anda sudah melakukan presensi pada sesi ini.');
        }

        // 3. Tentukan status hadir (present / late)
        $status = $this->determineStatus($session);

        // 4. Simpan
        return Attendance::create([
            'session_id'       => $session->id,
            'student_id'       => $studentId,
            'checked_in_at'    => now(),
            'status'           => $status,
            'similarity_score' => $similarityScore,
            'captured_photo'   => $capturedPhoto,
            'ip_address'       => $ipAddress,
        ]);
    }

    /**
     * Tentukan apakah mahasiswa hadir tepat waktu atau terlambat
     */
    private function determineStatus(Session $session): string
    {
        try {
            $startTime    = Carbon::parse(
                $session->date->format('Y-m-d') . ' ' . $session->start_time
            );
            $lateDeadline = $startTime->copy()->addMinutes($session->late_threshold_minutes);

            return now()->gt($lateDeadline) ? 'late' : 'present';
        } catch (\Exception $e) {
            return 'present'; // Default ke present jika parsing gagal
        }
    }

    /**
     * Statistik kehadiran untuk satu sesi
     */
    public function getSessionStats(Session $session): array
    {
        $totalStudents = $session->classRoom
            ? $session->classRoom->activeStudentsCount()
            : 0;

        $presentCount = $session->attendances()->where('status', 'present')->count();
        $lateCount    = $session->attendances()->where('status', 'late')->count();
        $checkedIn    = $presentCount + $lateCount;
        $absentCount  = max(0, $totalStudents - $checkedIn);

        return [
            'total'           => $totalStudents,
            'present'         => $presentCount,
            'late'            => $lateCount,
            'absent'          => $absentCount,
            'checked_in'      => $checkedIn,
            'attendance_rate' => $totalStudents > 0
                ? round(($checkedIn / $totalStudents) * 100, 1)
                : 0.0,
        ];
    }

    /**
     * Statistik global hari ini
     */
    public function getTodayStats(): array
    {
        $today = today();

        return [
            'total_present' => Attendance::whereDate('checked_in_at', $today)->count(),
            'total_late'    => Attendance::whereDate('checked_in_at', $today)->where('status', 'late')->count(),
        ];
    }
}
