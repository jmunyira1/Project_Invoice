<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFile extends Model
{
    protected $fillable = [
        'organisation_id',
        'project_id',
        'uploaded_by',
        'category',
        'original_name',
        'path',
        'mime',
        'size',
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

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ── Computed ───────────────────────────────────────────────────

    public function getCategoryLabelAttribute(): string
    {
        return ucfirst($this->category);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = (int) $this->size;
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        $units = ['KB', 'MB', 'GB', 'TB'];
        $i = -1;
        do {
            $bytes /= 1024;
            $i++;
        } while ($bytes >= 1024 && $i < count($units) - 1);

        return round($bytes, 1) . ' ' . $units[$i];
    }

    /**
     * Bootstrap-icon name based on mime/extension.
     */
    public function getIconAttribute(): string
    {
        $mime = (string) $this->mime;
        $ext = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));

        return match (true) {
            str_contains($mime, 'pdf') => 'bi-file-earmark-pdf',
            str_contains($mime, 'image') => 'bi-file-earmark-image',
            str_contains($mime, 'word') || in_array($ext, ['doc', 'docx']) => 'bi-file-earmark-word',
            str_contains($mime, 'sheet') || in_array($ext, ['xls', 'xlsx', 'csv']) => 'bi-file-earmark-spreadsheet',
            str_contains($mime, 'zip') || in_array($ext, ['zip', 'rar', '7z']) => 'bi-file-earmark-zip',
            default => 'bi-file-earmark',
        };
    }

    public function getCategoryBadgeAttribute(): string
    {
        return match ($this->category) {
            'contract' => 'primary',
            'brief' => 'info',
            'reference' => 'secondary',
            'signed' => 'success',
            default => 'secondary',
        };
    }

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }
}
