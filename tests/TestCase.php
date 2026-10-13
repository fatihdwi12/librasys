<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function authenticate(string $role = 'admin'): static
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        $token = auth('api')->login($user);

        return $this->withToken($token);
    }
}