<?php

namespace App\Http\Requests;

use App\Models\Member;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateMemberRequest extends ApiFormRequest
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
        /** @var Member $member */
        $member = $this->route('member');

        $required = $this->isMethod('patch')
            ? 'sometimes'
            : 'required';

        $uniqueUserEmail = Rule::unique('users', 'email');

        if ($member->user_id !== null) {
            $uniqueUserEmail->ignore($member->user_id);
        }

        return [
            'member_code' => [
                $required,
                'string',
                'max:50',
                Rule::unique('members', 'member_code')->ignore($member),
            ],

            'name' => [$required, 'string', 'max:255'],

            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
                Rule::unique('members', 'email')->ignore($member),
                $uniqueUserEmail,
            ],

            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'status' => [$required, Rule::in(['active', 'inactive'])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var Member $member */
            $member = $this->route('member');

            if ($member->user_id === null) {
                return;
            }

            // PATCH tanpa field email tidak bermaksud mengubah email.
            if (! array_key_exists('email', $this->all())) {
                return;
            }

            if ($validator->errors()->has('email')) {
                return;
            }

            $currentEmail = strtolower(trim((string) $member->email));
            $submittedEmail = strtolower(trim((string) $this->input('email')));

            if ($submittedEmail !== $currentEmail) {
                $validator->errors()->add(
                    'email',
                    'Email anggota yang memiliki akun login belum dapat ' .
                    'diubah atau dihapus melalui formulir ini.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'member_code.unique' => 'Kode anggota sudah digunakan.',
            'email.unique' => 'Email sudah digunakan oleh anggota atau akun lain.',
        ];
    }
}