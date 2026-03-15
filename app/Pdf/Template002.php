<?php

namespace App\Pdf;

/**
 * Template 001 — Based on invoice-8 style.
 * Centered INVOICE title, Invoice No + Date line,
 * side-by-side From / To address blocks, clean table.
 */
class Template002 extends BasePdfTemplate
{
    protected string $primaryColor = '#1A1A2E';
    protected string $lightGray = '#F5F5F5';
    protected string $borderGray = '#CCCCCC';

    protected function drawHeader(): void
    {
        $org = $this->org;
        $doc = $this->document;

        // ── Large centered document type ──────────────────────────
        $this->SetXY(0, 10);
        $this->SetFont('helvetica', 'B', 24);
        $this->SetTextColor(...$this->hexToRgb($this->primaryColor));
        $this->Cell(210, 12, strtoupper($doc->type_label), 0, 1, 'C');

        // ── Invoice No + Date — centered ──────────────────────────
        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(...$this->hexToRgb($this->textMuted));
        $this->SetX(0);
        $this->Cell(210, 5,
            'Invoice No: ' . $doc->number . '          Date: ' . $doc->issue_date->format('d M Y'),
            0, 1, 'C'
        );

        $this->Ln(3);
        $this->hLine($this->GetY());
        $this->Ln(5);

        // ── Side-by-side From / To ────────────────────────────────
        $col = $this->pageWidth / 2; // 90mm each
        $yStart = $this->GetY();
        $client = $this->document->project->client;

        // FROM
        $this->SetXY($this->marginLeft, $yStart);
        $this->SetFont('helvetica', 'B', 8);
        $this->SetTextColor(...$this->hexToRgb($this->textMuted));
        $this->Cell($col, 5, 'Invoice From:', 0, 1);

        $this->SetFont('helvetica', 'B', 10);
        $this->SetTextColor(...$this->hexToRgb($this->primaryColor));
        $this->SetX($this->marginLeft);
        $this->Cell($col, 5, strtoupper($org->name), 0, 1);

        $this->SetFont('helvetica', '', 8.5);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));
        foreach (array_filter([$org->address, $org->phone, $org->email]) as $value) {
            foreach (explode("\n", $value) as $line) {
                $this->SetX($this->marginLeft);
                $this->Cell($col, 4.5, trim($line), 0, 1);
            }
        }
        $yAfterFrom = $this->GetY();

        // TO — right column, same Y start
        $rightX = $this->marginLeft + $col;
        $this->SetXY($rightX, $yStart);
        $this->SetFont('helvetica', 'B', 8);
        $this->SetTextColor(...$this->hexToRgb($this->textMuted));
        $this->Cell($col, 5, 'Invoice To:', 0, 1);

        $this->SetFont('helvetica', 'B', 10);
        $this->SetTextColor(...$this->hexToRgb($this->primaryColor));
        $this->SetX($rightX);
        $this->Cell($col, 5, strtoupper($client->name), 0, 1);

        $this->SetFont('helvetica', '', 8.5);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));
        foreach (array_filter([$client->address, $client->phone, $client->email]) as $value) {
            foreach (explode("\n", $value) as $line) {
                $this->SetX($rightX);
                $this->Cell($col, 4.5, trim($line), 0, 1);
            }
        }
        $yAfterTo = $this->GetY();

        // Position below the taller column
        $this->SetY(max($yAfterFrom, $yAfterTo) + 4);
        $this->hLine($this->GetY());
        $this->Ln(5);

        $this->SetTextColor(...$this->hexToRgb($this->textDark));
    }

    /**
     * Override — Template001 shows From/To in header,
     * so skip the default buildAddressBlock.
     */
    protected function buildAddressBlock(): void
    {
        // Already handled in drawHeader()
    }

    /**
     * Override — Template001 shows number + date in header,
     * so skip the default buildDocumentMeta grey bar.
     */
    protected function buildDocumentMeta(): void
    {
        // Already handled in drawHeader()
    }
}
