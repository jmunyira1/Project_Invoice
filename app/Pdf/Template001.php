<?php

namespace App\Pdf;

/**
 * Template 001 — "Left masthead".
 *
 * Minimal, typographic layout: brand (logo or name) top-left, document title
 * top-right, a Bill-To column beside a right-aligned meta block, then the
 * shared minimal line-item table, totals, notes and footer from the base.
 */
class Template001 extends BasePdfTemplate
{
    protected function drawHeader(): void
    {
        $doc = $this->document;
        $topY = $this->marginTop;

        // Brand — left
        $brandBottom = $this->brandMasthead($this->marginLeft, $topY);

        // Document title — right
        $this->SetXY(0, $topY);
        $this->SetFont('helvetica', 'B', 16);
        $this->ink($this->primaryColor);
        $this->Cell(210 - $this->marginRight, 8, strtoupper($doc->document_title), 0, 1, 'R');

        $this->SetXY(0, $topY + 8);
        $this->SetFont('helvetica', '', 9);
        $this->ink($this->textMuted);
        $this->Cell(210 - $this->marginRight, 5, $doc->number, 0, 1, 'R');

        $this->SetY(max($brandBottom, $topY + 15) + 4);
        $this->rule($this->GetY(), $this->borderGray, 0.3);
    }

    protected function buildAddressBlock(): void
    {
        $doc = $this->document;
        $client = $doc->project->client;
        $col = $this->pageWidth / 2;
        $startY = $this->GetY() + 5;

        // LEFT — Bill To
        $this->SetXY($this->marginLeft, $startY);
        $this->caption('Bill To', $this->marginLeft, $col);
        $this->SetX($this->marginLeft);
        $this->SetFont('helvetica', 'B', 10.5);
        $this->ink($this->primaryColor);
        $this->Cell($col, 5.5, $client->name, 0, 1, 'L');

        $this->SetFont('helvetica', '', 9);
        $this->ink($this->textDark);
        foreach (array_filter([$client->email, $client->phone, $client->address]) as $value) {
            foreach (explode("\n", $value) as $line) {
                $this->SetX($this->marginLeft);
                $this->Cell($col, 4.6, trim($line), 0, 1, 'L');
            }
        }
        if ($client->kra_pin) {
            $this->SetX($this->marginLeft);
            $this->Cell($col, 4.6, 'PIN ' . $client->kra_pin, 0, 1, 'L');
        }
        $leftEndY = $this->GetY();

        // RIGHT — meta rows
        $rightX = $this->marginLeft + $col;
        $labelW = 26;
        $valueW = $col - $labelW;
        $rows = array_filter([
            [$doc->type_label . ' No', $doc->number],
            ['Issue Date', $doc->issue_date->format('d M Y')],
            $doc->due_date ? ['Due Date', $doc->due_date->format('d M Y')] : null,
            ['Project', $doc->project->title],
        ]);

        $y = $startY;
        foreach ($rows as [$label, $value]) {
            $this->SetXY($rightX, $y);
            $this->SetFont('helvetica', '', 8.5);
            $this->ink($this->textMuted);
            $this->Cell($labelW, 5, $label, 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 8.5);
            $this->ink($this->textDark);
            $this->MultiCell($valueW, 5, $value, 0, 'L');
            $y = $this->GetY();
        }
        $rightEndY = $y;

        $this->SetY(max($leftEndY, $rightEndY) + 4);
        $this->rule($this->GetY(), $this->borderGray, 0.2);
        $this->Ln(5);
    }

    protected function buildDocumentMeta(): void
    {
        // Meta is shown alongside Bill-To in buildAddressBlock().
    }
}
