<?php

namespace App\Http\Requests;

class StoreCategoryRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ];
    }
}