<?php

namespace App\Http\Controllers;

use App\Models\Expense;
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
        $query = Expense::with(['creator', 'approver'])
            ->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
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

        return view('expenses.index', compact('expenses', 'totalApproved', 'totalPending', 'totalThisMonth'));
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
                'description' => $expenseData['description'] ?? null,
                'receipt' => $receiptPath,
                'status' => 'pending',
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

        $expense->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'fund_source' => $request->fund_source,
            'fund_source_note' => $request->fund_source_note,
        ]);

        return back()->with('success', 'Expense approved successfully.');
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
}
