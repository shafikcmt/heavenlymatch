<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\PaymentTransaction;
use App\Models\ProfileReport;
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
            'pending_payments' => PaymentTransaction::where('status', 'pending')
                                    ->whereNotNull('external_transaction_id')
                                    ->count(),
            'open_reports'     => ProfileReport::whereIn('status', ['open', 'reviewing'])->count(),
            'total_revenue'    => (float) PaymentTransaction::where('status', 'paid')->sum('amount'),
        ];

        $recentUsers = Registration::latest()
            ->take(10)
            ->get(['registration_id', 'name', 'email', 'gender', 'account_status', 'membership_status', 'created_at']);

        $pendingBiodatas = Biodata::with('registration:registration_id,name,email,gender')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get(['id', 'registration_id', 'status', 'updated_at']);

        $pendingPayments = PaymentTransaction::with('registration:registration_id,name,email')
            ->where('status', 'pending')
            ->whereNotNull('external_transaction_id')
            ->oldest()
            ->take(5)
            ->get(['id', 'registration_id', 'transaction_no', 'plan_name', 'amount', 'updated_at']);

        return Inertia::render('Admin/Dashboard', [
            'stats'           => $stats,
            'recentUsers'     => $recentUsers,
            'pendingBiodatas' => $pendingBiodatas,
            'pendingPayments' => $pendingPayments,
        ]);
    }
}
