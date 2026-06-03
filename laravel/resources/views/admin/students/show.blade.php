@extends('layouts.admin')

@section('title', 'Detail Mahasiswa')
@section('page-title', 'Detail Mahasiswa')
@section('page-subtitle', $student->name . ' — ' . $student->nim)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Profile Card --}}
    <div class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex flex-col items-center text-center mb-4">
                <img src="{{ $student->photo_url }}" alt="{{ $student->name }}"
                     class="w-20 h-20 rounded-2xl object-cover border-2 border-gray-100 mb-3">
                <h3 class="font-semibold text-gray-800">{{ $student->name }}</h3>
                <p class="text-xs text-gray-400 font-mono">{{ $student->nim }}</p>
                <span class="mt-2 px-3 py-1 rounded-full text-xs font-medium
                    {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <dl class="space-y-2.5 text-sm">
                <div class="flex justify-between border-t border-gray-50 pt-2.5">
                    <dt class="text-gray-500">Kelas</dt>
                    <dd class="font-medium text-gray-800">{{ $student->classRoom->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Jenis Kelamin</dt>
                    <dd class="text-gray-700">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Email</dt>
                    <dd class="text-gray-700 text-xs truncate max-w-[140px]">{{ $student->email ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Telepon</dt>
                    <dd class="text-gray-700">{{ $student->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between border-t border-gray-50 pt-2.5">
                    <dt class="text-gray-500">Wajah Terdaftar</dt>
                    <dd>
                        @if($student->hasFaceRegistered())
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">✓ Ya</span>
                        @else
                            <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">Belum</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Total Presensi</dt>
                    <dd class="font-semibold text-gray-800">{{ $student->attendances->count() }} sesi</dd>
                </div>
            </dl>

            <div class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
                <a href="{{ route('admin.students.edit', $student) }}"
                   class="flex-1 py-2 text-xs text-center rounded-lg font-medium text-white"
                   style="background:#1F4E79">
                    Edit
                </a>
                <a href="{{ route('admin.faces.register', $student) }}"
                   class="flex-1 py-2 text-xs text-center rounded-lg font-medium text-white"
                   style="background:#2E75B6">
                    Wajah
                </a>
            </div>
        </div>
    </div>

    {{-- Attendance History --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 text-sm">Riwayat Kehadiran</h3>
            </div>

            @if($student->attendances->isEmpty())
                <div class="py-12 text-center text-gray-400 text-sm">
                    Belum ada riwayat kehadiran.
                </div>
            @else
                <div class="divide-y divide-gray-50 max-h-[500px] overflow-y-auto">
                    @foreach($student->attendances->sortByDesc('checked_in_at') as $att)
                        <div class="px-5 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $att->session->title }}</p>
                                <p class="text-xs text-gray-400">{{ $att->session->date->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500">{{ $att->checked_in_at->format('H:i:s') }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $att->status === 'present' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $att->status === 'present' ? 'Hadir' : 'Terlambat' }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ number_format($att->similarity_score * 100, 1) }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
