<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Contribution') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('contributions.update', $contribution) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Member Info -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Member</label>
                            <input type="text" value="{{ $contribution->user->name }} - {{ $contribution->month_year }}" disabled class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-gray-100">
                        </div>

                        <!-- Amount -->
                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Amount (৳) *
                            </label>
                            <input type="number" name="amount" id="amount" step="0.01" min="{{ $settings->monthly_contribution_amount }}" value="{{ old('amount', $contribution->amount) }}" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Minimum amount: ৳{{ number_format($settings->monthly_contribution_amount, 2) }}</p>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Payment Slip -->
                        @if($contribution->payment_slip)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Payment Slip</label>
                            <img src="{{ asset('storage/' . $contribution->payment_slip) }}" alt="Payment Slip" class="max-w-xs h-auto rounded-lg border dark:border-gray-700">
                        </div>
                        @endif

                        <!-- New Payment Slip -->
                        <div class="mb-4">
                            <label for="payment_slip" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                New Payment Slip (Optional)
                            </label>
                            <input type="file" name="payment_slip" id="payment_slip" accept="image/*" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload a new slip to replace the current one</p>
                        </div>

                        <!-- Transaction Reference -->
                        <div class="mb-4">
                            <label for="transaction_reference" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Transaction Reference / ID
                            </label>
                            <input type="text" name="transaction_reference" id="transaction_reference" value="{{ old('transaction_reference', $contribution->transaction_reference) }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $contribution->notes) }}</textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('contributions.show', $contribution) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                Update Contribution
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
