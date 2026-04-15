<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Pending Bank Settlement') }}
            </h2>
            <a href="{{ route('expenses.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Expenses
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 overflow-hidden">
            <!-- Summary Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 sm:p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900 rounded-lg flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-2 sm:ml-4 min-w-0">
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">Pending Settlement</p>
                            <p class="text-base sm:text-xl font-bold text-orange-600">৳{{ number_format($totalPendingSettlement, 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 sm:p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900 rounded-lg flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div class="ml-2 sm:ml-4 min-w-0">
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">Bank Balance</p>
                            <p class="text-base sm:text-xl font-bold text-emerald-600">৳{{ number_format($bankBalance, 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 sm:p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-2 sm:ml-4 min-w-0">
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">After Settlement</p>
                            <p class="text-base sm:text-xl font-bold {{ ($bankBalance - $totalPendingSettlement) < 0 ? 'text-red-600' : 'text-blue-600' }}">
                                ৳{{ number_format($bankBalance - $totalPendingSettlement, 0) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($totalPendingSettlement > 0)
            <!-- Info Box -->
            <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-amber-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-amber-800 dark:text-amber-300">Cash Expenses Awaiting Bank Settlement</h4>
                        <p class="text-sm text-amber-600 dark:text-amber-400 mt-1">
                            These expenses were paid in cash and approved. When you settle them, the amount will be deducted from the bank balance.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bulk Settle Form -->
            <form method="POST" action="{{ route('expenses.bulk-settle') }}" id="bulkSettleForm">
                @csrf
                
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden mb-4">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="selectAll" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="selectAll" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Select All</label>
                            </div>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
                                <input type="text" name="settlement_note" placeholder="Settlement note (optional)" 
                                    class="w-full sm:w-auto rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <button type="submit" id="bulkSettleBtn" disabled
                                    class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Settle Selected (<span id="selectedCount">0</span>)
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Table (hidden on mobile) -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12"></th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Purpose</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Spent By</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Approved</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($expenses as $expense)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" name="expense_ids[]" value="{{ $expense->id }}" 
                                            class="expense-checkbox w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                            data-amount="{{ $expense->amount }}">
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $expense->expense_date->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $expense->purpose }}</div>
                                        @if($expense->description)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $expense->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $expense->spent_by }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-bold text-orange-600">৳{{ number_format($expense->amount, 0) }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($expense->approver)
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $expense->approver->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $expense->approved_at->format('d M Y') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <button type="button" onclick="settleSingle({{ $expense->id }})" 
                                            class="inline-flex items-center px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-lg hover:bg-emerald-200 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Settle
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card Layout (hidden on desktop) -->
                    <div class="sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($expenses as $expense)
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" name="expense_ids[]" value="{{ $expense->id }}" 
                                    class="expense-checkbox w-4 h-4 mt-1 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                    data-amount="{{ $expense->amount }}">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $expense->purpose }}</p>
                                        <p class="text-sm font-bold text-orange-600 ml-2 whitespace-nowrap">৳{{ number_format($expense->amount, 0) }}</p>
                                    </div>
                                    @if($expense->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate mb-1">{{ $expense->description }}</p>
                                    @endif
                                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                        <span>{{ $expense->expense_date->format('d M Y') }}</span>
                                        <span>&middot;</span>
                                        <span>{{ $expense->spent_by }}</span>
                                    </div>
                                    <div class="flex items-center justify-between mt-2">
                                        @if($expense->approver)
                                            <span class="text-xs text-gray-400">Approved by {{ $expense->approver->name }}</span>
                                        @else
                                            <span></span>
                                        @endif
                                        <button type="button" onclick="settleSingle({{ $expense->id }})" 
                                            class="inline-flex items-center px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-lg hover:bg-emerald-200 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Settle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </form>

            {{ $expenses->links() }}
            @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">All Settled!</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">No cash expenses pending bank settlement.</p>
                <a href="{{ route('expenses.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    View All Expenses
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Single Settle Modal -->
    <div id="settleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-4 sm:mx-auto p-5 border w-auto sm:w-96 max-w-lg shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Settle Expense from Bank</h3>
            <form id="singleSettleForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Settlement Note (Optional)</label>
                    <textarea name="settlement_note" rows="2" placeholder="e.g., Settled via bank transfer"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeSettleModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        Confirm Settlement
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Select All functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.expense-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkButton();
        });

        // Individual checkbox change
        document.querySelectorAll('.expense-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkButton);
        });

        function updateBulkButton() {
            const checkedBoxes = document.querySelectorAll('.expense-checkbox:checked');
            const count = checkedBoxes.length;
            document.getElementById('selectedCount').textContent = count;
            document.getElementById('bulkSettleBtn').disabled = count === 0;
            
            // Update select all state
            const allBoxes = document.querySelectorAll('.expense-checkbox');
            document.getElementById('selectAll').checked = count === allBoxes.length && count > 0;
            document.getElementById('selectAll').indeterminate = count > 0 && count < allBoxes.length;
        }

        // Single settle
        function settleSingle(expenseId) {
            document.getElementById('singleSettleForm').action = `/expenses/${expenseId}/settle`;
            document.getElementById('settleModal').classList.remove('hidden');
        }

        function closeSettleModal() {
            document.getElementById('settleModal').classList.add('hidden');
        }

        // Bulk settle confirmation
        document.getElementById('bulkSettleForm').addEventListener('submit', function(e) {
            const count = document.querySelectorAll('.expense-checkbox:checked').length;
            if (!confirm(`Are you sure you want to settle ${count} expense(s)? This will deduct the amounts from the bank balance.`)) {
                e.preventDefault();
            }
        });
    </script>
    @endpush
</x-app-layout>
