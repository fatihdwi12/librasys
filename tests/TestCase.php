<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function authenticate(): static
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        return $this->withToken($token);
    }
}