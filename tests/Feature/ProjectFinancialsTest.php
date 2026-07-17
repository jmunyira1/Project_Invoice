<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectFinancialsTest extends TestCase
{
    use RefreshDatabase;

    public function test_agreed_value_overrides_deliverables_sum(): void
    {
        $org = $this->makeOrganisation();
        $project = $this->makeProject($org, null, ['value' => 450000]);
        $project->deliverables()->create(['name' => 'X', 'quantity' => 1, 'unit_price' => 10000]);
        $project->load('deliverables');

        $this->assertEquals(450000, $project->total_value);      // agreed value wins
        $this->assertEquals(10000, $project->deliverables_total); // breakdown still available
    }

    public function test_balance_reconciles_on_a_gross_basis_when_vat_registered(): void
    {
        $org = $this->makeOrganisation(['vat_registered' => true]);
        $project = $this->makeProject($org, null, ['value' => null]);
        $project->deliverables()->create(['name' => 'Design', 'quantity' => 1, 'unit_price' => 100000]);
        $project->load('deliverables');

        $this->assertEquals(100000, $project->total_value); // net
        $this->assertEquals(16000, $project->tax_total);    // 16% VAT
        $this->assertEquals(116000, $project->gross_value);

        // Client pays the gross amount
        $project->payments()->create([
            'organisation_id' => $org->id, 'kind' => 'balance',
            'amount' => 116000, 'method' => 'mpesa', 'paid_on' => now(),
        ]);
        $project->load('payments');

        $this->assertEquals(0, $project->balance);        // fully settled (not -16000)
        $this->assertEquals(100.0, $project->paid_percent);
    }

    public function test_non_vat_project_balance_stays_net(): void
    {
        $org = $this->makeOrganisation(['vat_registered' => false]);
        $project = $this->makeProject($org);
        $project->deliverables()->create(['name' => 'X', 'quantity' => 1, 'unit_price' => 100000]);
        $project->load('deliverables');

        $this->assertEquals(0.0, $project->tax_total);
        $this->assertEquals(100000, $project->gross_value);
        $this->assertEquals(100000, $project->balance);
    }
}
