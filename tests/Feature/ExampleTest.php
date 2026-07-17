<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_authenticated_users_reach_the_dashboard(): void
    {
        $owner = $this->makeOwner();

        $this->actingAs($owner)->get('/')->assertOk();
    }
}
