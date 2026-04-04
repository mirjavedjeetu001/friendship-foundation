<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Expense Details') }}
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
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <!-- Status Badge -->
                    <div class="flex items-center justify-between mb-6">
                        <span class="px-3 py-1 text-sm font-medium rounded-full {{ $expense->status_badge_class }}">
                            {{ ucfirst($expense->status) }}
                        </span>
                        @if($expense->isPending() && (auth()->id() === $expense->created_by || auth()->user()->hasAnyRole(['super-admin', 'admin'])))
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Main Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Purpose</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $expense->purpose }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Amount</label>
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">৳{{ number_format($expense->amount, 0) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Date</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $expense->expense_date->format('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Spent By</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $expense->spent_by }}</p>
                        </div>
                    </div>

                    @if($expense->description)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Description</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ $expense->description }}</p>
                    </div>
                    @endif

                    <!-- Receipt -->
                    @if($expense->receipt)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Receipt/Voucher</label>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
                            <a href="{{ $expense->receipt_url }}" target="_blank" class="block">
                                <img src="{{ $expense->receipt_url }}" alt="Receipt" class="max-w-full h-auto max-h-96 rounded-lg mx-auto">
                            </a>
                            <a href="{{ $expense->receipt_url }}" target="_blank" class="mt-2 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Open in new tab
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Divider -->
                    <hr class="border-gray-200 dark:border-gray-700 my-6">

                    <!-- Submission Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Submitted By</label>
                            <div class="flex items-center">
                                <img src="{{ $expense->creator->avatar_url }}" alt="{{ $expense->creator->name }}" class="w-8 h-8 rounded-full object-cover mr-2">
                                <span class="text-gray-900 dark:text-gray-100">{{ $expense->creator->name }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Submitted On</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $expense->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>

                    <!-- Approval Info -->
                    @if($expense->isApproved())
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <h4 class="font-medium text-green-800 dark:text-green-400 mb-3">Approval Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-green-700 dark:text-green-500 mb-1">Approved By</label>
                                <div class="flex items-center">
                                    <img src="{{ $expense->approver->avatar_url }}" alt="{{ $expense->approver->name }}" class="w-8 h-8 rounded-full object-cover mr-2">
                                    <span class="text-green-900 dark:text-green-100">{{ $expense->approver->name }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-green-700 dark:text-green-500 mb-1">Approved On</label>
                                <p class="text-green-900 dark:text-green-100">{{ $expense->approved_at->format('d M Y, h:i A') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-green-700 dark:text-green-500 mb-1">Fund Source</label>
                                <p class="text-green-900 dark:text-green-100">{{ $expense->fund_source_label }}</p>
                            </div>
                            @if($expense->fund_source === 'manual' && $expense->fund_source_note)
                            <div>
                                <label class="block text-sm font-medium text-green-700 dark:text-green-500 mb-1">Source Note</label>
                                <p class="text-green-900 dark:text-green-100">{{ $expense->fund_source_note }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Rejection Info -->
                    @if($expense->isRejected())
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <h4 class="font-medium text-red-800 dark:text-red-400 mb-3">Rejection Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-red-700 dark:text-red-500 mb-1">Rejected By</label>
                                <div class="flex items-center">
                                    <img src="{{ $expense->approver->avatar_url }}" alt="{{ $expense->approver->name }}" class="w-8 h-8 rounded-full object-cover mr-2">
                                    <span class="text-red-900 dark:text-red-100">{{ $expense->approver->name }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-red-700 dark:text-red-500 mb-1">Rejected On</label>
                                <p class="text-red-900 dark:text-red-100">{{ $expense->approved_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-red-700 dark:text-red-500 mb-1">Reason</label>
                            <p class="text-red-900 dark:text-red-100">{{ $expense->rejection_reason }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Pending Approval Actions -->
                    @if($expense->isPending() && auth()->user()->can('approve contributions'))
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mt-6">
                        <h4 class="font-medium text-yellow-800 dark:text-yellow-400 mb-4">Approve or Reject</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Approve Form -->
                            <form action="{{ route('expenses.approve', $expense) }}" method="POST">
                                @csrf
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Fund Source <span class="text-red-500">*</span>
                                        </label>
                                        <select name="fund_source" required onchange="toggleNote(this)"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Select fund source...</option>
                                            <option value="monthly_savings">Monthly Savings</option>
                                            <option value="manual">Manual Adjustment</option>
                                        </select>
                                    </div>
                                    <div id="fundNoteField" class="hidden">
                                        <input type="text" name="fund_source_note" placeholder="Describe the fund source..."
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Approve Expense
                                    </button>
                                </div>
                            </form>

                            <!-- Reject Form -->
                            <form action="{{ route('expenses.reject', $expense) }}" method="POST">
                                @csrf
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Rejection Reason <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="rejection_reason" required placeholder="Reason for rejection..."
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Reject Expense
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleNote(select) {
            const noteField = document.getElementById('fundNoteField');
            const noteInput = noteField.querySelector('input');
            if (select.value === 'manual') {
                noteField.classList.remove('hidden');
                noteInput.required = true;
            } else {
                noteField.classList.add('hidden');
                noteInput.required = false;
                noteInput.value = '';
            }
        }
    </script>
    @endpush
</x-app-layout>
