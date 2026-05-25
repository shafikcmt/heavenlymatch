<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => true,
            'status'           => session('status'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Throttle: 5 attempts per minute per email+IP
        $throttleKey = mb_strtolower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
            ]);
        }

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        /** @var Registration $user */
        $user = Auth::user();

        if (in_array($user->account_status, ['banned', 'suspended'], true)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => trans('auth.account_suspended'),
            ]);
        }

        RateLimiter::clear($throttleKey);
        $user->update(['last_login_at' => now()]);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function verifyNotice(Request $request): Response
    {
        /** @var Registration $user */
        $user = $request->user();

        return Inertia::render('Auth/VerifyEmail', [
            'email'  => $user?->email,
            'status' => session('status'),
        ]);
    }

    public function verifyEmail(Request $request): RedirectResponse
    {
        /** @var Registration $user */
        $user = $request->user();

        // Validate that this link was generated for the authenticated user
        if (! hash_equals((string) $request->route('id'), (string) $user->getKey())) {
            abort(403);
        }

        if (! hash_equals((string) $request->route('hash'), sha1($user->email))) {
            abort(403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->route('dashboard')
            ->with('success', trans('auth.verify_success'));
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        /** @var Registration $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', trans('auth.verify_resent'));
    }
}
