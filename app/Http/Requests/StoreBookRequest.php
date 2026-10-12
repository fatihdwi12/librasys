<?php

namespace App\Http\Requests;

class StoreBookRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:50|unique:books,isbn',
            'published_year' => 'nullable|integer|min:1000|max:'.date('Y'),
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ];
    }
}