<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'preview_path',
    ];

    public function organisations(): HasMany
    {
        return $this->hasMany(Organisation::class, 'default_template_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Resolve the TCPDF class for this template.
     * Convention: slug = 'template-001' → class = App\Pdf\Template001
     */
    public function getPdfClass(): string
    {
        // Convert slug like 'template-001' → 'Template001'
        $className = str_replace('-', '', ucwords($this->slug, '-'));
        return 'App\\Pdf\\' . $className;
    }
}
