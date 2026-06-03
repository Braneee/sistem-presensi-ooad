<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                  => ['required', 'string', 'max:255'],
            'class_id'               => ['required', 'integer', 'exists:classes,id'],
            'date'                   => ['required', 'date'],
            'start_time'             => ['required', 'date_format:H:i'],
            'end_time'               => ['required', 'date_format:H:i', 'after:start_time'],
            'notes'                  => ['nullable', 'string', 'max:1000'],
            'late_threshold_minutes' => ['nullable', 'integer', 'min:0', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_time.after'      => 'Waktu selesai harus setelah waktu mulai.',
            'class_id.exists'     => 'Kelas tidak ditemukan.',
            'date.required'       => 'Tanggal sesi wajib diisi.',
            'start_time.required' => 'Waktu mulai wajib diisi.',
            'end_time.required'   => 'Waktu selesai wajib diisi.',
        ];
    }
}
