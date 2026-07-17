<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModalCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_htmx_request_returns_the_client_modal_partial(): void
    {
        $owner = $this->makeOwner();

        $this->actingAs($owner)
            ->withHeaders(['HX-Request' => 'true'])
            ->get('/clients/create')
            ->assertOk()
            ->assertSee('modal-title', false)
            ->assertSee('New Client');
    }

    public function test_valid_htmx_create_redirects_via_header(): void
    {
        $owner = $this->makeOwner();

        $response = $this->actingAs($owner)
            ->withHeaders(['HX-Request' => 'true'])
            ->post('/clients', ['name' => 'Acme Ltd', 'email' => 'a@acme.test']);

        $response->assertNoContent();               // 204
        $response->assertHeader('HX-Redirect');     // client-side redirect
        $this->assertDatabaseHas('clients', [
            'name' => 'Acme Ltd',
            'organisation_id' => $owner->organisation_id,
        ]);
    }

    public function test_invalid_htmx_create_rerenders_the_form_with_errors(): void
    {
        $owner = $this->makeOwner();

        $this->actingAs($owner)
            ->withHeaders(['HX-Request' => 'true'])
            ->post('/clients', ['name' => '']) // name is required
            ->assertOk()                        // 200, not a redirect
            ->assertSee('is-invalid', false);

        $this->assertDatabaseCount('clients', 0);
    }

    public function test_non_htmx_create_still_works_as_a_full_page_redirect(): void
    {
        $owner = $this->makeOwner();

        $this->actingAs($owner)
            ->post('/clients', ['name' => 'Beta Co'])
            ->assertRedirect();

        $this->assertDatabaseHas('clients', ['name' => 'Beta Co']);
    }
}
