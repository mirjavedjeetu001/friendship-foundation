@extends('emails.layout')

@section('title', 'New Contribution Submitted')

@section('content')
<h2>New Contribution Submitted! 📋</h2>

<p>Dear {{ $recipient->name }},</p>

<p>A new contribution has been submitted and requires <strong>approval</strong>.</p>

<div class="info-box">
    <p><strong>Contribution Details:</strong></p>
    <p><strong>Member:</strong> {{ $contribution->user->name }}</p>
    <p><strong>Month:</strong> {{ \Carbon\Carbon::create($contribution->year, $contribution->month, 1)->format('F Y') }}</p>
    <p><strong>Amount:</strong> ৳{{ number_format($contribution->amount, 2) }}</p>
    <p><strong>Submitted By:</strong> {{ $contribution->submitter->name }}</p>
    <p><strong>Transaction Ref:</strong> {{ $contribution->transaction_reference ?? 'N/A' }}</p>
    <p><strong>Submitted On:</strong> {{ $contribution->created_at->format('d M Y, h:i A') }}</p>
    @if($contribution->is_late)
    <p style="color: #dc2626;"><strong>⚠ Late Payment</strong></p>
    @endif
</div>

<div class="warning">
    <p><strong>Approval Process:</strong></p>
    <p>1️⃣ Admin Approval → 2️⃣ Accountant Approval → ✅ Balance Updated</p>
</div>

<p style="text-align: center;">
    <a href="{{ url('/contributions/pending') }}" class="button">Review Contributions</a>
</p>

<p>Please login to the app to review and approve.</p>

<p>Best regards,<br>
<strong>{{ config('app.name', 'Allied Group') }} System</strong></p>
@endsection
