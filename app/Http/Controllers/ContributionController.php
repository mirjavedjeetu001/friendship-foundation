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
        $query = Contribution::with(['user', 'submitter', 'approver']);

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
        $contributions = Contribution::with(['user', 'submitter'])
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

        return redirect()->route('contributions.index')
            ->with('success', 'Contribution submitted successfully! Awaiting approval.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contribution $contribution)
    {
        $contribution->load(['user', 'submitter', 'approver']);
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
     * Approve a contribution
     */
    public function approve(Contribution $contribution)
    {
        if (!auth()->user()->can('approve contributions')) {
            return back()->with('error', 'You are not authorized to approve contributions.');
        }

        if ($contribution->status !== 'pending') {
            return back()->with('error', 'Only pending contributions can be approved.');
        }

        $contribution->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Update bank balance
        $settings = MonthlySetting::getSettings();
        $settings->updateBalance($contribution->amount, 'add');

        // Send payment approved email
        try {
            $contribution->load('user');
            Mail::to($contribution->user->email)->send(new PaymentApprovedMail($contribution->user, $contribution));
        } catch (\Exception $e) {
            \Log::error('Failed to send payment approved email: ' . $e->getMessage());
        }

        return back()->with('success', 'Contribution approved successfully!');
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
