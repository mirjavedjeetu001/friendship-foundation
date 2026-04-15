<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_date',
        'purpose',
        'spent_by',
        'amount',
        'description',
        'receipt',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'fund_source',
        'fund_source_note',
        'payment_type',
        'settlement_status',
        'settled_by',
        'settled_at',
        'settlement_note',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'settled_at' => 'datetime',
        ];
    }

    /**
     * Payment type constants
     */
    const PAYMENT_TYPE_CASH = 'cash';
    const PAYMENT_TYPE_BANK = 'bank';

    /**
     * Settlement status constants
     */
    const SETTLEMENT_PENDING = 'pending';
    const SETTLEMENT_SETTLED = 'settled';
    const SETTLEMENT_NOT_APPLICABLE = 'not_applicable';

    /**
     * Fund source options
     */
    const FUND_SOURCE_MONTHLY_SAVINGS = 'monthly_savings';
    const FUND_SOURCE_MANUAL = 'manual';

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the user who created this expense
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this expense
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who settled this expense
     */
    public function settler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'settled_by');
    }

    /**
     * Check if expense is cash payment
     */
    public function isCashPayment(): bool
    {
        return $this->payment_type === self::PAYMENT_TYPE_CASH;
    }

    /**
     * Check if expense is bank payment
     */
    public function isBankPayment(): bool
    {
        return $this->payment_type === self::PAYMENT_TYPE_BANK;
    }

    /**
     * Check if expense needs bank settlement
     */
    public function needsSettlement(): bool
    {
        return $this->isApproved() && $this->isCashPayment() && $this->settlement_status === self::SETTLEMENT_PENDING;
    }

    /**
     * Check if expense is settled
     */
    public function isSettled(): bool
    {
        return $this->settlement_status === self::SETTLEMENT_SETTLED || $this->settlement_status === self::SETTLEMENT_NOT_APPLICABLE;
    }

    /**
     * Scope for expenses pending settlement
     */
    public function scopePendingSettlement($query)
    {
        return $query->where('status', self::STATUS_APPROVED)
            ->where('payment_type', self::PAYMENT_TYPE_CASH)
            ->where('settlement_status', self::SETTLEMENT_PENDING);
    }

    /**
     * Scope for settled expenses
     */
    public function scopeSettledFromBank($query)
    {
        return $query->where('status', self::STATUS_APPROVED)
            ->where(function($q) {
                $q->where('settlement_status', self::SETTLEMENT_SETTLED)
                  ->orWhere(function($q2) {
                      $q2->where('payment_type', self::PAYMENT_TYPE_BANK)
                         ->where('settlement_status', self::SETTLEMENT_NOT_APPLICABLE);
                  });
            });
    }

    /**
     * Scope for pending expenses
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved expenses
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for rejected expenses
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Check if expense is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if expense is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if expense is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Get receipt URL
     */
    public function getReceiptUrlAttribute(): ?string
    {
        if ($this->receipt) {
            return Storage::url($this->receipt);
        }
        return null;
    }

    /**
     * Get fund source label
     */
    public function getFundSourceLabelAttribute(): ?string
    {
        return match($this->fund_source) {
            self::FUND_SOURCE_MONTHLY_SAVINGS => 'Monthly Savings',
            self::FUND_SOURCE_MANUAL => 'Manual Adjustment',
            default => null,
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_APPROVED => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
