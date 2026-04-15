<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Pending Withdrawals') }}
            </h2>
            <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                {{ $withdrawals->total() }} Pending
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    @if($withdrawals->count() > 0)
                    <div class="space-y-4">
                        @foreach($withdrawals as $withdrawal)
                        <div class="border dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <div class="flex flex-wrap items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $withdrawal->purpose }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $withdrawal->description }}</p>
                                    <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="font-semibold text-lg text-red-600 dark:text-red-400">-৳{{ number_format($withdrawal->amount, 2) }}</span>
                                        <span>by {{ $withdrawal->requester->name }}</span>
                                        <span>{{ $withdrawal->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-2 mt-4 sm:mt-0">
                                    <a href="{{ route('withdrawals.show', $withdrawal) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-center transition">
                                        View
                                    </a>
                                    <form action="{{ route('withdrawals.approve', $withdrawal) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                            Approve
                                        </button>
                                    </form>
                                    <button onclick="openRejectModal({{ $withdrawal->id }})" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $withdrawals->links() }}
                    </div>
                    @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">No pending withdrawals</h3>
                        <p class="mt-1 text-gray-500 dark:text-gray-400">All withdrawals have been reviewed.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-4 sm:mx-auto p-5 border w-auto sm:w-96 max-w-lg shadow-lg rounded-md bg-white dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Reject Withdrawal</h3>
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
            document.getElementById('reject-form').action = '/withdrawals/' + id + '/reject';
            document.getElementById('reject-modal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
        }
    </script>
</x-app-layout>
