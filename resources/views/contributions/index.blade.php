<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Contributions') }}
            </h2>
            <a href="{{ route('contributions.create') }}" class="inline-flex items-center justify-center bg-indigo-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Contribution
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('contributions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                            <select name="month" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Months</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                            <select name="year" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Years</option>
                                @for($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-indigo-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                Filter
                            </button>
                            <a href="{{ route('contributions.index') }}" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contributions Table -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                <div class="table-responsive">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase" style="width:18%">Member</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase" style="width:10%">Month</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase" style="width:10%">Amount</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase" style="width:8%">Status</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase hidden lg:table-cell" style="width:14%">Submitted By</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase hidden lg:table-cell" style="width:14%">Approved By</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase hidden md:table-cell" style="width:10%">Date</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase" style="width:16%">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($contributions as $contribution)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-3 py-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $contribution->user->name }}</div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $contribution->month_year }}</div>
                                    @if($contribution->is_late)
                                        <span class="text-xs text-red-500">Late</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">৳{{ number_format($contribution->amount, 0) }}</div>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                        @if($contribution->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                        @elseif($contribution->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @endif">
                                        {{ ucfirst($contribution->status) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 hidden lg:table-cell">
                                    <span class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $contribution->submitter->name }}</span>
                                </td>
                                <td class="px-3 py-3 hidden lg:table-cell">
                                    @if($contribution->status === 'approved' && $contribution->approver)
                                        <span class="text-sm text-green-600 dark:text-green-400 truncate">{{ $contribution->approver->name }}</span>
                                    @elseif($contribution->status === 'rejected' && $contribution->approver)
                                        <span class="text-sm text-red-500 dark:text-red-400 truncate">{{ $contribution->approver->name }}</span>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 hidden md:table-cell text-sm text-gray-500 dark:text-gray-400">
                                    {{ $contribution->created_at->format('M d') }}
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('contributions.show', $contribution) }}" class="px-2 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-medium rounded hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition">
                                            View
                                        </a>
                                        @if($contribution->status === 'pending')
                                            @can('approve contributions')
                                            <form action="{{ route('contributions.approve', $contribution) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-medium rounded hover:bg-green-100 dark:hover:bg-green-900/50 transition">
                                                    Approve
                                                </button>
                                            </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No contributions found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $contributions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
