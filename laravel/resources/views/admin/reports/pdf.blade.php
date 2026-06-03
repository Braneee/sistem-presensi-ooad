<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi — {{ $session->title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1a1a1a; }

        .header { padding-bottom: 12px; margin-bottom: 16px; border-bottom: 2px solid #1F4E79; }
        .header h1 { font-size: 16px; color: #1F4E79; margin-bottom: 4px; }
        .header-meta { color: #666; font-size: 9.5px; }

        .stats { display: table; width: 100%; margin-bottom: 16px; }
        .stat-box { display: table-cell; text-align: center; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; width: 25%; }
        .stat-box + .stat-box { margin-left: 8px; }
        .stat-number { font-size: 20px; font-weight: bold; }
        .stat-label { font-size: 9px; color: #666; margin-top: 2px; }

        table { width: 100%; border-collapse: collapse; }
        thead tr th {
            background: #1F4E79;
            color: white;
            padding: 7px 10px;
            text-align: left;
            font-size: 9.5px;
            font-weight: bold;
        }
        tbody tr td { padding: 6px 10px; border-bottom: 1px solid #f0f0f0; font-size: 10px; }
        tbody tr:nth-child(even) td { background: #f9fafb; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 8.5px;
            font-weight: bold;
        }
        .badge-present  { background: #dcfce7; color: #166534; }
        .badge-late     { background: #fef3c7; color: #92400e; }
        .badge-absent   { background: #fee2e2; color: #991b1b; }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h1>Laporan Presensi — {{ $session->title }}</h1>
        <div class="header-meta">
            Kelas: <strong>{{ $session->classRoom->name }}</strong> &nbsp;|&nbsp;
            Tanggal: <strong>{{ $session->date->isoFormat('dddd, D MMMM Y') }}</strong> &nbsp;|&nbsp;
            Waktu: <strong>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</strong> &nbsp;|&nbsp;
            Kode: <strong>{{ $session->code }}</strong> &nbsp;|&nbsp;
            Dicetak: <strong>{{ now()->format('d/m/Y H:i:s') }}</strong>
        </div>
    </div>

    <!-- Stats Row -->
    <table style="margin-bottom:16px">
        <tr>
            <td style="width:25%;padding:8px;text-align:center;border:1px solid #e5e7eb;border-radius:6px">
                <div style="font-size:22px;font-weight:bold;color:#1E7E34">{{ $stats['present'] }}</div>
                <div style="font-size:9px;color:#666">Hadir Tepat Waktu</div>
            </td>
            <td style="width:10px"></td>
            <td style="width:25%;padding:8px;text-align:center;border:1px solid #e5e7eb;border-radius:6px">
                <div style="font-size:22px;font-weight:bold;color:#d97706">{{ $stats['late'] }}</div>
                <div style="font-size:9px;color:#666">Terlambat</div>
            </td>
            <td style="width:10px"></td>
            <td style="width:25%;padding:8px;text-align:center;border:1px solid #e5e7eb;border-radius:6px">
                <div style="font-size:22px;font-weight:bold;color:#C0392B">{{ $stats['absent'] }}</div>
                <div style="font-size:9px;color:#666">Absen</div>
            </td>
            <td style="width:10px"></td>
            <td style="width:25%;padding:8px;text-align:center;border:1px solid #1F4E79;border-radius:6px;background:#f0f7ff">
                <div style="font-size:22px;font-weight:bold;color:#1F4E79">{{ $stats['attendance_rate'] }}%</div>
                <div style="font-size:9px;color:#1F4E79">Tingkat Kehadiran</div>
            </td>
        </tr>
    </table>

    <!-- Attendance Table -->
    <table>
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:14%">NIM</th>
                <th style="width:28%">Nama Mahasiswa</th>
                <th style="width:18%">Waktu Presensi</th>
                <th style="width:12%">Status</th>
                <th style="width:12%">Akurasi AI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $i => $a)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family:monospace">{{ $a->student->nim }}</td>
                    <td>{{ $a->student->name }}</td>
                    <td>{{ $a->checked_in_at->format('H:i:s') }}</td>
                    <td>
                        <span class="badge {{ $a->status === 'present' ? 'badge-present' : 'badge-late' }}">
                            {{ $a->status === 'present' ? 'Hadir' : 'Terlambat' }}
                        </span>
                    </td>
                    <td>{{ number_format($a->similarity_score * 100, 2) }}%</td>
                </tr>
            @endforeach

            @foreach($absentStudents as $j => $s)
                <tr>
                    <td>{{ $attendances->count() + $j + 1 }}</td>
                    <td style="font-family:monospace;color:#999">{{ $s->nim }}</td>
                    <td style="color:#999">{{ $s->name }}</td>
                    <td style="color:#999">—</td>
                    <td>
                        <span class="badge badge-absent">Absen</span>
                    </td>
                    <td style="color:#999">—</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Laporan ini digenerate otomatis oleh Sistem Presensi Face Recognition &mdash;
        {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
