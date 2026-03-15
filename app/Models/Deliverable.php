<?php

namespace App\Models;

use Database\Factories\DeliverableFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deliverable extends Model
{
    /** @use HasFactory<DeliverableFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'quantity',
        'unit_price',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Total price computed — not stored.
     */
    public function getTotalPriceAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'unit_price' => 'float',
        ];
    }
}
