<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;

class EmailVerificationController extends Controller
{
    // Show verify email page
    public function showVerifyForm()
    {
        $email = session('email') ?? '';
        return view('auth.verify-email', compact('email'));
    }

    // Verify code
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:registrations,email',
            'code'  => 'required|digits:6',
        ]);

        $user = Registration::where('email', $request->email)->first();

        if ($user && $user->email_verification_code === $request->code) {
            $user->update([
                'is_email_verified'       => 1,
                'email_verified_at'       => now(),
                'email_verification_code' => null,
            ]);

            return redirect()->route('login')->with('success', 'Email verified! You can now login.');
        }

        return back()->with('error', 'Invalid verification code.');
    }

    // Send or resend code
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:registrations,email',
        ]);

        $user = Registration::where('email', $request->email)->first();

        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->email_verification_code = $code;
        $user->save();

        try {
            Mail::to($user->email)->send(new \App\Mail\EmailVerificationMail($code));
        } catch (\Exception $e) {
            \Log::error('Email Verification Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to send verification email.');
        }

        return back()->with('success', 'Verification code sent to your email.');
    }
}
