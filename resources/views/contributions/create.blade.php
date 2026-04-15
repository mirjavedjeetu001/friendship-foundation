<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Submit New Contribution') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <!-- Bank Info Card -->
                    <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-purple-600 rounded-xl p-4 mb-4 text-white shadow-lg">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 10V17H6V10H4M10 10V17H12V10H10M2 22H22V19H2V22M16 10V17H18V10H16M12 1L2 6V8H22V6L12 1Z"/>
                            </svg>
                            <span class="font-bold">Bank Transfer Details</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="bg-white/10 rounded-lg p-2.5">
                                <p class="text-indigo-200 text-xs uppercase tracking-wider">Account Holder</p>
                                <p class="font-bold">{{ $settings->account_holder ?? 'Allied Group' }}</p>
                            </div>
                            <div class="bg-white/10 rounded-lg p-2.5">
                                <p class="text-indigo-200 text-xs uppercase tracking-wider">Account Number</p>
                                <p class="font-mono font-bold">{{ $settings->account_number ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-white/10 rounded-lg p-2.5">
                                <p class="text-indigo-200 text-xs uppercase tracking-wider">Bank</p>
                                <p class="font-semibold">{{ $settings->bank_name ?? 'Bank' }}</p>
                            </div>
                            @if($settings->routing_number)
                            <div class="bg-white/10 rounded-lg p-2.5">
                                <p class="text-indigo-200 text-xs uppercase tracking-wider">Routing</p>
                                <p class="font-mono font-semibold">{{ $settings->routing_number }}</p>
                            </div>
                            @endif
                            @if($settings->branch)
                            <div class="bg-white/10 rounded-lg p-2.5 col-span-2">
                                <p class="text-indigo-200 text-xs uppercase tracking-wider">Branch</p>
                                <p class="font-semibold">{{ $settings->branch }}</p>
                            </div>
                            @endif
                        </div>
                        
                        <div class="mt-3 pt-3 border-t border-white/20 flex items-center justify-between">
                            <span class="text-indigo-100 text-sm">Monthly Contribution</span>
                            <span class="text-xl font-bold">৳{{ number_format($settings->monthly_contribution_amount, 2) }}</span>
                        </div>
                    </div>
                    
                    <!-- How to Deposit Money (Collapsible) -->
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-gray-700 dark:to-gray-600 rounded-xl mb-5 border border-emerald-200 dark:border-gray-500 overflow-hidden shadow-sm">
                        <button type="button" onclick="document.getElementById('deposit-guide').classList.toggle('hidden'); document.getElementById('collapse-icon').classList.toggle('rotate-180'); this.querySelector('.tap-hint').classList.toggle('hidden');" class="w-full flex items-center justify-between p-4 text-left hover:bg-emerald-100/50 dark:hover:bg-gray-600 transition-colors active:bg-emerald-100 dark:active:bg-gray-500">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 dark:text-white text-base">How to Deposit Money</p>
                                    <p class="tap-hint text-sm text-emerald-600 dark:text-emerald-400">Tap to view guide</p>
                                </div>
                            </div>
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-gray-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg id="collapse-icon" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>
                        
                        <div id="deposit-guide" class="hidden px-4 pb-4">
                            <div class="space-y-3">
                                <!-- Method 1: Other Bank Transfer -->
                                <div class="flex items-start space-x-3 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">1</div>
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-white">From Other Bank (BEFTN/NPSB)</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            Use your bank app/internet banking → Fund Transfer → Enter A/C: <span class="font-mono font-bold">{{ $settings->account_number }}</span>
                                            @if($settings->routing_number)
                                            <br>Routing No: <span class="font-mono font-bold">{{ $settings->routing_number }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Method 2: Mobile Banking -->
                                <div class="flex items-start space-x-3 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                                    <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm">2</div>
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-white">From Mobile Banking (bKash/Nagad/Rocket)</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            Send Money → Bank Transfer → Select {{ $settings->bank_name ?? 'Bank' }} → Enter A/C: <span class="font-mono font-bold">{{ $settings->account_number }}</span>
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Method 3: Branch Deposit -->
                                <div class="flex items-start space-x-3 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                                    <div class="flex-shrink-0 w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold text-sm">3</div>
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-white">Cash Deposit at Branch</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            Visit any {{ $settings->bank_name ?? 'bank' }} branch → Fill deposit slip with A/C: <span class="font-mono font-bold">{{ $settings->account_number }}</span>
                                            @if($settings->branch)
                                            <br>Nearest Branch: <span class="font-semibold">{{ $settings->branch }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg border-l-4 border-yellow-500">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    <strong>Important:</strong> After payment, upload the receipt/screenshot below as proof.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('contributions.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Member Selection -->
                        <div class="mb-4">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Contributing Member *
                            </label>
                            @if($canContributeForOthers)
                            <select name="user_id" id="user_id" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="{{ auth()->id() }}">{{ auth()->user()->name }} (Myself)</option>
                                @foreach($users as $user)
                                    @if($user->id !== auth()->id())
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You can submit contributions on behalf of other members.</p>
                            @else
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            <input type="text" value="{{ auth()->user()->name }}" disabled class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-gray-100">
                            @endif
                        </div>

                        <!-- Month/Year Selection -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Month *
                                </label>
                                <select name="month" id="month" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ (old('month', $currentMonth) == $i) ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                    @endfor
                                </select>
                                <p id="month-warning" class="mt-1 text-sm text-red-500 hidden">This month is already paid!</p>
                            </div>
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Year *
                                </label>
                                <select name="year" id="year" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @for($i = date('Y'); $i >= 2020; $i--)
                                        <option value="{{ $i }}" {{ (old('year', $currentYear) == $i) ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        
                        <!-- Paid Months Data for JS -->
                        <script>
                            const paidMonthsByUser = @json($paidMonthsByUser);
                            
                            function updateMonthOptions() {
                                const userId = document.getElementById('user_id')?.value || {{ auth()->id() }};
                                const year = document.getElementById('year').value;
                                const monthSelect = document.getElementById('month');
                                const warning = document.getElementById('month-warning');
                                const paidMonths = paidMonthsByUser[userId] || [];
                                
                                // Reset all options
                                for (let i = 0; i < monthSelect.options.length; i++) {
                                    const option = monthSelect.options[i];
                                    const monthKey = year + '-' + option.value;
                                    
                                    if (paidMonths.includes(monthKey)) {
                                        option.disabled = true;
                                        option.text = option.text.replace(' (Paid)', '') + ' (Paid)';
                                    } else {
                                        option.disabled = false;
                                        option.text = option.text.replace(' (Paid)', '');
                                    }
                                }
                                
                                // Check if current selection is paid
                                const currentKey = year + '-' + monthSelect.value;
                                if (paidMonths.includes(currentKey)) {
                                    warning.classList.remove('hidden');
                                } else {
                                    warning.classList.add('hidden');
                                }
                            }
                            
                            document.addEventListener('DOMContentLoaded', function() {
                                const userSelect = document.getElementById('user_id');
                                const yearSelect = document.getElementById('year');
                                const monthSelect = document.getElementById('month');
                                
                                if (userSelect) userSelect.addEventListener('change', updateMonthOptions);
                                yearSelect.addEventListener('change', updateMonthOptions);
                                monthSelect.addEventListener('change', updateMonthOptions);
                                
                                // Initial update
                                updateMonthOptions();
                            });
                        </script>

                        <!-- Amount -->
                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Amount (৳) *
                            </label>
                            <input type="number" name="amount" id="amount" step="0.01" min="{{ $settings->monthly_contribution_amount }}" value="{{ old('amount', $settings->monthly_contribution_amount) }}" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Minimum amount: ৳{{ number_format($settings->monthly_contribution_amount, 2) }}</p>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Slip -->
                        <div class="mb-4">
                            <label for="payment_slip" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Payment Slip / Screenshot *
                            </label>
                            <input type="file" name="payment_slip" id="payment_slip" accept="image/*" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload the payment receipt/screenshot (Max: 5MB)</p>
                        </div>

                        <!-- Transaction Reference -->
                        <div class="mb-4">
                            <label for="transaction_reference" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Transaction Reference / ID
                            </label>
                            <input type="text" name="transaction_reference" id="transaction_reference" value="{{ old('transaction_reference') }}" placeholder="e.g., TRX123456789" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3" placeholder="Any additional information..." class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Due Date Warning -->
                        @if(date('d') > $settings->due_day)
                        <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                        <strong>Note:</strong> The due date ({{ $settings->due_day }}th) has passed. This contribution will be marked as late.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('contributions.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                Submit Contribution
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
