<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateMemberRequest extends ApiFormRequest
{
    public function rules(): array
    {
        // PUT = semua field wajib, PATCH = boleh sebagian
        $required = $this->isMethod('patch') ? 'sometimes' : 'required';

        return [
            'member_code' => [
                $required,
                'string',
                'max:50',
                Rule::unique('members', 'member_code')->ignore($this->route('member')),
            ],
            'name' => [$required, 'string', 'max:255'],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => [$required, 'in:active,inactive'],
        ];
    }
}