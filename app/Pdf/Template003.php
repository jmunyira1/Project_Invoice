<?php

namespace App\Pdf;

/**
 * Template 003 — "Accent band".
 *
 * A full-width coloured header band with a reversed (white) brand and title,
 * followed by a stacked Bill-To + meta block and the shared minimal table.
 * The accent colour also carries through to the client name and grand total,
 * giving a bolder, more branded feel than the monochrome 001 / 002.
 */
class Template003 extends BasePdfTemplate
{
    protected string $primaryColor = '#4F46E5'; // indigo accent
    protected string $accentColor = '#4F46E5';

    protected function drawHeader(): void
    {
        $org = $this->org;
        $doc = $this->document;
        $bandH = 28;

        // Full-bleed accent band
        [$r, $g, $b] = $this->hexToRgb($this->accentColor);
        $this->SetFillColor($r, $g, $b);
        $this->Rect(0, 0, 210, $bandH, 'F');

        // Brand — white, left
        $this->SetXY($this->marginLeft, 8.5);
        $this->SetFont('helvetica', 'B', 15);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($this->pageWidth / 2, 9, $org->name, 0, 0, 'L');

        // Document title — white, right
        $this->SetXY(0, 8.5);
        $this->SetFont('helvetica', 'B', 15);
        $this->Cell(210 - $this->marginRight, 9, strtoupper($doc->document_title), 0, 0, 'R');

        // Number under the title — soft white
        $this->SetXY(0, 17.5);
        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(226, 224, 250);
        $this->Cell(210 - $this->marginRight, 5, $doc->number, 0, 0, 'R');

        $this->SetY($bandH + 6);
        $this->ink($this->textDark);
    }

    // Bill-To + meta use the base stacked layout; line items, totals, notes and
    // footer all come from the shared minimal base.
}
