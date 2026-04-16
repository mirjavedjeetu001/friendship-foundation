<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'submitted_by',
        'amount',
        'month',
        'year',
        'payment_slip',
        'transaction_reference',
        'notes',
        'status',
        'is_late',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'admin_approved_by',
        'admin_approved_at',
        'accountant_approved_by',
        'accountant_approved_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_late' => 'boolean',
            'approved_at' => 'datetime',
            'admin_approved_at' => 'datetime',
            'accountant_approved_at' => 'datetime',
        ];
    }

    /**
     * The member this contribution belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User who submitted this contribution
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * User who approved this contribution
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Admin who approved this contribution
     */
    public function adminApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    /**
     * Accountant who approved this contribution
     */
    public function accountantApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accountant_approved_by');
    }

    /**
     * Check if admin has approved
     */
    public function isAdminApproved(): bool
    {
        return $this->admin_approved_by !== null;
    }

    /**
     * Check if accountant has approved
     */
    public function isAccountantApproved(): bool
    {
        return $this->accountant_approved_by !== null;
    }

    /**
     * Get formatted month/year
     */
    public function getMonthYearAttribute(): string
    {
        return date('F Y', mktime(0, 0, 0, $this->month, 1, $this->year));
    }

    /**
     * Check if this is a self-submitted contribution
     */
    public function getIsSelfSubmittedAttribute(): bool
    {
        return $this->user_id === $this->submitted_by;
    }

    /**
     * Scope for pending contributions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved contributions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for a specific month/year
     */
    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }
}
