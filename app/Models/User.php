<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_verified' => 'boolean',
    ];

    // Role constants
    const ROLE_ADMIN = 'admin';
    const ROLE_LANDLORD = 'landlord';
    const ROLE_TENANT = 'tenant';

    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin (PSU)',
            self::ROLE_LANDLORD => 'Landlord',
            self::ROLE_TENANT => 'Tenant/Student',
        ];
    }

    // Relationships
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'tenant_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function savedSearches(): HasMany
    {
        return $this->hasMany(SavedSearch::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // Role checking methods
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isLandlord(): bool
    {
        return $this->role === self::ROLE_LANDLORD;
    }

    public function isTenant(): bool
    {
        return $this->role === self::ROLE_TENANT;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    // Scopes
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Accessors
    public function getRoleNameAttribute(): string
    {
        return self::getRoles()[$this->role] ?? 'Unknown';
    }

    public function getIsVerifiedBadgeAttribute(): string
    {
        return $this->is_verified ? 'Verified' : 'Unverified';
    }

    public function inquiries()
    {
    return $this->hasMany(Inquiry::class);
    }
}