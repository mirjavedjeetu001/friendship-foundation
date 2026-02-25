<?php

namespace App\Http\Controllers;

use App\Models\MonthlySetting;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Withdrawal::with(['requester', 'approver']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->latest()->paginate(15);
        $settings = MonthlySetting::getSettings();

        return view('withdrawals.index', compact('withdrawals', 'settings'));
    }

    /**
     * Display pending withdrawals for approval
     */
    public function pending()
    {
        $withdrawals = Withdrawal::with('requester')
            ->pending()
            ->latest()
            ->paginate(15);

        return view('withdrawals.pending', compact('withdrawals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $settings = MonthlySetting::getSettings();
        return view('withdrawals.create', compact('settings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'purpose' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'receipt' => 'nullable|image|max:5120',
            'withdrawal_date' => 'required|date',
        ]);

        // Handle file upload
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        Withdrawal::create([
            'requested_by' => auth()->id(),
            'amount' => $validated['amount'],
            'purpose' => $validated['purpose'],
            'description' => $validated['description'],
            'receipt' => $receiptPath,
            'withdrawal_date' => $validated['withdrawal_date'],
            'status' => 'pending',
        ]);

        return redirect()->route('withdrawals.index')
            ->with('success', 'Withdrawal request submitted successfully! Awaiting approval.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load(['requester', 'approver']);
        return view('withdrawals.show', compact('withdrawal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Only pending withdrawals can be edited.');
        }

        return view('withdrawals.edit', compact('withdrawal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Only pending withdrawals can be updated.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'purpose' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'receipt' => 'nullable|image|max:5120',
            'withdrawal_date' => 'required|date',
        ]);

        // Handle file upload
        if ($request->hasFile('receipt')) {
            if ($withdrawal->receipt) {
                Storage::disk('public')->delete($withdrawal->receipt);
            }
            $validated['receipt'] = $request->file('receipt')->store('receipts', 'public');
        }

        $withdrawal->update($validated);

        return redirect()->route('withdrawals.show', $withdrawal)
            ->with('success', 'Withdrawal request updated successfully!');
    }

    /**
     * Approve a withdrawal
     */
    public function approve(Withdrawal $withdrawal)
    {
        if (!auth()->user()->can('approve withdrawals')) {
            return back()->with('error', 'You are not authorized to approve withdrawals.');
        }

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Only pending withdrawals can be approved.');
        }

        // Check if there's sufficient balance
        $settings = MonthlySetting::getSettings();
        if ($settings->bank_balance < $withdrawal->amount) {
            return back()->with('error', 'Insufficient balance to approve this withdrawal.');
        }

        $withdrawal->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Update bank balance
        $settings->updateBalance($withdrawal->amount, 'subtract');

        return back()->with('success', 'Withdrawal approved successfully!');
    }

    /**
     * Reject a withdrawal
     */
    public function reject(Request $request, Withdrawal $withdrawal)
    {
        if (!auth()->user()->can('reject withdrawals')) {
            return back()->with('error', 'You are not authorized to reject withdrawals.');
        }

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Only pending withdrawals can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $withdrawal->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Withdrawal rejected.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Withdrawal $withdrawal)
    {
        if ($withdrawal->status === 'approved') {
            return back()->with('error', 'Approved withdrawals cannot be deleted.');
        }

        if ($withdrawal->receipt) {
            Storage::disk('public')->delete($withdrawal->receipt);
        }

        $withdrawal->delete();

        return redirect()->route('withdrawals.index')
            ->with('success', 'Withdrawal deleted successfully!');
    }
}
