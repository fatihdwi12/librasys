<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateBookRequest extends ApiFormRequest
{
    public function rules(): array
    {
        // PUT = semua field wajib, PATCH = boleh sebagian
        $required = $this->isMethod('patch') ? 'sometimes' : 'required';

        return [
            'category_id' => [$required, 'integer', 'exists:categories,id'],
            'title' => [$required, 'string', 'max:255'],
            'author' => [$required, 'string', 'max:255'],
            'isbn' => [
                $required,
                'string',
                'max:50',
                Rule::unique('books', 'isbn')->ignore($this->route('book')),
            ],
            'published_year' => 'nullable|integer|min:1000|max:'.date('Y'),
            'stock' => [$required, 'integer', 'min:0'],
            'description' => 'nullable|string',
        ];
    }
}