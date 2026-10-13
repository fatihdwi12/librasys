<?php

namespace App\Http\Requests;

class ResetMemberPasswordRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'admin_password' => [
                'required',
                'string',
                'current_password:api',
            ],
            'password' => [
                'required',
                'string',
                'min:15',
                'max:64',
                'confirmed',
            ],
            'password_confirmation' => [
                'required',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'admin_password.required' =>
                'Kata sandi admin wajib diisi.',

            'admin_password.current_password' =>
                'Kata sandi admin tidak sesuai.',

            'password.required' =>
                'Kata sandi baru anggota wajib diisi.',

            'password.min' =>
                'Kata sandi baru minimal 15 karakter.',

            'password.max' =>
                'Kata sandi baru maksimal 64 karakter.',

            'password.confirmed' =>
                'Konfirmasi kata sandi tidak cocok.',

            'password_confirmation.required' =>
                'Konfirmasi kata sandi wajib diisi.',
        ];
    }
}