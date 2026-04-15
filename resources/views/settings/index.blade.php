<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Branding Settings -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg lg:col-span-2">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Branding</h3>
                        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- App Name -->
                                <div>
                                    <label for="app_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Organization Name *
                                    </label>
                                    <input type="text" name="app_name" id="app_name" value="{{ old('app_name', $settings->app_name ?? 'Allied Group') }}" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This name will appear throughout the app</p>
                                </div>

                                <!-- Logo Upload -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Logo (Optional)
                                    </label>
                                    <div class="flex items-center gap-4">
                                        <div id="logo-preview" class="w-16 h-16 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300 dark:border-gray-500">
                                            @if($settings->logo)
                                                <img src="{{ $settings->logo_url }}" class="w-full h-full object-contain">
                                            @else
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <input type="file" name="logo" id="logo" accept="image/*" class="hidden" onchange="previewLogo(this)">
                                            <label for="logo" class="cursor-pointer px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                                {{ $settings->logo ? 'Change Logo' : 'Upload Logo' }}
                                            </label>
                                            @if($settings->logo)
                                                <button type="button" onclick="removeLogo()" class="ml-2 px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-md text-sm hover:bg-red-200 dark:hover:bg-red-900/50 transition">
                                                    Remove
                                                </button>
                                                <input type="hidden" name="remove_logo" id="remove_logo" value="0">
                                            @endif
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max 2MB, PNG/JPG/SVG</p>
                                        </div>
                                    </div>
                                    @error('logo')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                    Save Branding
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

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

                            <!-- Start Month -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Program Start Date *
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <select name="start_month" required class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        @php
                                            $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                                        @endphp
                                        @foreach($months as $index => $month)
                                            <option value="{{ $index + 1 }}" {{ ($settings->start_month ?? 4) == $index + 1 ? 'selected' : '' }}>{{ $month }}</option>
                                        @endforeach
                                    </select>
                                    <select name="start_year" required class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        @for($y = 2024; $y <= 2030; $y++)
                                            <option value="{{ $y }}" {{ ($settings->start_year ?? 2025) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Months before this will not be shown as due</p>
                            </div>

                            <!-- Force App Update Toggle -->
                            <div class="mb-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="force_app_update" value="1" {{ $settings->force_app_update ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Force App Update (Version < 9)
                                    </span>
                                </label>
                                <p class="mt-1 ml-6 text-sm text-gray-500 dark:text-gray-400">When enabled, users with version < 9 cannot dismiss the update popup</p>
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

                            <!-- Bank Name -->
                            <div class="mb-4">
                                <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Bank Name
                                </label>
                                <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $settings->bank_name) }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Account Holder -->
                            <div class="mb-4">
                                <label for="account_holder" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Account Holder Name
                                </label>
                                <input type="text" name="account_holder" id="account_holder" value="{{ old('account_holder', $settings->account_holder) }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Account Number -->
                            <div class="mb-4">
                                <label for="account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Account Number
                                </label>
                                <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $settings->account_number) }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Routing Number -->
                            <div class="mb-4">
                                <label for="routing_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Routing Number
                                </label>
                                <input type="text" name="routing_number" id="routing_number" value="{{ old('routing_number', $settings->routing_number) }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Branch -->
                            <div class="mb-4">
                                <label for="branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Branch Name
                                </label>
                                <input type="text" name="branch" id="branch" value="{{ old('branch', $settings->branch) }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                Update Bank Info
                            </button>
                        </form>
                    </div>
                </div>

                <!-- How to Transfer Money -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            <svg class="w-5 h-5 inline-block mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            How to Transfer Money
                        </h3>
                        
                        @if($settings->account_number)
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold text-gray-800 dark:text-white mb-3">Bank Account Details</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Account Name:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $settings->account_holder ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">A/C No:</span>
                                    <span class="font-medium text-gray-900 dark:text-white font-mono">{{ $settings->account_number }}</span>
                                </div>
                                @if($settings->routing_number)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Routing No:</span>
                                    <span class="font-medium text-gray-900 dark:text-white font-mono">{{ $settings->routing_number }}</span>
                                </div>
                                @endif
                                @if($settings->bank_name)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Bank:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $settings->bank_name }}</span>
                                </div>
                                @endif
                                @if($settings->branch)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Branch:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $settings->branch }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <h4 class="font-semibold text-gray-800 dark:text-white">Transfer Methods</h4>
                            
                            <div class="border-l-4 border-blue-500 pl-3 py-2 bg-blue-50 dark:bg-blue-900/30 rounded-r">
                                <p class="font-medium text-blue-800 dark:text-blue-300">From Other Bank (BEFTN/NPSB)</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Use Routing Number: <span class="font-mono font-bold">{{ $settings->routing_number ?? 'N/A' }}</span>
                                </p>
                            </div>
                            
                            <div class="border-l-4 border-green-500 pl-3 py-2 bg-green-50 dark:bg-green-900/30 rounded-r">
                                <p class="font-medium text-green-800 dark:text-green-300">From Mobile Banking</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Send Money → Bank Transfer → Enter A/C No
                                </p>
                            </div>
                            
                            <div class="border-l-4 border-orange-500 pl-3 py-2 bg-orange-50 dark:bg-orange-900/30 rounded-r">
                                <p class="font-medium text-orange-800 dark:text-orange-300">Cash Deposit</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Visit any branch with A/C number
                                </p>
                            </div>
                        </div>
                        @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-6">
                            Please add bank account details above
                        </p>
                        @endif
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
        <div class="relative top-20 mx-4 sm:mx-auto p-5 border w-auto sm:w-96 max-w-lg shadow-lg rounded-md bg-white dark:bg-gray-800">
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

    <script>
        function previewLogo(input) {
            const preview = document.getElementById('logo-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-contain">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        function removeLogo() {
            document.getElementById('remove_logo').value = '1';
            document.getElementById('logo-preview').innerHTML = '<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
        }
    </script>
</x-app-layout>
