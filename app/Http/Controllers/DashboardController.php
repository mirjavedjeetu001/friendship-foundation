<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Expense;
use App\Models\MonthlySetting;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $settings = MonthlySetting::getSettings();
        
        // Get start month/year settings
        $startMonth = $settings->start_month ?? 4;
        $startYear = $settings->start_year ?? 2025;
        $startDate = Carbon::create($startYear, $startMonth, 1);
        $now = Carbon::now();
        
        // Check if we're past the program start date
        $isProgramStarted = $now->greaterThanOrEqualTo($startDate);

        // Stats for dashboard - All users are members EXCEPT super-admin
        $superAdminEmail = 'alliedgroup@gmail.com';
        $totalMembers = User::where('email', '!=', $superAdminEmail)
            ->where('status', 'approved')
            ->count();

        // Calculate contributions/withdrawals only from start date onwards
        $totalContributions = Contribution::approved()
            ->where(function ($q) use ($startMonth, $startYear) {
                $q->where('year', '>', $startYear)
                  ->orWhere(function ($q2) use ($startMonth, $startYear) {
                      $q2->where('year', '=', $startYear)
                         ->where('month', '>=', $startMonth);
                  });
            })
            ->sum('amount');
            
        $totalWithdrawals = Withdrawal::approved()
            ->where('withdrawal_date', '>=', $startDate)
            ->sum('amount');

        // Total approved expenses settled from savings (deducted from bank)
        $totalExpensesFromSavings = Expense::approved()
            ->where('fund_source', 'monthly_savings')
            ->settledFromBank()
            ->sum('amount');

        // Total approved expenses from manual adjustment
        $totalExpensesFromManual = Expense::approved()
            ->where('fund_source', 'manual')
            ->sum('amount');

        $currentBalance = $totalContributions - $totalWithdrawals - $totalExpensesFromSavings - $totalExpensesFromManual;

        // Current month stats (only if program has started)
        $currentMonthContributions = 0;
        $pendingContributions = 0;
        $unpaidMembers = collect();
        $paidUserIds = collect();
        
        if ($isProgramStarted) {
            $currentMonthContributions = Contribution::forMonth($currentMonth, $currentYear)
                ->approved()
                ->sum('amount');

            $pendingContributions = Contribution::pending()->count();

            // Members who haven't paid this month
            $paidUserIds = Contribution::forMonth($currentMonth, $currentYear)
                ->approved()
                ->pluck('user_id');

            $unpaidMembers = User::where('email', '!=', $superAdminEmail)
              ->where('status', 'approved')
              ->whereNotIn('id', $paidUserIds)
              ->get();
        }
        
        $pendingWithdrawals = Withdrawal::pending()->count();

        // Recent activities
        $recentContributions = Contribution::with(['user', 'submitter'])
            ->latest()
            ->take(5)
            ->get();

        $recentWithdrawals = Withdrawal::with('requester')
            ->latest()
            ->take(5)
            ->get();

        // Check if current user is due
        $user = auth()->user();
        $isDue = false;
        $dayOfMonth = Carbon::now()->day;

        // Only check dues if program has started
        if ($isProgramStarted && $dayOfMonth > $settings->due_day) {
            $hasPaid = Contribution::where('user_id', $user->id)
                ->forMonth($currentMonth, $currentYear)
                ->whereIn('status', ['approved', 'pending'])
                ->exists();

            $isDue = !$hasPaid;
        }

        // ====== NEW: User's contribution statistics ======
        $userTotalContributions = Contribution::where('user_id', $user->id)
            ->approved()
            ->sum('amount');

        $userTotalMonthsPaid = Contribution::where('user_id', $user->id)
            ->approved()
            ->count();

        $userLastContribution = Contribution::where('user_id', $user->id)
            ->approved()
            ->latest('created_at')
            ->first();

        // Calculate user's share
        $userShare = $totalMembers > 0 ? $currentBalance / $totalMembers : 0;

        // ====== NEW: Monthly chart data (last 12 months) ======
        $chartLabels = [];
        $chartContributions = [];
        $chartWithdrawals = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartLabels[] = $date->format('M Y');

            $monthlyContribution = Contribution::forMonth($date->month, $date->year)
                ->approved()
                ->sum('amount');
            $chartContributions[] = $monthlyContribution;

            $monthlyWithdrawal = Withdrawal::whereYear('withdrawal_date', $date->year)
                ->whereMonth('withdrawal_date', $date->month)
                ->approved()
                ->sum('amount');
            $chartWithdrawals[] = $monthlyWithdrawal;
        }

        // ====== NEW: Member contribution stats for pie chart ======
        $paidThisMonth = $paidUserIds->count();
        $unpaidThisMonth = $unpaidMembers->count();

        // ====== Recent Approved Expenses ======
        $recentExpenses = Expense::with(['creator', 'approver'])
            ->approved()
            ->latest('approved_at')
            ->take(5)
            ->get();

        // Expense summary
        $totalApprovedExpenses = Expense::approved()->sum('amount');
        $expensesFromSavings = Expense::approved()
            ->where('fund_source', 'monthly_savings')
            ->sum('amount');
        $expensesFromManual = Expense::approved()
            ->where('fund_source', 'manual')
            ->sum('amount');

        return view('dashboard', compact(
            'totalMembers',
            'totalContributions',
            'totalWithdrawals',
            'currentBalance',
            'currentMonthContributions',
            'pendingContributions',
            'pendingWithdrawals',
            'unpaidMembers',
            'recentContributions',
            'recentWithdrawals',
            'settings',
            'isDue',
            'currentMonth',
            'currentYear',
            'userTotalContributions',
            'userTotalMonthsPaid',
            'userLastContribution',
            'userShare',
            'chartLabels',
            'chartContributions',
            'chartWithdrawals',
            'paidThisMonth',
            'unpaidThisMonth',
            'isProgramStarted',
            'startMonth',
            'startYear',
            'recentExpenses',
            'totalApprovedExpenses',
            'expensesFromSavings',
            'expensesFromManual'
        ));
    }
}
