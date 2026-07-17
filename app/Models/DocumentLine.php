<?php

namespace App\Models;

use Database\Factories\DocumentLineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentLine extends Model
{
    /** @use HasFactory<DocumentLineFactory> */
    use HasFactory;

    protected $fillable = [
        'document_id',
        'name',
        'description',
        'quantity',
        'unit_price',
        'total_price',
        'tax_rate',
        'sort_order',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * VAT amount for this line (tax-exclusive pricing).
     */
    public function getTaxAmountAttribute(): float
    {
        return round($this->total_price * ($this->tax_rate / 100), 2);
    }

    /**
     * Line total including tax.
     */
    public function getTotalWithTaxAttribute(): float
    {
        return round($this->total_price + $this->tax_amount, 2);
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'unit_price' => 'float',
            'total_price' => 'float',
            'tax_rate' => 'float',
        ];
    }
}
