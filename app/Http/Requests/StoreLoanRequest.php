<?php

namespace App\Http\Requests;

class StoreLoanRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'member_id' => 'required|integer|exists:members,id',
            'book_id' => 'required|integer|exists:books,id',
            'due_at' => 'required|date|after_or_equal:today',
        ];
    }
}