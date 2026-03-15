<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'project_id',
        'document_id',
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

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
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

    // ── Helpers ────────────────────────────────────────────────────

    protected function casts(): array
    {
        return [
            'paid_on' => 'date',
            'amount' => 'float',
        ];
    }
}
