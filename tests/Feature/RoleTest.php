<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_member_account_and_ignores_role_input(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Rina',
            'email' => 'rina@example.com',
            'password' => 'password123',
            'role' => 'admin',
        ])
            ->assertCreated()
            ->assertJsonPath('data.user.role', 'member');

        $user = User::where('email', 'rina@example.com')->first();

        $this->assertSame('member', $user->role);
        $this->assertDatabaseHas('members', [
            'user_id' => $user->id,
            'email' => 'rina@example.com',
            'status' => 'active',
        ]);
    }

    public function test_member_cannot_manage_books(): void
    {
        $this->authenticate('member')
            ->postJson('/api/books', [])
            ->assertStatus(403)
            ->assertJsonPath('message', 'Forbidden');
    }

    public function test_member_can_view_books(): void
    {
        $this->authenticate('member')
            ->getJson('/api/books')
            ->assertOk();
    }

    public function test_member_cannot_access_members_or_loans(): void
    {
        $client = $this->authenticate('member');

        $client->getJson('/api/members')->assertStatus(403);
        $client->getJson('/api/loans')->assertStatus(403);
    }

    public function test_member_sees_only_own_loans(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'member'])->save();

        $member = Member::create([
            'user_id' => $user->id,
            'member_code' => 'MBR-T01',
            'name' => $user->name,
            'status' => 'active',
        ]);
        $otherMember = Member::create([
            'member_code' => 'MBR-T02',
            'name' => 'Other',
            'status' => 'active',
        ]);

        $category = Category::create(['name' => 'Programming']);
        $book = Book::create([
            'category_id' => $category->id,
            'title' => 'Laravel Basics',
            'author' => 'John Doe',
            'isbn' => '9780000000099',
            'stock' => 5,
        ]);

        foreach ([$member, $otherMember] as $borrower) {
            Loan::create([
                'member_id' => $borrower->id,
                'book_id' => $book->id,
                'borrowed_at' => now(),
                'due_at' => now()->addDays(7),
                'status' => 'borrowed',
            ]);
        }

        $token = auth('api')->login($user);

        $this->withToken($token)
            ->getJson('/api/my-loans')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}