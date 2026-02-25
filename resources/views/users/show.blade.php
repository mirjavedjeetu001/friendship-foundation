<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $user->name }}
            </h2>
            <a href="{{ route('users.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                ← Back to Members
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Member Info Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="h-20 w-20 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                <span class="text-indigo-600 dark:text-indigo-400 font-bold text-2xl">
                                    {{ substr($user->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}</h3>
                                <p class="text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                <div class="mt-2">
                                    @foreach($user->roles as $role)
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                                        @if($role->name === 'super-admin') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300
                                        @elseif($role->name === 'admin') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                        @elseif($role->name === 'accountant') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                        {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Contributed</div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">৳{{ number_format($totalContributed, 2) }}</div>
                            @if($pendingAmount > 0)
                            <div class="text-sm text-yellow-600 dark:text-yellow-400">৳{{ number_format($pendingAmount, 2) }} pending</div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Phone</div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Joined Date</div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->joined_date?->format('M d, Y') ?? 'N/A' }}</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Status</div>
                            <div class="font-medium {{ $user->is_active ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Actions</div>
                            <div class="flex space-x-2 mt-1">
                                @can('edit users')
                                <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">Edit</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yearly Payment Status -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment Status - {{ $currentYear }}</h3>
                    <div class="grid grid-cols-3 md:grid-cols-6 lg:grid-cols-12 gap-2">
                        @foreach($months as $monthData)
                        <div class="text-center p-3 rounded-lg
                            @if($monthData['status'] === 'approved') bg-green-100 dark:bg-green-900
                            @elseif($monthData['status'] === 'pending') bg-yellow-100 dark:bg-yellow-900
                            @elseif($monthData['status'] === 'rejected') bg-red-100 dark:bg-red-900
                            @else bg-gray-100 dark:bg-gray-700 @endif">
                            <div class="text-xs font-medium
                                @if($monthData['status'] === 'approved') text-green-800 dark:text-green-300
                                @elseif($monthData['status'] === 'pending') text-yellow-800 dark:text-yellow-300
                                @elseif($monthData['status'] === 'rejected') text-red-800 dark:text-red-300
                                @else text-gray-600 dark:text-gray-400 @endif">
                                {{ substr($monthData['name'], 0, 3) }}
                            </div>
                            <div class="mt-1">
                                @if($monthData['status'] === 'approved')
                                    <svg class="w-5 h-5 mx-auto text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @elseif($monthData['status'] === 'pending')
                                    <svg class="w-5 h-5 mx-auto text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif($monthData['status'] === 'rejected')
                                    <svg class="w-5 h-5 mx-auto text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex flex-wrap gap-4 text-sm">
                        <div class="flex items-center"><span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span> Paid</div>
                        <div class="flex items-center"><span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span> Pending</div>
                        <div class="flex items-center"><span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span> Rejected</div>
                        <div class="flex items-center"><span class="w-3 h-3 rounded-full bg-gray-400 mr-2"></span> Not Paid</div>
                    </div>
                </div>
            </div>

            <!-- Contribution History -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Contribution History</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Month/Year</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($contributions as $contribution)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $contribution->month_year }}
                                        @if($contribution->is_late)
                                            <span class="text-xs text-red-500">(Late)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        ৳{{ number_format($contribution->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($contribution->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                            @elseif($contribution->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @endif">
                                            {{ ucfirst($contribution->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $contribution->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('contributions.show', $contribution) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No contributions found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $contributions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
