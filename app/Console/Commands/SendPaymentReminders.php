<?php

namespace App\Console\Commands;

use App\Mail\PaymentReminderMail;
use App\Models\Contribution;
use App\Models\EmailLog;
use App\Models\MonthlySetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminders extends Command
{
    protected $signature = 'reminders:send-payment 
                            {--days=7 : Days before due date to send reminder}
                            {--force : Send reminders regardless of date}
                            {--user= : Send to specific user ID only}';

    protected $description = 'Send payment reminder emails to members who have not paid for the current month';

    public function handle()
    {
        $settings = MonthlySetting::getSettings();
        $daysBeforeDue = $this->option('days');
        $force = $this->option('force');
        $specificUserId = $this->option('user');

        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        
        $dueDate = Carbon::create($currentYear, $currentMonth, $settings->due_day);
        $dueMonth = $dueDate->format('F Y');
        $dueAmount = $settings->monthly_contribution_amount;

        // Check if we should send reminders
        $daysUntilDue = $now->diffInDays($dueDate, false);
        
        if (!$force && !$specificUserId && ($daysUntilDue < 0 || $daysUntilDue > $daysBeforeDue)) {
            $this->info("Not sending reminders. Current: {$now->format('Y-m-d')}, Due: {$dueDate->format('Y-m-d')}, Days until due: {$daysUntilDue}");
            return Command::SUCCESS;
        }

        // Get members to send to
        $membersQuery = User::where('status', 'approved')
            ->where('is_active', true)
            ->where('email', '!=', 'alliedgroup@gmail.com');

        if ($specificUserId) {
            $membersQuery->where('id', $specificUserId);
        }

        $members = $membersQuery->get();

        $this->info("Processing {$members->count()} members...");
        $sentCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        foreach ($members as $member) {
            // Check if already paid for current month
            $hasPaid = Contribution::where('user_id', $member->id)
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->whereIn('status', ['approved', 'pending'])
                ->exists();

            if ($hasPaid && !$force) {
                $skippedCount++;
                continue;
            }

            // Check if already sent reminder this month (avoid duplicate)
            $alreadySent = EmailLog::where('user_id', $member->id)
                ->where('type', EmailLog::TYPE_PAYMENT_REMINDER)
                ->where('month_year', $dueMonth)
                ->where('status', EmailLog::STATUS_SENT)
                ->exists();

            if ($alreadySent && !$force) {
                $this->line("⊘ Already sent to: {$member->email}");
                $skippedCount++;
                continue;
            }

            // Create log entry
            $log = EmailLog::create([
                'user_id' => $member->id,
                'recipient_email' => $member->email,
                'recipient_name' => $member->name,
                'type' => EmailLog::TYPE_PAYMENT_REMINDER,
                'subject' => "Payment Reminder - {$dueMonth}",
                'message_preview' => "Your monthly contribution of ৳{$dueAmount} is due by {$dueDate->format('d F Y')}.",
                'status' => EmailLog::STATUS_PENDING,
                'month_year' => $dueMonth,
                'amount' => $dueAmount,
            ]);

            // Send reminder
            try {
                Mail::to($member->email)->send(new PaymentReminderMail($member, $dueMonth, $dueAmount));
                
                $log->update([
                    'status' => EmailLog::STATUS_SENT,
                    'sent_at' => now(),
                ]);
                
                $this->line("✓ Reminder sent to: {$member->email}");
                $sentCount++;
            } catch (\Exception $e) {
                $log->update([
                    'status' => EmailLog::STATUS_FAILED,
                    'error_message' => $e->getMessage(),
                    'sent_at' => now(),
                ]);
                
                $this->error("✗ Failed to send to {$member->email}: " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->info("Completed! Sent: {$sentCount}, Skipped: {$skippedCount}, Failed: {$failedCount}");

        return Command::SUCCESS;
    }
}
