<?php

namespace App\Http\Requests;

class UpdateLoanRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $required = $this->isMethod('patch') ? 'sometimes' : 'required';

        return [
            'due_at' => [$required, 'date', 'after_or_equal:today'],
        ];
    }
}