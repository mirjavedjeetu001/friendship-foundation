<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Pending Expenses') }}
            </h2>
            <a href="{{ route('expenses.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                All Expenses
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($expenses->isEmpty())
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">All caught up!</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">There are no pending expenses to approve.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($expenses as $expense)
                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                            <div class="p-4 sm:p-6">
                                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                    <!-- Expense Details -->
                                    <div class="flex-1">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/50 rounded-full flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 break-words">{{ $expense->purpose }}</h3>
                                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 whitespace-nowrap">
                                                        Pending
                                                    </span>
                                                </div>
                                                <div class="grid grid-cols-2 gap-3 text-sm">
                                                    <div>
                                                        <span class="text-gray-500 dark:text-gray-400">Date:</span>
                                                        <span class="ml-1 text-gray-900 dark:text-gray-100">{{ $expense->expense_date->format('d M Y') }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500 dark:text-gray-400">Amount:</span>
                                                        <span class="ml-1 font-semibold text-gray-900 dark:text-gray-100">৳{{ number_format($expense->amount, 0) }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500 dark:text-gray-400">Spent By:</span>
                                                        <span class="ml-1 text-gray-900 dark:text-gray-100">{{ $expense->spent_by }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500 dark:text-gray-400">Created By:</span>
                                                        <span class="ml-1 text-gray-900 dark:text-gray-100">{{ $expense->creator->name }}</span>
                                                    </div>
                                                </div>
                                                @if($expense->description)
                                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $expense->description }}</p>
                                                @endif
                                                @if($expense->receipt)
                                                    <a href="{{ $expense->receipt_url }}" target="_blank" class="mt-2 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        View Receipt
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex flex-col gap-2 lg:w-72">
                                        <!-- Approve Form -->
                                        <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="approve-form">
                                            @csrf
                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Fund Source <span class="text-red-500">*</span>
                                                </label>
                                                <select name="fund_source" required onchange="toggleFundNote(this, {{ $expense->id }})"
                                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                    <option value="">Select fund source...</option>
                                                    <option value="monthly_savings">Monthly Savings</option>
                                                    <option value="manual">Manual Adjustment</option>
                                                </select>
                                                <div id="fundNote_{{ $expense->id }}" class="hidden">
                                                    <input type="text" name="fund_source_note" placeholder="Describe the fund source..."
                                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                </div>
                                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Approve
                                                </button>
                                            </div>
                                        </form>

                                        <!-- Reject Button (opens modal) -->
                                        <button type="button" onclick="openRejectModal({{ $expense->id }})" 
                                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 dark:bg-red-900/50 dark:text-red-400 dark:hover:bg-red-900 transition">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Reject
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Modal -->
                        <div id="rejectModal_{{ $expense->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                            <div class="relative top-20 mx-4 sm:mx-auto p-5 border w-auto sm:w-96 max-w-lg shadow-lg rounded-lg bg-white dark:bg-gray-800">
                                <div class="mt-3">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Reject Expense</h3>
                                    <form action="{{ route('expenses.reject', $expense) }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Reason for rejection <span class="text-red-500">*</span>
                                            </label>
                                            <textarea name="rejection_reason" rows="3" required
                                                placeholder="Please provide a reason..."
                                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" onclick="closeRejectModal({{ $expense->id }})"
                                                class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                                                Confirm Reject
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($expenses->hasPages())
                <div class="mt-6">
                    {{ $expenses->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleFundNote(select, expenseId) {
            const noteDiv = document.getElementById('fundNote_' + expenseId);
            const noteInput = noteDiv.querySelector('input');
            if (select.value === 'manual') {
                noteDiv.classList.remove('hidden');
                noteInput.required = true;
            } else {
                noteDiv.classList.add('hidden');
                noteInput.required = false;
                noteInput.value = '';
            }
        }

        function openRejectModal(expenseId) {
            document.getElementById('rejectModal_' + expenseId).classList.remove('hidden');
        }

        function closeRejectModal(expenseId) {
            document.getElementById('rejectModal_' + expenseId).classList.add('hidden');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('fixed')) {
                e.target.classList.add('hidden');
            }
        });
    </script>
    @endpush
</x-app-layout>
