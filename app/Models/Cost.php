<?php

namespace App\Models;

use Database\Factories\CostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cost extends Model
{
    /** @use HasFactory<CostFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'amount',
        'incurred_on',
        'notes',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    protected function casts(): array
    {
        return [
            'incurred_on' => 'date',
            'amount' => 'float',
        ];
    }
}
