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
        App::setLocale($this->resolveLocale($request));
        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        // 1. Explicit route parameter (e.g. /en/... or /bn/... prefix routes)
        $fromRoute = $request->route('locale');
        if (is_string($fromRoute) && in_array($fromRoute, self::SUPPORTED, true)) {
            return $fromRoute;
        }

        // 2. Session — ONLY if StartSession has already run (guard prevents
        //    "Session store not set on request" when middleware runs early)
        if ($request->hasSession()) {
            $fromSession = $request->session()->get('locale');
            if (is_string($fromSession) && in_array($fromSession, self::SUPPORTED, true)) {
                return $fromSession;
            }
        }

        // 3. Authenticated user's saved preference
        $user = $request->user();
        if ($user
            && isset($user->preferred_locale)
            && is_string($user->preferred_locale)
            && in_array($user->preferred_locale, self::SUPPORTED, true)) {
            return $user->preferred_locale;
        }

        // 4. Cookie (persists locale across sessions without requiring login)
        $fromCookie = $request->cookie('locale');
        if (is_string($fromCookie) && in_array($fromCookie, self::SUPPORTED, true)) {
            return $fromCookie;
        }

        // 5. Browser Accept-Language header
        foreach ($request->getLanguages() as $lang) {
            $code = substr($lang, 0, 2);
            if (in_array($code, self::SUPPORTED, true)) {
                return $code;
            }
        }

        return self::DEFAULT;
    }
}
