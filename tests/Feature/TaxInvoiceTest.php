<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentLine;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_effective_tax_rate_respects_vat_registration(): void
    {
        $org = Organisation::factory()->make(['vat_registered' => false, 'default_tax_rate' => 16]);
        $this->assertSame(0.0, $org->effectiveTaxRate(null));
        $this->assertSame(0.0, $org->effectiveTaxRate(16.0));

        $org->vat_registered = true;
        $this->assertSame(16.0, $org->effectiveTaxRate(null));   // inherit default
        $this->assertSame(0.0, $org->effectiveTaxRate(0.0));     // zero-rated item
        $this->assertSame(8.0, $org->effectiveTaxRate(8.0));     // per-item override
    }

    public function test_document_totals_include_vat(): void
    {
        $org = $this->makeOrganisation(['vat_registered' => true, 'etims_enabled' => true]);
        $project = $this->makeProject($org);

        $doc = Document::create([
            'organisation_id' => $org->id,
            'project_id' => $project->id,
            'template_id' => $org->default_template_id,
            'type' => 'invoice',
            'number' => 'INV-TEST-1',
            'issue_date' => now(),
        ]);
        DocumentLine::create([
            'document_id' => $doc->id,
            'name' => 'Design',
            'quantity' => 1,
            'unit_price' => 450000,
            'total_price' => 450000,
            'tax_rate' => 16,
        ]);
        $doc->load('lines');

        $this->assertEquals(450000, $doc->subtotal);
        $this->assertEquals(72000, $doc->tax_total);
        $this->assertEquals(522000, $doc->total);
        $this->assertTrue($doc->is_tax_invoice);
        $this->assertSame('Tax Invoice', $doc->document_title);
    }

    public function test_generating_a_document_snapshots_the_tax_rate(): void
    {
        $org = $this->makeOrganisation(['vat_registered' => true]);
        $owner = $this->makeOwner($org);
        $project = $this->makeProject($org);
        $project->deliverables()->create([
            'name' => 'Design', 'quantity' => 1, 'unit_price' => 100000,
        ]);

        $this->actingAs($owner)->post('/documents', [
            'project_id' => $project->id,
            'template_id' => $org->default_template_id,
            'type' => 'invoice',
            'issue_date' => now()->toDateString(),
        ])->assertRedirect();

        $doc = Document::latest('id')->first();
        $this->assertEquals(16.0, $doc->lines->first()->tax_rate);
        $this->assertEquals(116000, $doc->total); // 100000 + 16%
    }

    public function test_tax_invoice_title_is_gated_on_etims(): void
    {
        // VAT is charged, but without eTIMS we must not claim "Tax Invoice".
        $org = $this->makeOrganisation(['vat_registered' => true, 'etims_enabled' => false]);
        $project = $this->makeProject($org);

        $doc = Document::create([
            'organisation_id' => $org->id,
            'project_id' => $project->id,
            'template_id' => $org->default_template_id,
            'type' => 'invoice',
            'number' => 'INV-TEST-2',
            'issue_date' => now(),
        ]);
        DocumentLine::create([
            'document_id' => $doc->id, 'name' => 'Design', 'quantity' => 1,
            'unit_price' => 100000, 'total_price' => 100000, 'tax_rate' => 16,
        ]);
        $doc->load('lines');

        $this->assertEquals(16000, $doc->tax_total);   // VAT still charged…
        $this->assertEquals(116000, $doc->total);
        $this->assertFalse($doc->is_tax_invoice);      // …but not labelled a Tax Invoice
        $this->assertSame('Invoice', $doc->document_title);

        // Once eTIMS is switched on, the legal title applies.
        $org->update(['etims_enabled' => true]);
        $this->assertSame('Tax Invoice', $doc->fresh()->load('lines')->document_title);
    }

    public function test_non_vat_organisation_produces_no_tax(): void
    {
        $org = $this->makeOrganisation(['vat_registered' => false]);
        $owner = $this->makeOwner($org);
        $project = $this->makeProject($org);
        $project->deliverables()->create([
            'name' => 'Design', 'quantity' => 1, 'unit_price' => 100000,
        ]);

        $this->actingAs($owner)->post('/documents', [
            'project_id' => $project->id,
            'template_id' => $org->default_template_id,
            'type' => 'invoice',
            'issue_date' => now()->toDateString(),
        ])->assertRedirect();

        $doc = Document::latest('id')->first();
        $this->assertEquals(0.0, $doc->lines->first()->tax_rate);
        $this->assertEquals(100000, $doc->total);
        $this->assertFalse($doc->is_tax_invoice);
    }
}
