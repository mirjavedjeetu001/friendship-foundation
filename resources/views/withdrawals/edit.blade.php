<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Withdrawal') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('withdrawals.update', $withdrawal) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Amount -->
                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Amount (৳) *
                            </label>
                            <input type="number" name="amount" id="amount" step="0.01" min="1" value="{{ old('amount', $withdrawal->amount) }}" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Purpose -->
                        <div class="mb-4">
                            <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Purpose *
                            </label>
                            <input type="text" name="purpose" id="purpose" value="{{ old('purpose', $withdrawal->purpose) }}" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $withdrawal->description) }}</textarea>
                        </div>

                        <!-- Withdrawal Date -->
                        <div class="mb-4">
                            <label for="withdrawal_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Withdrawal Date *
                            </label>
                            <input type="date" name="withdrawal_date" id="withdrawal_date" value="{{ old('withdrawal_date', $withdrawal->withdrawal_date->format('Y-m-d')) }}" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Current Receipt -->
                        @if($withdrawal->receipt)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Receipt</label>
                            <img src="{{ asset('storage/' . $withdrawal->receipt) }}" alt="Receipt" class="max-w-xs h-auto rounded-lg border dark:border-gray-700">
                        </div>
                        @endif

                        <!-- New Receipt -->
                        <div class="mb-6">
                            <label for="receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                New Receipt (Optional)
                            </label>
                            <input type="file" name="receipt" id="receipt" accept="image/*" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('withdrawals.show', $withdrawal) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                Update Withdrawal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
