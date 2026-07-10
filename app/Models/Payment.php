<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'project_id',
        'document_id',
        'installment_id',
        'kind',
        'amount',
        'method',
        'reference',
        'paid_on',
        'notes',
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

    /**
     * The invoice/quote this payment is allocated against.
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function installment(): BelongsTo
    {
        return $this->belongsTo(Installment::class);
    }

    /**
     * The receipt document generated for this payment (if any).
     */
    public function receipt(): HasOne
    {
        return $this->hasOne(Document::class, 'payment_id')
            ->where('type', 'receipt');
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'mpesa' => 'M-Pesa',
            'cash' => 'Cash',
            'bank' => 'Bank Transfer',
            'cheque' => 'Cheque',
            'card' => 'Card',
            default => ucfirst($this->method),
        };
    }

    public function getKindLabelAttribute(): string
    {
        return match ($this->kind) {
            'deposit' => 'Deposit',
            'part_payment' => 'Part Payment',
            'balance' => 'Balance',
            'installment' => 'Installment',
            'refund' => 'Refund',
            default => ucfirst(str_replace('_', ' ', (string) $this->kind)),
        };
    }

    public function getKindBadgeAttribute(): string
    {
        return match ($this->kind) {
            'deposit' => 'primary',
            'balance' => 'success',
            'installment' => 'info',
            'refund' => 'danger',
            default => 'secondary',
        };
    }

    // ── Helpers ────────────────────────────────────────────────────

    protected function casts(): array
    {
        return [
            'paid_on' => 'date',
            'amount' => 'float',
        ];
    }
}
