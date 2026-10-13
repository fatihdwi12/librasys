<?php

namespace App\Http\Requests;

class CreateMemberAccountRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return auth('api')->check()
            && auth('api')->user()->role === 'admin';
    }

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
            'admin_password.current_password' =>
                'Kata sandi admin saat ini tidak sesuai.',

            'password.min' =>
                'Kata sandi anggota minimal 15 karakter.',

            'password.max' =>
                'Kata sandi anggota maksimal 64 karakter.',

            'password.confirmed' =>
                'Konfirmasi kata sandi anggota tidak cocok.',
        ];
    }
}