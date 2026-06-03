<?php

namespace App\Http\Middleware;

use App\Services\UserAccessService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Biodata completion + approval gate.
 *
 * - The biodata wizard is always allowed through.
 * - Matching features (matches / search / interests / inbox / shortlist /
 *   profile browsing) are HARD-BLOCKED unless the biodata is approved. A direct
 *   URL hit before approval is redirected to the dashboard with a state-aware
 *   message (incomplete / pending / rejected / hidden).
 * - Upgrade checkout still requires a minimum 30% profile (separate from the
 *   approval gate, since upgrading is allowed before approval).
 */
class CheckBiodataCompletion
{
    /** Route-name patterns that require an APPROVED biodata. */
    private const APPROVAL_GATED = [
        'matches.*',
        'search.*',
        'interests.*',
        'inbox.*',
        'shortlist.*',
        'profile.who-viewed',
        'profile.show',
    ];

    /** Action routes that require at least a 30% complete profile. */
    private const BLOCKED_BELOW_30 = [
        'upgrade.checkout',
        'upgrade.manual.submit',
    ];

    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();

        // Always allow wizard and biodata management routes.
        if ($request->routeIs('biodata.*')) {
            return $next($request);
        }

        // Approval gate for all matching features.
        if ($user && $request->routeIs(...self::APPROVAL_GATED)) {
            $status = $user->biodata?->status;

            if ($status !== 'approved') {
                $message = UserAccessService::gateMessage($status);

                if ($request->wantsJson()) {
                    return response()->json(['message' => $message], 403);
                }

                return redirect()->route('dashboard')->with('error', $message);
            }
        }

        // Minimum-profile gate for upgrade actions.
        if ($request->routeIs(...self::BLOCKED_BELOW_30)) {
            $score = $user->biodata?->completeness_score ?? 0;

            if ($score < 30) {
                $message = __('dashboard.access_msg_min_profile');

                if ($request->wantsJson()) {
                    return response()->json(['message' => $message], 422);
                }

                return back()->with('error', $message);
            }
        }

        return $next($request);
    }
}
