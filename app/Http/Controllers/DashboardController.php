<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
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

        // Stats for dashboard
        $totalMembers = User::whereHas('roles', function ($q) {
            $q->where('name', 'member');
        })->count();

        $totalContributions = Contribution::approved()->sum('amount');
        $totalWithdrawals = Withdrawal::approved()->sum('amount');
        $currentBalance = $totalContributions - $totalWithdrawals;

        // Current month stats
        $currentMonthContributions = Contribution::forMonth($currentMonth, $currentYear)
            ->approved()
            ->sum('amount');

        $pendingContributions = Contribution::pending()->count();
        $pendingWithdrawals = Withdrawal::pending()->count();

        // Members who haven't paid this month
        $paidUserIds = Contribution::forMonth($currentMonth, $currentYear)
            ->approved()
            ->pluck('user_id');

        $unpaidMembers = User::whereHas('roles', function ($q) {
            $q->where('name', 'member');
        })->whereNotIn('id', $paidUserIds)->get();

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

        if ($dayOfMonth > $settings->due_day) {
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
            ->latest('contribution_date')
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
            'unpaidThisMonth'
        ));
    }
}
