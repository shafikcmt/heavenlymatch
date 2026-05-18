<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && ! $user->is_email_verified) {
            return redirect()->route('email.verify.notice', ['email' => $user->email])
                ->with('email', $user->email)
                ->with('error', 'Please verify your email before continuing.');
        }

        return $next($request);
    }
}
