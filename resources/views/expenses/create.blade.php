<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add Expenses') }}
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
                    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" id="expenseForm">
                        @csrf

                        <div id="expenseEntries">
                            <!-- Expense Entry Template -->
                            <div class="expense-entry border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4" data-index="0">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        Expense #<span class="entry-number">1</span>
                                    </h3>
                                    <button type="button" class="remove-entry hidden text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Remove">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Date -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Date <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="expenses[0][expense_date]" required
                                            value="{{ date('Y-m-d') }}"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('expenses.0.expense_date')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Amount -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Amount (৳) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="expenses[0][amount]" required min="1" step="1"
                                            placeholder="Enter amount"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('expenses.0.amount')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Purpose -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Purpose <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="expenses[0][purpose]" required
                                            placeholder="e.g., Office supplies, Travel, Food"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('expenses.0.purpose')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Spent By -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Spent By <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="expenses[0][spent_by]" required
                                            placeholder="Name of person who made the expense"
                                            value="{{ auth()->user()->name }}"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('expenses.0.spent_by')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Description -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Description (Optional)
                                        </label>
                                        <textarea name="expenses[0][description]" rows="2"
                                            placeholder="Additional details about the expense..."
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                    </div>

                                    <!-- Receipt -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Receipt/Voucher (Optional)
                                        </label>
                                        <input type="file" name="expenses[0][receipt]" accept="image/*"
                                            class="w-full text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-400">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Max 5MB. Supported formats: JPG, PNG, GIF</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add More Button -->
                        <div class="flex justify-center mb-6">
                            <button type="button" id="addMoreBtn" class="inline-flex items-center px-4 py-2 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:border-indigo-500 hover:text-indigo-600 dark:hover:border-indigo-400 dark:hover:text-indigo-400 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Another Expense
                            </button>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">Approval Required</h4>
                                    <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                                        All expenses will be submitted for approval. An Admin or Accountant will review and approve with the appropriate fund source.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Submit for Approval
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('expenseEntries');
            const addBtn = document.getElementById('addMoreBtn');
            let entryIndex = 1;

            // Add more expense entry
            addBtn.addEventListener('click', function() {
                const firstEntry = container.querySelector('.expense-entry');
                const newEntry = firstEntry.cloneNode(true);
                
                // Update data-index
                newEntry.dataset.index = entryIndex;
                
                // Update entry number
                newEntry.querySelector('.entry-number').textContent = entryIndex + 1;
                
                // Update all input names
                newEntry.querySelectorAll('input, textarea').forEach(function(input) {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/\[\d+\]/, '[' + entryIndex + ']'));
                    }
                    // Clear values except date and spent_by
                    if (!name.includes('expense_date') && !name.includes('spent_by')) {
                        input.value = '';
                    }
                });
                
                // Show remove button
                const removeBtn = newEntry.querySelector('.remove-entry');
                removeBtn.classList.remove('hidden');
                
                container.appendChild(newEntry);
                entryIndex++;
                
                updateRemoveButtons();
            });

            // Remove expense entry
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-entry')) {
                    const entry = e.target.closest('.expense-entry');
                    entry.remove();
                    updateEntryNumbers();
                    updateRemoveButtons();
                }
            });

            function updateEntryNumbers() {
                const entries = container.querySelectorAll('.expense-entry');
                entries.forEach(function(entry, index) {
                    entry.querySelector('.entry-number').textContent = index + 1;
                    entry.querySelectorAll('input, textarea').forEach(function(input) {
                        const name = input.getAttribute('name');
                        if (name) {
                            input.setAttribute('name', name.replace(/\[\d+\]/, '[' + index + ']'));
                        }
                    });
                });
            }

            function updateRemoveButtons() {
                const entries = container.querySelectorAll('.expense-entry');
                entries.forEach(function(entry, index) {
                    const removeBtn = entry.querySelector('.remove-entry');
                    if (entries.length > 1) {
                        removeBtn.classList.remove('hidden');
                    } else {
                        removeBtn.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
