@extends('emails.layout')

@section('title', 'Password Reset OTP')

@section('content')
<h2>Password Reset Request</h2>

<p>Hello,</p>

<p>We received a request to reset the password for your account associated with <strong>{{ $email }}</strong>.</p>

<p>Use the following OTP (One-Time Password) to reset your password:</p>

<p style="text-align: center;">
    <span class="otp-box">{{ $otp }}</span>
</p>

<div class="warning">
    <p><strong>Important:</strong></p>
    <ul style="margin: 10px 0; padding-left: 20px;">
        <li>This OTP is valid for <strong>10 minutes</strong> only.</li>
        <li>Do not share this OTP with anyone.</li>
        <li>If you didn't request this, please ignore this email.</li>
    </ul>
</div>

<p>If you didn't request a password reset, your account is safe. Someone may have entered your email by mistake.</p>

<p>Best regards,<br>
<strong>{{ config('app.name', 'Allied Group') }} Team</strong></p>
@endsection
