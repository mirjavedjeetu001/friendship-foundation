<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReminderMail;
use App\Models\Contribution;
use App\Models\EmailLog;
use App\Models\MonthlySetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class EmailLogController extends Controller
{
    /**
     * Display email logs
     */
    public function index(Request $request)
    {
        $query = EmailLog::with(['user', 'sender'])
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by month
        if ($request->filled('month_year')) {
            $query->where('month_year', $request->month_year);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Get stats
        $totalSent = EmailLog::sent()->count();
        $totalFailed = EmailLog::failed()->count();
        $sentThisMonth = EmailLog::sent()
            ->whereMonth('sent_at', now()->month)
            ->whereYear('sent_at', now()->year)
            ->count();

        // Get current month reminder status
        $settings = MonthlySetting::getSettings();
        $currentMonth = now()->format('F Y');
        $dueDate = Carbon::create(now()->year, now()->month, $settings->due_day);
        
        $remindersSentThisMonth = EmailLog::paymentReminders()
            ->sent()
            ->where('month_year', $currentMonth)
            ->count();

        // Get unpaid members count for current month
        $paidUserIds = Contribution::where('month', now()->month)
            ->where('year', now()->year)
            ->whereIn('status', ['approved', 'pending'])
            ->pluck('user_id');

        $unpaidMembersCount = User::where('status', 'approved')
            ->where('is_active', true)
            ->where('email', '!=', 'alliedgroup@gmail.com')
            ->whereNotIn('id', $paidUserIds)
            ->count();

        return view('admin.email-logs.index', compact(
            'logs',
            'totalSent',
            'totalFailed',
            'sentThisMonth',
            'remindersSentThisMonth',
            'unpaidMembersCount',
            'currentMonth',
            'dueDate',
            'settings'
        ));
    }

    /**
     * Send payment reminders to all unpaid members
     */
    public function sendReminders(Request $request)
    {
        $settings = MonthlySetting::getSettings();
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $dueMonth = $now->format('F Y');
        $dueDate = Carbon::create($currentYear, $currentMonth, $settings->due_day);
        $dueAmount = $settings->monthly_contribution_amount;

        // Get unpaid members
        $paidUserIds = Contribution::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->whereIn('status', ['approved', 'pending'])
            ->pluck('user_id');

        $members = User::where('status', 'approved')
            ->where('is_active', true)
            ->where('email', '!=', 'alliedgroup@gmail.com')
            ->whereNotIn('id', $paidUserIds)
            ->get();

        if ($members->isEmpty()) {
            return back()->with('info', 'All members have already paid for this month!');
        }

        $sentCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($members as $member) {
            // Check if already sent reminder this month
            $alreadySent = EmailLog::where('user_id', $member->id)
                ->where('type', EmailLog::TYPE_PAYMENT_REMINDER)
                ->where('month_year', $dueMonth)
                ->where('status', EmailLog::STATUS_SENT)
                ->exists();

            if ($alreadySent && !$request->has('force')) {
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
                'sent_by' => auth()->id(),
            ]);

            try {
                Mail::to($member->email)->send(new PaymentReminderMail($member, $dueMonth, $dueAmount));
                
                $log->update([
                    'status' => EmailLog::STATUS_SENT,
                    'sent_at' => now(),
                ]);
                
                $sentCount++;
            } catch (\Exception $e) {
                $log->update([
                    'status' => EmailLog::STATUS_FAILED,
                    'error_message' => $e->getMessage(),
                    'sent_at' => now(),
                ]);
                
                $failedCount++;
            }
        }

        $message = "Reminders sent: {$sentCount}";
        if ($skippedCount > 0) {
            $message .= ", Skipped (already sent): {$skippedCount}";
        }
        if ($failedCount > 0) {
            $message .= ", Failed: {$failedCount}";
        }

        return back()->with('success', $message);
    }

    /**
     * Send reminder to a specific member
     */
    public function sendToMember(Request $request, User $member)
    {
        $settings = MonthlySetting::getSettings();
        $now = Carbon::now();
        $dueMonth = $now->format('F Y');
        $dueDate = Carbon::create($now->year, $now->month, $settings->due_day);
        $dueAmount = $settings->monthly_contribution_amount;

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
            'sent_by' => auth()->id(),
        ]);

        try {
            Mail::to($member->email)->send(new PaymentReminderMail($member, $dueMonth, $dueAmount));
            
            $log->update([
                'status' => EmailLog::STATUS_SENT,
                'sent_at' => now(),
            ]);
            
            return back()->with('success', "Reminder sent to {$member->name}");
        } catch (\Exception $e) {
            $log->update([
                'status' => EmailLog::STATUS_FAILED,
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
            ]);
            
            return back()->with('error', "Failed to send to {$member->name}: " . $e->getMessage());
        }
    }

    /**
     * Retry failed email
     */
    public function retry(EmailLog $log)
    {
        if ($log->status !== EmailLog::STATUS_FAILED) {
            return back()->with('error', 'Only failed emails can be retried.');
        }

        $settings = MonthlySetting::getSettings();
        $dueAmount = $log->amount ?? $settings->monthly_contribution_amount;
        $dueMonth = $log->month_year ?? now()->format('F Y');

        try {
            if ($log->type === EmailLog::TYPE_PAYMENT_REMINDER && $log->user) {
                Mail::to($log->recipient_email)->send(new PaymentReminderMail($log->user, $dueMonth, $dueAmount));
            }
            
            $log->update([
                'status' => EmailLog::STATUS_SENT,
                'error_message' => null,
                'sent_at' => now(),
                'sent_by' => auth()->id(),
            ]);
            
            return back()->with('success', "Email resent successfully to {$log->recipient_email}");
        } catch (\Exception $e) {
            $log->update([
                'error_message' => $e->getMessage(),
            ]);
            
            return back()->with('error', "Failed to resend: " . $e->getMessage());
        }
    }
}
