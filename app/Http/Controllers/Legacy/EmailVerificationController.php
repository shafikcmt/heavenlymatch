<?php

/**
 * LEGACY — No active routes reference this controller.
 * Email verification now handled by App\Http\Controllers\Auth\LoginController + Laravel MustVerifyEmail.
 * Broken route refs: route('email.verify.notice'), route('email.verify.code') — both defunct.
 */

namespace App\Http\Controllers\Legacy;

use App\Models\Registration;
use App\Services\EmailVerificationSender;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function showVerifyForm(Request $request)
    {
        $email = $request->query('email') ?: session('email') ?: old('email') ?: '';
        $user = $email ? Registration::where('email', strtolower($email))->first() : null;

        $remaining = 0;
        if ($user && $user->email_verification_sent_at) {
            $remaining = max(0, 60 - $user->email_verification_sent_at->diffInSeconds(now()));
        }

        $devCode = session('dev_code');

        return view('auth.verify-email', compact('email', 'remaining', 'devCode'));
    }

    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:registrations,email',
            'code' => ['required', 'regex:/^[0-9]{6}$/'],
        ], [
            'code.regex' => 'Please enter the 6-digit verification code.',
        ]);

        $email = strtolower($validated['email']);
        $user = Registration::where('email', $email)->first();

        if (! $user) {
            return back()->withInput()->with('email', $email)->with('error', 'No account found with this email address.');
        }

        if ($user->is_email_verified) {
            return redirect()->route('login')->with('success', 'Email is already verified. You can log in now.');
        }

        if ($user->email_verification_sent_at && $user->email_verification_sent_at->diffInHours(now()) > 24) {
            return back()->withInput()->with('email', $email)->with('error', 'Verification code expired. Please resend a new code.');
        }

        if (! hash_equals((string) $user->email_verification_code, (string) $validated['code'])) {
            return back()->withInput()->with('email', $email)->with('error', 'Invalid verification code. Please check your email and try again.');
        }

        $this->markVerified($user);

        return redirect()->route('login')->with('success', 'Email verified successfully. You can now log in.');
    }

    public function sendCode(Request $request, EmailVerificationSender $verificationSender)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:registrations,email',
        ]);

        $email = strtolower($validated['email']);
        $user = Registration::where('email', $email)->firstOrFail();

        if ($user->is_email_verified) {
            return redirect()->route('login')->with('success', 'Email is already verified. You can log in now.');
        }

        if ($user->email_verification_sent_at && $user->email_verification_sent_at->diffInSeconds(now()) < 60) {
            $remaining = 60 - $user->email_verification_sent_at->diffInSeconds(now());
            return back()->withInput()->with('email', $email)->with('error', "Please wait {$remaining} seconds before resending the code.");
        }

        [$code, $token] = $verificationSender->createCode($user);
        $sent = $verificationSender->send($user, $code, $token);

        $redirect = redirect()->route('email.verify.notice', ['email' => $user->email])
            ->with('email', $user->email);

        if (! $sent) {
            return $redirect
                ->with('dev_code', app()->environment(['local', 'development']) ? $code : null)
                ->with('error', 'Email could not be sent. Please check MAIL settings in .env, then try again.');
        }

        return $redirect->with('success', 'Verification code sent successfully. Please check inbox or spam.');
    }

    public function verifyLink(string $token)
    {
        $user = Registration::where('email_verification_token', $token)->first();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Invalid or expired verification link.');
        }

        if ($user->is_email_verified) {
            return redirect()->route('login')->with('success', 'Email is already verified. You can log in now.');
        }

        if ($user->email_verification_sent_at && $user->email_verification_sent_at->diffInHours(now()) > 24) {
            return redirect()->route('email.verify.notice', ['email' => $user->email])
                ->with('email', $user->email)
                ->with('error', 'Verification link expired. Please resend a new code.');
        }

        $this->markVerified($user);

        return redirect()->route('login')->with('success', 'Email verified successfully. You can now log in.');
    }

    private function markVerified(Registration $user): void
    {
        $user->forceFill([
            'is_email_verified' => true,
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_token' => null,
            'status' => $user->status === 'pending' ? 'active' : $user->status,
        ])->save();
    }
}
