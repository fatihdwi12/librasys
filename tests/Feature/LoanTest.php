<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    private function makeBook(int $stock = 5): Book
    {
        $category = Category::firstOrCreate(['name' => 'Programming']);

        return Book::create([
            'category_id' => $category->id,
            'title' => 'Laravel Basics',
            'author' => 'John Doe',
            'isbn' => fake()->unique()->numerify('#############'),
            'stock' => $stock,
        ]);
    }

    private function makeMember(string $status = 'active'): Member
    {
        return Member::create([
            'member_code' => 'MBR-'.fake()->unique()->numerify('####'),
            'name' => 'Test Member',
            'status' => $status,
        ]);
    }

    private function makeBorrowedLoan(Book $book, Member $member): Loan
    {
        return Loan::create([
            'member_id' => $member->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(7),
            'status' => 'borrowed',
        ]);
    }

    private function dueDate(): string
    {
        return now()->addDays(7)->toDateString();
    }

    public function test_borrow_available_book(): void
    {
        $book = $this->makeBook(5);
        $member = $this->makeMember();

        $this->authenticate()
            ->postJson('/api/loans', [
                'member_id' => $member->id,
                'book_id' => $book->id,
                'due_at' => $this->dueDate(),
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'borrowed');

        $this->assertDatabaseHas('loans', [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'status' => 'borrowed',
        ]);
    }

    public function test_stock_decreases_when_borrowing(): void
    {
        $book = $this->makeBook(5);
        $member = $this->makeMember();

        $this->authenticate()->postJson('/api/loans', [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'due_at' => $this->dueDate(),
        ])->assertCreated();

        $this->assertSame(4, $book->fresh()->stock);
    }

    public function test_cannot_borrow_when_stock_is_zero(): void
    {
        $book = $this->makeBook(0);
        $member = $this->makeMember();

        $this->authenticate()
            ->postJson('/api/loans', [
                'member_id' => $member->id,
                'book_id' => $book->id,
                'due_at' => $this->dueDate(),
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Book is out of stock');

        $this->assertDatabaseCount('loans', 0);
        $this->assertSame(0, $book->fresh()->stock);
    }

    public function test_inactive_member_cannot_borrow(): void
    {
        $book = $this->makeBook(5);
        $member = $this->makeMember('inactive');

        $this->authenticate()
            ->postJson('/api/loans', [
                'member_id' => $member->id,
                'book_id' => $book->id,
                'due_at' => $this->dueDate(),
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Member is not active');

        $this->assertDatabaseCount('loans', 0);
        $this->assertSame(5, $book->fresh()->stock);
    }

    public function test_return_book(): void
    {
        $book = $this->makeBook(4);
        $loan = $this->makeBorrowedLoan($book, $this->makeMember());

        $this->authenticate()
            ->putJson("/api/loans/{$loan->id}/return")
            ->assertOk()
            ->assertJsonPath('data.status', 'returned');

        $loan->refresh();
        $this->assertSame('returned', $loan->status);
        $this->assertNotNull($loan->returned_at);
    }

    public function test_stock_increases_when_returning(): void
    {
        $book = $this->makeBook(4);
        $loan = $this->makeBorrowedLoan($book, $this->makeMember());

        $this->authenticate()
            ->putJson("/api/loans/{$loan->id}/return")
            ->assertOk();

        $this->assertSame(5, $book->fresh()->stock);
    }

    public function test_cannot_return_the_same_loan_twice(): void
    {
        $book = $this->makeBook(4);
        $loan = $this->makeBorrowedLoan($book, $this->makeMember());

        $client = $this->authenticate();
        $client->putJson("/api/loans/{$loan->id}/return")->assertOk();
        $client->putJson("/api/loans/{$loan->id}/return")->assertStatus(409);

        $this->assertSame(5, $book->fresh()->stock);
    }

    public function test_loan_validation_fails(): void
    {
        $this->authenticate()
            ->postJson('/api/loans', [
                'member_id' => 999,
                'book_id' => 999,
                'due_at' => '2020-01-01',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['member_id', 'book_id', 'due_at']);
    }
}