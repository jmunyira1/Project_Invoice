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

    public function getTotalAttribute(): float
    {
        return $this->lines->sum('total_price');
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
