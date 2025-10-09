<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Registration;
use App\Models\Biodata;

class CustomLoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
        public function login(Request $request)
        {
            // Validate input
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);

            $remember = $request->filled('remember');

            // Find user
            $user = Registration::where('email', $credentials['email'])->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => __('No account found with this email.'),
                ]);
            }

            if (!$user->is_email_verified) {
                throw ValidationException::withMessages([
                    'email' => __('Please verify your email before logging in.'),
                ]);
            }

            // Attempt login
            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();

                // ✅ Check biodata completion
                $biodataCompleted = $user->biodata && $user->biodata->is_completed;

                if ($biodataCompleted) {
                    return redirect()->intended(route('myhome'))
                        ->with('success', '🎉 Welcome back! You have logged in successfully.');
                } else {
                    return redirect()->route('biodata.create')
                        ->with('info', '📝 Please complete your biodata before continuing.');
                }
            }

            throw ValidationException::withMessages([
                'email' => __('Invalid email or password.'),
            ]);
        }




    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', '👋 You have been logged out successfully.');
    }
}
