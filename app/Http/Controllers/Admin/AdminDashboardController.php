<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\PaymentTransaction;
use App\Models\Registration;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $userQuery = Registration::query();
        $biodataQuery = Biodata::query();
        $paymentTableExists = Schema::hasTable('payment_transactions');

        $stats = [
            'users' => (clone $userQuery)->count(),
            'active_users' => Schema::hasColumn('registrations', 'account_status')
                ? (clone $userQuery)->where('account_status', '!=', 'blocked')->count()
                : (clone $userQuery)->count(),
            'email_unverified' => Schema::hasColumn('registrations', 'is_email_verified')
                ? (clone $userQuery)->where(function ($q) {
                    $q->whereNull('is_email_verified')->orWhere('is_email_verified', false);
                })->count()
                : 0,
            'mobile_unverified' => Schema::hasColumn('registrations', 'is_mobile_verified')
                ? (clone $userQuery)->where(function ($q) {
                    $q->whereNull('is_mobile_verified')->orWhere('is_mobile_verified', false);
                })->count()
                : 0,
            'total_payment' => $paymentTableExists ? (float) PaymentTransaction::where('status', 'paid')->sum('amount') : 0,
            'pending_payment' => $paymentTableExists ? (float) PaymentTransaction::whereIn('status', ['pending', 'submitted'])->sum('amount') : 0,
            'rejected_payment' => $paymentTableExists ? (float) PaymentTransaction::whereIn('status', ['failed', 'cancelled', 'refunded'])->sum('amount') : 0,
            'payment_charge' => $paymentTableExists ? round((float) PaymentTransaction::where('status', 'paid')->sum('amount') * 0.02, 2) : 0,
            'purchased_package' => $paymentTableExists ? (float) PaymentTransaction::where('status', 'paid')->sum('amount') : 0,
            'total_interests' => 0,
            'ignored_profiles' => 0,
            'reports' => 0,
            'pending_biodatas' => Schema::hasColumn('biodatas', 'status')
                ? (clone $biodataQuery)->where('status', 'pending')->count()
                : 0,
            'approved_biodatas' => Schema::hasColumn('biodatas', 'status')
                ? (clone $biodataQuery)->where('status', 'approved')->count()
                : 0,
            'rejected_biodatas' => Schema::hasColumn('biodatas', 'status')
                ? (clone $biodataQuery)->where('status', 'rejected')->count()
                : 0,
        ];

        $charts = [
            [
                'title' => 'Users By Account Status',
                'segments' => $this->segments([
                    ['label' => 'Active', 'value' => $stats['active_users']],
                    ['label' => 'Blocked', 'value' => Schema::hasColumn('registrations', 'account_status') ? (clone $userQuery)->where('account_status', 'blocked')->count() : 0],
                    ['label' => 'Unverified', 'value' => $stats['email_unverified']],
                ]),
            ],
            [
                'title' => 'Payment Status Distribution',
                'segments' => $this->segments([
                    ['label' => 'Paid', 'value' => $paymentTableExists ? PaymentTransaction::where('status', 'paid')->count() : 0],
                    ['label' => 'Pending', 'value' => $paymentTableExists ? PaymentTransaction::whereIn('status', ['pending', 'submitted'])->count() : 0],
                    ['label' => 'Rejected', 'value' => $paymentTableExists ? PaymentTransaction::whereIn('status', ['failed', 'cancelled', 'refunded'])->count() : 0],
                ]),
            ],
            [
                'title' => 'Biodata Review Status',
                'segments' => $this->segments([
                    ['label' => 'Approved', 'value' => $stats['approved_biodatas']],
                    ['label' => 'Pending', 'value' => $stats['pending_biodatas']],
                    ['label' => 'Rejected', 'value' => $stats['rejected_biodatas']],
                ]),
            ],
        ];

        $recentUsers = Registration::latest('id')->take(6)->get();
        $recentPayments = $paymentTableExists
            ? PaymentTransaction::with('registration')->latest('id')->take(6)->get()
            : collect();

        return view('admin.dashboard', compact('stats', 'charts', 'recentUsers', 'recentPayments'));
    }

    private function segments(array $segments): array
    {
        $palette = ['#f97878', '#6558e8', '#f8a52c', '#f5df8a', '#2cc76f', '#32415f'];
        $total = collect($segments)->sum('value');
        $start = 0;

        return collect($segments)->values()->map(function ($segment, $index) use (&$start, $palette, $total) {
            $percent = $total > 0 ? round(($segment['value'] / $total) * 100, 2) : 0;
            $output = [
                'label' => $segment['label'],
                'value' => $segment['value'],
                'percent' => $percent,
                'color' => $palette[$index % count($palette)],
                'start' => $start,
                'end' => $start + $percent,
            ];
            $start += $percent;
            return $output;
        })->all();
    }
}
