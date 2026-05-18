<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;

class SystemMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        if (! SystemSetting::bool('maintenance.enabled')) {
            return $next($request);
        }

        if ($request->is('admin') || $request->is('admin/*')) {
            return $next($request);
        }

        $allowedIps = collect(preg_split('/\r\n|\r|\n|,/', (string) SystemSetting::get('maintenance.allowed_ips', '')))
            ->map(fn ($ip) => trim($ip))
            ->filter()
            ->all();

        if (in_array($request->ip(), $allowedIps, true)) {
            return $next($request);
        }

        return response()->view('errors.maintenance', [
            'title' => SystemSetting::get('maintenance.title', 'We will be back soon'),
            'message' => SystemSetting::get('maintenance.message', 'The site is temporarily down for maintenance. Please check back soon.'),
        ], 503);
    }
}
