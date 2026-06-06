@extends('layouts.admin')

@section('title', 'Mahasiswa')
@section('page-title', 'Data Mahasiswa')
@section('page-subtitle', 'Kelola data mahasiswa terdaftar')

@section('content')

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <form method="GET" action="{{ route('admin.students.index') }}" class="flex items-center gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama, NIM, email, atau kelas..."
                class="w-full md:w-80 px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

            <button type="submit" class="px-4 py-2.5 rounded-xl text-white text-sm font-semibold"
                style="background:#1F4E79">
                Cari
            </button>

            @if(request('search'))
                <a href="{{ route('admin.students.index') }}"
                    class="px-4 py-2.5 rounded-xl bg-gray-100 text-gray-600 text-sm font-semibold hover:bg-gray-200">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('admin.students.create') }}"
            class="px-4 py-2.5 rounded-xl text-white text-sm font-semibold flex items-center justify-center gap-2"
            style="background:#1F4E79">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Mahasiswa
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-sm">Daftar Mahasiswa</h3>
            <span class="text-xs text-gray-400">Total: {{ $students->total() }} mahasiswa</span>
        </div>

        @if($students->isEmpty())
            <div class="py-16 text-center text-gray-400 text-sm">
                @if(request('search'))
                    Data mahasiswa dengan kata kunci
                    <span class="font-semibold text-gray-600">"{{ request('search') }}"</span>
                    tidak ditemukan.
                    <br>
                    <a href="{{ route('admin.students.index') }}" class="text-blue-600 hover:underline">
                        Tampilkan semua data →
                    </a>
                @else
                    Belum ada mahasiswa.
                    <a href="{{ route('admin.students.create') }}" class="text-blue-600 hover:underline">
                        Tambah sekarang →
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mahasiswa</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NIM</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Wajah</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($students as $student)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $student->photo_url }}" alt="{{ $student->name }}"
                                            class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $student->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $student->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600 font-mono text-xs">{{ $student->nim }}</td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $student->classRoom->name }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($student->hasFaceRegistered())
                                        <span
                                            class="inline-flex items-center gap-1 text-xs text-green-700 bg-green-100 px-2 py-0.5 rounded-full font-medium">
                                            ✓ Terdaftar
                                        </span>
                                    @else
                                        <a href="{{ route('admin.faces.register', $student) }}"
                                            class="inline-flex items-center gap-1 text-xs text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full font-medium hover:bg-amber-200 transition-colors">
                                            + Daftar
                                        </a>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-xs font-medium
                                                            {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.students.edit', $student) }}"
                                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                        <a href="{{ route('admin.faces.register', $student) }}"
                                            class="text-xs text-purple-600 hover:text-purple-800 font-medium">Wajah</a>
                                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Hapus mahasiswa {{ $student->name }}?')"
                                                class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $students->links() }}
            </div>
        @endif
    </div>

@endsection