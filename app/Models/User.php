<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'organisation_id',
        'name',
        'email',
        'password',
        'role',
        'is_super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    // ── Relationships ──────────────────────────────────────────────

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function isSuperAdmin(): bool
    {
        return (bool)$this->is_super_admin;
    }

    /**
     * Initials for the avatar circle in the navbar.
     */
    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->take(2)
            ->implode('');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }
}
