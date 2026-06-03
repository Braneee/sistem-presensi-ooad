@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan sistem presensi hari ini')

@section('content')

{{-- Flask Offline Warning --}}
@if(! $stats['flask_online'])
    <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <span class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse flex-shrink-0"></span>
        <div>
            <strong>AI Engine Offline</strong> — Sistem face recognition tidak dapat diakses.
            <span class="text-red-600">Pastikan Flask service berjalan: <code class="font-mono text-xs">python run.py</code> di port 5000.</span>
        </div>
    </div>
@endif

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-500 mb-1">Total Mahasiswa</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_students'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#1F4E7915">
                <svg class="w-5 h-5" style="color:#1F4E79" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">{{ $stats['total_classes'] }} kelas aktif</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-500 mb-1">Sesi Hari Ini</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['sessions_today'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#2E75B615">
                <svg class="w-5 h-5" style="color:#2E75B6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">
            <span class="text-green-600 font-medium">{{ $stats['open_sessions'] }} aktif</span> sekarang
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-500 mb-1">Hadir Hari Ini</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['present_today'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#1E7E3415">
                <svg class="w-5 h-5" style="color:#1E7E34" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">
            <span class="text-amber-500 font-medium">{{ $stats['late_today'] }} terlambat</span>
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-gray-500 mb-1">AI Engine</p>
                <p class="text-2xl font-bold {{ $stats['flask_online'] ? 'text-green-600' : 'text-red-500' }}">
                    {{ $stats['flask_online'] ? 'Online' : 'Offline' }}
                </p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center
                        {{ $stats['flask_online'] ? '' : '' }}"
                 style="background:{{ $stats['flask_online'] ? '#1E7E3415' : '#C0392B15' }}">
                <svg class="w-5 h-5" style="color:{{ $stats['flask_online'] ? '#1E7E34' : '#C0392B' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">Flask Recognition Engine</p>
    </div>
</div>

{{-- Main Content Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Sessions Today --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-sm">Sesi Presensi Hari Ini</h3>
            <a href="{{ route('admin.sessions.create') }}"
               class="text-xs px-3 py-1.5 rounded-lg text-white font-medium"
               style="background:#1F4E79">
                + Buat Sesi
            </a>
        </div>

        @if($recentSessions->isEmpty())
            <div class="py-12 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-400 text-sm">Belum ada sesi hari ini.</p>
                <a href="{{ route('admin.sessions.create') }}" class="mt-2 inline-block text-xs text-blue-600 hover:underline">
                    Buat sesi sekarang →
                </a>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($recentSessions as $session)
                    <div class="px-5 py-3.5 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-2 h-2 rounded-full flex-shrink-0 {{ $session->status === 'open' ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $session->title }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $session->classRoom->name }} &mdash;
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-3">
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-700">{{ $session->presentCount() }}</p>
                                <p class="text-xs text-gray-400">hadir</p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $session->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $session->status === 'open' ? 'Aktif' : 'Tutup' }}
                            </span>
                            <a href="{{ route('admin.sessions.monitor', $session) }}"
                               class="text-xs text-blue-600 hover:text-blue-800">
                                Monitor →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recent Attendance --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 text-sm">Kehadiran Terbaru</h3>
        </div>

        @if($recentAttendances->isEmpty())
            <div class="py-10 text-center text-gray-400 text-sm">
                Belum ada kehadiran hari ini.
            </div>
        @else
            <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
                @foreach($recentAttendances as $att)
                    <div class="px-5 py-3 flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                             style="background: {{ $att->status === 'present' ? '#1E7E34' : '#d97706' }}">
                            {{ strtoupper(substr($att->student->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-800 truncate">{{ $att->student->name }}</p>
                            <p class="text-xs text-gray-400">{{ $att->checked_in_at->format('H:i:s') }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0
                            {{ $att->status === 'present' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $att->status === 'present' ? 'Hadir' : 'Terlambat' }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
