<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Installment extends Model
{
    protected $fillable = [
        'organisation_id',
        'project_id',
        'document_id',
        'label',
        'amount',
        'due_date',
        'status',
        'sort_order',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ── Computed ───────────────────────────────────────────────────

    public function getPaidAttribute(): float
    {
        return (float) $this->payments->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return round($this->amount - $this->paid, 2);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date
            && $this->balance > 0
            && $this->due_date->isPast();
    }

    /**
     * Fraction of this installment settled (0–1).
     */
    public function getProgressAttribute(): float
    {
        if ($this->amount <= 0) {
            return 1.0;
        }
        return min(1.0, round($this->paid / $this->amount, 4));
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->effective_status) {
            'paid' => 'success',
            'partial' => 'info',
            'overdue' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->effective_status);
    }

    /**
     * Status derived live from payments + due date (authoritative for display).
     */
    public function getEffectiveStatusAttribute(): string
    {
        if ($this->balance <= 0) {
            return 'paid';
        }
        if ($this->is_overdue) {
            return 'overdue';
        }
        return $this->paid > 0 ? 'partial' : 'pending';
    }

    /**
     * Persist the derived status back to the column.
     */
    public function syncStatus(): void
    {
        $this->update(['status' => $this->effective_status]);
    }

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'amount' => 'float',
        ];
    }
}
