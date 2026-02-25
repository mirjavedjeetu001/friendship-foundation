<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Yearly Report') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Year Filter -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.yearly') }}" class="flex flex-wrap items-end gap-4">
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

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Members</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $users->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Contributions</p>
                    <p class="text-2xl font-bold text-green-600">৳{{ number_format($contributions->sum('amount'), 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Expected (12 months)</p>
                    @php
                        $expectedTotal = $users->count() * $settings->monthly_contribution_amount * 12;
                    @endphp
                    <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">৳{{ number_format($expectedTotal, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Collection Rate</p>
                    @php
                        $collectionRate = $expectedTotal > 0 ? ($contributions->sum('amount') / $expectedTotal) * 100 : 0;
                    @endphp
                    <p class="text-2xl font-bold {{ $collectionRate >= 80 ? 'text-green-600' : ($collectionRate >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ number_format($collectionRate, 1) }}%
                    </p>
                </div>
            </div>

            <!-- Yearly Grid -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ $year }} - Member Contribution Matrix
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase sticky left-0 bg-gray-50 dark:bg-gray-700">Member</th>
                                    @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $monthName)
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ $monthName }}</th>
                                    @endforeach
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($users as $user)
                                @php
                                    $userTotal = 0;
                                @endphp
                                <tr>
                                    <td class="px-3 py-3 sticky left-0 bg-white dark:bg-gray-800">
                                        <div class="flex items-center">
                                            <div class="h-7 w-7 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-medium mr-2 text-xs">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate max-w-[120px]">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    @for($m = 1; $m <= 12; $m++)
                                    @php
                                        $contribution = $contributions->where('user_id', $user->id)->where('month', $m)->first();
                                        if($contribution) $userTotal += $contribution->amount;
                                    @endphp
                                    <td class="px-2 py-3 text-center">
                                        @if($contribution)
                                            @if($contribution->is_late)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-400" title="Late - ৳{{ number_format($contribution->amount, 0) }}">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                            @else
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400" title="Paid - ৳{{ number_format($contribution->amount, 0) }}">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                            @endif
                                        @elseif($m <= date('n') || $year < date('Y'))
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400" title="Unpaid">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                        @else
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500" title="Future">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                        @endif
                                    </td>
                                    @endfor
                                    <td class="px-3 py-3 text-center">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">৳{{ number_format($userTotal, 0) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <td class="px-3 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100 sticky left-0 bg-gray-50 dark:bg-gray-700">Monthly Total</td>
                                    @for($m = 1; $m <= 12; $m++)
                                    @php
                                        $monthTotal = $contributions->where('month', $m)->sum('amount');
                                    @endphp
                                    <td class="px-2 py-3 text-center text-xs font-medium text-gray-700 dark:text-gray-300">
                                        {{ $monthTotal > 0 ? '৳' . number_format($monthTotal / 1000, 1) . 'k' : '-' }}
                                    </td>
                                    @endfor
                                    <td class="px-3 py-3 text-center text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                        ৳{{ number_format($contributions->sum('amount'), 0) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="mt-4 flex flex-wrap gap-4 text-sm">
                        <div class="flex items-center">
                            <span class="w-4 h-4 rounded-full bg-green-100 mr-2"></span>
                            <span class="text-gray-600 dark:text-gray-400">Paid On Time</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-4 h-4 rounded-full bg-orange-100 mr-2"></span>
                            <span class="text-gray-600 dark:text-gray-400">Paid Late</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-4 h-4 rounded-full bg-red-100 mr-2"></span>
                            <span class="text-gray-600 dark:text-gray-400">Unpaid</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-4 h-4 rounded-full bg-gray-100 mr-2"></span>
                            <span class="text-gray-600 dark:text-gray-400">Future</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
