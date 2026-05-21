<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $social = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect()->route('login')->with('error', 'Google login failed. Please try again.');
        }

        $registration = Registration::firstOrCreate(
            ['email' => $social->getEmail()],
            [
                'name'                => $social->getName(),
                'password'            => Hash::make(Str::random(32)),
                'google_id'           => $social->getId(),
                'is_email_verified'   => true,
                'email_verified_at'   => now(),
                'profile_created_for' => 'self',
                // gender/looking_for left null — user sets them in biodata wizard
                'photo_visibility'    => 'members_only',
                'platform_mode'       => 'general',
                // account_status and role omitted — migration defaults: 'active' / 'user'
            ]
        );

        // Link google_id on existing accounts that authenticated with Google for the first time
        if (! $registration->google_id) {
            $registration->update(['google_id' => $social->getId()]);
        }

        Auth::login($registration, true);

        // New OAuth users (no gender set yet) go to biodata wizard to complete setup
        if (! $registration->gender) {
            return redirect()->route('biodata.wizard', ['step' => 1])
                ->with('info', 'Welcome! Please complete your profile to find matches.');
        }

        return redirect()->intended(route('dashboard'));
    }
}
