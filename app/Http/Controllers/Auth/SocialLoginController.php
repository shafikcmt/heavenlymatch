<?php

declare(strict_types=1);

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
        if (! $this->isConfigured()) {
            return redirect()->route('login')
                ->with('error', __('auth.google_not_configured'));
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return redirect()->route('login')
                ->with('error', __('auth.google_not_configured'));
        }

        try {
            $social = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect()->route('login')
                ->with('error', __('auth.google_login_failed'));
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
                ->with('info', __('auth.google_welcome'));
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Returns true only when all three required Google OAuth values are set.
     * Guards both redirect() and callback() so a missing .env key never crashes the app.
     */
    private function isConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }
}
