<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Monthly Report') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Month/Year Filter -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.monthly') }}" class="flex flex-wrap items-end gap-4">
                        <div>
                            <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                            <select name="month" id="month" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $name)
                                <option value="{{ $index + 1 }}" {{ $month == ($index + 1) ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
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
                    <p class="text-sm text-gray-500 dark:text-gray-400">Paid</p>
                    <p class="text-2xl font-bold text-green-600">{{ $contributions->where('status', 'approved')->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Unpaid</p>
                    @php
                        $paidUserIds = $contributions->where('status', 'approved')->pluck('user_id')->toArray();
                        $unpaidCount = $users->whereNotIn('id', $paidUserIds)->count();
                    @endphp
                    <p class="text-2xl font-bold text-red-600">{{ $unpaidCount }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Collected</p>
                    <p class="text-2xl font-bold text-indigo-600">৳{{ number_format($contributions->where('status', 'approved')->sum('amount'), 2) }}</p>
                </div>
            </div>

            <!-- Report Title -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }} - Member Contribution Status
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Member</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Paid On</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Late?</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Slip</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($users as $index => $user)
                                @php
                                    $contribution = $contributions->where('user_id', $user->id)->where('status', 'approved')->first();
                                    $pendingContribution = $contributions->where('user_id', $user->id)->where('status', 'pending')->first();
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-medium mr-2">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($contribution)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Paid
                                        </span>
                                        @elseif($pendingContribution)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                            Pending
                                        </span>
                                        @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            Unpaid
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $contribution ? '৳' . number_format($contribution->amount, 2) : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $contribution && $contribution->contribution_date ? $contribution->contribution_date->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($contribution)
                                            @if($contribution->is_late)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                                Late
                                            </span>
                                            @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                On Time
                                            </span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($contribution && $contribution->payment_slip)
                                        <a href="{{ Storage::url($contribution->payment_slip) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-sm">
                                            View
                                        </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
