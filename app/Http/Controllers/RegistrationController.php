<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function showForm()
    {
        return view('auth.registration');
    }

    /**
     * ✅ Handle new registration & send verification email
     */
    public function store(Request $request)
    {
        $request->validate([
            'looking_for'   => 'required|string|max:255',
            'name'          => 'required|string|max:255',
            'gender'        => 'required|in:male,female',
            'email'         => 'required|email|unique:registrations,email',
            'country_code'  => 'required|string|max:10',
            'mobile_number' => 'required|string|max:20|unique:registrations,mobile_number',
            'password'      => 'required|confirmed|min:6',
        ]);

        // ✅ Create new user
        $user = Registration::create([
            'looking_for'       => $request->looking_for,
            'name'              => $request->name,
            'gender'            => $request->gender,
            'email'             => $request->email,
            'country_code'      => $request->country_code,
            'mobile_number'     => $request->mobile_number,
            'password'          => Hash::make($request->password),
            'is_email_verified' => false,
            'is_mobile_verified'=> false,
        ]);

        // ✅ Generate code & token
        $code  = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(40);

        $user->update([
            'email_verification_code'     => $code,
            'email_verification_token'    => $token,
            'email_verification_sent_at'  => now(),
        ]);

        // ✅ Send email
        try {
            Mail::to($user->email)->send(new EmailVerificationMail($user->name, $code, $token));
        } catch (\Exception $e) {
            \Log::error('Email Verification Error: '.$e->getMessage());
        }

        return redirect()->route('email.verify.notice')->with('email', $user->email);
    }

    /**
     * ✅ Verify email by code
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
        ]);

        $user = Registration::where('email', $request->email)
                            ->where('email_verification_code', $request->code)
                            ->first();

        if (!$user) {
            return back()->with('error', 'Invalid verification code.');
        }

        $user->update([
            'is_email_verified'         => true,
            'email_verified_at'         => now(),
            'email_verification_code'   => null,
            'email_verification_token'  => null,
        ]);

        return redirect()->route('login')->with('success', '🎉 Email verified successfully!');
    }

    /**
     * ✅ Verify email by clickable link
     */
    public function verifyLink($token)
    {
        $user = Registration::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid or expired verification link.');
        }

        // Optional: expire after 24 hours
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

    /**
     * ✅ Resend email verification (2-minute cooldown)
     */
    public function resendEmail(Request $request)
    {
        $user = Registration::where('email', $request->email)->firstOrFail();

        if ($user->email_verification_sent_at && $user->email_verification_sent_at->diffInSeconds(now()) < 120) {
            $remaining = 120 - $user->email_verification_sent_at->diffInSeconds(now());
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
            \Log::error('Email Verification Resend Error: '.$e->getMessage());
        }

        return back()->with('success', '📧 Verification code resent successfully!');
    }
}
