<?php

namespace App\Services;

use App\Models\Session;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    public function __construct(private AttendanceService $attendanceService) {}

    /**
     * Generate dan download PDF laporan
     */
    public function generatePdf(Session $session): mixed
    {
        $session->load('classRoom');

        $attendances = Attendance::with('student')
            ->where('session_id', $session->id)
            ->orderBy('checked_in_at')
            ->get();

        $absentStudents = $session->classRoom
            ->students()
            ->active()
            ->whereNotIn('id', $attendances->pluck('student_id'))
            ->orderBy('name')
            ->get();

        $stats = $this->attendanceService->getSessionStats($session);

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'session'        => $session,
            'attendances'    => $attendances,
            'absentStudents' => $absentStudents,
            'stats'          => $stats,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("laporan-presensi-{$session->code}.pdf");
    }

    /**
     * Generate dan download CSV laporan
     */
    public function generateCsv(Session $session): StreamedResponse
    {
        $session->load('classRoom');
        $filename = "laporan-presensi-{$session->code}.csv";

        return response()->streamDownload(function () use ($session) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, ['No', 'NIM', 'Nama Mahasiswa', 'Kelas', 'Waktu Presensi', 'Status', 'Similarity Score']);

            $row = 1;

            // Data hadir (present + late)
            $attendances = Attendance::with('student.classRoom')
                ->where('session_id', $session->id)
                ->orderBy('checked_in_at')
                ->get();

            foreach ($attendances as $a) {
                fputcsv($handle, [
                    $row++,
                    $a->student->nim,
                    $a->student->name,
                    $a->student->classRoom->name ?? '-',
                    $a->checked_in_at->format('d/m/Y H:i:s'),
                    $a->status === 'present' ? 'Hadir' : 'Terlambat',
                    number_format($a->similarity_score * 100, 2) . '%',
                ]);
            }

            // Data absen
            $absentStudents = $session->classRoom
                ->students()
                ->active()
                ->whereNotIn('id', $attendances->pluck('student_id'))
                ->orderBy('name')
                ->get();

            foreach ($absentStudents as $s) {
                fputcsv($handle, [
                    $row++,
                    $s->nim,
                    $s->name,
                    $s->classRoom->name ?? '-',
                    '-',
                    'Absen',
                    '0%',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
