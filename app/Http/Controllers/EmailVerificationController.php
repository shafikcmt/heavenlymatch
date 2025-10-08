<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Str;

class EmailVerificationController extends Controller
{
    /**
     * Show email verification page
     */
    public function showVerifyForm()
    {
        $email = session('email') ?? '';
        return view('auth.verify-email', compact('email'));
    }

    /**
     * Verify 6-digit email code
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:registrations,email',
            'code'  => 'required|digits:6',
        ]);

        $user = Registration::where('email', $request->email)
                            ->where('email_verification_code', $request->code)
                            ->first();

        if (!$user) {
            return back()->with('error', '❌ Invalid or expired verification code.');
        }

        $user->update([
            'is_email_verified'         => true,
            'email_verified_at'         => now(),
            'email_verification_code'   => null,
            'email_verification_token'  => null,
        ]);

        return redirect()->route('login')->with('success', '🎉 Email verified successfully! You can now log in.');
    }

    /**
     * Send or resend verification code & link
     */
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:registrations,email',
        ]);

        $user = Registration::where('email', $request->email)->first();

        // Prevent frequent resend (60 sec cooldown)
        if ($user->email_verification_sent_at && $user->email_verification_sent_at->diffInSeconds(now()) < 60) {
            $remaining = 60 - $user->email_verification_sent_at->diffInSeconds(now());
            return back()->with('error', "⏳ Please wait {$remaining} seconds before resending the code.");
        }

        $code  = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(40);

        $user->update([
            'email_verification_code'     => $code,
            'email_verification_token'    => $token,
            'email_verification_sent_at'  => now(),
        ]);

        try {
            Mail::to($user->email)->send(new EmailVerificationMail($user->name, $code, $token));
        } catch (\Exception $e) {
            \Log::error('Email Verification Send Error: ' . $e->getMessage());
            return back()->with('error', '⚠️ Failed to send verification email.');
        }

        return back()->with('success', '📧 Verification email sent successfully!');
    }

    /**
     * Verify via clickable link
     */
    public function verifyLink($token)
    {
        $user = Registration::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid or expired verification link.');
        }

        // Optional expiry: 24 hours
        if ($user->email_verification_sent_at && $user->email_verification_sent_at->diffInHours(now()) > 24) {
            return redirect()->route('login')->with('error', 'Verification link has expired.');
        }

        $user->update([
            'is_email_verified'         => true,
            'email_verified_at'         => now(),
            'email_verification_code'   => null,
            'email_verification_token'  => null,
        ]);

        return redirect()->route('login')->with('success', '🎉 Email verified successfully!');
    }
}
