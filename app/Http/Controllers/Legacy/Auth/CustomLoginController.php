<?php

/**
 * LEGACY — No active routes reference this controller.
 * Superseded by App\Http\Controllers\Auth\LoginController (Inertia).
 * Broken route refs: route('myhome'), route('email.verify.notice') — both defunct.
 */

namespace App\Http\Controllers\Legacy\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Registration;

class CustomLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = Registration::where('email', strtolower($credentials['email']))->first();

        if (! $user) {
            throw ValidationException::withMessages(['email' => __('No account found with this email.')]);
        }

        if (! $user->is_email_verified) {
            return redirect()->route('email.verify.notice', ['email' => $user->email])
                ->with('email', $user->email)
                ->with('error', 'Please verify your email before logging in.');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt(['email' => strtolower($credentials['email']), 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();
            Auth::user()->update(['last_login_at' => now()]);

            return redirect()->intended(route('myhome'))
                ->with('success', 'Welcome back to HeavenlyMatch.');
        }

        throw ValidationException::withMessages(['email' => __('Invalid email or password.')]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
