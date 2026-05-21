<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    /** Translation namespaces to share with the frontend. */
    private const NAMESPACES = [
        'common',
        'auth',
        'biodata',
        'dashboard',
        'admin',
        'pricing',
        'notifications',
        'validation',
    ];

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Global data shared with every Inertia page component.
     * Keep this lean — it is sent on every request.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        /** @var Registration|null $user */
        $user = $request->user();

        $locale = App::getLocale();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'registration_id'   => $user->registration_id,
                    'name'              => $user->name,
                    'gender'            => $user->gender,
                    'platform_mode'     => $user->platform_mode,
                    'photo_visibility'  => $user->photo_visibility,
                    'account_status'    => $user->account_status,
                    'role'              => $user->role,
                    'is_admin'          => $user->is_admin,
                    'membership_status' => $user->membership_status,
                    'membership_plan'   => $user->membership_plan_name,
                    'membership_expires'=> $user->membership_expires_at?->toDateTimeString(),
                    'is_email_verified' => $user->is_email_verified,
                    'biodata_status'    => $user->biodata?->status,
                    'biodata_complete'  => (bool) $user->biodata?->is_completed,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'info'    => fn () => $request->session()->get('info'),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'locale'        => $locale,
            'translations'  => fn () => $this->loadTranslations($locale),
            'googleEnabled' => filled(config('services.google.client_id'))
                && filled(config('services.google.client_secret'))
                && filled(config('services.google.redirect')),
        ]);
    }

    /**
     * Load all translation namespaces for the given locale.
     * Falls back to English for any key missing in the active locale.
     *
     * @return array<string, array<string, mixed>>
     */
    private function loadTranslations(string $locale): array
    {
        $translations = [];

        foreach (self::NAMESPACES as $namespace) {
            $lines = Lang::get($namespace, [], $locale);

            // If the file doesn't exist or returned the key itself, try English
            if (! is_array($lines)) {
                $lines = Lang::get($namespace, [], 'en');
            }

            $translations[$namespace] = is_array($lines) ? $lines : [];
        }

        return $translations;
    }
}

