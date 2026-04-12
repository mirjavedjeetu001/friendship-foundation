<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_email',
        'recipient_name',
        'type',
        'subject',
        'message_preview',
        'status',
        'error_message',
        'month_year',
        'amount',
        'sent_by',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Email types
     */
    const TYPE_PAYMENT_REMINDER = 'payment_reminder';
    const TYPE_REGISTRATION_APPROVED = 'registration_approved';
    const TYPE_REGISTRATION_REJECTED = 'registration_rejected';
    const TYPE_CONTRIBUTION_APPROVED = 'contribution_approved';
    const TYPE_WITHDRAWAL_APPROVED = 'withdrawal_approved';

    /**
     * Status constants
     */
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';
    const STATUS_PENDING = 'pending';

    /**
     * Get the recipient user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who sent the email manually
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Scope for sent emails
     */
    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    /**
     * Scope for failed emails
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope for payment reminders
     */
    public function scopePaymentReminders($query)
    {
        return $query->where('type', self::TYPE_PAYMENT_REMINDER);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_SENT => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            self::STATUS_FAILED => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_PAYMENT_REMINDER => 'Payment Reminder',
            self::TYPE_REGISTRATION_APPROVED => 'Registration Approved',
            self::TYPE_REGISTRATION_REJECTED => 'Registration Rejected',
            self::TYPE_CONTRIBUTION_APPROVED => 'Contribution Approved',
            self::TYPE_WITHDRAWAL_APPROVED => 'Withdrawal Approved',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
