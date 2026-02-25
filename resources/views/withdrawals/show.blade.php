<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Withdrawal Details') }}
            </h2>
            <a href="{{ route('withdrawals.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                ← Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <!-- Status Badge -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-red-600 dark:text-red-400">
                                -৳{{ number_format($withdrawal->amount, 2) }}
                            </h3>
                            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $withdrawal->purpose }}</p>
                        </div>
                        <span class="px-4 py-2 text-sm font-semibold rounded-full
                            @if($withdrawal->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                            @elseif($withdrawal->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @endif">
                            {{ ucfirst($withdrawal->status) }}
                        </span>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Requested By</label>
                            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $withdrawal->requester->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $withdrawal->requester->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Withdrawal Date</label>
                            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $withdrawal->withdrawal_date->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Request Date</label>
                            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $withdrawal->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>

                    @if($withdrawal->description)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Description</label>
                        <p class="text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded">{{ $withdrawal->description }}</p>
                    </div>
                    @endif

                    <!-- Receipt -->
                    @if($withdrawal->receipt)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Receipt</label>
                        <img src="{{ asset('storage/' . $withdrawal->receipt) }}" alt="Receipt" class="max-w-full h-auto rounded-lg shadow-lg border dark:border-gray-700">
                    </div>
                    @endif

                    <!-- Approval Info -->
                    @if($withdrawal->status !== 'pending')
                    <div class="border-t dark:border-gray-700 pt-6 mt-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            {{ $withdrawal->status === 'approved' ? 'Approval' : 'Rejection' }} Details
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ $withdrawal->status === 'approved' ? 'Approved' : 'Rejected' }} By</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $withdrawal->approver?->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Date</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $withdrawal->approved_at?->format('F d, Y h:i A') ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($withdrawal->rejection_reason)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Rejection Reason</label>
                            <p class="text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-3 rounded">{{ $withdrawal->rejection_reason }}</p>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Actions -->
                    @if($withdrawal->status === 'pending')
                    <div class="border-t dark:border-gray-700 pt-6 mt-6">
                        <div class="flex flex-wrap gap-3">
                            @can('approve withdrawals')
                            <form action="{{ route('withdrawals.approve', $withdrawal) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    Approve
                                </button>
                            </form>

                            <button onclick="document.getElementById('reject-modal').classList.remove('hidden')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Reject
                            </button>
                            @endcan

                            @if($withdrawal->requested_by === auth()->id())
                            <a href="{{ route('withdrawals.edit', $withdrawal) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                Edit
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    @can('reject withdrawals')
                    <div id="reject-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Reject Withdrawal</h3>
                            <form action="{{ route('withdrawals.reject', $withdrawal) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Rejection *</label>
                                    <textarea name="rejection_reason" id="rejection_reason" rows="3" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" placeholder="Please provide a reason..."></textarea>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                        Reject
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
