@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Laporan Presensi')
@section('page-subtitle', 'Export dan unduh laporan kehadiran')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-sm">Semua Sesi — Pilih untuk Export</h3>
    </div>

    @if($sessions->isEmpty())
        <div class="py-16 text-center text-gray-400 text-sm">
            Belum ada sesi presensi.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sesi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Kehadiran</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Export</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($sessions as $session)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-gray-800">{{ $session->title }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $session->code }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $session->classRoom->name }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $session->date->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="font-semibold text-gray-800">{{ $session->presentCount() }}</div>
                                <div class="text-xs text-gray-400">{{ $session->attendanceRate() }}%</div>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $session->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $session->status === 'open' ? 'Aktif' : 'Tutup' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.reports.pdf', $session) }}"
                                       class="px-3 py-1.5 text-xs rounded-lg text-white font-medium"
                                       style="background:#C0392B">
                                        📄 PDF
                                    </a>
                                    <a href="{{ route('admin.reports.csv', $session) }}"
                                       class="px-3 py-1.5 text-xs rounded-lg text-white font-medium"
                                       style="background:#1E7E34">
                                        📊 CSV
                                    </a>
                                    <a href="{{ route('admin.sessions.monitor', $session) }}"
                                       class="px-3 py-1.5 text-xs rounded-lg text-gray-600 font-medium bg-gray-100 hover:bg-gray-200">
                                        Monitor
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $sessions->links() }}
        </div>
    @endif
</div>

@endsection
