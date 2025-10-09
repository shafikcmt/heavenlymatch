<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBiodataCompletion
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // ✅ Check if user has completed biodata
        if (!$user->biodata || !$user->biodata->is_completed) {
            // Allow only biodata creation routes
            if (!$request->is('biodata/create*') && !$request->is('biodata/store*')) {
                return redirect()->route('biodata.create')
                    ->with('warning', '📝 Please complete your biodata before continuing.');
            }
        }

        return $next($request);
    }
}
