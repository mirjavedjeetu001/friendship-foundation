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

        // Get all members (everyone except super-admin)
        $users = User::where('email', '!=', 'alliedgroup@gmail.com')
            ->where('status', 'approved')
            ->get();

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

        // Get all members (everyone except super-admin)
        $users = User::where('email', '!=', 'alliedgroup@gmail.com')
            ->where('status', 'approved')
            ->get();

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

        // Get all members (everyone except super-admin)
        $users = User::where('email', '!=', 'alliedgroup@gmail.com')
            ->where('status', 'approved')
            ->get();

        // Get user IDs who have paid for this month
        $paidUserIds = Contribution::forMonth($month, $year)
            ->whereIn('status', ['approved', 'pending'])
            ->pluck('user_id');

        // Get members who haven't paid
        $dueMembers = User::where('email', '!=', 'alliedgroup@gmail.com')
            ->where('status', 'approved')
            ->whereNotIn('id', $paidUserIds)
            ->get();

        // Calculate total due
        $totalDue = $dueMembers->count() * $settings->monthly_contribution_amount;

        // Get current month contributions for paid/unpaid sections
        $currentMonthContributions = Contribution::with('user')
            ->forMonth($month, $year)
            ->get();

        return view('reports.due', compact('users', 'dueMembers', 'totalDue', 'month', 'year', 'settings', 'currentMonthContributions'));
    }

    /**
     * Expense report - detailed expense listing with download
     */
    public function expense(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year', Carbon::now()->year);
        $status = $request->input('status', 'approved');
        $paymentType = $request->input('payment_type');
        $settlementStatus = $request->input('settlement_status');
        
        $settings = MonthlySetting::getSettings();

        $query = \App\Models\Expense::with(['creator', 'approver', 'settler'])
            ->where('status', '!=', 'pending'); // Show approved and rejected

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($month) {
            $query->whereMonth('expense_date', $month);
        }

        $query->whereYear('expense_date', $year);

        if ($paymentType) {
            $query->where('payment_type', $paymentType);
        }

        if ($settlementStatus) {
            if ($settlementStatus === 'settled') {
                $query->where('settlement_status', 'settled');
            } elseif ($settlementStatus === 'pending') {
                $query->where('settlement_status', 'pending')->where('payment_type', 'cash');
            }
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        // Summary statistics
        $totalExpenses = $expenses->where('status', 'approved')->sum('amount');
        $cashExpenses = $expenses->where('status', 'approved')->where('payment_type', 'cash')->sum('amount');
        $bankExpenses = $expenses->where('status', 'approved')->where('payment_type', 'bank')->sum('amount');
        $settledAmount = $expenses->where('status', 'approved')->where('settlement_status', 'settled')->sum('amount');
        $pendingSettlement = $expenses->where('status', 'approved')->where('settlement_status', 'pending')->where('payment_type', 'cash')->sum('amount');

        // Monthly breakdown
        $monthlyTotals = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyTotals[$m] = \App\Models\Expense::whereYear('expense_date', $year)
                ->whereMonth('expense_date', $m)
                ->approved()
                ->sum('amount');
        }

        return view('reports.expense', compact(
            'expenses',
            'totalExpenses',
            'cashExpenses',
            'bankExpenses',
            'settledAmount',
            'pendingSettlement',
            'monthlyTotals',
            'month',
            'year',
            'status',
            'paymentType',
            'settlementStatus',
            'settings'
        ));
    }
}
