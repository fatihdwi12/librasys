<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends ApiFormRequest
{
    public function rules(): array
    {
        // PUT = semua field wajib, PATCH = boleh sebagian
        $required = $this->isMethod('patch') ? 'sometimes' : 'required';

        return [
            'name' => [
                $required,
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($this->route('category')),
            ],
            'description' => 'nullable|string',
        ];
    }
}