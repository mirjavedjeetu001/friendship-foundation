@extends('emails.layout')

@section('title', 'Registration Approved')

@section('content')
<h2>🎉 Congratulations!</h2>

<p>Dear {{ $user->name }},</p>

<p>Great news! Your registration with {{ config('app.name', 'Allied Group') }} has been <strong>approved</strong>.</p>

<div class="success">
    <p><strong>Your account is now active!</strong></p>
    <p>You can now log in and access all member features.</p>
</div>

<div class="info-box">
    <p><strong>Account Details:</strong></p>
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Status:</strong> <span style="color: #10b981; font-weight: 600;">Approved</span></p>
    <p><strong>Approved On:</strong> {{ now()->format('d M Y, h:i A') }}</p>
</div>

<p style="text-align: center;">
    <a href="{{ url('/login') }}" class="button">Login to Your Account</a>
</p>

<p>Welcome to our community! We're excited to have you on board.</p>

<p>Best regards,<br>
<strong>{{ config('app.name', 'Allied Group') }} Team</strong></p>
@endsection
