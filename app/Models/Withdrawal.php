<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'requested_by',
        'amount',
        'purpose',
        'description',
        'receipt',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'withdrawal_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'withdrawal_date' => 'date',
        ];
    }

    /**
     * User who requested this withdrawal
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * User who approved this withdrawal
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope for pending withdrawals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved withdrawals
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
