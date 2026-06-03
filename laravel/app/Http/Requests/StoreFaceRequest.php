<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'image'      => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (! preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $value)) {
                        $fail('Format gambar tidak valid.');
                        return;
                    }
                    $base64  = preg_replace('/^data:image\/\w+;base64,/', '', $value);
                    $decoded = base64_decode($base64, true);
                    if ($decoded === false || strlen($decoded) > 5 * 1024 * 1024) {
                        $fail('Gambar tidak valid atau terlalu besar (maks 5MB).');
                    }
                },
            ],
        ];
    }
}
