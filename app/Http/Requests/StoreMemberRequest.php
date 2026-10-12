<?php

namespace App\Http\Requests;

class StoreMemberRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'member_code' => 'required|string|max:50|unique:members,member_code',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];
    }
}