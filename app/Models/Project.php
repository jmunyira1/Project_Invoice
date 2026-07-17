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
        'value',
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

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class)->orderBy('sort_order');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class)->latest();
    }

    /**
     * Sum of all deliverable line items.
     */
    public function getDeliverablesTotalAttribute(): float
    {
        return $this->deliverables
            ->sum(fn($d) => $d->quantity * $d->unit_price);
    }

    /**
     * Headline value of the project (what we charge the client).
     * Uses the agreed value set up-front when present, otherwise falls
     * back to the sum of deliverables.
     */
    public function getTotalValueAttribute(): float
    {
        return $this->value !== null
            ? (float) $this->value
            : $this->deliverables_total;
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
     * VAT the client is charged across the project (0 when not VAT registered).
     * Mirrors how invoices compute tax so on-screen figures reconcile with the
     * documents the client actually receives.
     */
    public function getTaxTotalAttribute(): float
    {
        $org = $this->organisation;
        if (!$org || !$org->vat_registered) {
            return 0.0;
        }

        if ($this->value !== null) {
            return round($this->value * ((float) $org->default_tax_rate / 100), 2);
        }

        return round($this->deliverables->sum(function ($d) use ($org) {
            $net = $d->quantity * $d->unit_price;
            return $net * ($org->effectiveTaxRate($d->tax_rate) / 100);
        }), 2);
    }

    /**
     * Total the client owes, including VAT.
     */
    public function getGrossValueAttribute(): float
    {
        return round($this->total_value + $this->tax_total, 2);
    }

    /**
     * Outstanding balance = gross value (incl. VAT) - paid.
     */
    public function getBalanceAttribute(): float
    {
        return round($this->gross_value - $this->total_paid, 2);
    }

    /**
     * Total received as deposits.
     */
    public function getDepositPaidAttribute(): float
    {
        return (float) $this->payments->where('kind', 'deposit')->sum('amount');
    }

    /**
     * Percentage of the project value collected so far (0–100).
     */
    public function getPaidPercentAttribute(): float
    {
        if ($this->gross_value <= 0) {
            return 0.0;
        }
        return min(100, round(($this->total_paid / $this->gross_value) * 100, 1));
    }

    /**
     * Total value scheduled across the installment plan.
     */
    public function getScheduledTotalAttribute(): float
    {
        return (float) $this->installments->sum('amount');
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
            'value' => 'float',
        ];
    }
}
