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
        'sort_order',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'unit_price' => 'float',
            'total_price' => 'float',
        ];
    }
}
