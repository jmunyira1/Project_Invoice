<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_installment_status_transitions(): void
    {
        $org = $this->makeOrganisation();
        $project = $this->makeProject($org);

        $inst = $project->installments()->create([
            'organisation_id' => $org->id,
            'label' => 'Milestone 1',
            'amount' => 50000,
            'due_date' => now()->subDays(3),
            'sort_order' => 1,
        ]);
        $inst->syncStatus();
        $this->assertSame('overdue', $inst->fresh()->effective_status);

        // Partial payment — still overdue (past due with a balance)
        $project->payments()->create([
            'organisation_id' => $org->id, 'installment_id' => $inst->id,
            'kind' => 'installment', 'amount' => 20000, 'method' => 'cash', 'paid_on' => now(),
        ]);
        $inst->load('payments');
        $this->assertEquals(20000, $inst->paid);
        $this->assertEquals(30000, $inst->balance);
        $this->assertSame('overdue', $inst->effective_status);

        // Settle the rest — paid
        $project->payments()->create([
            'organisation_id' => $org->id, 'installment_id' => $inst->id,
            'kind' => 'installment', 'amount' => 30000, 'method' => 'cash', 'paid_on' => now(),
        ]);
        $inst->load('payments');
        $this->assertSame('paid', $inst->effective_status);
        $this->assertEquals(0, $inst->balance);
    }

    public function test_future_unpaid_installment_is_pending(): void
    {
        $org = $this->makeOrganisation();
        $project = $this->makeProject($org);
        $inst = $project->installments()->create([
            'organisation_id' => $org->id, 'label' => 'Deposit',
            'amount' => 10000, 'due_date' => now()->addWeek(), 'sort_order' => 1,
        ]);
        $this->assertSame('pending', $inst->effective_status);
    }

    public function test_owner_can_add_an_installment(): void
    {
        $org = $this->makeOrganisation();
        $owner = $this->makeOwner($org);
        $project = $this->makeProject($org);

        $this->actingAs($owner)
            ->post("/projects/{$project->id}/installments", ['label' => 'Deposit', 'amount' => 15000])
            ->assertRedirect();

        $this->assertDatabaseHas('installments', [
            'project_id' => $project->id, 'label' => 'Deposit', 'amount' => 15000,
        ]);
    }
}
