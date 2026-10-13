<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class RegisterRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge([
                'email' => strtolower(trim($this->input('email'))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('members', 'email'),
            ],

            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' =>
                'Email sudah terdaftar sebagai akun atau anggota. ' .
                'Jika belum memiliki akun login, hubungi admin.',
        ];
    }
}