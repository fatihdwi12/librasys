<?php

namespace App\Http\Requests;

class ChangePasswordRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => [
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
                'different:current_password',
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
            'current_password.required' =>
                'Kata sandi saat ini wajib diisi.',

            'current_password.current_password' =>
                'Kata sandi saat ini tidak sesuai.',

            'password.required' =>
                'Kata sandi baru wajib diisi.',

            'password.min' =>
                'Kata sandi baru minimal 15 karakter.',

            'password.max' =>
                'Kata sandi baru maksimal 64 karakter.',

            'password.confirmed' =>
                'Konfirmasi kata sandi baru tidak cocok.',

            'password.different' =>
                'Kata sandi baru harus berbeda dari kata sandi saat ini.',

            'password_confirmation.required' =>
                'Konfirmasi kata sandi baru wajib diisi.',
        ];
    }
}