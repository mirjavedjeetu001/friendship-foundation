<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\MonthlySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display all expenses
     */
    public function index(Request $request)
    {
        $query = Expense::with(['creator', 'approver', 'settler'])
            ->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by settlement status
        if ($request->filled('settlement') && $request->settlement !== 'all') {
            $query->where('settlement_status', $request->settlement);
        }

        // Filter by payment type
        if ($request->filled('payment_type') && $request->payment_type !== 'all') {
            $query->where('payment_type', $request->payment_type);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->whereMonth('expense_date', $request->month);
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->whereYear('expense_date', $request->year);
        }

        // Filter by fund source
        if ($request->filled('fund_source') && $request->fund_source !== 'all') {
            $query->where('fund_source', $request->fund_source);
        }

        $expenses = $query->paginate(15)->withQueryString();

        // Calculate totals
        $totalApproved = Expense::approved()->sum('amount');
        $totalPending = Expense::pending()->sum('amount');
        $totalThisMonth = Expense::approved()
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
        
        // Settlement totals
        $totalSettled = Expense::settledFromBank()->sum('amount');
        $totalPendingSettlement = Expense::pendingSettlement()->sum('amount');
        
        // Bank balance
        $settings = MonthlySetting::getSettings();
        $bankBalance = $settings->bank_balance;

        return view('expenses.index', compact(
            'expenses', 
            'totalApproved', 
            'totalPending', 
            'totalThisMonth',
            'totalSettled',
            'totalPendingSettlement',
            'bankBalance'
        ));
    }

    /**
     * Show pending expenses for approval
     */
    public function pending()
    {
        $expenses = Expense::with(['creator'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('expenses.pending', compact('expenses'));
    }

    /**
     * Show form to create new expense
     */
    public function create()
    {
        return view('expenses.create');
    }

    /**
     * Store new expense(s)
     */
    public function store(Request $request)
    {
        $request->validate([
            'expenses' => 'required|array|min:1',
            'expenses.*.expense_date' => 'required|date',
            'expenses.*.purpose' => 'required|string|max:255',
            'expenses.*.spent_by' => 'required|string|max:255',
            'expenses.*.amount' => 'required|numeric|min:1',
            'expenses.*.payment_type' => 'required|in:cash,bank',
            'expenses.*.description' => 'nullable|string|max:1000',
            'expenses.*.receipt' => 'nullable|image|max:5120', // 5MB max
        ]);

        $createdCount = 0;

        foreach ($request->expenses as $index => $expenseData) {
            $receiptPath = null;
            
            // Handle receipt upload
            if (isset($expenseData['receipt']) && $expenseData['receipt']) {
                $receiptPath = $expenseData['receipt']->store('receipts', 'public');
            }

            Expense::create([
                'expense_date' => $expenseData['expense_date'],
                'purpose' => $expenseData['purpose'],
                'spent_by' => $expenseData['spent_by'],
                'amount' => $expenseData['amount'],
                'payment_type' => $expenseData['payment_type'],
                'description' => $expenseData['description'] ?? null,
                'receipt' => $receiptPath,
                'status' => 'pending',
                'settlement_status' => 'not_applicable', // Will be updated on approval
                'created_by' => Auth::id(),
            ]);

            $createdCount++;
        }

        return redirect()->route('expenses.index')
            ->with('success', $createdCount . ' expense(s) submitted for approval.');
    }

    /**
     * Show a specific expense
     */
    public function show(Expense $expense)
    {
        $expense->load(['creator', 'approver']);
        return view('expenses.show', compact('expense'));
    }

    /**
     * Approve an expense
     */
    public function approve(Request $request, Expense $expense)
    {
        if (!$expense->isPending()) {
            return back()->with('error', 'This expense has already been processed.');
        }

        $request->validate([
            'fund_source' => 'required|in:monthly_savings,manual',
            'fund_source_note' => 'required_if:fund_source,manual|nullable|string|max:500',
        ], [
            'fund_source.required' => 'Please select a fund source.',
            'fund_source_note.required_if' => 'Please provide a note for manual adjustment.',
        ]);

        // Determine settlement status based on payment type
        $settlementStatus = $expense->payment_type === 'bank' ? 'not_applicable' : 'pending';

        $expense->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'fund_source' => $request->fund_source,
            'fund_source_note' => $request->fund_source_note,
            'settlement_status' => $settlementStatus,
        ]);

        // For bank payments, deduct from bank balance immediately
        if ($expense->payment_type === 'bank') {
            $settings = MonthlySetting::getSettings();
            $settings->updateBalance($expense->amount, 'subtract');
            
            // Mark as settled since it's direct bank payment
            $expense->update([
                'settlement_status' => 'not_applicable',
                'settled_by' => Auth::id(),
                'settled_at' => now(),
                'settlement_note' => 'Direct bank payment - auto settled on approval',
            ]);
        }

        $message = 'Expense approved successfully.';
        if ($expense->payment_type === 'cash') {
            $message .= ' Cash expense pending bank settlement.';
        } else {
            $message .= ' Bank balance updated.';
        }

        return back()->with('success', $message);
    }

    /**
     * Reject an expense
     */
    public function reject(Request $request, Expense $expense)
    {
        if (!$expense->isPending()) {
            return back()->with('error', 'This expense has already been processed.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $expense->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Expense rejected.');
    }

    /**
     * Delete an expense (only if pending and own expense)
     */
    public function destroy(Expense $expense)
    {
        // Only the creator can delete, and only if pending
        if ($expense->created_by !== Auth::id() && !Auth::user()->hasAnyRole(['super-admin', 'admin'])) {
            return back()->with('error', 'You cannot delete this expense.');
        }

        if (!$expense->isPending() && !Auth::user()->hasAnyRole(['super-admin', 'admin'])) {
            return back()->with('error', 'Only pending expenses can be deleted.');
        }

        // Delete receipt if exists
        if ($expense->receipt) {
            Storage::disk('public')->delete($expense->receipt);
        }

        $expense->delete();

        return back()->with('success', 'Expense deleted successfully.');
    }

    /**
     * Show expenses pending bank settlement
     */
    public function pendingSettlement()
    {
        $expenses = Expense::with(['creator', 'approver'])
            ->pendingSettlement()
            ->orderBy('approved_at', 'desc')
            ->paginate(15);

        $totalPendingSettlement = Expense::pendingSettlement()->sum('amount');
        
        $settings = MonthlySetting::getSettings();
        $bankBalance = $settings->bank_balance;

        return view('expenses.pending-settlement', compact('expenses', 'totalPendingSettlement', 'bankBalance'));
    }

    /**
     * Settle a cash expense from bank
     */
    public function settle(Request $request, Expense $expense)
    {
        // Check if expense can be settled
        if (!$expense->isApproved()) {
            return back()->with('error', 'Only approved expenses can be settled.');
        }

        if ($expense->payment_type !== 'cash') {
            return back()->with('error', 'Only cash expenses need bank settlement.');
        }

        if ($expense->settlement_status === 'settled') {
            return back()->with('error', 'This expense is already settled.');
        }

        $request->validate([
            'settlement_note' => 'nullable|string|max:500',
        ]);

        // Deduct from bank balance
        $settings = MonthlySetting::getSettings();
        $settings->updateBalance($expense->amount, 'subtract');

        // Update expense
        $expense->update([
            'settlement_status' => 'settled',
            'settled_by' => Auth::id(),
            'settled_at' => now(),
            'settlement_note' => $request->settlement_note ?? 'Settled from bank',
        ]);

        return back()->with('success', 'Expense settled from bank. Bank balance updated.');
    }

    /**
     * Bulk settle multiple expenses
     */
    public function bulkSettle(Request $request)
    {
        $request->validate([
            'expense_ids' => 'required|array|min:1',
            'expense_ids.*' => 'exists:expenses,id',
            'settlement_note' => 'nullable|string|max:500',
        ]);

        $expenses = Expense::whereIn('id', $request->expense_ids)
            ->where('status', 'approved')
            ->where('payment_type', 'cash')
            ->where('settlement_status', 'pending')
            ->get();

        if ($expenses->isEmpty()) {
            return back()->with('error', 'No valid expenses to settle.');
        }

        $totalAmount = $expenses->sum('amount');
        $settings = MonthlySetting::getSettings();
        
        // Deduct total from bank balance
        $settings->updateBalance($totalAmount, 'subtract');

        // Update all expenses
        foreach ($expenses as $expense) {
            $expense->update([
                'settlement_status' => 'settled',
                'settled_by' => Auth::id(),
                'settled_at' => now(),
                'settlement_note' => $request->settlement_note ?? 'Bulk settled from bank',
            ]);
        }

        return back()->with('success', count($expenses) . ' expense(s) settled. ৳' . number_format($totalAmount, 2) . ' deducted from bank.');
    }
}
