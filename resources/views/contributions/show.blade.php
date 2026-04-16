<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Contribution Details') }}
            </h2>
            <a href="{{ route('contributions.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                ← Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <!-- Status Badge -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                ৳{{ number_format($contribution->amount, 2) }}
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400">{{ $contribution->month_year }}</p>
                        </div>
                        <span class="px-4 py-2 text-sm font-semibold rounded-full
                            @if($contribution->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                            @elseif($contribution->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @endif">
                            {{ ucfirst($contribution->status) }}
                        </span>
                    </div>

                    @if($contribution->is_late)
                    <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 mb-6">
                        <p class="text-red-700 dark:text-red-300">This contribution was submitted after the due date (Late Payment).</p>
                    </div>
                    @endif

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Member</label>
                            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $contribution->user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $contribution->user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Submitted By</label>
                            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $contribution->submitter->name }}</p>
                            @if(!$contribution->is_self_submitted)
                                <p class="text-sm text-indigo-500">(On behalf of member)</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Submission Date</label>
                            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $contribution->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Transaction Reference</label>
                            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $contribution->transaction_reference ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($contribution->notes)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Notes</label>
                        <p class="text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded">{{ $contribution->notes }}</p>
                    </div>
                    @endif

                    <!-- Payment Slip -->
                    @if($contribution->payment_slip)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Payment Slip</label>
                        <img src="{{ asset('storage/' . $contribution->payment_slip) }}" alt="Payment Slip" class="max-w-full h-auto rounded-lg shadow-lg border dark:border-gray-700">
                    </div>
                    @endif

                    <!-- Approval Info -->
                    @if($contribution->status !== 'pending' || $contribution->isAdminApproved())
                    <div class="border-t dark:border-gray-700 pt-6 mt-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Approval Progress
                        </h4>

                        {{-- Approval Steps --}}
                        <div class="space-y-4">
                            {{-- Step 1: Admin Approval --}}
                            <div class="flex items-start gap-3 p-3 rounded-lg {{ $contribution->isAdminApproved() ? 'bg-green-50 dark:bg-green-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20' }}">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $contribution->isAdminApproved() ? 'bg-green-500' : 'bg-yellow-500' }} text-white text-sm font-bold">
                                    @if($contribution->isAdminApproved()) ✓ @else 1 @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">Admin Approval</p>
                                    @if($contribution->isAdminApproved())
                                        <p class="text-sm text-green-600 dark:text-green-400">
                                            Approved by <strong>{{ $contribution->adminApprover->name }}</strong>
                                            on {{ $contribution->admin_approved_at->format('d M Y, h:i A') }}
                                        </p>
                                    @else
                                        <p class="text-sm text-yellow-600 dark:text-yellow-400">Waiting for admin approval</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Step 2: Accountant Approval --}}
                            <div class="flex items-start gap-3 p-3 rounded-lg {{ $contribution->isAccountantApproved() ? 'bg-green-50 dark:bg-green-900/20' : ($contribution->isAdminApproved() ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-gray-50 dark:bg-gray-700/30') }}">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $contribution->isAccountantApproved() ? 'bg-green-500' : ($contribution->isAdminApproved() ? 'bg-yellow-500' : 'bg-gray-400') }} text-white text-sm font-bold">
                                    @if($contribution->isAccountantApproved()) ✓ @else 2 @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">Accountant Approval</p>
                                    @if($contribution->isAccountantApproved())
                                        <p class="text-sm text-green-600 dark:text-green-400">
                                            Approved by <strong>{{ $contribution->accountantApprover->name }}</strong>
                                            on {{ $contribution->accountant_approved_at->format('d M Y, h:i A') }}
                                        </p>
                                    @elseif($contribution->isAdminApproved())
                                        <p class="text-sm text-yellow-600 dark:text-yellow-400">Waiting for accountant approval</p>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Locked - Admin approval needed first</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($contribution->rejection_reason)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Rejection Reason</label>
                            <p class="text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-3 rounded">{{ $contribution->rejection_reason }}</p>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Actions -->
                    @if($contribution->status === 'pending')
                    <div class="border-t dark:border-gray-700 pt-6 mt-6">
                        <div class="flex flex-wrap gap-3">
                            {{-- Admin Approve Button --}}
                            @if(auth()->user()->hasRole(['admin', 'super-admin']) && !$contribution->isAdminApproved())
                            <form action="{{ route('contributions.admin-approve', $contribution) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    Admin Approve
                                </button>
                            </form>
                            @endif

                            {{-- Accountant Approve Button --}}
                            @if(auth()->user()->hasRole(['accountant', 'super-admin']) && $contribution->isAdminApproved() && !$contribution->isAccountantApproved())
                            <form action="{{ route('contributions.accountant-approve', $contribution) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    Final Approve (Accountant)
                                </button>
                            </form>
                            @elseif(auth()->user()->hasRole(['accountant']) && !$contribution->isAdminApproved())
                            <button disabled class="px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed opacity-50" title="Admin approval needed first">
                                Final Approve (Locked)
                            </button>
                            @endif

                            @can('reject contributions')
                            <button onclick="document.getElementById('reject-modal').classList.remove('hidden')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Reject
                            </button>
                            @endcan

                            <a href="{{ route('contributions.edit', $contribution) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                Edit
                            </a>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    @can('reject contributions')
                    <div id="reject-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div class="relative top-20 mx-4 sm:mx-auto p-5 border w-auto sm:w-96 max-w-lg shadow-lg rounded-md bg-white dark:bg-gray-800">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Reject Contribution</h3>
                            <form action="{{ route('contributions.reject', $contribution) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Rejection *</label>
                                    <textarea name="rejection_reason" id="rejection_reason" rows="3" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" placeholder="Please provide a reason..."></textarea>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                        Reject
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
