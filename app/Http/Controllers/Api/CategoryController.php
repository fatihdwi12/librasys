<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $categories = Category::withCount('books')->orderBy('name')->get();

        return $this->success(
            'Categories retrieved successfully',
            CategoryResource::collection($categories)
        );
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return $this->success(
            'Category created successfully',
            new CategoryResource($category),
            201
        );
    }

    public function show(Category $category): JsonResponse
    {
        $category->loadCount('books');

        return $this->success(
            'Category retrieved successfully',
            new CategoryResource($category)
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        return $this->success(
            'Category updated successfully',
            new CategoryResource($category)
        );
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->books()->exists()) {
            return $this->error('Category cannot be deleted because it still has books', 409);
        }

        $category->delete();

        return $this->success('Category deleted successfully');
    }
}