<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    private function makeBook(array $overrides = []): Book
    {
        $category = Category::firstOrCreate(['name' => 'Programming']);

        return Book::create(array_merge([
            'category_id' => $category->id,
            'title' => 'Laravel Basics',
            'author' => 'John Doe',
            'isbn' => fake()->unique()->numerify('#############'),
            'published_year' => 2024,
            'stock' => 5,
        ], $overrides));
    }

    public function test_get_books(): void
    {
        $this->makeBook();
        $this->makeBook();
        $this->makeBook();

        $this->authenticate()
            ->getJson('/api/books')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');
    }

    public function test_search_and_filter_books(): void
    {
        $this->makeBook(['title' => 'Laravel Basics']);
        $this->makeBook(['title' => 'Clean Code']);

        $this->authenticate()
            ->getJson('/api/books?search=laravel')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_books_are_paginated(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->makeBook();
        }

        $this->authenticate()
            ->getJson('/api/books?per_page=2')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 5);
    }

    public function test_create_book(): void
    {
        $category = Category::create(['name' => 'Database']);

        $this->authenticate()
            ->postJson('/api/books', [
                'category_id' => $category->id,
                'title' => 'Learning SQL',
                'author' => 'Alan Beaulieu',
                'isbn' => '9781492057611',
                'published_year' => 2020,
                'stock' => 4,
            ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Learning SQL');

        $this->assertDatabaseHas('books', ['isbn' => '9781492057611', 'stock' => 4]);
    }

    public function test_get_single_book(): void
    {
        $book = $this->makeBook();

        $this->authenticate()
            ->getJson("/api/books/{$book->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $book->id);
    }

    public function test_update_book(): void
    {
        $book = $this->makeBook();

        $this->authenticate()
            ->putJson("/api/books/{$book->id}", [
                'category_id' => $book->category_id,
                'title' => 'Updated Title',
                'author' => $book->author,
                'isbn' => $book->isbn,
                'stock' => 7,
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated Title');

        $this->assertDatabaseHas('books', ['id' => $book->id, 'stock' => 7]);
    }

    public function test_delete_book(): void
    {
        $book = $this->makeBook();

        $this->authenticate()
            ->deleteJson("/api/books/{$book->id}")
            ->assertOk();

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_book_validation_fails(): void
    {
        $this->authenticate()
            ->postJson('/api/books', ['stock' => -1])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['category_id', 'title', 'author', 'isbn', 'stock']);
    }

    public function test_isbn_must_be_unique(): void
    {
        $book = $this->makeBook();

        $this->authenticate()
            ->postJson('/api/books', [
                'category_id' => $book->category_id,
                'title' => 'Duplicate',
                'author' => 'X',
                'isbn' => $book->isbn,
                'stock' => 1,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['isbn']);
    }
}