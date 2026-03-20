<?php

namespace App\Console\Commands;

use App\Mail\PasswordResetOtpMail;
use App\Mail\PaymentApprovedMail;
use App\Mail\PaymentReminderMail;
use App\Mail\RegistrationApprovedMail;
use App\Mail\RegistrationPendingMail;
use App\Models\Contribution;
use App\Models\MonthlySetting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmails extends Command
{
    protected $signature = 'email:test {email : The email address to send test emails to}';

    protected $description = 'Send all types of test emails to a specified email address';

    public function handle()
    {
        $email = $this->argument('email');
        $settings = MonthlySetting::getSettings();
        
        // Create a dummy user for testing
        $testUser = new User([
            'name' => 'Test User',
            'email' => $email,
            'phone' => '01811480222',
            'created_at' => now(),
        ]);
        $testUser->id = 1;

        $this->info("Sending test emails to: {$email}");
        $this->newLine();

        // 1. Registration Pending Email
        try {
            $this->info('1. Sending Registration Pending email...');
            Mail::to($email)->send(new RegistrationPendingMail($testUser));
            $this->info('   ✓ Registration Pending email sent!');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed: ' . $e->getMessage());
        }

        // 2. Registration Approved Email
        try {
            $this->info('2. Sending Registration Approved email...');
            Mail::to($email)->send(new RegistrationApprovedMail($testUser));
            $this->info('   ✓ Registration Approved email sent!');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed: ' . $e->getMessage());
        }

        // 3. Password Reset OTP Email
        try {
            $this->info('3. Sending Password Reset OTP email...');
            Mail::to($email)->send(new PasswordResetOtpMail($email, '123456', 15));
            $this->info('   ✓ Password Reset OTP email sent!');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed: ' . $e->getMessage());
        }

        // 4. Payment Reminder Email
        try {
            $this->info('4. Sending Payment Reminder email...');
            $dueMonth = now()->format('F Y');
            $dueAmount = $settings->monthly_contribution_amount ?? 1000;
            Mail::to($email)->send(new PaymentReminderMail($testUser, $dueMonth, $dueAmount));
            $this->info('   ✓ Payment Reminder email sent!');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed: ' . $e->getMessage());
        }

        // 5. Payment Approved Email
        try {
            $this->info('5. Sending Payment Approved email...');
            $contribution = new Contribution([
                'month' => now()->month,
                'year' => now()->year,
                'amount' => $settings->monthly_contribution_amount ?? 1000,
                'status' => 'approved',
            ]);
            Mail::to($email)->send(new PaymentApprovedMail($testUser, $contribution));
            $this->info('   ✓ Payment Approved email sent!');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('All test emails have been sent!');
        $this->info('Please check inbox and spam folder at: ' . $email);

        return 0;
    }
}
