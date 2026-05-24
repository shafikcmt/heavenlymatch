<?php

/**
 * LEGACY — No active routes reference this controller.
 * Superseded by App\Http\Controllers\Admin\AdminLoginController (Inertia).
 * Used old Blade views: admin.login. Now fully replaced.
 */

namespace App\Http\Controllers\Legacy\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Registration;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $isAdmin = (bool) ($user->is_admin ?? false) || (($user->role ?? 'user') === 'admin');

            if ($isAdmin) {
                return redirect()->route('admin.dashboard');
            }
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = Registration::where('email', $credentials['email'])->first();

        if (! $admin || ! Hash::check($credentials['password'], $admin->password)) {
            return back()->withInput($request->only('email'))->with('error', 'Invalid admin email or password.');
        }

        $isAdmin = (bool) ($admin->is_admin ?? false) || (($admin->role ?? 'user') === 'admin');

        if (! $isAdmin) {
            return back()->withInput($request->only('email'))->with('error', 'This account is not allowed to access admin dashboard.');
        }

        if (($admin->account_status ?? 'active') === 'blocked') {
            return back()->withInput($request->only('email'))->with('error', 'This admin account is blocked.');
        }

        Auth::login($admin, $request->boolean('remember'));
        $request->session()->regenerate();

        if (method_exists($admin, 'forceFill')) {
            $admin->forceFill(['last_login_at' => now()])->saveQuietly();
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}
