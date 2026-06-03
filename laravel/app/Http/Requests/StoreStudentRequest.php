<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student')?->id;

        return [
            'nim'      => [
                'required', 'string', 'max:20',
                Rule::unique('students', 'nim')->ignore($studentId)->whereNull('deleted_at'),
            ],
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'nullable', 'email', 'max:255',
                Rule::unique('students', 'email')->ignore($studentId)->whereNull('deleted_at'),
            ],
            'phone'    => ['nullable', 'string', 'max:20'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'gender'   => ['required', 'in:L,P'],
            'photo'    => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'is_active'=> ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nim.unique'       => 'NIM sudah terdaftar.',
            'email.unique'     => 'Email sudah digunakan.',
            'class_id.exists'  => 'Kelas tidak ditemukan.',
            'gender.in'        => 'Jenis kelamin harus L atau P.',
            'photo.max'        => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
