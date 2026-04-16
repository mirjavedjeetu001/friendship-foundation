<?php

namespace App\Http\Controllers;

use App\Mail\PaymentApprovedMail;
use App\Models\Contribution;
use App\Models\MonthlySetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contribution::with(['user', 'submitter', 'approver', 'adminApprover', 'accountantApprover']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by month/year
        if ($request->has('month') && $request->has('year')) {
            $query->forMonth($request->month, $request->year);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $contributions = $query->latest()->paginate(15);
        $users = User::all();
        $settings = MonthlySetting::getSettings();

        return view('contributions.index', compact('contributions', 'users', 'settings'));
    }

    /**
     * Display pending contributions for approval
     */
    public function pending()
    {
        $contributions = Contribution::with(['user', 'submitter', 'adminApprover', 'accountantApprover'])
            ->pending()
            ->latest()
            ->paginate(15);

        return view('contributions.pending', compact('contributions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $settings = MonthlySetting::getSettings();
        // All users are members except super-admin
        $users = User::where('email', '!=', 'alliedgroup@gmail.com')
            ->where('status', 'approved')
            ->get();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Check if current user can contribute for others
        $canContributeForOthers = auth()->user()->can('create contributions for others');

        // Get paid months for each user for the dropdown
        $paidMonthsByUser = [];
        foreach ($users as $user) {
            $paidMonthsByUser[$user->id] = Contribution::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'pending'])
                ->get()
                ->map(function ($c) {
                    return $c->year . '-' . $c->month;
                })
                ->toArray();
        }

        return view('contributions.create', compact('settings', 'users', 'currentMonth', 'currentYear', 'canContributeForOthers', 'paidMonthsByUser'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $settings = MonthlySetting::getSettings();
        $minAmount = $settings->monthly_contribution_amount;

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:' . $minAmount,
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
            'payment_slip' => 'required|image|max:5120',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.min' => 'The contribution amount must be at least ৳' . number_format($minAmount, 2),
        ]);

        // Check if user is submitting for themselves or someone else
        $userId = $validated['user_id'];

        if ($userId != auth()->id() && !auth()->user()->can('create contributions for others')) {
            return back()->with('error', 'You are not authorized to submit contributions for others.');
        }

        // Check if contribution already exists for this month (approved or pending)
        $existing = Contribution::where('user_id', $userId)
            ->forMonth($validated['month'], $validated['year'])
            ->whereIn('status', ['approved', 'pending'])
            ->first();

        if ($existing) {
            $statusText = $existing->status === 'approved' ? 'approved' : 'pending approval';
            return back()->with('error', 'A contribution for this month is already ' . $statusText . '. You cannot submit another one.');
        }

        // Check if it's late
        $dueDate = Carbon::create($validated['year'], $validated['month'], $settings->due_day);
        $isLate = Carbon::now()->greaterThan($dueDate);

        // Handle file upload
        $paymentSlipPath = null;
        if ($request->hasFile('payment_slip')) {
            $paymentSlipPath = $request->file('payment_slip')->store('payment-slips', 'public');
        }

        $contribution = Contribution::create([
            'user_id' => $userId,
            'submitted_by' => auth()->id(),
            'amount' => $validated['amount'],
            'month' => $validated['month'],
            'year' => $validated['year'],
            'payment_slip' => $paymentSlipPath,
            'transaction_reference' => $validated['transaction_reference'],
            'notes' => $validated['notes'],
            'status' => 'pending',
            'is_late' => $isLate,
        ]);

        // Send email notification to admins and accountants
        try {
            $contribution->load('user', 'submitter');
            $notifyUsers = User::role(['admin', 'super-admin', 'accountant'])->get();
            foreach ($notifyUsers as $notifyUser) {
                Mail::to($notifyUser->email)->send(new \App\Mail\ContributionSubmittedMail($contribution, $notifyUser));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send contribution notification email: ' . $e->getMessage());
        }

        return redirect()->route('contributions.index')
            ->with('success', 'Contribution submitted successfully! Awaiting approval.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contribution $contribution)
    {
        $contribution->load(['user', 'submitter', 'approver', 'adminApprover', 'accountantApprover']);
        return view('contributions.show', compact('contribution'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contribution $contribution)
    {
        if ($contribution->status !== 'pending') {
            return back()->with('error', 'Only pending contributions can be edited.');
        }

        $settings = MonthlySetting::getSettings();
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'member');
        })->get();

        return view('contributions.edit', compact('contribution', 'settings', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contribution $contribution)
    {
        if ($contribution->status !== 'pending') {
            return back()->with('error', 'Only pending contributions can be updated.');
        }

        $settings = MonthlySetting::getSettings();
        $minAmount = $settings->monthly_contribution_amount;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:' . $minAmount,
            'payment_slip' => 'nullable|image|max:5120',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.min' => 'The contribution amount must be at least ৳' . number_format($minAmount, 2),
        ]);

        // Handle file upload
        if ($request->hasFile('payment_slip')) {
            // Delete old file
            if ($contribution->payment_slip) {
                Storage::disk('public')->delete($contribution->payment_slip);
            }
            $validated['payment_slip'] = $request->file('payment_slip')->store('payment-slips', 'public');
        }

        $contribution->update($validated);

        return redirect()->route('contributions.show', $contribution)
            ->with('success', 'Contribution updated successfully!');
    }

    /**
     * Admin approves a contribution (Step 1)
     */
    public function adminApprove(Contribution $contribution)
    {
        $user = auth()->user();
        
        if (!$user->hasRole(['admin', 'super-admin'])) {
            return back()->with('error', 'Only admins can perform this approval.');
        }

        if ($contribution->status !== 'pending') {
            return back()->with('error', 'Only pending contributions can be approved.');
        }

        if ($contribution->isAdminApproved()) {
            return back()->with('error', 'Admin has already approved this contribution.');
        }

        $contribution->update([
            'admin_approved_by' => $user->id,
            'admin_approved_at' => now(),
        ]);

        // Notify accountants that admin approved - they can now approve
        try {
            $accountants = User::role(['accountant'])->get();
            foreach ($accountants as $accountant) {
                Mail::to($accountant->email)->send(new \App\Mail\AdminApprovedContributionMail($contribution, $accountant, $user));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send admin approval notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Admin approval given! Waiting for accountant approval.');
    }

    /**
     * Accountant approves a contribution (Step 2 - Final)
     */
    public function accountantApprove(Contribution $contribution)
    {
        $user = auth()->user();
        
        if (!$user->hasRole(['accountant', 'super-admin'])) {
            return back()->with('error', 'Only accountants can perform this approval.');
        }

        if ($contribution->status !== 'pending') {
            return back()->with('error', 'Only pending contributions can be approved.');
        }

        if (!$contribution->isAdminApproved()) {
            return back()->with('error', 'Admin must approve first before accountant can approve.');
        }

        if ($contribution->isAccountantApproved()) {
            return back()->with('error', 'Accountant has already approved this contribution.');
        }

        // Final approval
        $contribution->update([
            'accountant_approved_by' => $user->id,
            'accountant_approved_at' => now(),
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        // Update bank balance
        $settings = MonthlySetting::getSettings();
        $settings->updateBalance($contribution->amount, 'add');

        // Send final approval email to member
        try {
            $contribution->load('user');
            Mail::to($contribution->user->email)->send(new PaymentApprovedMail($contribution->user, $contribution));
        } catch (\Exception $e) {
            \Log::error('Failed to send payment approved email: ' . $e->getMessage());
        }

        return back()->with('success', 'Contribution fully approved! Balance updated.');
    }

    /**
     * Legacy approve method - redirects based on role
     */
    public function approve(Contribution $contribution)
    {
        $user = auth()->user();
        
        if ($user->hasRole(['admin', 'super-admin']) && !$contribution->isAdminApproved()) {
            return $this->adminApprove($contribution);
        }
        
        if ($user->hasRole(['accountant', 'super-admin']) && $contribution->isAdminApproved()) {
            return $this->accountantApprove($contribution);
        }

        return back()->with('error', 'You cannot approve this contribution at this stage.');
    }

    /**
     * Reject a contribution
     */
    public function reject(Request $request, Contribution $contribution)
    {
        if (!auth()->user()->can('reject contributions')) {
            return back()->with('error', 'You are not authorized to reject contributions.');
        }

        if ($contribution->status !== 'pending') {
            return back()->with('error', 'Only pending contributions can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $contribution->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Contribution rejected.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contribution $contribution)
    {
        if ($contribution->status === 'approved') {
            return back()->with('error', 'Approved contributions cannot be deleted.');
        }

        // Delete payment slip file
        if ($contribution->payment_slip) {
            Storage::disk('public')->delete($contribution->payment_slip);
        }

        $contribution->delete();

        return redirect()->route('contributions.index')
            ->with('success', 'Contribution deleted successfully!');
    }
}
