<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialUser;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    private const ALLOWED_PROVIDERS = ['google', 'facebook'];

    public function redirect(string $provider): RedirectResponse
    {
        if (! $this->isEnabled($provider)) {
            return redirect()->route('login')
                ->with('error', __('auth.social_login_disabled'));
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        if (! $this->isEnabled($provider)) {
            return redirect()->route('login')
                ->with('error', __('auth.social_login_disabled'));
        }

        try {
            /** @var SocialUser $social */
            $social = Socialite::driver($provider)->user();
        } catch (\Throwable) {
            return redirect()->route('login')
                ->with('error', __('auth.social_login_failed'));
        }

        $email = $social->getEmail();

        if (! $email) {
            return redirect()->route('register')
                ->with('error', __('auth.social_email_missing'));
        }

        $registration = $this->findOrCreateUser($provider, $social->getId(), $email, $social);

        Auth::login($registration, true);
        request()->session()->regenerate();

        if (! $registration->gender) {
            return redirect()->route('biodata.wizard', ['step' => 1])
                ->with('info', __('auth.social_welcome'));
        }

        return redirect()->intended(route('dashboard'));
    }

    private function findOrCreateUser(string $provider, string $providerId, string $email, SocialUser $social): Registration
    {
        // 1. Match by provider_id — fastest path, avoids email collisions
        $user = Registration::where('provider_name', $provider)
            ->where('provider_id', $providerId)
            ->first();

        // 2. Google backward-compat: check legacy google_id column for pre-migration accounts
        if (! $user && $provider === 'google') {
            $user = Registration::where('google_id', $providerId)->first();
        }

        // 3. Match by email — links social provider to an existing email/password account
        if (! $user) {
            $user = Registration::where('email', $email)->first();
        }

        if ($user) {
            $updates = [];

            // Attach provider fields if this is the first time logging in via this provider
            if (! $user->provider_id) {
                $updates['provider_name'] = $provider;
                $updates['provider_id']   = $providerId;
            }
            if (! $user->avatar_url && ($avatar = $social->getAvatar())) {
                $updates['avatar_url'] = $avatar;
            }
            // Keep legacy google_id column in sync
            if ($provider === 'google' && ! $user->google_id) {
                $updates['google_id'] = $providerId;
            }

            if ($updates) {
                $user->forceFill($updates)->save();
            }

            return $user;
        }

        // 4. New user — create with safe defaults; gender/mode set later in biodata wizard
        return Registration::create([
            'name'                => $social->getName() ?: Str::before($email, '@'),
            'email'               => $email,
            'password'            => Hash::make(Str::random(40)),
            'provider_name'       => $provider,
            'provider_id'         => $providerId,
            'avatar_url'          => $social->getAvatar(),
            'google_id'           => $provider === 'google' ? $providerId : null,
            'is_email_verified'   => true,
            'email_verified_at'   => now(),
            'profile_created_for' => 'self',
            'photo_visibility'    => 'members_only',
            'platform_mode'       => 'general',
            'terms_accepted_at'   => now(),
        ]);
    }

    /**
     * A provider is considered enabled when:
     * - It is in the hard-coded allowed list (belt-and-suspenders after route constraint)
     * - The admin has not disabled it in system_settings
     * - All three required .env credentials are present
     */
    private function isEnabled(string $provider): bool
    {
        if (! in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            return false;
        }

        if (! SystemSetting::bool("social.{$provider}_enabled", true)) {
            return false;
        }

        return match ($provider) {
            'google'   => filled(config('services.google.client_id'))
                       && filled(config('services.google.client_secret'))
                       && filled(config('services.google.redirect')),
            'facebook' => filled(config('services.facebook.client_id'))
                       && filled(config('services.facebook.client_secret'))
                       && filled(config('services.facebook.redirect')),
            default    => false,
        };
    }
}
