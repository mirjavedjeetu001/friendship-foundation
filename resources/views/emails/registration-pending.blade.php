@extends('emails.layout')

@section('title', 'Registration Pending')

@section('content')
<h2>Registration Received!</h2>

<p>Dear {{ $user->name }},</p>

<p>Thank you for registering with {{ config('app.name', 'Allied Group') }}. Your registration has been successfully submitted and is now pending approval.</p>

<div class="info-box">
    <p><strong>Registration Details:</strong></p>
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Submitted:</strong> {{ $user->created_at ? $user->created_at->format('d M Y, h:i A') : now()->format('d M Y, h:i A') }}</p>
    <p><strong>Status:</strong> <span style="color: #f59e0b; font-weight: 600;">Pending Approval</span></p>
</div>

<div class="warning">
    <p><strong>What's Next?</strong></p>
    <p>Our team will review your application and documents. You will receive an email notification once your registration is approved.</p>
</div>

<p>If you have any questions, please contact our support team.</p>

<p>Best regards,<br>
<strong>{{ config('app.name', 'Allied Group') }} Team</strong></p>
@endsection
