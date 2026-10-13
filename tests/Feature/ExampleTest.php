<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_login_and_register_pages_are_accessible(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }
}