<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_works(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Fatih',
            'email' => 'fatih@example.com',
            'password' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['user', 'token' => ['access_token', 'token_type', 'expires_in']],
            ]);

        $this->assertDatabaseHas('users', ['email' => 'fatih@example.com']);
    }

    public function test_register_validation_fails(): void
    {
        $this->postJson('/api/register', [])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_login_works(): void
    {
        User::factory()->create(['email' => 'user@example.com']);

        $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token' => ['access_token']]]);
    }

    public function test_invalid_login_is_rejected(): void
    {
        User::factory()->create(['email' => 'user@example.com']);

        $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ])
            ->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_protected_endpoints_require_jwt(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
        $this->getJson('/api/books')->assertUnauthorized();
        $this->getJson('/api/loans')->assertUnauthorized();
    }

    public function test_me_returns_authenticated_user(): void
    {
        $this->authenticate()
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonStructure(['data' => ['id', 'name', 'email']]);
    }

    public function test_logout_works(): void
    {
        $this->authenticate()
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}