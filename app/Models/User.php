<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'city',
        'province',
        'is_verified',
        'valid_id_path',
        'profile_picture',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
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
        return $this->hasMany(Booking::class, 'user_id');
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

    // Add these relationships to your existing User.php file after the existing relationships

public function transactions(): HasMany
{
    return $this->hasMany(Transaction::class);
}

public function notifications(): HasMany
{
    return $this->hasMany(Notification::class);
}

public function scheduledVisits(): HasMany
{
    return $this->hasMany(ScheduledVisit::class);
}

// Method to get unread notification count
public function getUnreadNotificationsCountAttribute(): int
{
    return $this->notifications()->unread()->count();
}

/**
 * Send the password reset notification.
 */
public function sendPasswordResetNotification($token)
{
    $this->notify(new \App\Notifications\CustomPasswordResetNotification($token));
}

}