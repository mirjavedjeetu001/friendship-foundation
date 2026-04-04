<?php

use App\Http\Controllers\ContributionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OrganizationDocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VotingController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', \App\Http\Middleware\CheckApproved::class])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Member profile edit routes
    Route::get('/profile/member', [ProfileController::class, 'memberEdit'])->name('profile.member.edit');
    Route::put('/profile/member', [ProfileController::class, 'memberUpdate'])->name('profile.member.update');

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

    // Expense routes
    Route::get('/expenses/pending', [ExpenseController::class, 'pending'])
        ->name('expenses.pending')
        ->middleware('permission:approve contributions');
    Route::post('/expenses/{expense}/approve', [ExpenseController::class, 'approve'])
        ->name('expenses.approve')
        ->middleware('permission:approve contributions');
    Route::post('/expenses/{expense}/reject', [ExpenseController::class, 'reject'])
        ->name('expenses.reject')
        ->middleware('permission:approve contributions');
    Route::resource('expenses', ExpenseController::class)->except(['edit', 'update']);

    // User management routes
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggle-status')
        ->middleware('permission:edit users');
    Route::resource('users', UserController::class)
        ->middleware('permission:view users');

    // Member management routes
    Route::get('/members', [MemberController::class, 'index'])
        ->name('members.index')
        ->middleware('permission:view users');
    Route::get('/members/pending', [MemberController::class, 'pending'])
        ->name('members.pending')
        ->middleware('permission:view users');
    Route::get('/members/download-all', [MemberController::class, 'downloadAll'])
        ->name('members.download-all')
        ->middleware('permission:view users');
    Route::get('/members/{member}', [MemberController::class, 'show'])
        ->name('members.show')
        ->middleware('permission:view users');
    Route::post('/members/{member}/approve', [MemberController::class, 'approve'])
        ->name('members.approve')
        ->middleware('permission:edit users');
    Route::post('/members/{member}/reject', [MemberController::class, 'reject'])
        ->name('members.reject')
        ->middleware('permission:edit users');
    Route::patch('/members/{member}/role', [MemberController::class, 'updateRole'])
        ->name('members.update-role')
        ->middleware('role:super-admin');
    Route::get('/members/{member}/download', [MemberController::class, 'downloadDocuments'])
        ->name('members.download')
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

    // ========== ELECTION & VOTING ROUTES ==========
    
    // Member voting routes (all authenticated members can access)
    Route::prefix('elections')->name('elections.')->group(function () {
        Route::get('/', [VotingController::class, 'index'])->name('index');
        Route::get('/history', [VotingController::class, 'history'])->name('history');
        Route::get('/committee', [VotingController::class, 'committee'])->name('committee');
        Route::get('/{election}/vote', [VotingController::class, 'show'])->name('vote');
        Route::post('/{election}/vote', [VotingController::class, 'vote']);
        Route::get('/{election}/results', [VotingController::class, 'results'])->name('results');
        Route::get('/{election}/live', [VotingController::class, 'liveResults'])->name('live');
    });

    // Admin election management routes
    Route::prefix('admin/elections')->name('admin.elections.')->middleware('permission:manage settings')->group(function () {
        Route::get('/', [ElectionController::class, 'index'])->name('index');
        Route::get('/create', [ElectionController::class, 'create'])->name('create');
        Route::post('/', [ElectionController::class, 'store'])->name('store');
        Route::get('/history', [ElectionController::class, 'history'])->name('history');
        Route::get('/{election}', [ElectionController::class, 'show'])->name('show');
        Route::get('/{election}/edit', [ElectionController::class, 'edit'])->name('edit');
        Route::put('/{election}', [ElectionController::class, 'update'])->name('update');
        Route::delete('/{election}', [ElectionController::class, 'destroy'])->name('destroy');
        Route::get('/{election}/results', [ElectionController::class, 'results'])->name('results');
        Route::post('/{election}/start', [ElectionController::class, 'start'])->name('start');
        Route::post('/{election}/stop', [ElectionController::class, 'stop'])->name('stop');
        Route::post('/{election}/resume', [ElectionController::class, 'resume'])->name('resume');
        Route::post('/{election}/end', [ElectionController::class, 'end'])->name('end');
        Route::post('/{election}/cancel', [ElectionController::class, 'cancel'])->name('cancel');
        Route::post('/{election}/publish', [ElectionController::class, 'publish'])->name('publish');
        Route::post('/{election}/unpublish', [ElectionController::class, 'unpublish'])->name('unpublish');
        Route::post('/{election}/toggle-winner/{candidate}', [ElectionController::class, 'toggleWinner'])->name('toggle-winner');
        Route::post('/{election}/candidates', [ElectionController::class, 'addCandidate'])->name('add-candidate');
        Route::delete('/{election}/candidates/{candidate}', [ElectionController::class, 'removeCandidate'])->name('remove-candidate');
    });

    // ========== DOCUMENT ROUTES ==========
    
    // Member document viewing routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [OrganizationDocumentController::class, 'index'])->name('index');
        Route::get('/type/{type}', [OrganizationDocumentController::class, 'byType'])->name('type');
        Route::get('/{document}', [OrganizationDocumentController::class, 'show'])->name('show');
        Route::get('/{document}/download', [OrganizationDocumentController::class, 'download'])->name('download');
    });

    // Admin document management routes
    Route::prefix('admin/documents')->name('admin.documents.')->middleware('permission:manage settings')->group(function () {
        Route::get('/', [OrganizationDocumentController::class, 'adminIndex'])->name('index');
        Route::get('/create', [OrganizationDocumentController::class, 'create'])->name('create');
        Route::post('/', [OrganizationDocumentController::class, 'store'])->name('store');
        Route::get('/{document}/edit', [OrganizationDocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [OrganizationDocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [OrganizationDocumentController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
