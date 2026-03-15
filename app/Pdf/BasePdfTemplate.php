<?php

namespace App\Pdf;

use App\Models\Document;
use App\Models\Organisation;
use TCPDF;

abstract class BasePdfTemplate extends TCPDF
{
    protected Document $document;
    protected Organisation $org;
    protected string $currency;

    protected float $marginLeft = 15;
    protected float $marginRight = 15;
    protected float $marginTop = 10;
    protected float $marginBottom = 15;
    protected float $pageWidth = 180;
    protected float $radius = 3; // corner radius used everywhere

    protected string $primaryColor = '#1A1A2E';
    protected string $accentColor = '#1A1A2E';
    protected string $lightGray = '#F5F5F5';
    protected string $borderGray = '#CCCCCC';
    protected string $textDark = '#1A1A2E';
    protected string $textMuted = '#6C757D';

    public function __construct(Document $document, Organisation $org)
    {
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8', false);

        $this->document = $document;
        $this->org = $org;
        $this->currency = $org->currency;

        $this->SetCreator($org->name);
        $this->SetAuthor($org->name);
        $this->SetTitle($document->type_label . ' ' . $document->number);

        $this->SetMargins($this->marginLeft, $this->marginTop, $this->marginRight);
        $this->SetFooterMargin($this->marginBottom);
        $this->SetAutoPageBreak(true, $this->marginBottom);

        $this->setPrintHeader(false);
        $this->setPrintFooter(false);

        $this->SetFont('helvetica', '', 9);
    }

    // ── Entry point ────────────────────────────────────────────────

    public function generate(): string
    {
        $this->AddPage();

        // Rounded page border — drawn first so content sits on top
        $this->drawPageBorder();

        $this->drawHeader();
        $this->buildAddressBlock();
        $this->buildDocumentMeta();
        $this->buildLinesTable();
        $this->buildTotalsBlock();
        $this->buildNotesBlock();
        $this->buildContactFooter();

        return $this->Output('', 'S');
    }

    // ── Page border ────────────────────────────────────────────────

    protected function drawPageBorder(): void
    {
        $this->SetDrawColor(...$this->hexToRgb($this->borderGray));
        $this->SetLineWidth(0.4);
        $this->RoundedRect(5, 5, 200, 287, $this->radius, '1111', 'D');
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(0, 0, 0);
    }

    // ── Abstract ───────────────────────────────────────────────────

    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    // ── Address block ──────────────────────────────────────────────

    abstract protected function drawHeader(): void;

    // ── Document meta — rounded rect background ────────────────────

    protected function buildAddressBlock(): void
    {
        $client = $this->document->project->client;

        $this->Ln(3);
        $this->SetFont('helvetica', 'B', 8);
        $this->SetTextColor(...$this->hexToRgb($this->textMuted));
        $this->SetX($this->marginLeft);
        $this->Cell(0, 5, 'Invoice To:', 0, 1);

        $this->SetFont('helvetica', 'B', 10);
        $this->SetTextColor(...$this->hexToRgb($this->primaryColor));
        $this->SetX($this->marginLeft);
        $this->Cell(0, 5, $client->name, 0, 1);

        $this->SetFont('helvetica', '', 8.5);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));

        foreach (array_filter([$client->email, $client->phone, $client->address]) as $value) {
            foreach (explode("\n", $value) as $line) {
                $this->SetX($this->marginLeft);
                $this->Cell(0, 4.5, trim($line), 0, 1);
            }
        }

        $this->Ln(4);
    }

    // ── Lines table — numbered ─────────────────────────────────────

    protected function buildDocumentMeta(): void
    {
        $doc = $this->document;
        $y = $this->GetY();
        $h = 16;

        // Rounded background
        $this->SetFillColor(...$this->hexToRgb($this->lightGray));
        $this->SetDrawColor(...$this->hexToRgb($this->lightGray));
        $this->RoundedRect($this->marginLeft, $y, $this->pageWidth, $h, $this->radius, '1111', 'F');
        $this->SetDrawColor(0, 0, 0);

        $col = $this->pageWidth / 4;

        $this->SetY($y + 2);
        $this->SetFont('helvetica', '', 7.5);
        $this->SetTextColor(...$this->hexToRgb($this->textMuted));
        $this->SetX($this->marginLeft + 2);
        $this->Cell($col, 4, strtoupper($doc->type_label) . ' NO', 0, 0);
        $this->Cell($col, 4, 'ISSUE DATE', 0, 0);
        $this->Cell($col, 4, 'DUE DATE', 0, 0);
        $this->Cell($col, 4, 'PROJECT', 0, 1);

        $this->SetFont('helvetica', 'B', 8.5);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));
        $this->SetX($this->marginLeft + 2);
        $this->Cell($col, 5, $doc->number, 0, 0);
        $this->Cell($col, 5, $doc->issue_date->format('d M Y'), 0, 0);
        $this->Cell($col, 5, $doc->due_date?->format('d M Y') ?? '—', 0, 0);
        $this->Cell($col, 5, $doc->project->title, 0, 1);

        $this->Ln(5);
    }

    // ── Totals block — rounded rect ────────────────────────────────

    protected function buildLinesTable(): void
    {
        $lines = $this->document->lines;

        $colNo = 10;
        $colDesc = 76;
        $colPrice = 32;
        $colQty = 20;
        $colTotal = 42;
        // 10 + 76 + 32 + 20 + 42 = 180 ✓

        // Rounded header background
        $headerY = $this->GetY();
        $this->SetFillColor(...$this->hexToRgb($this->primaryColor));
        $this->SetDrawColor(...$this->hexToRgb($this->primaryColor));
        $this->RoundedRect($this->marginLeft, $headerY, $this->pageWidth, 8, $this->radius, '1100', 'F');
        $this->SetDrawColor(0, 0, 0);

        $this->SetY($headerY);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell($colNo, 8, '#', 0, 0, 'C');
        $this->Cell($colDesc, 8, 'Item', 0, 0, 'L');
        $this->Cell($colPrice, 8, 'Unit Price', 0, 0, 'R');
        $this->Cell($colQty, 8, 'Qty', 0, 0, 'C');
        $this->Cell($colTotal, 8, 'Sub-Total', 0, 1, 'R');

        $this->SetTextColor(...$this->hexToRgb($this->textDark));
        $this->SetFont('helvetica', '', 9);

        $i = 1;
        $fill = false;
        foreach ($lines as $line) {
            if ($fill) {
                $this->SetFillColor(...$this->hexToRgb($this->lightGray));
            }

            $descText = $line->name . ($line->description ? "\n" . $line->description : '');
            $lineH = $line->description ? 12 : 7;

            $xStart = $this->GetX();
            $yStart = $this->GetY();

            $this->Cell($colNo, $lineH, $i++, 0, 0, 'C', $fill);

            $descX = $xStart + $colNo;
            $this->SetXY($descX, $yStart);
            $this->MultiCell($colDesc, $lineH, $descText, 0, 'L', $fill);
            $yEnd = $this->GetY();

            $this->SetXY($descX + $colDesc, $yStart);
            $this->Cell($colPrice, $lineH, $this->money($line->unit_price), 0, 0, 'R', $fill);
            $this->Cell($colQty, $lineH, number_format($line->quantity, 2), 0, 0, 'C', $fill);
            $this->Cell($colTotal, $lineH, $this->money($line->total_price), 0, 0, 'R', $fill);

            $this->SetY($yEnd);
            $fill = !$fill;
        }

        $this->Ln(3);
    }

    // ── Notes block — rounded rect ─────────────────────────────────

    protected function money(float $amount): string
    {
        return $this->currency . ' ' . number_format($amount, 2);
    }

    // ── Contact footer ─────────────────────────────────────────────

    protected function buildTotalsBlock(): void
    {
        $doc = $this->document;
        $total = $doc->total;
        $paid = $doc->total_paid;
        $balance = $doc->balance;

        $labelW = 40;
        $valueW = 42;
        $startX = $this->marginLeft + $this->pageWidth - $labelW - $valueW;
        $blockW = $labelW + $valueW;

        // Measure how tall the block will be
        $rows = 1 + ($paid > 0 ? 2 : 0);
        $blockH = ($rows * 6) + 6;

        // Rounded background on totals area
        $blockY = $this->GetY();
        $this->SetFillColor(...$this->hexToRgb($this->lightGray));
        $this->SetDrawColor(...$this->hexToRgb($this->lightGray));
        $this->RoundedRect($startX, $blockY, $blockW, $blockH, $this->radius, '1111', 'F');
        $this->SetDrawColor(0, 0, 0);

        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(...$this->hexToRgb($this->textMuted));

        $this->SetXY($startX, $blockY + 2);
        $this->Cell($labelW, 6, 'Total:', 0, 0, 'R');
        $this->Cell($valueW, 6, $this->money($total), 0, 1, 'R');

        if ($paid > 0) {
            $this->SetX($startX);
            $this->Cell($labelW, 6, 'Amount Paid:', 0, 0, 'R');
            $this->Cell($valueW, 6, '(' . $this->money($paid) . ')', 0, 1, 'R');

            // Balance due — rounded filled row
            $balY = $this->GetY();
            $this->SetFillColor(...$this->hexToRgb($this->primaryColor));
            $this->SetDrawColor(...$this->hexToRgb($this->primaryColor));
            $this->RoundedRect($startX, $balY, $blockW, 8, $this->radius, '0011', 'F');
            $this->SetDrawColor(0, 0, 0);

            $this->SetFont('helvetica', 'B', 9);
            $this->SetTextColor(255, 255, 255);
            $this->SetXY($startX, $balY);
            $this->Cell($labelW, 8, 'Balance Due:', 0, 0, 'R');
            $this->Cell($valueW, 8, $this->money($balance), 0, 1, 'R');
        }

        $this->Ln(5);
    }

    // ── Helpers ────────────────────────────────────────────────────

    protected function buildNotesBlock(): void
    {
        if (!$this->document->notes) return;

        $y = $this->GetY();

        // Measure approximate height
        $lines = ceil($this->GetStringWidth($this->document->notes) / ($this->pageWidth - 6)) + 1;
        $blockH = 8 + ($lines * 5);

        $this->SetFillColor(...$this->hexToRgb($this->lightGray));
        $this->SetDrawColor(...$this->hexToRgb($this->lightGray));
        $this->RoundedRect($this->marginLeft, $y, $this->pageWidth, $blockH, $this->radius, '1111', 'F');
        $this->SetDrawColor(0, 0, 0);

        $this->SetXY($this->marginLeft + 3, $y + 3);
        $this->SetFont('helvetica', 'B', 8);
        $this->SetTextColor(...$this->hexToRgb($this->textMuted));
        $this->Cell(0, 5, 'NOTES', 0, 1);

        $this->SetX($this->marginLeft + 3);
        $this->SetFont('helvetica', '', 8.5);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));
        $this->MultiCell($this->pageWidth - 6, 5, $this->document->notes, 0, 'L');
        $this->Ln(4);
    }

    protected function buildContactFooter(): void
    {
        $org = $this->org;

        $this->Ln(3);
        $this->hLine($this->GetY());
        $this->Ln(3);

        $this->SetFont('helvetica', 'B', 8);
        $this->SetTextColor(...$this->hexToRgb($this->textMuted));
        $this->SetX($this->marginLeft);
        $this->Cell($this->pageWidth / 2, 4, 'Contact:', 0, 0, 'L');
        $this->SetFont('helvetica', '', 7);
        $this->Cell($this->pageWidth / 2, 4,
            'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(),
            0, 1, 'R'
        );

        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(...$this->hexToRgb($this->textDark));
        $this->SetX($this->marginLeft);
        $this->Cell(0, 4, $org->name, 0, 1);

        if ($org->phone) {
            $this->SetX($this->marginLeft);
            $this->Cell(0, 4, 'Phone: ' . $org->phone, 0, 1);
        }
        if ($org->email) {
            $this->SetX($this->marginLeft);
            $this->Cell(0, 4, 'Email: ' . $org->email, 0, 1);
        }

        $this->Ln(3);
        $this->SetFont('helvetica', 'I', 7.5);
        $this->SetTextColor(...$this->hexToRgb($this->textMuted));
        $this->SetX($this->marginLeft);
        $this->Cell(0, 4, 'Thank you for your business.', 0, 1, 'C');
        $this->SetX($this->marginLeft);
        $this->Cell(0, 4, 'This is a computer-generated document. No signature is required.', 0, 1, 'C');
    }

    protected function hLine(float $y = null, string $color = null): void
    {
        $y = $y ?? $this->GetY();
        $color = $color ?? $this->borderGray;
        [$r, $g, $b] = $this->hexToRgb($color);
        $this->SetDrawColor($r, $g, $b);
        $this->Line($this->marginLeft, $y, $this->marginLeft + $this->pageWidth, $y);
        $this->SetDrawColor(0, 0, 0);
    }
}
