<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Pending Contributions') }}
            </h2>
            <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                {{ $contributions->total() }} Pending
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    @if($contributions->count() > 0)
                    <div class="space-y-4">
                        @foreach($contributions as $contribution)
                        <div class="border dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ $contribution->user->avatar_url }}" alt="{{ $contribution->user->name }}" class="h-12 w-12 rounded-full object-cover flex-shrink-0">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $contribution->user->name }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $contribution->month_year }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="font-semibold text-lg text-gray-900 dark:text-gray-100">৳{{ number_format($contribution->amount, 2) }}</span>
                                        @if($contribution->is_late)
                                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded text-xs">Late</span>
                                        @endif
                                        @if(!$contribution->is_self_submitted)
                                            <span class="text-indigo-500 text-xs">by {{ $contribution->submitter->name }}</span>
                                        @endif
                                        <span class="text-xs">{{ $contribution->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <a href="{{ route('contributions.show', $contribution) }}" class="px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium transition">
                                        View
                                    </a>
                                    <form action="{{ route('contributions.approve', $contribution) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium transition">
                                            Approve
                                        </button>
                                    </form>
                                    <button onclick="openRejectModal({{ $contribution->id }})" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium transition">
                                        Reject
                                    </button>
                                </div>
                            </div>
                            @if($contribution->payment_slip)
                            <div class="mt-4">
                                <a href="{{ asset('storage/' . $contribution->payment_slip) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                                    View Payment Slip →
                                </a>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $contributions->links() }}
                    </div>
                    @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">No pending contributions</h3>
                        <p class="mt-1 text-gray-500 dark:text-gray-400">All contributions have been reviewed.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Pending Withdrawals Link -->
            @php
                $pendingWithdrawals = \App\Models\Withdrawal::pending()->count();
            @endphp
            @if($pendingWithdrawals > 0)
            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-yellow-700 dark:text-yellow-300">{{ $pendingWithdrawals }} withdrawal request(s) pending approval</span>
                    </div>
                    <a href="{{ route('withdrawals.pending') }}" class="text-yellow-700 dark:text-yellow-300 hover:underline font-medium">
                        Review →
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Reject Contribution</h3>
            <form id="reject-form" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Rejection *</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="3" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" placeholder="Please provide a reason..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(id) {
            document.getElementById('reject-form').action = '/contributions/' + id + '/reject';
            document.getElementById('reject-modal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
        }
    </script>
</x-app-layout>
