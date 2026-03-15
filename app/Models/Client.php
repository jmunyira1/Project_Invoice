<?php

namespace App\Models;

use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'name',
        'email',
        'phone',
        'address',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Initials for avatar display.
     */
    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
            ->take(2)
            ->implode('');
    }

    // ── Helpers ────────────────────────────────────────────────────

    /**
     * Total value of all projects for this client.
     */
    public function getTotalProjectValueAttribute(): float
    {
        return $this->projects()
            ->with('deliverables')
            ->get()
            ->sum(fn($p) => $p->deliverables->sum(fn($d) => $d->quantity * $d->unit_price));
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
