<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'latitude',
        'longitude',
        'balance',
        'is_active',
        'profile_photo',
        'is_verified',
        'verified_at',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function donorProfile()
    {
        return $this->hasOne(DonorProfile::class);
    }

    public function foods()
    {
        return $this->hasMany(Food::class, 'donor_id');
    }

    public function claims()
    {
        return $this->hasMany(FoodClaim::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class, 'donor_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDonatur(): bool
    {
        return $this->role === 'donatur';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function dashboardRouteName(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'donatur' => 'donatur.dashboard',
            default => 'user.dashboard',
        };
    }
}
