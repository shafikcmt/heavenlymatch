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
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();

        $biodata = $user->biodata()->first();

        if (! $biodata || ! $biodata->is_completed) {
            // Allow the wizard routes to pass through so the user can complete biodata
            if (! $request->is('biodata/wizard*')) {
                return redirect()->route('biodata.wizard')
                    ->with('warning', 'Please complete your biodata before continuing.');
            }
        }

        return $next($request);
    }
}
