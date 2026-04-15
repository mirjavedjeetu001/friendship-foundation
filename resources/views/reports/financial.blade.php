<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Financial Report') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Year Filter -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.financial') }}" class="flex flex-wrap items-end gap-4">
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                            <select name="year" id="year" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                @for($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            View Report
                        </button>
                    </form>
                </div>
            </div>

            <!-- Financial Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-sm p-5 text-white">
                    <p class="text-sm opacity-80">Total Contributions</p>
                    <p class="text-3xl font-bold mt-1">৳{{ number_format($contributions->sum('amount'), 2) }}</p>
                    <p class="text-xs opacity-70 mt-2">{{ $contributions->count() }} transactions</p>
                </div>
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-sm p-5 text-white">
                    <p class="text-sm opacity-80">Total Withdrawals</p>
                    <p class="text-3xl font-bold mt-1">৳{{ number_format($withdrawals->sum('amount'), 2) }}</p>
                    <p class="text-xs opacity-70 mt-2">{{ $withdrawals->count() }} transactions</p>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-sm p-5 text-white">
                    <p class="text-sm opacity-80">Net Balance ({{ $year }})</p>
                    @php
                        $netBalance = $contributions->sum('amount') - $withdrawals->sum('amount');
                    @endphp
                    <p class="text-3xl font-bold mt-1">৳{{ number_format($netBalance, 2) }}</p>
                    <p class="text-xs opacity-70 mt-2">Contributions - Withdrawals</p>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-sm p-5 text-white">
                    <p class="text-sm opacity-80">Current Bank Balance</p>
                    <p class="text-3xl font-bold mt-1">৳{{ number_format($settings->bank_balance, 2) }}</p>
                    <p class="text-xs opacity-70 mt-2">As of today</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Monthly Breakdown -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Monthly Breakdown</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Month</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">In</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Out</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Net</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @php
                                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                    @endphp
                                    @for($m = 1; $m <= 12; $m++)
                                    @php
                                        $monthIn = $contributions->where('month', $m)->sum('amount');
                                        $monthOut = $withdrawals->filter(function($w) use ($m) {
                                            return $w->approved_at && $w->approved_at->month == $m;
                                        })->sum('amount');
                                        $monthNet = $monthIn - $monthOut;
                                    @endphp
                                    <tr class="{{ $m > date('n') && $year == date('Y') ? 'opacity-50' : '' }}">
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $months[$m - 1] }}</td>
                                        <td class="px-4 py-2 text-sm text-right text-green-600">
                                            {{ $monthIn > 0 ? '৳' . number_format($monthIn, 0) : '-' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-right text-red-600">
                                            {{ $monthOut > 0 ? '৳' . number_format($monthOut, 0) : '-' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-right font-medium {{ $monthNet >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $monthNet != 0 ? '৳' . number_format($monthNet, 0) : '-' }}
                                        </td>
                                    </tr>
                                    @endfor
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">Total</td>
                                        <td class="px-4 py-3 text-sm text-right font-bold text-green-600">৳{{ number_format($contributions->sum('amount'), 0) }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-bold text-red-600">৳{{ number_format($withdrawals->sum('amount'), 0) }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-bold {{ $netBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">৳{{ number_format($netBalance, 0) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Top Contributors -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top Contributors</h3>
                        @php
                            $topContributors = $contributions->groupBy('user_id')->map(function($items) {
                                return [
                                    'user' => $items->first()->user,
                                    'total' => $items->sum('amount'),
                                    'count' => $items->count()
                                ];
                            })->sortByDesc('total')->take(10);
                        @endphp
                        <div class="space-y-3">
                            @forelse($topContributors as $contributor)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-medium mr-3">
                                        {{ strtoupper(substr($contributor['user']->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $contributor['user']->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $contributor['count'] }} payments</p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-green-600">৳{{ number_format($contributor['total'], 0) }}</span>
                            </div>
                            @empty
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">No contributions yet</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved Contributions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Approved Contributions in {{ $year }}</h3>
                    @if($contributions->count() > 0)
                    <!-- Desktop Table -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Member</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Month</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Approved By</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($contributions as $contribution)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $contribution->approved_at ? $contribution->approved_at->format('M d, Y') : $contribution->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $contribution->user->name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $contribution->month_year }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $contribution->approver->name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-medium text-green-600">৳{{ number_format($contribution->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card Layout -->
                    <div class="sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($contributions as $contribution)
                        <div class="p-3">
                            <div class="flex items-start justify-between">
                                <div class="min-w-0 mr-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $contribution->user->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $contribution->month_year }} · {{ $contribution->approved_at ? $contribution->approved_at->format('M d') : $contribution->created_at->format('M d') }}</p>
                                </div>
                                <span class="text-sm font-medium text-green-600 whitespace-nowrap">৳{{ number_format($contribution->amount, 0) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-8">No approved contributions for {{ $year }}</p>
                    @endif
                </div>
            </div>

            <!-- Recent Withdrawals -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Withdrawals in {{ $year }}</h3>
                    @if($withdrawals->count() > 0)
                    <!-- Desktop Table -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Purpose</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Requested By</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Approved By</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($withdrawals as $withdrawal)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $withdrawal->approved_at ? $withdrawal->approved_at->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $withdrawal->purpose }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $withdrawal->requester->name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $withdrawal->approver->name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-medium text-red-600">৳{{ number_format($withdrawal->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card Layout -->
                    <div class="sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($withdrawals as $withdrawal)
                        <div class="p-3">
                            <div class="flex items-start justify-between">
                                <div class="min-w-0 mr-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $withdrawal->purpose }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $withdrawal->requester->name ?? 'Unknown' }} · {{ $withdrawal->approved_at ? $withdrawal->approved_at->format('M d') : '-' }}</p>
                                </div>
                                <span class="text-sm font-medium text-red-600 whitespace-nowrap">৳{{ number_format($withdrawal->amount, 0) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-8">No withdrawals recorded for {{ $year }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
