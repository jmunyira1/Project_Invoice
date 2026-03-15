<?php

namespace App\Pdf;

/**
 * Template 002 — Exact visual match of the provided invoice image.
 *
 * - White background throughout
 * - Org name (large bold) left + INVOICE (large bold) right, black text
 * - Horizontal rules separating every section
 * - Invoice No bold + Date bold, on separate sides
 * - Invoice To block, plain text
 * - Description block with left blue accent bar + light grey background
 * - Bordered table, bold header, Total row inside table
 * - Contact Us block
 * - Centred thank-you lines at bottom
 */
class Template001 extends BasePdfTemplate
{
    protected string $primaryColor = '#222222';
    protected string $accentColor = '#2C5F9E'; // blue left border on description
    protected string $lightGray = '#F5F5F5';
    protected string $borderGray = '#CCCCCC';
    protected string $textDark = '#222222';
    protected string $textMuted = '#555555';

    // ── Header: org name left, INVOICE right, white bg ────────────

    protected function drawHeader(): void
    {
        $org = $this->org;
        $doc = $this->document;

        // Org name — bold, large, left
        $this->SetXY($this->marginLeft, 12);
        $this->SetFont('helvetica', 'B', 18);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));
        $this->Cell(100, 10, strtoupper($org->name), 0, 0, 'L');

        // Document type — bold, large, right
        $this->SetFont('helvetica', 'B', 18);
        $this->SetXY(0, 12);
        $this->Cell(210 - $this->marginRight, 10, strtoupper($doc->type_label), 0, 0, 'R');

        // Rule below header
        $this->SetY(26);
        $this->hLine($this->GetY());
        $this->Ln(5);

        $this->SetTextColor(...$this->hexToRgb($this->textDark));
    }

    // ── Address + description block ───────────────────────────────

    protected function buildAddressBlock(): void
    {
        $client = $this->document->project->client;
        $doc = $this->document;

        // Invoice No (bold) left — Date (bold) right
        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));
        $this->SetX($this->marginLeft);

        // "Invoice No: " normal + number bold
        $this->SetFont('helvetica', '', 9);
        $this->Cell(30, 6, 'Invoice No: ', 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(60, 6, $doc->number, 0, 0, 'L');

        // "Date: " normal + date bold — right aligned
        $this->SetFont('helvetica', '', 9);
        $dateLabel = 'Date: ';
        $dateValue = $doc->issue_date->format('d M Y');
        $dateLabelW = $this->GetStringWidth($dateLabel);
        $dateValueW = $this->GetStringWidth($dateValue);
        $rightEdge = $this->marginLeft + $this->pageWidth;

        $this->SetX($rightEdge - $dateLabelW - $dateValueW - 2);
        $this->Cell($dateLabelW + 2, 6, $dateLabel, 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell($dateValueW + 2, 6, $dateValue, 0, 1, 'L');

        // Rule
        $this->hLine($this->GetY() + 1);
        $this->Ln(5);

        // Invoice To label — bold
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));
        $this->SetX($this->marginLeft);
        $this->Cell(0, 6, 'Invoice To:', 0, 1);

        // Client details — normal
        $this->SetFont('helvetica', '', 9);
        foreach (array_filter([$client->name, $client->email, $client->phone]) as $value) {
            $this->SetX($this->marginLeft);
            $this->Cell(0, 5.5, $value, 0, 1);
        }
        if ($client->address) {
            foreach (explode("\n", $client->address) as $line) {
                $this->SetX($this->marginLeft);
                $this->Cell(0, 5.5, trim($line), 0, 1);
            }
        }

        // Rule
        $this->Ln(2);
        $this->hLine($this->GetY());
        $this->Ln(4);

        // Description block — light grey background + left blue accent bar
        $descY = $this->GetY();
        $descText = $doc->project->title;

        // Calculate height needed
        $descLines = ceil($this->GetStringWidth($descText) / ($this->pageWidth - 8)) + 1;
        $descH = max(18, 8 + ($descLines * 5));

        // Rounded light grey background
        $this->SetFillColor(...$this->hexToRgb($this->lightGray));
        $this->SetDrawColor(...$this->hexToRgb($this->lightGray));
        $this->RoundedRect($this->marginLeft, $descY, $this->pageWidth, $descH, $this->radius, '1111', 'F');

        // Left blue accent bar — rounded on left side only
        $this->SetFillColor(...$this->hexToRgb($this->accentColor));
        $this->RoundedRect($this->marginLeft, $descY, 3, $descH, $this->radius, '1010', 'F');
        $this->SetDrawColor(0, 0, 0);

        // "Description:" label — bold, indented past accent bar
        $this->SetXY($this->marginLeft + 6, $descY + 3);
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));
        $this->Cell(0, 5, 'Description:', 0, 1);

        // Description text
        $this->SetX($this->marginLeft + 6);
        $this->SetFont('helvetica', '', 9);
        $this->MultiCell($this->pageWidth - 8, 5, $descText, 0, 'L');

        $this->SetY($descY + $descH + 3);

        // Rule
        $this->hLine($this->GetY());
        $this->Ln(4);

        $this->SetTextColor(...$this->hexToRgb($this->textDark));
    }

    // ── Skip default meta block ────────────────────────────────────

    protected function buildDocumentMeta(): void
    {
        // All meta shown in buildAddressBlock()
    }

    // ── Table with full cell borders, bold header, Total inside ───

    protected function buildLinesTable(): void
    {
        $lines = $this->document->lines;

        $colNo = 12;
        $colDesc = 72;
        $colPrice = 36;
        $colQty = 18;
        $colTotal = 42;
        // 12 + 72 + 36 + 18 + 42 = 180 ✓

        $border = 1; // full cell border

        // Rounded navy header row
        $headerY = $this->GetY();
        $this->SetFillColor(...$this->hexToRgb($this->primaryColor));
        $this->SetDrawColor(...$this->hexToRgb($this->primaryColor));
        $this->RoundedRect($this->marginLeft, $headerY, $this->pageWidth, 8, $this->radius, '1100', 'F');
        $this->SetDrawColor(...$this->hexToRgb($this->borderGray));

        $this->SetY($headerY);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('helvetica', 'B', 8.5);
        $this->Cell($colNo, 8, '#', 0, 0, 'C');
        $this->Cell($colDesc, 8, 'Item', 0, 0, 'L');
        $this->Cell($colPrice, 8, 'Unit Price', 0, 0, 'R');
        $this->Cell($colQty, 8, 'Qty', 0, 0, 'C');
        $this->Cell($colTotal, 8, 'Total', 0, 1, 'R');

        // Data rows — reset text color to dark after white header
        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));

        $i = 1;
        foreach ($lines as $line) {
            $descText = $line->name . ($line->description ? "\n" . $line->description : '');
            $lineH = $line->description ? 12 : 8;

            $xStart = $this->GetX();
            $yStart = $this->GetY();

            // # cell
            $this->Cell($colNo, $lineH, $i++, $border, 0, 'C');

            // Item — multiline
            $descX = $xStart + $colNo;
            $this->SetXY($descX, $yStart);
            $this->MultiCell($colDesc, $lineH, $descText, $border, 'L');
            $yEnd = $this->GetY();

            $this->SetXY($descX + $colDesc, $yStart);
            $this->Cell($colPrice, $lineH, $this->money($line->unit_price), $border, 0, 'L');
            $this->Cell($colQty, $lineH, number_format($line->quantity, 0), $border, 0, 'C');
            $this->Cell($colTotal, $lineH, $this->money($line->total_price), $border, 0, 'L');

            $this->SetY($yEnd);
        }

        // Total row — rounded bottom corners
        $totalY = $this->GetY();
        $this->SetFillColor(...$this->hexToRgb($this->lightGray));
        $this->SetDrawColor(...$this->hexToRgb($this->lightGray));
        $this->RoundedRect($this->marginLeft, $totalY, $this->pageWidth, 8, $this->radius, '0011', 'F');
        $this->SetDrawColor(...$this->hexToRgb($this->borderGray));

        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));
        $this->SetY($totalY);
        $spanW = $colNo + $colDesc + $colPrice;
        $this->Cell($spanW, 8, 'Total:', $border, 0, 'R');
        $this->Cell($colQty, 8, '', $border, 0, 'C');
        $this->Cell($colTotal, 8, $this->money($this->document->total), $border, 1, 'L');

        $this->Ln(4);
    }

    // ── Totals block — only shown if there are payments ───────────

    protected function buildTotalsBlock(): void
    {
        $doc = $this->document;
        $paid = $doc->total_paid;

        if ($paid <= 0) return; // total already shown inside table

        $labelW = 40;
        $valueW = 42;
        $startX = $this->marginLeft + $this->pageWidth - $labelW - $valueW;

        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));

        $this->SetX($startX);
        $this->Cell($labelW, 6, 'Amount Paid:', 0, 0, 'R');
        $this->Cell($valueW, 6, '(' . $this->money($paid) . ')', 0, 1, 'R');

        $lineY = $this->GetY() + 1;
        $this->hLine($lineY);
        $this->SetY($lineY + 2);

        $this->SetFont('helvetica', 'B', 9);
        $this->SetX($startX);
        $this->Cell($labelW, 6, 'Balance Due:', 0, 0, 'R');
        $this->Cell($valueW, 6, $this->money($doc->balance), 0, 1, 'R');

        $this->Ln(4);
    }

    // ── Contact footer ────────────────────────────────────────────

    protected function buildContactFooter(): void
    {
        $org = $this->org;

        $this->hLine($this->GetY());
        $this->Ln(4);

        // "Contact Us:" bold
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));
        $this->SetX($this->marginLeft);
        $this->Cell(0, 5, 'Contact Us:', 0, 1);

        // Org details — normal
        $this->SetFont('helvetica', '', 9);
        $this->SetX($this->marginLeft);
        $this->Cell(0, 5, $org->name, 0, 1);

        if ($org->phone) {
            $this->SetX($this->marginLeft);
            $this->Cell(0, 5, 'Phone: ' . $org->phone, 0, 1);
        }
        if ($org->email) {
            $this->SetX($this->marginLeft);
            $this->Cell(0, 5, 'Email: ' . $org->email, 0, 1);
        }

        $this->Ln(5);

        // Thank you lines — normal weight, centred
        $this->SetFont('helvetica', '', 8.5);
        $this->SetTextColor(...$this->hexToRgb($this->textMuted));
        $this->SetX($this->marginLeft);
        $this->Cell(0, 5, 'Thank you for your business.', 0, 1, 'C');
        $this->SetX($this->marginLeft);
        $this->Cell(0, 5, 'This is a computer-generated document. No signature is required.', 0, 1, 'C');
    }
}
