<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonthlySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'logo',
        'monthly_contribution_amount',
        'due_day',
        'start_month',
        'start_year',
        'bank_balance',
        'bank_name',
        'account_number',
        'account_holder',
        'routing_number',
        'branch',
        'is_active',
        'force_app_update',
    ];

    protected function casts(): array
    {
        return [
            'monthly_contribution_amount' => 'decimal:2',
            'bank_balance' => 'decimal:2',
            'is_active' => 'boolean',
            'force_app_update' => 'boolean',
            'start_month' => 'integer',
            'start_year' => 'integer',
        ];
    }

    /**
     * Get the active settings
     */
    public static function getSettings()
    {
        return self::where('is_active', true)->first() ?? self::create([
            'app_name' => 'Allied Group',
            'monthly_contribution_amount' => 500,
            'due_day' => 10,
            'start_month' => 4, // April
            'start_year' => 2025,
            'bank_balance' => 0,
        ]);
    }

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    /**
     * Update bank balance
     */
    public function updateBalance(float $amount, string $type = 'add'): void
    {
        if ($type === 'add') {
            $this->increment('bank_balance', $amount);
        } else {
            $this->decrement('bank_balance', $amount);
        }
    }
}
