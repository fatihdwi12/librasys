<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreMemberRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $email = strtolower(trim($this->input('email')));

            $this->merge([
                'email' => $email !== '' ? $email : null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'member_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('members', 'member_code'),
            ],

            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('members', 'email'),
                Rule::unique('users', 'email'),
            ],

            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'member_code.unique' => 'Kode anggota sudah digunakan.',

            'email.unique' =>
                'Email sudah digunakan oleh anggota atau akun yang ada. ' .
                'Jangan membuat record anggota kedua.',
        ];
    }
}