<?php

namespace App\Pdf;

/**
 * Template 002 — "Centered".
 *
 * Minimal centered masthead with the document title, a single meta line, and
 * side-by-side From / To blocks. Line items, totals, notes and footer come
 * from the shared minimal base.
 */
class Template002 extends BasePdfTemplate
{
    protected function drawHeader(): void
    {
        $org = $this->org;
        $doc = $this->document;

        // Centered title
        $this->SetXY(0, $this->marginTop);
        $this->SetFont('helvetica', 'B', 20);
        $this->ink($this->primaryColor);
        $this->Cell(210, 10, strtoupper($doc->document_title), 0, 1, 'C');

        // Number · date (muted, centered)
        $this->SetX(0);
        $this->SetFont('helvetica', '', 8.5);
        $this->ink($this->textMuted);
        $this->Cell(210, 5,
            $doc->number . '     ·     ' . $doc->issue_date->format('d M Y'),
            0, 1, 'C');

        $this->Ln(3);
        $this->rule($this->GetY(), $this->borderGray, 0.3);
        $this->Ln(5);

        // From / To
        $col = $this->pageWidth / 2;
        $yStart = $this->GetY();
        $client = $doc->project->client;

        // FROM (left)
        $this->SetXY($this->marginLeft, $yStart);
        $this->caption('From', $this->marginLeft, $col);
        $this->SetX($this->marginLeft);
        $this->SetFont('helvetica', 'B', 10);
        $this->ink($this->primaryColor);
        $this->Cell($col, 5.2, $org->name, 0, 1, 'L');
        $this->SetFont('helvetica', '', 8.5);
        $this->ink($this->textDark);
        foreach (array_filter([$org->address, $org->phone, $org->email]) as $value) {
            foreach (explode("\n", $value) as $line) {
                $this->SetX($this->marginLeft);
                $this->Cell($col, 4.4, trim($line), 0, 1, 'L');
            }
        }
        if ($org->kra_pin) {
            $this->SetX($this->marginLeft);
            $this->Cell($col, 4.4, 'PIN ' . $org->kra_pin, 0, 1, 'L');
        }
        $yAfterFrom = $this->GetY();

        // TO (right)
        $rightX = $this->marginLeft + $col;
        $this->SetXY($rightX, $yStart);
        $this->caption('Bill To', $rightX, $col);
        $this->SetX($rightX);
        $this->SetFont('helvetica', 'B', 10);
        $this->ink($this->primaryColor);
        $this->Cell($col, 5.2, $client->name, 0, 1, 'L');
        $this->SetFont('helvetica', '', 8.5);
        $this->ink($this->textDark);
        foreach (array_filter([$client->address, $client->phone, $client->email]) as $value) {
            foreach (explode("\n", $value) as $line) {
                $this->SetX($rightX);
                $this->Cell($col, 4.4, trim($line), 0, 1, 'L');
            }
        }
        if ($client->kra_pin) {
            $this->SetX($rightX);
            $this->Cell($col, 4.4, 'PIN ' . $client->kra_pin, 0, 1, 'L');
        }
        $yAfterTo = $this->GetY();

        $this->SetY(max($yAfterFrom, $yAfterTo) + 5);
        $this->rule($this->GetY(), $this->borderGray, 0.2);
        $this->Ln(5);
    }

    protected function buildAddressBlock(): void
    {
        // Handled in drawHeader().
    }

    protected function buildDocumentMeta(): void
    {
        // Handled in drawHeader().
    }
}
