<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'client_id',
        'title',
        'description',
        'status',
        'due_date',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    // ── Relationships ──────────────────────────────────────────────

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(Deliverable::class);
    }

    public function costs(): HasMany
    {
        return $this->hasMany(Cost::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Total value of all deliverables (what we charge the client).
     */
    public function getTotalValueAttribute(): float
    {
        return $this->deliverables
            ->sum(fn($d) => $d->quantity * $d->unit_price);
    }

    // ── Computed values ────────────────────────────────────────────

    /**
     * Total internal costs.
     */
    public function getTotalCostsAttribute(): float
    {
        return $this->costs->sum('amount');
    }

    /**
     * Gross profit = value - costs.
     */
    public function getProfitAttribute(): float
    {
        return $this->total_value - $this->total_costs;
    }

    /**
     * Total amount paid against this project.
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments->sum('amount');
    }

    /**
     * Outstanding balance = value - paid.
     */
    public function getBalanceAttribute(): float
    {
        return $this->total_value - $this->total_paid;
    }

    /**
     * Human-readable status badge colour for Cuba.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'quoted' => 'info',
            'active' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * All allowed status transitions from the current status.
     */
    public function getAllowedTransitionsAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['quoted', 'active', 'cancelled'],
            'quoted' => ['active', 'cancelled'],
            'active' => ['completed', 'cancelled'],
            'cancelled' => ['draft'],
            default => [],
        };
    }

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }
}
