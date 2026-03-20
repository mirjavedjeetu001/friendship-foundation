<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="py-2">
        <!-- Due Alert -->
        @if($isDue)
        <div class="mb-6 bg-gradient-to-r from-red-500 to-rose-500 rounded-2xl p-5 text-white shadow-lg shadow-red-500/20">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start space-x-4">
                    <div class="p-3 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Payment Due!</h3>
                        <p class="text-white/80 text-sm mt-1">Your contribution for {{ date('F', mktime(0, 0, 0, $currentMonth, 1)) }} {{ $currentYear }} is overdue.</p>
                    </div>
                </div>
                <a href="{{ route('contributions.create') }}" class="mt-4 sm:mt-0 inline-flex items-center justify-center px-5 py-2.5 bg-white text-red-600 rounded-xl font-semibold text-sm hover:bg-white/90 transition shadow-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Submit Payment Now
                </a>
            </div>
        </div>
        @endif

        <!-- My Balance Card (User's Personal Stats) -->
        <div class="mb-6 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-6 shadow-xl shadow-indigo-500/20 text-white">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-4 lg:mb-0">
                    <div class="flex items-center space-x-3 mb-2">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-white/30">
                        <div>
                            <p class="text-white/80 text-sm">Welcome back,</p>
                            <h2 class="text-xl font-bold">{{ auth()->user()->name }}</h2>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-3 text-center">
                        <p class="text-white/70 text-xs uppercase tracking-wider">My Contribution</p>
                        <p class="text-2xl font-bold">৳{{ number_format($userTotalContributions, 0) }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-3 text-center">
                        <p class="text-white/70 text-xs uppercase tracking-wider">Months Paid</p>
                        <p class="text-2xl font-bold">{{ $userTotalMonthsPaid }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-3 text-center">
                        <p class="text-white/70 text-xs uppercase tracking-wider">Last Payment</p>
                        <p class="text-lg font-bold">{{ $userLastContribution ? $userLastContribution->created_at?->format('M d') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
            <!-- Total Members -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-100 dark:border-slate-700 card-hover">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg shadow-violet-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white">{{ $totalMembers }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Total Members</p>
            </div>

            <!-- Current Balance -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-100 dark:border-slate-700 card-hover">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl lg:text-3xl font-bold text-emerald-600 dark:text-emerald-400">৳{{ number_format($currentBalance, 0) }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Current Balance</p>
            </div>

            <!-- Total Deposits -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-100 dark:border-slate-700 card-hover">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl lg:text-3xl font-bold text-blue-600 dark:text-blue-400">৳{{ number_format($totalContributions, 0) }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Total Deposits</p>
            </div>

            <!-- Total Withdrawals -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-100 dark:border-slate-700 card-hover">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-lg shadow-rose-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl lg:text-3xl font-bold text-rose-600 dark:text-rose-400">৳{{ number_format($totalWithdrawals, 0) }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Total Withdrawals</p>
            </div>
        </div>

        <!-- Middle Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Pending Approvals -->
            @can('approve contributions')
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Pending Approvals</h3>
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800/30">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                </svg>
                            </div>
                            <span class="text-slate-700 dark:text-slate-300 font-medium">Contributions</span>
                        </div>
                        <span class="bg-amber-500 text-white px-3 py-1 rounded-full text-sm font-bold">{{ $pendingContributions }}</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800/30">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                </svg>
                            </div>
                            <span class="text-slate-700 dark:text-slate-300 font-medium">Withdrawals</span>
                        </div>
                        <span class="bg-amber-500 text-white px-3 py-1 rounded-full text-sm font-bold">{{ $pendingWithdrawals }}</span>
                    </div>
                </div>
                <a href="{{ route('contributions.pending') }}" class="flex items-center justify-center mt-5 py-3 px-4 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition font-medium text-sm">
                    <span>View All Pending</span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @endcan

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Quick Actions</h3>
                    <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('contributions.create') }}" class="flex items-center p-4 rounded-xl bg-gradient-to-r from-teal-500 to-cyan-500 text-white hover:from-teal-600 hover:to-cyan-600 transition shadow-lg shadow-teal-500/30">
                        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <span class="font-semibold">Submit Contribution</span>
                    </a>
                    @can('create withdrawals')
                    <a href="{{ route('withdrawals.create') }}" class="flex items-center p-4 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                        <div class="w-10 h-10 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="font-medium">Request Withdrawal</span>
                    </a>
                    @endcan
                    @can('view reports')
                    <a href="{{ route('reports.monthly') }}" class="flex items-center p-4 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                        <div class="w-10 h-10 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="font-medium">View Reports</span>
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Unpaid Members This Month -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                @if($isProgramStarted)
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Unpaid - {{ date('M Y') }}</h3>
                    <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
                @if($unpaidMembers->count() > 0)
                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                    @foreach($unpaidMembers as $member)
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="w-8 h-8 rounded-full object-cover">
                            <span class="text-slate-700 dark:text-slate-300 text-sm font-medium">{{ $member->name }}</span>
                        </div>
                        <span class="text-red-500 text-xs font-bold bg-red-100 dark:bg-red-900/30 px-2 py-1 rounded-lg">৳{{ number_format($settings->monthly_contribution_amount, 0) }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-emerald-600 dark:text-emerald-400 font-semibold">All members have paid!</p>
                </div>
                @endif
                @else
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Program Status</h3>
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-blue-600 dark:text-blue-400 font-semibold">Starting {{ \Carbon\Carbon::create($startYear, $startMonth, 1)->format('F Y') }}</p>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Contributions will begin soon</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Contributions & Withdrawals Chart -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Financial Overview</h3>
                    <div class="flex items-center space-x-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></div>
                            <span class="text-slate-500 dark:text-slate-400">Contributions</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-rose-500 mr-2"></div>
                            <span class="text-slate-500 dark:text-slate-400">Withdrawals</span>
                        </div>
                    </div>
                </div>
                <div class="h-72">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>

            <!-- Payment Status Pie Chart -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ date('M Y') }} Status</h3>
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                    </div>
                </div>
                <div class="h-56 flex items-center justify-center">
                    <canvas id="paymentStatusChart"></canvas>
                </div>
                <div class="flex justify-center space-x-6 mt-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $paidThisMonth }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">Paid</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-rose-600 dark:text-rose-400">{{ $unpaidThisMonth }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">Unpaid</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Recent Contributions -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Recent Contributions</h3>
                    <a href="{{ route('contributions.index') }}" class="text-sm text-teal-600 dark:text-teal-400 hover:underline font-medium">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentContributions as $contribution)
                    <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $contribution->user->avatar_url }}" alt="{{ $contribution->user->name }}" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <p class="font-semibold text-slate-800 dark:text-white text-sm">{{ $contribution->user->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $contribution->month_year }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-slate-800 dark:text-white">৳{{ number_format($contribution->amount, 0) }}</p>
                            <span class="inline-flex px-2 py-0.5 text-xs font-bold rounded-full
                                @if($contribution->status === 'approved') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400
                                @elseif($contribution->status === 'pending') bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400
                                @else bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400 @endif">
                                {{ ucfirst($contribution->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500 dark:text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm">No contributions yet</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Withdrawals -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-700">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Recent Withdrawals</h3>
                    <a href="{{ route('withdrawals.index') }}" class="text-sm text-teal-600 dark:text-teal-400 hover:underline font-medium">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentWithdrawals as $withdrawal)
                    <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800 dark:text-white text-sm">{{ Str::limit($withdrawal->purpose, 20) }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $withdrawal->requester->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-rose-600 dark:text-rose-400">-৳{{ number_format($withdrawal->amount, 0) }}</p>
                            <span class="inline-flex px-2 py-0.5 text-xs font-bold rounded-full
                                @if($withdrawal->status === 'approved') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400
                                @elseif($withdrawal->status === 'pending') bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400
                                @else bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400 @endif">
                                {{ ucfirst($withdrawal->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500 dark:text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <p class="text-sm">No withdrawals yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Bank Info Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden shadow-xl border border-slate-200 dark:border-slate-700">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 lg:p-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-white text-xl font-bold mb-1">Bank Account Information</h3>
                        <p class="text-indigo-200 text-sm">Send your contributions to this account</p>
                    </div>
                    <div class="mt-4 lg:mt-0 flex items-center gap-4">
                        <div class="bg-white/20 backdrop-blur rounded-xl px-5 py-3 text-center">
                            <p class="text-indigo-200 text-xs uppercase tracking-wider">Monthly Amount</p>
                            <p class="text-white text-2xl font-bold">৳{{ number_format($settings->monthly_contribution_amount, 0) }}</p>
                        </div>
                        <div class="bg-white/20 backdrop-blur rounded-xl px-5 py-3 text-center">
                            <p class="text-indigo-200 text-xs uppercase tracking-wider">Due Date</p>
                            <p class="text-white text-2xl font-bold">{{ $settings->due_day }}th</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 lg:p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4">
                        <p class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider mb-1">Bank Name</p>
                        <p class="text-slate-900 dark:text-white font-semibold">{{ $settings->bank_name ?? 'Not Set' }}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4">
                        <p class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider mb-1">Account Number</p>
                        <p class="text-slate-900 dark:text-white font-semibold font-mono">{{ $settings->account_number ?? 'Not Set' }}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4">
                        <p class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider mb-1">Account Holder</p>
                        <p class="text-slate-900 dark:text-white font-semibold">{{ $settings->account_holder ?? 'Not Set' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if dark mode is active
            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? '#94a3b8' : '#64748b';
            const gridColor = isDarkMode ? 'rgba(148, 163, 184, 0.1)' : 'rgba(100, 116, 139, 0.1)';

            // Financial Overview Chart
            const financialCtx = document.getElementById('financialChart').getContext('2d');
            new Chart(financialCtx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Contributions',
                        data: @json($chartContributions),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: '#10b981'
                    }, {
                        label: 'Withdrawals',
                        data: @json($chartWithdrawals),
                        borderColor: '#f43f5e',
                        backgroundColor: 'rgba(244, 63, 94, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: '#f43f5e'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: isDarkMode ? '#1e293b' : '#fff',
                            titleColor: isDarkMode ? '#f1f5f9' : '#1e293b',
                            bodyColor: isDarkMode ? '#94a3b8' : '#64748b',
                            borderColor: isDarkMode ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ৳' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: textColor,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                color: textColor,
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    return '৳' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Payment Status Pie Chart
            const statusCtx = document.getElementById('paymentStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Paid', 'Unpaid'],
                    datasets: [{
                        data: [{{ $paidThisMonth }}, {{ $unpaidThisMonth }}],
                        backgroundColor: ['#10b981', '#f43f5e'],
                        borderColor: isDarkMode ? '#1e293b' : '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: isDarkMode ? '#1e293b' : '#fff',
                            titleColor: isDarkMode ? '#f1f5f9' : '#1e293b',
                            bodyColor: isDarkMode ? '#94a3b8' : '#64748b',
                            borderColor: isDarkMode ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((context.parsed / total) * 100);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
