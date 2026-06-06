@extends('layouts.admin')

@section('title', 'Buat Sesi')
@section('page-title', 'Buat Sesi Presensi')
@section('page-subtitle', 'Tambah sesi kehadiran baru')

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <form action="{{ route('admin.sessions.store') }}" method="POST">
            @csrf 

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Title --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Sesi <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           placeholder="cth: Pemrograman Web - Pertemuan 3"
                           class="w-full border {{ $errors->has('title') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Class --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kelas <span class="text-red-500">*</span></label>
                    <select name="class_id"
                            class="w-full border {{ $errors->has('class_id') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="">— Pilih Kelas —</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }} ({{ $class->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('class_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', today()->format('Y-m-d')) }}"
                           class="w-full border {{ $errors->has('date') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @error('date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Start Time --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Waktu Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" value="{{ old('start_time', '08:00') }}"
                           class="w-full border {{ $errors->has('start_time') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @error('start_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- End Time --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Waktu Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" value="{{ old('end_time', '10:00') }}"
                           class="w-full border {{ $errors->has('end_time') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @error('end_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Late threshold --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Batas Toleransi Keterlambatan (menit)</label>
                    <input type="number" name="late_threshold_minutes"
                           value="{{ old('late_threshold_minutes', 15) }}"
                           min="0" max="120"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-400">Mahasiswa dihitung terlambat setelah menit ini</p>
                </div>

                {{-- Notes --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan (opsional)</label>
                    <textarea name="notes" rows="2"
                              placeholder="Catatan untuk sesi ini..."
                              class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-5 border-t border-gray-100">
                <a href="{{ route('admin.sessions.index') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition-all hover:opacity-90"
                        style="background:#1F4E79">
                    Buat Sesi
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
