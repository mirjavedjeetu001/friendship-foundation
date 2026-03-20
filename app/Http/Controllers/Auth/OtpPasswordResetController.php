<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OtpPasswordResetController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotForm(): View
    {
        return view('auth.forgot-password-otp');
    }

    /**
     * Send OTP to email
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'No account found with this email address.',
        ]);

        $email = $request->email;

        // Check if user is approved
        $user = User::where('email', $email)->first();
        if ($user->status !== 'approved') {
            return back()->withErrors([
                'email' => 'Your account is not approved yet. Please wait for admin approval.',
            ]);
        }

        // Generate OTP
        $otpRecord = PasswordResetOtp::generateFor($email);

        // Send OTP email
        try {
            Mail::to($email)->send(new PasswordResetOtpMail($otpRecord->otp, $email));
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Failed to send OTP email. Please try again.',
            ]);
        }

        // Store email in session
        session(['password_reset_email' => $email]);

        return redirect()->route('password.otp.verify.form')
            ->with('success', 'OTP sent to your email address.');
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyForm(): View|RedirectResponse
    {
        if (!session('password_reset_email')) {
            return redirect()->route('password.otp.request');
        }

        return view('auth.verify-otp');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $email = session('password_reset_email');

        if (!$email) {
            return redirect()->route('password.otp.request')
                ->withErrors(['otp' => 'Session expired. Please start again.']);
        }

        $otpRecord = PasswordResetOtp::where('email', $email)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return back()->withErrors([
                'otp' => 'Invalid or expired OTP. Please try again.',
            ]);
        }

        // Mark OTP as verified (but not used yet)
        session(['otp_verified' => true]);

        return redirect()->route('password.otp.reset.form');
    }

    /**
     * Show reset password form
     */
    public function showResetForm(): View|RedirectResponse
    {
        if (!session('password_reset_email') || !session('otp_verified')) {
            return redirect()->route('password.otp.request');
        }

        return view('auth.reset-password-otp');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = session('password_reset_email');

        if (!$email || !session('otp_verified')) {
            return redirect()->route('password.otp.request')
                ->withErrors(['password' => 'Session expired. Please start again.']);
        }

        // Update password
        $user = User::where('email', $email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Mark OTP as used
        PasswordResetOtp::where('email', $email)->update(['used' => true]);

        // Clear session
        session()->forget(['password_reset_email', 'otp_verified']);

        return redirect()->route('login')
            ->with('status', 'Password reset successfully! Please login with your new password.');
    }

    /**
     * Resend OTP
     */
    public function resendOtp(): RedirectResponse
    {
        $email = session('password_reset_email');

        if (!$email) {
            return redirect()->route('password.otp.request');
        }

        // Generate new OTP
        $otpRecord = PasswordResetOtp::generateFor($email);

        // Send OTP email
        try {
            Mail::to($email)->send(new PasswordResetOtpMail($otpRecord->otp, $email));
        } catch (\Exception $e) {
            return back()->withErrors([
                'otp' => 'Failed to resend OTP. Please try again.',
            ]);
        }

        return back()->with('success', 'New OTP sent to your email address.');
    }
}
