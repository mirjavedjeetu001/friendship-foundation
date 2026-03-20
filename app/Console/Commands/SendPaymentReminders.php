<?php

namespace App\Console\Commands;

use App\Mail\PaymentReminderMail;
use App\Models\Contribution;
use App\Models\MonthlySetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminders extends Command
{
    protected $signature = 'reminders:send-payment 
                            {--days=3 : Days before due date to send reminder}
                            {--force : Send reminders regardless of date}';

    protected $description = 'Send payment reminder emails to members who have not paid for the current month';

    public function handle()
    {
        $settings = MonthlySetting::getSettings();
        $daysBeforeDue = $this->option('days');
        $force = $this->option('force');

        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        
        $dueDate = Carbon::create($currentYear, $currentMonth, $settings->due_day);

        // Check if we should send reminders
        $daysUntilDue = $now->diffInDays($dueDate, false);
        
        if (!$force && ($daysUntilDue < 0 || $daysUntilDue > $daysBeforeDue)) {
            $this->info("Not sending reminders. Current: {$now->format('Y-m-d')}, Due: {$dueDate->format('Y-m-d')}, Days until due: {$daysUntilDue}");
            return Command::SUCCESS;
        }

        // Get all approved members (everyone except super-admin)
        $members = User::where('status', 'approved')
            ->where('is_active', true)
            ->where('email', '!=', 'alliedgroup@gmail.com')
            ->get();

        $this->info("Processing {$members->count()} members...");
        $sentCount = 0;
        $skippedCount = 0;

        foreach ($members as $member) {
            // Check if already paid for current month
            $hasPaid = Contribution::where('user_id', $member->id)
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->whereIn('status', ['approved', 'pending'])
                ->exists();

            if ($hasPaid) {
                $skippedCount++;
                continue;
            }

            // Send reminder
            try {
                $dueMonth = $dueDate->format('F Y');
                $dueAmount = $settings->monthly_contribution_amount;

                Mail::to($member->email)->send(new PaymentReminderMail($member, $dueMonth, $dueAmount));
                
                $this->line("✓ Reminder sent to: {$member->email}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$member->email}: " . $e->getMessage());
            }
        }

        $this->info("Completed! Sent: {$sentCount}, Skipped (already paid): {$skippedCount}");

        return Command::SUCCESS;
    }
}
