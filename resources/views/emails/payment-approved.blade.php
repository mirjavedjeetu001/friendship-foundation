@extends('emails.layout')

@section('title', 'Payment Approved')

@section('content')
<h2>Payment Confirmed! ✓</h2>

<p>Dear {{ $user->name }},</p>

<p>Your contribution payment has been <strong>approved</strong> and recorded in our system.</p>

<div class="success">
    <p><strong>Payment successfully processed!</strong></p>
</div>

<div class="info-box">
    <p><strong>Payment Details:</strong></p>
    <p><strong>Member:</strong> {{ $user->name }}</p>
    <p><strong>Month:</strong> {{ \Carbon\Carbon::create($contribution->year, $contribution->month, 1)->format('F Y') }}</p>
    <p><strong>Amount:</strong> ৳{{ number_format($contribution->amount, 2) }}</p>
    <p><strong>Payment Method:</strong> {{ ucfirst($contribution->payment_method ?? 'Cash') }}</p>
    <p><strong>Transaction ID:</strong> {{ $contribution->transaction_id ?? 'N/A' }}</p>
    <p><strong>Approved On:</strong> {{ now()->format('d M Y, h:i A') }}</p>
</div>

<p style="text-align: center;">
    <a href="{{ url('/contributions') }}" class="button">View Your Contributions</a>
</p>

<p>Thank you for your contribution!</p>

<p>Best regards,<br>
<strong>{{ config('app.name', 'Allied Group') }} Team</strong></p>
@endsection
