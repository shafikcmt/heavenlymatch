<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\Registration;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'total_users'      => Registration::count(),
            'active_users'     => Registration::where('account_status', 'active')->count(),
            'pending_biodatas' => Biodata::where('status', 'pending')->count(),
            'new_today'        => Registration::whereDate('created_at', today())->count(),
            'total_premium'    => Registration::where('membership_status', 'active')->count(),
        ];

        $recentUsers = Registration::with('biodata')
            ->latest()
            ->take(10)
            ->get(['registration_id', 'name', 'email', 'gender', 'account_status', 'created_at']);

        return Inertia::render('Admin/Dashboard', [
            'stats'       => $stats,
            'recentUsers' => $recentUsers,
        ]);
    }
}
