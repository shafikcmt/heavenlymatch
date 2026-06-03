<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;

class EnsureUserIsVerified
{
    /** Routes the user may visit even while unverified. */
    private const ALLOWED_ROUTES = [
        'verification.notice',
        'verification.send',
        'verification.verify',
        'logout',
    ];

    public function handle(Request $request, Closure $next): mixed
    {
        // Admin can switch off email verification entirely.
        if (! SystemSetting::bool('system.require_email_verification', true)) {
            return $next($request);
        }

        $user = $request->user();

        if ($user && ! $user->is_email_verified) {
            // Prevent redirect loop — let the user stay on verification pages
            if (in_array($request->route()?->getName(), self::ALLOWED_ROUTES, true)) {
                return $next($request);
            }

            return redirect()->route('verification.notice')
                ->with('error', trans('auth.verify_notice'));
        }

        return $next($request);
    }
}
