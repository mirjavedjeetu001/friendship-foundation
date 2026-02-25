<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- General Settings -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">General Settings</h3>
                        <form method="POST" action="{{ route('settings.update') }}">
                            @csrf
                            @method('PUT')

                            <!-- Minimum Contribution Amount -->
                            <div class="mb-4">
                                <label for="monthly_contribution_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Minimum Contribution Amount (৳) *
                                </label>
                                <input type="number" name="monthly_contribution_amount" id="monthly_contribution_amount" step="0.01" min="1" value="{{ old('monthly_contribution_amount', $settings->monthly_contribution_amount) }}" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Members can contribute any amount >= this minimum</p>
                            </div>

                            <!-- Due Day -->
                            <div class="mb-4">
                                <label for="due_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Due Day of Month *
                                </label>
                                <select name="due_day" id="due_day" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @for($i = 1; $i <= 28; $i++)
                                    <option value="{{ $i }}" {{ $settings->due_day == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Payments after this day will be marked as late</p>
                            </div>

                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                Save Settings
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Bank Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Bank Information</h3>
                        <form method="POST" action="{{ route('settings.update') }}">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="monthly_contribution_amount" value="{{ $settings->monthly_contribution_amount }}">
                            <input type="hidden" name="due_day" value="{{ $settings->due_day }}">

                            <!-- Bank Name -->
                            <div class="mb-4">
                                <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Bank Name
                                </label>
                                <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $settings->bank_name) }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Account Number -->
                            <div class="mb-4">
                                <label for="account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Account Number
                                </label>
                                <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $settings->account_number) }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Account Holder -->
                            <div class="mb-4">
                                <label for="account_holder" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Account Holder Name
                                </label>
                                <input type="text" name="account_holder" id="account_holder" value="{{ old('account_holder', $settings->account_holder) }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                Update Bank Info
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Current Balance -->
                <div class="bg-gradient-to-r from-green-500 to-teal-600 overflow-hidden shadow-sm rounded-lg lg:col-span-2">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Current Bank Balance</h3>
                                <p class="text-4xl font-bold">৳{{ number_format($settings->bank_balance, 2) }}</p>
                            </div>
                            <button onclick="document.getElementById('balance-modal').classList.remove('hidden')" class="px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition">
                                Manual Adjustment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Adjustment Modal -->
    <div id="balance-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Adjust Bank Balance</h3>
            <form action="{{ route('settings.balance') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="bank_balance" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Balance (৳) *</label>
                    <input type="number" name="bank_balance" id="bank_balance" step="0.01" min="0" value="{{ $settings->bank_balance }}" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                </div>
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Adjustment *</label>
                    <textarea name="reason" id="reason" rows="2" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" placeholder="e.g., Bank statement reconciliation"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('balance-modal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Update Balance
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
