@extends('layouts.admin')

@section('title', 'Data Kehadiran')
@section('page-title', 'Data Kehadiran')
@section('page-subtitle', 'Riwayat seluruh presensi mahasiswa')

@section('content')

{{-- Filter --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
    <form method="GET" class="flex items-end gap-3 flex-wrap">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Filter Sesi</label>
            <select name="session_id"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                <option value="">Semua Sesi</option>
                @foreach($sessions as $s)
                    <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>
                        {{ $s->title }} ({{ $s->date->format('d/m/Y') }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal</label>
            <input type="date" name="date" value="{{ request('date') }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select name="status"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="">Semua Status</option>
                <option value="present"  {{ request('status') === 'present'  ? 'selected' : '' }}>Hadir</option>
                <option value="late"     {{ request('status') === 'late'     ? 'selected' : '' }}>Terlambat</option>
            </select>
        </div>
        <button type="submit"
                class="px-4 py-2 rounded-lg text-white text-sm font-medium"
                style="background:#1F4E79">
            Filter
        </button>
        @if(request()->hasAny(['session_id','date','status']))
            <a href="{{ route('admin.attendance.index') }}"
               class="px-4 py-2 rounded-lg text-gray-600 text-sm font-medium bg-gray-100 hover:bg-gray-200">
                Reset
            </a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-sm">Riwayat Kehadiran</h3>
        <span class="text-xs text-gray-400">{{ $attendances->total() }} record</span>
    </div>

    @if($attendances->isEmpty())
        <div class="py-16 text-center text-gray-400 text-sm">
            Tidak ada data kehadiran sesuai filter.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mahasiswa</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sesi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Akurasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($attendances as $att)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $att->student->name }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $att->student->nim }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-gray-700">{{ $att->session->title }}</p>
                                <p class="text-xs text-gray-400">{{ $att->session->date->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-5 py-3 text-gray-600 text-xs">
                                {{ $att->checked_in_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $att->status === 'present' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $att->status === 'present' ? 'Hadir' : 'Terlambat' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center text-xs text-gray-500">
                                {{ number_format($att->similarity_score * 100, 1) }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $attendances->links() }}
        </div>
    @endif
</div>

@endsection
