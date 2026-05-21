<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['en', 'bn'];
    private const DEFAULT   = 'bn';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        // 1. Session (set by language switcher)
        $fromSession = $request->session()->get('locale');
        if ($fromSession && in_array($fromSession, self::SUPPORTED, true)) {
            return $fromSession;
        }

        // 2. Authenticated user's saved preference
        $user = $request->user();
        if ($user && isset($user->preferred_locale) && in_array($user->preferred_locale, self::SUPPORTED, true)) {
            return $user->preferred_locale;
        }

        // 3. Browser Accept-Language header (best supported match)
        foreach ($request->getLanguages() as $lang) {
            $code = substr($lang, 0, 2);
            if (in_array($code, self::SUPPORTED, true)) {
                return $code;
            }
        }

        return self::DEFAULT;
    }
}
