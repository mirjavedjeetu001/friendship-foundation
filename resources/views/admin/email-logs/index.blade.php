<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Email Notifications') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Sent</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $totalSent }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Failed</p>
                            <p class="text-lg font-semibold text-red-600 dark:text-red-400">{{ $totalFailed }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">This Month</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $sentThisMonth }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Unpaid Members</p>
                            <p class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">{{ $unpaidMembersCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Month Reminder Status -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Payment Reminders - {{ $currentMonth }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Due date: {{ $dueDate->format('d F Y') }} 
                            @if(now()->lt($dueDate))
                                <span class="text-green-600">({{ now()->diffInDays($dueDate) }} days remaining)</span>
                            @else
                                <span class="text-red-600">(Due date passed)</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Reminders sent this month: <span class="font-semibold">{{ $remindersSentThisMonth }}</span>
                            | Unpaid members: <span class="font-semibold text-yellow-600">{{ $unpaidMembersCount }}</span>
                        </p>
                    </div>
                    <div class="flex gap-2">
                        @if($unpaidMembersCount > 0)
                        <form action="{{ route('admin.email-logs.send-reminders') }}" method="POST" onsubmit="return confirm('Send payment reminders to {{ $unpaidMembersCount }} unpaid members?')">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Send Reminders ({{ $unpaidMembersCount }})
                            </button>
                        </form>
                        <form action="{{ route('admin.email-logs.send-reminders') }}" method="POST" onsubmit="return confirm('Force resend to ALL {{ $unpaidMembersCount }} unpaid members (even if already sent)?')">
                            @csrf
                            <input type="hidden" name="force" value="1">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Force Resend All
                            </button>
                        </form>
                        @else
                        <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 text-sm font-medium rounded-lg dark:bg-green-900 dark:text-green-300">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            All members have paid!
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('admin.email-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                            <select name="type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm">
                                <option value="all">All Types</option>
                                <option value="payment_reminder" {{ request('type') == 'payment_reminder' ? 'selected' : '' }}>Payment Reminder</option>
                                <option value="registration_approved" {{ request('type') == 'registration_approved' ? 'selected' : '' }}>Registration Approved</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm">
                                <option value="all">All Status</option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                            <input type="text" name="month_year" value="{{ request('month_year') }}" placeholder="e.g., April 2026"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-indigo-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-indigo-700 transition">
                                Filter
                            </button>
                            <a href="{{ route('admin.email-logs.index') }}" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Email Logs Table -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 min-w-[800px]">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Recipient</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Subject</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Sent At</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        @if($log->user)
                                            <img src="{{ $log->user->avatar_url }}" alt="{{ $log->recipient_name }}" class="h-8 w-8 rounded-full object-cover mr-2">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center mr-2">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $log->recipient_name ?? 'Unknown' }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->recipient_email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-900 dark:text-gray-100">{{ $log->type_label }}</span>
                                    @if($log->month_year)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->month_year }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($log->subject, 30) }}</div>
                                    @if($log->amount)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">৳{{ number_format($log->amount, 0) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $log->status_badge_class }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                    @if($log->error_message)
                                        <div class="text-xs text-red-500 mt-1 truncate max-w-xs" title="{{ $log->error_message }}">
                                            {{ Str::limit($log->error_message, 30) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($log->sent_at)
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $log->sent_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->sent_at->format('h:i A') }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                    @if($log->sender)
                                        <div class="text-xs text-indigo-600 dark:text-indigo-400">by {{ $log->sender->name }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($log->status === 'failed')
                                        <form action="{{ route('admin.email-logs.retry', $log) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                                                Retry
                                            </button>
                                        </form>
                                    @elseif($log->user && $log->type === 'payment_reminder')
                                        <form action="{{ route('admin.email-logs.send-to-member', $log->user) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                                                Resend
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="mt-2">No email logs found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($logs->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
