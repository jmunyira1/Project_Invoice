<?php

namespace Tests\Feature;

use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_recording_a_payment_can_generate_a_receipt(): void
    {
        $org = $this->makeOrganisation(['vat_registered' => true]);
        $owner = $this->makeOwner($org);
        $project = $this->makeProject($org);

        $this->actingAs($owner)->post('/payments', [
            'project_id' => $project->id,
            'kind' => 'deposit',
            'amount' => 50000,
            'method' => 'mpesa',
            'reference' => 'ABC123',
            'paid_on' => now()->toDateString(),
            'generate_receipt' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'project_id' => $project->id, 'amount' => 50000, 'kind' => 'deposit',
        ]);

        $receipt = Document::where('type', 'receipt')->first();
        $this->assertNotNull($receipt, 'A receipt document should have been generated.');
        $this->assertNotNull($receipt->payment_id);
        $this->assertEquals($project->id, $receipt->project_id);
    }

    public function test_payment_without_receipt_flag_creates_no_document(): void
    {
        $org = $this->makeOrganisation();
        $owner = $this->makeOwner($org);
        $project = $this->makeProject($org);

        $this->actingAs($owner)->post('/payments', [
            'project_id' => $project->id,
            'kind' => 'part_payment',
            'amount' => 5000,
            'method' => 'cash',
            'paid_on' => now()->toDateString(),
        ])->assertRedirect();

        $this->assertDatabaseCount('documents', 0);
        $this->assertDatabaseHas('payments', ['project_id' => $project->id, 'amount' => 5000]);
    }
}
