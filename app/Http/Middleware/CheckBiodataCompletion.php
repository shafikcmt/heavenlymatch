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

            $biodata = $user->biodata()->first(); // fetch fresh from DB

            if (!$biodata || !$biodata->is_completed) {
                // allow only biodata creation
                if (!$request->is('biodata/create*') && !$request->is('biodata/store*')) {
                    return redirect()->route('biodata.create')
                        ->with('warning', '📝 Please complete your biodata before continuing.');
                }
            }

            return $next($request);
        }
    }
