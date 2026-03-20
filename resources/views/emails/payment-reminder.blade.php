@extends('emails.layout')

@section('title', 'Payment Reminder')

@section('content')
<h2>Payment Reminder</h2>

<p>Dear {{ $user->name }},</p>

<p>This is a friendly reminder that your monthly contribution for <strong>{{ $dueMonth }}</strong> is due.</p>

<div class="info-box">
    <p><strong>Payment Details:</strong></p>
    <p><strong>Member:</strong> {{ $user->name }}</p>
    <p><strong>Month:</strong> {{ $dueMonth }}</p>
    <p><strong>Amount Due:</strong> ৳{{ number_format($dueAmount, 2) }}</p>
</div>

<div class="warning">
    <p><strong>Please Note:</strong></p>
    <p>Timely payments help maintain the strength of our group savings. Please submit your contribution at your earliest convenience.</p>
</div>

<p style="text-align: center;">
    <a href="{{ url('/contributions/create') }}" class="button">Submit Payment</a>
</p>

<p>If you have already made this payment, please disregard this reminder.</p>

<p>Thank you for being a valued member!</p>

<p>Best regards,<br>
<strong>{{ config('app.name', 'Allied Group') }} Team</strong></p>
@endsection
