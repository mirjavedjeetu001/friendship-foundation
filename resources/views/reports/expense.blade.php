<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Expense Report') }}
            </h2>
            @if($expenses->count() > 0)
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition print:hidden">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print / Download
            </button>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6 print:hidden">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.expense') }}" class="flex flex-wrap items-end gap-4">
                        <div>
                            <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                            <select name="month" id="month" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm">
                                <option value="">All Months</option>
                                @php $months = ['January','February','March','April','May','June','July','August','September','October','November','December']; @endphp
                                @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $months[$m-1] }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                            <select name="year" id="year" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm">
                                @for($y = date('Y'); $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" id="status" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm">
                                <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All</option>
                            </select>
                        </div>
                        <div>
                            <label for="payment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment</label>
                            <select name="payment_type" id="payment_type" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm">
                                <option value="">All Types</option>
                                <option value="cash" {{ $paymentType == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank" {{ $paymentType == 'bank' ? 'selected' : '' }}>Bank</option>
                            </select>
                        </div>
                        <div>
                            <label for="settlement_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Settlement</label>
                            <select name="settlement_status" id="settlement_status" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm">
                                <option value="">All</option>
                                <option value="settled" {{ $settlementStatus == 'settled' ? 'selected' : '' }}>Settled</option>
                                <option value="pending" {{ $settlementStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-sm">
                            Filter
                        </button>
                        <a href="{{ route('reports.expense') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition text-sm">
                            Reset
                        </a>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-sm p-4 text-white">
                    <p class="text-xs opacity-80">Total Expenses</p>
                    <p class="text-xl font-bold mt-1">৳{{ number_format($totalExpenses, 0) }}</p>
                    <p class="text-xs opacity-70 mt-1">{{ $expenses->where('status', 'approved')->count() }} items</p>
                </div>
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg shadow-sm p-4 text-white">
                    <p class="text-xs opacity-80">Cash Expenses</p>
                    <p class="text-xl font-bold mt-1">৳{{ number_format($cashExpenses, 0) }}</p>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-sm p-4 text-white">
                    <p class="text-xs opacity-80">Bank Expenses</p>
                    <p class="text-xl font-bold mt-1">৳{{ number_format($bankExpenses, 0) }}</p>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-sm p-4 text-white">
                    <p class="text-xs opacity-80">Settled</p>
                    <p class="text-xl font-bold mt-1">৳{{ number_format($settledAmount, 0) }}</p>
                </div>
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-sm p-4 text-white">
                    <p class="text-xs opacity-80">Pending Settlement</p>
                    <p class="text-xl font-bold mt-1">৳{{ number_format($pendingSettlement, 0) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Monthly Breakdown -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Monthly Breakdown ({{ $year }})</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Month</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @php
                                        $monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                                        $yearTotal = 0;
                                    @endphp
                                    @for($m = 1; $m <= 12; $m++)
                                    @php $yearTotal += $monthlyTotals[$m]; @endphp
                                    <tr class="{{ $m > date('n') && $year == date('Y') ? 'opacity-50' : '' }}">
                                        <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $monthNames[$m-1] }}</td>
                                        <td class="px-3 py-2 text-sm text-right {{ $monthlyTotals[$m] > 0 ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                            {{ $monthlyTotals[$m] > 0 ? '৳' . number_format($monthlyTotals[$m], 0) : '-' }}
                                        </td>
                                    </tr>
                                    @endfor
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <td class="px-3 py-2 text-sm font-bold text-gray-900 dark:text-gray-100">Total</td>
                                        <td class="px-3 py-2 text-sm text-right font-bold text-red-600">৳{{ number_format($yearTotal, 0) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Expense List -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Expense Details
                            <span class="text-sm font-normal text-gray-500">({{ $expenses->count() }} records)</span>
                        </h3>
                        
                        @if($expenses->count() > 0)
                        <!-- Desktop Table -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Purpose</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Spent By</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Payment</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Settlement</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($expenses as $expense)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-3 py-2 text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                            {{ $expense->expense_date->format('d M Y') }}
                                        </td>
                                        <td class="px-3 py-2 text-gray-900 dark:text-gray-100 max-w-[200px] truncate" title="{{ $expense->purpose }}">
                                            {{ $expense->purpose }}
                                        </td>
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                            {{ $expense->spent_by }}
                                        </td>
                                        <td class="px-3 py-2 text-right font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                            ৳{{ number_format($expense->amount, 0) }}
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $expense->payment_type === 'cash' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-400' }}">
                                                {{ ucfirst($expense->payment_type) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            @if($expense->status === 'approved')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-400">Approved</span>
                                            @elseif($expense->status === 'rejected')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-400">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            @if($expense->status === 'approved')
                                                @if($expense->payment_type === 'bank')
                                                <span class="text-xs text-gray-400">N/A</span>
                                                @elseif($expense->settlement_status === 'settled')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-400">Settled</span>
                                                @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-400">Pending</span>
                                                @endif
                                            @else
                                            <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <td colspan="3" class="px-3 py-2 text-sm font-bold text-gray-900 dark:text-gray-100">Total (Approved)</td>
                                        <td class="px-3 py-2 text-sm text-right font-bold text-red-600">৳{{ number_format($totalExpenses, 0) }}</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Mobile Card Layout -->
                        <div class="sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($expenses as $expense)
                            <div class="p-3">
                                <div class="flex items-start justify-between mb-1.5">
                                    <div class="flex-1 min-w-0 mr-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $expense->purpose }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $expense->expense_date->format('d M Y') }} · {{ $expense->spent_by }}</p>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100 whitespace-nowrap">৳{{ number_format($expense->amount, 0) }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $expense->payment_type === 'cash' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-400' }}">
                                        {{ ucfirst($expense->payment_type) }}
                                    </span>
                                    @if($expense->status === 'approved')
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-400">Approved</span>
                                    @elseif($expense->status === 'rejected')
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-400">Rejected</span>
                                    @endif
                                    @if($expense->status === 'approved')
                                        @if($expense->payment_type === 'bank')
                                        @elseif($expense->settlement_status === 'settled')
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-400">Settled</span>
                                        @else
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-400">Pending</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            @endforeach
                            <div class="p-3 bg-gray-50 dark:bg-gray-700">
                                <div class="flex justify-between">
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">Total (Approved)</span>
                                    <span class="text-sm font-bold text-red-600">৳{{ number_format($totalExpenses, 0) }}</span>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                            </svg>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">No expenses found for the selected filters.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body { background: white !important; }
            .dark body { background: white !important; }
            nav, .print\\:hidden, header button { display: none !important; }
            .dark .bg-gray-800 { background: white !important; }
            .dark .text-gray-100, .dark .text-gray-200, .dark .text-gray-300 { color: #111 !important; }
            .dark .text-gray-400, .dark .text-gray-500 { color: #666 !important; }
            .dark .divide-gray-700 > * + * { border-color: #ddd !important; }
            .dark .bg-gray-700 { background: #f3f4f6 !important; }
            .shadow-sm { box-shadow: none !important; }
            .rounded-lg { border: 1px solid #ddd !important; }
        }
    </style>
</x-app-layout>
