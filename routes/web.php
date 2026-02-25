<?php

use App\Http\Controllers\ContributionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Contribution routes
    Route::get('/contributions/pending', [ContributionController::class, 'pending'])
        ->name('contributions.pending')
        ->middleware('permission:approve contributions');
    Route::post('/contributions/{contribution}/approve', [ContributionController::class, 'approve'])
        ->name('contributions.approve')
        ->middleware('permission:approve contributions');
    Route::post('/contributions/{contribution}/reject', [ContributionController::class, 'reject'])
        ->name('contributions.reject')
        ->middleware('permission:reject contributions');
    Route::resource('contributions', ContributionController::class);

    // Withdrawal routes
    Route::get('/withdrawals/pending', [WithdrawalController::class, 'pending'])
        ->name('withdrawals.pending')
        ->middleware('permission:approve withdrawals');
    Route::post('/withdrawals/{withdrawal}/approve', [WithdrawalController::class, 'approve'])
        ->name('withdrawals.approve')
        ->middleware('permission:approve withdrawals');
    Route::post('/withdrawals/{withdrawal}/reject', [WithdrawalController::class, 'reject'])
        ->name('withdrawals.reject')
        ->middleware('permission:reject withdrawals');
    Route::resource('withdrawals', WithdrawalController::class);

    // User management routes
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggle-status')
        ->middleware('permission:edit users');
    Route::resource('users', UserController::class)
        ->middleware('permission:view users');

    // Settings routes
    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings.index')
        ->middleware('permission:manage settings');
    Route::put('/settings', [SettingsController::class, 'update'])
        ->name('settings.update')
        ->middleware('permission:manage settings');
    Route::put('/settings/balance', [SettingsController::class, 'updateBalance'])
        ->name('settings.balance')
        ->middleware('permission:manage settings');

    // Report routes
    Route::prefix('reports')->name('reports.')->middleware('permission:view reports')->group(function () {
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/yearly', [ReportController::class, 'yearly'])->name('yearly');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/due', [ReportController::class, 'due'])->name('due');
    });
});

require __DIR__.'/auth.php';
