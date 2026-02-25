<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'monthly_amount',
        'is_active',
        'joined_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
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
            'password' => 'hashed',
            'joined_date' => 'date',
            'is_active' => 'boolean',
            'monthly_amount' => 'decimal:2',
        ];
    }

    /**
     * User's contributions
     */
    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    /**
     * Contributions submitted by this user (for others)
     */
    public function submittedContributions(): HasMany
    {
        return $this->hasMany(Contribution::class, 'submitted_by');
    }

    /**
     * Contributions approved by this user
     */
    public function approvedContributions(): HasMany
    {
        return $this->hasMany(Contribution::class, 'approved_by');
    }

    /**
     * Withdrawals requested by this user
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class, 'requested_by');
    }

    /**
     * Get total approved contributions for this user
     */
    public function getTotalContributionsAttribute(): float
    {
        return $this->contributions()->where('status', 'approved')->sum('amount');
    }

    /**
     * Check if user has paid for a specific month/year
     */
    public function hasPaidForMonth(int $month, int $year): bool
    {
        return $this->contributions()
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Get pending contribution for current month
     */
    public function getPendingContributionForMonth(int $month, int $year)
    {
        return $this->contributions()
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'pending')
            ->first();
    }
}
