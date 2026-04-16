@extends('emails.layout')

@section('title', 'Admin Approved - Your Approval Needed')

@section('content')
<h2>Admin Approved! Your Turn 🔔</h2>

<p>Dear {{ $recipient->name }},</p>

<p>An admin has approved a contribution. Now <strong>your approval is needed</strong> to finalize it.</p>

<div class="success">
    <p><strong>✓ Admin Approved by:</strong> {{ $admin->name }}</p>
</div>

<div class="info-box">
    <p><strong>Contribution Details:</strong></p>
    <p><strong>Member:</strong> {{ $contribution->user->name }}</p>
    <p><strong>Month:</strong> {{ \Carbon\Carbon::create($contribution->year, $contribution->month, 1)->format('F Y') }}</p>
    <p><strong>Amount:</strong> ৳{{ number_format($contribution->amount, 2) }}</p>
    <p><strong>Admin Approved On:</strong> {{ $contribution->admin_approved_at->format('d M Y, h:i A') }}</p>
</div>

<div class="warning">
    <p><strong>Action Required:</strong> Please review and approve to complete the process and update the balance.</p>
</div>

<p style="text-align: center;">
    <a href="{{ url('/contributions/pending') }}" class="button">Approve Now</a>
</p>

<p>Best regards,<br>
<strong>{{ config('app.name', 'Allied Group') }} System</strong></p>
@endsection
