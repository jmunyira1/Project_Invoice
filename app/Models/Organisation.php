<?php

namespace App\Models;

use Database\Factories\OrganisationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    /** @use HasFactory<OrganisationFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'logo_path',
        'currency',
        'default_template_id',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function defaultTemplate(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'default_template_id');
    }

    // ── Helpers ────────────────────────────────────────────────────

    /**
     * The currency symbol for display.
     */
    public function getCurrencySymbolAttribute(): string
    {
        return match ($this->currency) {
            'KES' => 'KES',
            'USD' => '$',
            'GBP' => '£',
            'EUR' => '€',
            default => $this->currency,
        };
    }

    /**
     * Generate the next document number for a given type.
     * Format: INV-2501-001, QT-2501-002, DN-2501-001 etc.
     */
    public function nextDocumentNumber(string $type): string
    {
        $prefix = match ($type) {
            'quote' => 'QT',
            'invoice' => 'INV',
            'receipt' => 'REC',
            'delivery_note' => 'DN',
            'statement' => 'ST',
            default => strtoupper(substr($type, 0, 3)),
        };

        $period = now()->format('ym'); // e.g. 2501

        $last = Document::where('organisation_id', $this->id)
            ->where('type', $type)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $sequence = str_pad($last + 1, 3, '0', STR_PAD_LEFT);

        return "$prefix-$period-$sequence";
    }
}
