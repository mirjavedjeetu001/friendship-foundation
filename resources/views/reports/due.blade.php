<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Due Payments Report') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Members</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $users->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Members with Due</p>
                    <p class="text-2xl font-bold text-red-600">{{ $dueMembers->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Due Amount</p>
                    <p class="text-2xl font-bold text-orange-600">৳{{ number_format($totalDue, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Due Day</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $settings->due_day }}th</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">of each month</p>
                </div>
            </div>

            <!-- Current Month Status Alert -->
            @php
                $currentDay = date('j');
                $dueDay = $settings->due_day;
                $daysRemaining = $dueDay - $currentDay;
            @endphp
            @if($daysRemaining > 0 && $daysRemaining <= 5)
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            <strong>Reminder:</strong> Only {{ $daysRemaining }} day(s) remaining until the due date for {{ date('F Y') }}.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($currentDay > $dueDay)
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 dark:text-red-300">
                            <strong>Due date passed:</strong> The due date for {{ date('F Y') }} has passed. Any new payments will be marked as late.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Members with Due Payments -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Members with Due Payments</h3>

                    @if($dueMembers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Member</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Phone</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Due Months</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Due</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($dueMembers as $index => $member)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center text-red-600 dark:text-red-400 font-medium mr-2">
                                                {{ strtoupper(substr($member->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $member->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $member->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $member->phone ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1 justify-center">
                                            <span class="px-2 py-0.5 text-xs font-medium rounded bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                {{ date('M', mktime(0, 0, 0, $month, 1)) }} {{ $year }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-lg font-bold text-red-600">৳{{ number_format($settings->monthly_contribution_amount, 2) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if(auth()->user()->can('approve contributions'))
                                        <a href="{{ route('contributions.create', ['user_id' => $member->id]) }}" class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Add Payment
                                        </a>
                                        @elseif(auth()->id() === $member->id)
                                        <a href="{{ route('contributions.create') }}" class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Pay Now
                                        </a>
                                        @else
                                        <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">All Paid Up!</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All members have paid their contributions. Great job!</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Members Status Summary -->
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Paid Members This Month -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            <span class="inline-flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Paid This Month ({{ date('F Y') }})
                            </span>
                        </h3>
                        @php
                            $paidThisMonth = $users->filter(function($user) use ($currentMonthContributions) {
                                return $currentMonthContributions->where('user_id', $user->id)->where('status', 'approved')->isNotEmpty();
                            });
                        @endphp
                        <div class="space-y-2">
                            @forelse($paidThisMonth as $user)
                            @php
                                $contribution = $currentMonthContributions->where('user_id', $user->id)->where('status', 'approved')->first();
                            @endphp
                            <div class="flex items-center justify-between p-2 bg-green-50 dark:bg-green-900/20 rounded">
                                <div class="flex items-center">
                                    <div class="h-6 w-6 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center text-green-600 dark:text-green-400 font-medium mr-2 text-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-medium text-green-600">৳{{ number_format($contribution->amount ?? 0, 0) }}</span>
                                    @if($contribution && $contribution->is_late)
                                    <span class="ml-1 text-xs text-orange-500">(Late)</span>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm py-4 text-center">No payments yet this month</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Unpaid Members This Month -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            <span class="inline-flex items-center">
                                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Unpaid This Month ({{ date('F Y') }})
                            </span>
                        </h3>
                        @php
                            $unpaidThisMonth = $users->filter(function($user) use ($currentMonthContributions) {
                                return $currentMonthContributions->where('user_id', $user->id)->where('status', 'approved')->isEmpty();
                            });
                        @endphp
                        <div class="space-y-2">
                            @forelse($unpaidThisMonth as $user)
                            <div class="flex items-center justify-between p-2 bg-red-50 dark:bg-red-900/20 rounded">
                                <div class="flex items-center">
                                    <div class="h-6 w-6 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center text-red-600 dark:text-red-400 font-medium mr-2 text-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</span>
                                </div>
                                @can('create contributions')
                                <a href="{{ route('contributions.create', ['user_id' => $user->id]) }}" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                                    Add Payment
                                </a>
                                @endcan
                            </div>
                            @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm py-4 text-center">Everyone has paid!</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
