<?php

namespace App\Models;

use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'project_id',
        'payment_id',
        'template_id',
        'type',
        'number',
        'issue_date',
        'due_date',
        'notes',
        'file_path',
        'sent_at',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    // ── Relationships ──────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(DocumentLine::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * For receipt documents: the payment this receipt evidences.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Net total before tax.
     */
    public function getSubtotalAttribute(): float
    {
        return round($this->lines->sum('total_price'), 2);
    }

    /**
     * Total VAT across all lines.
     */
    public function getTaxTotalAttribute(): float
    {
        return round($this->lines->sum(fn ($l) => $l->tax_amount), 2);
    }

    /**
     * Grand total the client owes (net + VAT).
     */
    public function getTotalAttribute(): float
    {
        return round($this->subtotal + $this->tax_total, 2);
    }

    /**
     * VAT grouped by rate, e.g. [16.0 => ['net' => 3500, 'tax' => 560]].
     * Only rates greater than zero are included.
     */
    public function getTaxBreakdownAttribute(): array
    {
        $groups = [];
        foreach ($this->lines as $line) {
            if ($line->tax_rate <= 0) {
                continue;
            }
            $rate = (float) $line->tax_rate;
            $groups[(string) $rate] ??= ['rate' => $rate, 'net' => 0.0, 'tax' => 0.0];
            $groups[(string) $rate]['net'] += $line->total_price;
            $groups[(string) $rate]['tax'] += $line->tax_amount;
        }
        return array_values($groups);
    }

    /**
     * Whether VAT actually applies to this document.
     */
    public function getHasTaxAttribute(): bool
    {
        return $this->tax_total > 0;
    }

    /**
     * A VAT invoice is titled "Tax Invoice" in Kenya.
     */
    public function getIsTaxInvoiceAttribute(): bool
    {
        return $this->type === 'invoice' && $this->has_tax;
    }

    // ── Computed ───────────────────────────────────────────────────

    public function getTotalPaidAttribute(): float
    {
        return $this->payments->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return $this->total - $this->total_paid;
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'quote' => 'file-text',
            'invoice' => 'file',
            'receipt' => 'check-circle',
            'delivery_note' => 'truck',
            'statement' => 'list',
            default => 'file',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'quote' => 'Quote',
            'invoice' => 'Invoice',
            'receipt' => 'Receipt',
            'delivery_note' => 'Delivery Note',
            'statement' => 'Statement',
            default => ucfirst($this->type),
        };
    }

    /**
     * The heading shown on the document — a VAT invoice reads "Tax Invoice".
     */
    public function getDocumentTitleAttribute(): string
    {
        return $this->is_tax_invoice ? 'Tax Invoice' : $this->type_label;
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->sent_at) {
            return $this->balance <= 0 ? 'success' : 'warning';
        }
        return 'secondary';
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->sent_at) {
            return $this->balance <= 0 ? 'Paid' : 'Sent';
        }
        return 'Draft';
    }

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'sent_at' => 'datetime',
        ];
    }
}
