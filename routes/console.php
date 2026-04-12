<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule payment reminders to run daily at 9 AM
// The command itself checks if it's 7 days before due date
Schedule::command('reminders:send-payment --days=7')
    ->dailyAt('09:00')
    ->timezone('Asia/Dhaka')
    ->appendOutputTo(storage_path('logs/reminders.log'));
