<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * A dashboard user. Roles and permissions come from spatie/laravel-permission
 * (HasRoles): $user->assignRole('Editor'), $user->can('projects.update').
 */
#[Fillable(['name', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, RecordsActivity;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /** Super Admins pass every permission check (see AppServiceProvider). */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(config('access.super_admin'));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Is this the only active Super Admin left? (Then it must stay one.) */
    public function isLastActiveSuperAdmin(): bool
    {
        if (! $this->isSuperAdmin() || ! $this->is_active) {
            return false;
        }

        return static::role(config('access.super_admin'))->active()->whereKeyNot($this->getKey())->doesntExist();
    }

    /** "Mohamed Ehab" → "ME" (avatar circle in the dashboard). */
    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->name)) ?: [];
        $letters = array_map(fn ($w) => mb_substr($w, 0, 1), array_slice($words, 0, 2));

        return mb_strtoupper(implode('', $letters)) ?: '?';
    }
}
