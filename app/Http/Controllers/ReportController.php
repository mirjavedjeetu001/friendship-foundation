<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\MonthlySetting;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Monthly report
     */
    public function monthly(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $settings = MonthlySetting::getSettings();

        // Get all members
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'member');
        })->get();

        // Get contributions for the month
        $contributions = Contribution::with('user')
            ->forMonth($month, $year)
            ->get();

        return view('reports.monthly', compact(
            'users',
            'contributions',
            'month',
            'year',
            'settings'
        ));
    }

    /**
     * Yearly summary report
     */
    public function yearly(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        $settings = MonthlySetting::getSettings();

        // Get all members
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'member');
        })->get();

        // Get all contributions for the year
        $contributions = Contribution::with('user')
            ->where('year', $year)
            ->where('status', 'approved')
            ->get();

        // Monthly totals
        $monthlyTotals = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyTotals[$month] = Contribution::forMonth($month, $year)
                ->approved()
                ->sum('amount');
        }

        $yearlyTotal = array_sum($monthlyTotals);

        return view('reports.yearly', compact('users', 'contributions', 'year', 'monthlyTotals', 'yearlyTotal', 'settings'));
    }

    /**
     * Financial summary report
     */
    public function financial(Request $request)
    {
        $settings = MonthlySetting::getSettings();
        $year = $request->input('year', Carbon::now()->year);

        // Get contributions for the year
        $contributions = Contribution::with(['user', 'approver'])
            ->where('year', $year)
            ->where('status', 'approved')
            ->get();

        // Get withdrawals for the year
        $withdrawals = Withdrawal::with(['requester', 'approver'])
            ->whereYear('withdrawal_date', $year)
            ->where('status', 'approved')
            ->get();

        // Total contributions
        $totalContributions = Contribution::approved()->sum('amount');

        // Total withdrawals
        $totalWithdrawals = Withdrawal::approved()->sum('amount');

        // Current balance
        $currentBalance = $totalContributions - $totalWithdrawals;

        // Monthly breakdown for current year
        $monthlyContributions = [];
        $monthlyWithdrawals = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthlyContributions[$month] = Contribution::forMonth($month, $year)
                ->approved()
                ->sum('amount');

            $monthlyWithdrawals[$month] = Withdrawal::whereYear('withdrawal_date', $year)
                ->whereMonth('withdrawal_date', $month)
                ->approved()
                ->sum('amount');
        }

        // Recent transactions
        $recentContributions = Contribution::with('user')
            ->approved()
            ->latest('approved_at')
            ->take(10)
            ->get();

        $recentWithdrawals = Withdrawal::with('requester')
            ->approved()
            ->latest('approved_at')
            ->take(10)
            ->get();

        return view('reports.financial', compact(
            'contributions',
            'withdrawals',
            'totalContributions',
            'totalWithdrawals',
            'currentBalance',
            'monthlyContributions',
            'monthlyWithdrawals',
            'recentContributions',
            'recentWithdrawals',
            'year',
            'settings'
        ));
    }

    /**
     * Due members report
     */
    public function due(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $settings = MonthlySetting::getSettings();

        // Get all members
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'member');
        })->get();

        // Get user IDs who have paid for this month
        $paidUserIds = Contribution::forMonth($month, $year)
            ->whereIn('status', ['approved', 'pending'])
            ->pluck('user_id');

        // Get members who haven't paid
        $dueMembers = User::whereHas('roles', function ($q) {
            $q->where('name', 'member');
        })->whereNotIn('id', $paidUserIds)->get();

        // Calculate total due
        $totalDue = $dueMembers->count() * $settings->monthly_contribution_amount;

        // Get current month contributions for paid/unpaid sections
        $currentMonthContributions = Contribution::with('user')
            ->forMonth($month, $year)
            ->get();

        return view('reports.due', compact('users', 'dueMembers', 'totalDue', 'month', 'year', 'settings', 'currentMonthContributions'));
    }
}
