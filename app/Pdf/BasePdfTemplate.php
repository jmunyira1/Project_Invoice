<?php

namespace App\Pdf;

use App\Models\Document;
use App\Models\Organisation;
use Illuminate\Support\Facades\Storage;
use TCPDF;

/**
 * Minimal, professional document base.
 *
 * Design language: no borders or filled blocks, generous whitespace, hairline
 * rules and small uppercase labels. Shared body (line items, totals, notes,
 * footer, receipt) lives here; templates only differ in the masthead.
 */
abstract class BasePdfTemplate extends TCPDF
{
    protected Document $document;
    protected Organisation $org;
    protected string $currency;

    protected float $marginLeft = 16;
    protected float $marginRight = 16;
    protected float $marginTop = 16;
    protected float $marginBottom = 16;
    protected float $pageWidth = 178;   // 210 - 2*16
    protected float $radius = 1.5;

    // ── Minimal palette ────────────────────────────────────────────
    protected string $primaryColor = '#111111';
    protected string $accentColor = '#111111';
    protected string $lightGray = '#F6F6F6';
    protected string $borderGray = '#E4E4E4';
    protected string $textDark = '#1A1A1A';
    protected string $textMuted = '#8C8C8C';

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

        $this->SetFont('helvetica', '', 9.5);
    }

    // ── Entry point ────────────────────────────────────────────────

    public function generate(): string
    {
        $this->AddPage();
        $this->drawHeader();

        if ($this->document->type === 'receipt') {
            $this->buildReceiptBody();
        } else {
            $this->buildAddressBlock();
            $this->buildDocumentMeta();
            $this->buildLinesTable();
            $this->buildTotalsBlock();
        }

        $this->buildNotesBlock();
        $this->buildContactFooter();

        return $this->Output('', 'S');
    }

    abstract protected function drawHeader(): void;

    // ── Shared helpers ─────────────────────────────────────────────

    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }

    protected function money(float $amount): string
    {
        return $this->currency . ' ' . number_format($amount, 2);
    }

    protected function ink(string $color): void
    {
        $this->SetTextColor(...$this->hexToRgb($color));
    }

    /**
     * A hairline rule across the content width (or a custom span).
     */
    protected function rule(?float $y = null, ?string $color = null, float $w = 0.2, ?float $x1 = null, ?float $x2 = null): void
    {
        $y ??= $this->GetY();
        $color ??= $this->borderGray;
        $x1 ??= $this->marginLeft;
        $x2 ??= $this->marginLeft + $this->pageWidth;
        [$r, $g, $b] = $this->hexToRgb($color);
        $this->SetLineWidth($w);
        $this->SetDrawColor($r, $g, $b);
        $this->Line($x1, $y, $x2, $y);
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(0, 0, 0);
    }

    /** Backwards-compatible alias used by templates. */
    protected function hLine(?float $y = null, ?string $color = null): void
    {
        $this->rule($y, $color, 0.2);
    }

    /**
     * A small uppercase muted caption (e.g. "BILL TO").
     */
    protected function caption(string $text, ?float $x = null, float $w = 0, int $align = 0): void
    {
        $this->SetFont('helvetica', 'B', 6.8);
        $this->ink($this->textMuted);
        if ($x !== null) {
            $this->SetX($x);
        }
        $this->Cell($w ?: 0, 4, strtoupper($text), 0, 1, $align === 0 ? 'L' : $align);
    }

    /**
     * Render the organisation logo if one is set, otherwise its name.
     * Returns the Y coordinate just below the brand.
     */
    protected function brandMasthead(float $x, float $y, float $maxH = 13): float
    {
        $org = $this->org;
        $logo = $org->logo_path ? Storage::disk('public')->path($org->logo_path) : null;

        if ($logo && is_file($logo)) {
            try {
                $this->Image($logo, $x, $y, 0, $maxH, '', '', 'T', false, 300, 'L');
                return $y + $maxH;
            } catch (\Throwable $e) {
                // fall through to text
            }
        }

        $this->SetXY($x, $y);
        $this->SetFont('helvetica', 'B', 15);
        $this->ink($this->primaryColor);
        $this->Cell($this->pageWidth / 2, 8, $org->name, 0, 1, 'L');
        return $y + 8;
    }

    // ── Address / meta (default; templates usually override) ───────

    protected function buildAddressBlock(): void
    {
        $client = $this->document->project->client;

        $this->Ln(4);
        $this->caption('Bill To', $this->marginLeft);

        $this->SetFont('helvetica', 'B', 10.5);
        $this->ink($this->primaryColor);
        $this->SetX($this->marginLeft);
        $this->Cell(0, 5.5, $client->name, 0, 1);

        $this->SetFont('helvetica', '', 9);
        $this->ink($this->textDark);
        foreach (array_filter([$client->email, $client->phone, $client->address]) as $value) {
            foreach (explode("\n", $value) as $line) {
                $this->SetX($this->marginLeft);
                $this->Cell(0, 4.6, trim($line), 0, 1);
            }
        }
        if ($client->kra_pin) {
            $this->SetX($this->marginLeft);
            $this->Cell(0, 4.6, 'PIN ' . $client->kra_pin, 0, 1);
        }
        $this->Ln(2);
    }

    protected function buildDocumentMeta(): void
    {
        $doc = $this->document;
        $this->Ln(2);

        $rows = array_filter([
            [strtoupper($doc->type_label) . ' No', $doc->number],
            ['Issue Date', $doc->issue_date->format('d M Y')],
            $doc->due_date ? ['Due Date', $doc->due_date->format('d M Y')] : null,
            ['Project', $doc->project->title],
        ]);

        foreach ($rows as [$label, $value]) {
            $this->SetX($this->marginLeft);
            $this->SetFont('helvetica', '', 8.5);
            $this->ink($this->textMuted);
            $this->Cell(34, 5, $label, 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 8.5);
            $this->ink($this->textDark);
            $this->Cell(0, 5, $value, 0, 1, 'L');
        }
        $this->Ln(2);
    }

    // ── Line items — minimal ───────────────────────────────────────

    protected function buildLinesTable(): void
    {
        $lines = $this->document->lines;
        $showTax = $this->document->has_tax;

        $colNo = 8;
        $colPrice = 32;
        $colQty = 14;
        $colTax = $showTax ? 16 : 0;
        $colTotal = 44;
        $colDesc = $this->pageWidth - $colNo - $colPrice - $colQty - $colTax - $colTotal;

        // Column captions
        $this->SetX($this->marginLeft);
        $this->SetFont('helvetica', 'B', 6.8);
        $this->ink($this->textMuted);
        $this->Cell($colNo, 5, '', 0, 0, 'L');
        $this->Cell($colDesc, 5, 'DESCRIPTION', 0, 0, 'L');
        $this->Cell($colPrice, 5, 'UNIT PRICE', 0, 0, 'R');
        $this->Cell($colQty, 5, 'QTY', 0, 0, 'C');
        if ($showTax) {
            $this->Cell($colTax, 5, 'TAX', 0, 0, 'C');
        }
        $this->Cell($colTotal, 5, 'AMOUNT', 0, 1, 'R');

        $this->rule($this->GetY() + 0.5, $this->textDark, 0.3);
        $this->Ln(2);

        $i = 1;
        foreach ($lines as $line) {
            $rowY = $this->GetY();
            $descX = $this->marginLeft + $colNo;

            // number
            $this->SetXY($this->marginLeft, $rowY);
            $this->SetFont('helvetica', '', 9);
            $this->ink($this->textMuted);
            $this->Cell($colNo, 5, (string) $i++, 0, 0, 'L');

            // name (+ description under)
            $this->SetXY($descX, $rowY);
            $this->SetFont('helvetica', 'B', 9);
            $this->ink($this->textDark);
            $this->MultiCell($colDesc, 5, $line->name, 0, 'L');
            $descEndY = $this->GetY();
            if ($line->description) {
                $this->SetXY($descX, $descEndY);
                $this->SetFont('helvetica', '', 8);
                $this->ink($this->textMuted);
                $this->MultiCell($colDesc, 4.2, $line->description, 0, 'L');
                $descEndY = $this->GetY();
            }

            // right-aligned figures on the first line
            $this->SetFont('helvetica', '', 9);
            $this->ink($this->textDark);
            $this->SetXY($descX + $colDesc, $rowY);
            $this->Cell($colPrice, 5, $this->money($line->unit_price), 0, 0, 'R');
            $this->Cell($colQty, 5, rtrim(rtrim(number_format($line->quantity, 2), '0'), '.'), 0, 0, 'C');
            if ($showTax) {
                $this->Cell($colTax, 5, rtrim(rtrim(number_format($line->tax_rate, 2), '0'), '.') . '%', 0, 0, 'C');
            }
            $this->Cell($colTotal, 5, $this->money($line->total_price), 0, 1, 'R');

            $rowEndY = max($descEndY, $rowY + 5) + 2;
            $this->rule($rowEndY, $this->borderGray, 0.15);
            $this->SetY($rowEndY + 2);
        }
    }

    // ── Totals — minimal, right aligned ────────────────────────────

    protected function buildTotalsBlock(): void
    {
        $doc = $this->document;
        $paid = $doc->total_paid;

        $labelW = 44;
        $valueW = 40;
        $blockW = $labelW + $valueW;
        $startX = $this->marginLeft + $this->pageWidth - $blockW;

        $this->Ln(1);

        $small = function (string $label, string $value) use ($startX, $labelW, $valueW) {
            $this->SetXY($startX, $this->GetY());
            $this->SetFont('helvetica', '', 9);
            $this->ink($this->textMuted);
            $this->Cell($labelW, 5.6, $label, 0, 0, 'R');
            $this->ink($this->textDark);
            $this->Cell($valueW, 5.6, $value, 0, 1, 'R');
        };

        if ($doc->has_tax) {
            $small('Subtotal', $this->money($doc->subtotal));
            foreach ($doc->tax_breakdown as $g) {
                $rateLabel = rtrim(rtrim(number_format($g['rate'], 2), '0'), '.');
                $small("VAT ({$rateLabel}%)", $this->money($g['tax']));
            }
        }

        // Rule + emphasised grand total
        $this->rule($this->GetY() + 0.5, $this->textDark, 0.3, $startX);
        $this->Ln(2);
        $this->SetXY($startX, $this->GetY());
        $this->SetFont('helvetica', 'B', 11.5);
        $this->ink($this->primaryColor);
        $this->Cell($labelW, 6.5, 'Total', 0, 0, 'R');
        $this->Cell($valueW, 6.5, $this->money($doc->total), 0, 1, 'R');

        if ($paid > 0) {
            $small('Amount Paid', '(' . $this->money($paid) . ')');
            $this->rule($this->GetY() + 0.5, $this->borderGray, 0.2, $startX);
            $this->Ln(1.5);
            $this->SetXY($startX, $this->GetY());
            $this->SetFont('helvetica', 'B', 10);
            $this->ink($this->primaryColor);
            $this->Cell($labelW, 6, 'Balance Due', 0, 0, 'R');
            $this->Cell($valueW, 6, $this->money($doc->balance), 0, 1, 'R');
        }

        $this->Ln(4);
    }

    // ── Notes — minimal ────────────────────────────────────────────

    protected function buildNotesBlock(): void
    {
        if (!$this->document->notes) {
            return;
        }

        $this->Ln(4);
        $this->caption('Notes', $this->marginLeft);
        $this->SetX($this->marginLeft);
        $this->SetFont('helvetica', '', 8.8);
        $this->ink($this->textDark);
        $this->MultiCell($this->pageWidth, 4.6, $this->document->notes, 0, 'L');
    }

    // ── Footer — minimal ───────────────────────────────────────────

    protected function buildContactFooter(): void
    {
        $org = $this->org;

        $this->Ln(8);
        $this->rule($this->GetY(), $this->borderGray, 0.2);
        $this->Ln(2.5);

        $parts = array_filter([
            $org->phone ? 'Tel ' . $org->phone : null,
            $org->email ?: null,
            $org->kra_pin ? 'PIN ' . $org->kra_pin : null,
        ]);

        $this->SetX($this->marginLeft);
        $this->SetFont('helvetica', 'B', 8);
        $this->ink($this->textDark);
        $this->Cell($this->pageWidth, 4.4, $org->name, 0, 1, 'C');

        if ($parts) {
            $this->SetX($this->marginLeft);
            $this->SetFont('helvetica', '', 7.6);
            $this->ink($this->textMuted);
            $this->Cell($this->pageWidth, 4.2, implode('   ·   ', $parts), 0, 1, 'C');
        }

        $this->Ln(1.5);
        $this->SetX($this->marginLeft);
        $this->SetFont('helvetica', '', 7.4);
        $this->ink($this->textMuted);
        $this->Cell($this->pageWidth, 4, 'Thank you for your business.', 0, 1, 'C');
    }

    // ── Receipt — minimal ──────────────────────────────────────────

    protected function buildReceiptBody(): void
    {
        $doc = $this->document;
        $payment = $doc->payment;
        $project = $doc->project;
        $client = $project->client;
        $green = '#1E7E34';

        $invoice = $payment?->document;
        if ($invoice) {
            $refLabel = 'Invoice ' . $invoice->number;
            $refTotal = $invoice->total;
            $refPaid = $invoice->total_paid;
            $refBalance = $invoice->balance;
        } else {
            $refLabel = $project->title;
            $refTotal = $project->total_value;
            $refPaid = $project->total_paid;
            $refBalance = $project->balance;
        }
        $amountPaid = $payment?->amount ?? $refPaid;

        $this->Ln(4);

        $topY = $this->GetY();
        $col = $this->pageWidth / 2;
        $labelH = 4;
        $valH = 5.5;

        // A stacked label + value at an explicit position (no auto line-break).
        $field = function (float $x, float $y, string $label, string $value) use ($col, $labelH, $valH): float {
            $this->SetXY($x, $y);
            $this->SetFont('helvetica', 'B', 6.8);
            $this->ink($this->textMuted);
            $this->Cell($col, $labelH, strtoupper($label), 0, 0, 'L');
            $this->SetXY($x, $y + $labelH);
            $this->SetFont('helvetica', 'B', 10);
            $this->ink($this->textDark);
            $this->Cell($col, $valH, $value, 0, 0, 'L');
            return $y + $labelH + $valH;
        };

        // Row 1: Receipt No (left) + PAID badge (right)
        $y1 = $field($this->marginLeft, $topY, 'Receipt No', $doc->number);

        $badgeW = 30;
        $badgeH = 10;
        $badgeX = $this->marginLeft + $this->pageWidth - $badgeW;
        [$gr, $gg, $gb] = $this->hexToRgb($green);
        $this->SetLineWidth(0.4);
        $this->SetDrawColor($gr, $gg, $gb);
        $this->RoundedRect($badgeX, $topY, $badgeW, $badgeH, 1.5, '1111', 'D');
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(0, 0, 0);
        $this->SetXY($badgeX, $topY + 0.4);
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor($gr, $gg, $gb);
        $this->Cell($badgeW, 9, 'PAID', 0, 0, 'C');

        // Row 2: Date
        $y2 = $field($this->marginLeft, $y1 + 2, 'Date', $doc->issue_date->format('d M Y'));

        // Row 3: Received From (left) + For (right)
        $rowY = $y2 + 3;
        $field($this->marginLeft, $rowY, 'Received From', $client->name);
        $y3 = $field($this->marginLeft + $col, $rowY, 'For', $project->title);

        $this->SetY($y3 + 5);

        // Amount received — large, rule underneath
        $this->rule($this->GetY(), $this->borderGray, 0.2);
        $this->Ln(2.5);
        $this->SetX($this->marginLeft);
        $this->SetFont('helvetica', '', 9);
        $this->ink($this->textMuted);
        $this->Cell($this->pageWidth / 2, 10, 'Amount Received', 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 17);
        $this->ink($this->primaryColor);
        $this->Cell($this->pageWidth / 2, 10, $this->money($amountPaid), 0, 1, 'R');
        $this->Ln(1);
        $this->rule($this->GetY(), $this->borderGray, 0.2);
        $this->Ln(4);

        // Payment details
        if ($payment) {
            $rows = array_filter([
                ['Payment Method', $payment->method_label],
                ['Payment Type', $payment->kind_label],
                $payment->reference ? ['Reference', $payment->reference] : null,
                ['Paid On', $payment->paid_on->format('d M Y')],
                ['Applied To', $refLabel],
            ]);
            foreach ($rows as [$label, $value]) {
                $this->SetX($this->marginLeft);
                $this->SetFont('helvetica', '', 9);
                $this->ink($this->textMuted);
                $this->Cell(45, 6, $label, 0, 0, 'L');
                $this->SetFont('helvetica', 'B', 9);
                $this->ink($this->textDark);
                $this->Cell($this->pageWidth - 45, 6, $value, 0, 1, 'R');
            }
            $this->Ln(3);
        }

        // Settlement summary
        $labelW = 46;
        $valueW = 42;
        $startX = $this->marginLeft + $this->pageWidth - $labelW - $valueW;

        $this->SetXY($startX, $this->GetY());
        $this->SetFont('helvetica', '', 9);
        $this->ink($this->textMuted);
        $this->Cell($labelW, 5.6, $invoice ? 'Invoice Total' : 'Project Total', 0, 0, 'R');
        $this->ink($this->textDark);
        $this->Cell($valueW, 5.6, $this->money($refTotal), 0, 1, 'R');
        $this->SetX($startX);
        $this->ink($this->textMuted);
        $this->Cell($labelW, 5.6, 'Total Paid To Date', 0, 0, 'R');
        $this->ink($this->textDark);
        $this->Cell($valueW, 5.6, $this->money($refPaid), 0, 1, 'R');

        $this->rule($this->GetY() + 0.5, $this->textDark, 0.3, $startX);
        $this->Ln(2);
        $this->SetXY($startX, $this->GetY());
        $this->SetFont('helvetica', 'B', 10.5);
        $this->SetTextColor(...$this->hexToRgb($refBalance <= 0 ? $green : $this->primaryColor));
        $this->Cell($labelW, 6, 'Balance Remaining', 0, 0, 'R');
        $this->Cell($valueW, 6, $this->money(max(0, $refBalance)), 0, 1, 'R');

        $this->Ln(3);
    }
}
