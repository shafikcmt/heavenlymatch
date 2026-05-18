<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('admin.login');
        }

        $isAdmin = (bool) ($user->is_admin ?? false) || (($user->role ?? 'user') === 'admin');

        if (! $isAdmin) {
            abort(403, 'You do not have permission to access the admin dashboard.');
        }

        if (($user->account_status ?? 'active') === 'blocked') {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->with('error', 'This admin account is blocked.');
        }

        return $next($request);
    }
}
