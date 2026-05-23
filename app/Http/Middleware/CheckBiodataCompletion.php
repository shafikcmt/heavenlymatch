<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Soft biodata completion check.
 *
 * - Most authenticated routes are allowed through regardless of completion state.
 * - Only hard-blocks specific action routes that genuinely require profile data:
 *     interests.store, upgrade.checkout, upgrade.manual.submit
 *   when the profile is < 30% complete.
 * - The biodata wizard is always allowed through.
 */
class CheckBiodataCompletion
{
    private const BLOCKED_BELOW_30 = [
        'interests.store',
        'upgrade.checkout',
        'upgrade.manual.submit',
    ];

    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();

        // Always allow wizard and biodata management routes
        if ($request->routeIs('biodata.*')) {
            return $next($request);
        }

        // For action routes that require a minimum profile, check completeness
        if ($request->routeIs(...self::BLOCKED_BELOW_30)) {
            $score = $user->biodata?->completeness_score ?? 0;

            if ($score < 30) {
                $message = 'Please complete at least 30% of your biodata profile before taking this action.';

                if ($request->wantsJson()) {
                    return response()->json(['message' => $message], 422);
                }

                return back()->with('error', $message);
            }
        }

        return $next($request);
    }
}
