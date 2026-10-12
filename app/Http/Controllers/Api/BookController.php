<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    use ApiResponse;

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);

        $books = Book::with('category')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->query('search');

                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->query('category_id'));
            })
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return BookResource::collection($books)->additional([
            'success' => true,
            'message' => 'Books retrieved successfully',
        ]);
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = Book::create($request->validated());
        $book->load('category');

        return $this->success('Book created successfully', new BookResource($book), 201);
    }

    public function show(Book $book): JsonResponse
    {
        $book->load('category');

        return $this->success('Book retrieved successfully', new BookResource($book));
    }

    public function update(UpdateBookRequest $request, Book $book): JsonResponse
    {
        $book->update($request->validated());
        $book->load('category');

        return $this->success('Book updated successfully', new BookResource($book));
    }

    public function destroy(Book $book): JsonResponse
    {
        if ($book->loans()->exists()) {
            return $this->error('Book cannot be deleted because it has loan records', 409);
        }

        $book->delete();

        return $this->success('Book deleted successfully');
    }
}