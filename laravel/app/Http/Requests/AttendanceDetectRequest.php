<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceDetectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => ['required', 'integer', 'exists:sessions,id'],
            'image'      => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    // Validasi format base64 image
                    if (! preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $value)) {
                        $fail('Format gambar tidak valid. Gunakan base64 JPEG/PNG/WebP.');
                        return;
                    }
                    // Validasi ukuran maksimal 5MB
                    $base64  = preg_replace('/^data:image\/\w+;base64,/', '', $value);
                    $decoded = base64_decode($base64, true);
                    if ($decoded === false) {
                        $fail('Data gambar tidak valid (corrupt base64).');
                        return;
                    }
                    if (strlen($decoded) > 5 * 1024 * 1024) {
                        $fail('Ukuran gambar maksimal 5MB.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'session_id.required' => 'Sesi presensi harus dipilih.',
            'session_id.exists'   => 'Sesi presensi tidak ditemukan.',
            'image.required'      => 'Gambar wajah wajib disertakan.',
        ];
    }
}
