@extends('layouts.admin')

@section('title', 'Edit Sesi')
@section('page-title', 'Edit Sesi Presensi')
@section('page-subtitle', 'Perbarui sesi: ' . $session->title)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        @if($session->status === 'closed')
            <div class="mb-5 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-700 text-sm">
                ⚠️ Sesi ini sudah ditutup. Perubahan tetap bisa disimpan, namun tidak akan mengaktifkan kembali sesi.
            </div>
        @endif

        <form action="{{ route('admin.sessions.update', $session) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Sesi <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $session->title) }}" required
                           class="w-full border {{ $errors->has('title') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kelas <span class="text-red-500">*</span></label>
                    <select name="class_id"
                            class="w-full border {{ $errors->has('class_id') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="">— Pilih Kelas —</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $session->class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->name }} ({{ $class->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('class_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', $session->date->format('Y-m-d')) }}" required
                           class="w-full border {{ $errors->has('date') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @error('date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Waktu Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time"
                           value="{{ old('start_time', \Carbon\Carbon::parse($session->start_time)->format('H:i')) }}"
                           required
                           class="w-full border {{ $errors->has('start_time') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @error('start_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Waktu Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time"
                           value="{{ old('end_time', \Carbon\Carbon::parse($session->end_time)->format('H:i')) }}"
                           required
                           class="w-full border {{ $errors->has('end_time') ? 'border-red-400' : 'border-gray-300' }} rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @error('end_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Toleransi Keterlambatan (menit)</label>
                    <input type="number" name="late_threshold_minutes"
                           value="{{ old('late_threshold_minutes', $session->late_threshold_minutes) }}"
                           min="0" max="120"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 resize-none">{{ old('notes', $session->notes) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6 pt-5 border-t border-gray-100">
                <a href="{{ route('admin.sessions.monitor', $session) }}"
                   class="text-sm text-blue-600 hover:text-blue-800">← Monitor Sesi</a>
                <div class="flex gap-3">
                    <a href="{{ route('admin.sessions.index') }}"
                       class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100">Batal</a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold"
                            style="background:#1F4E79">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
