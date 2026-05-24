<?php

/**
 * LEGACY — No active routes reference this controller.
 * Superseded by App\Http\Controllers\Payment\PaymentController::plans() (Inertia).
 * Broken route refs: route('myhome') — now route('dashboard').
 */

namespace App\Http\Controllers\Legacy;

use App\Models\MembershipPlan;
use App\Models\PaymentGateway;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;

class MembershipController extends Controller
{
    public function index()
    {
        if (! SystemSetting::bool('system.enable_membership_payment', true)) {
            return redirect()->route('myhome')->with('warning', 'Membership payment is currently disabled.');
        }

        $plans = collect();
        $gateways = collect();

        if (Schema::hasTable('membership_plans')) {
            $plans = MembershipPlan::query()
                ->where('is_active', true)
                ->orderBy('duration_months')
                ->orderBy('sort_order')
                ->orderBy('price')
                ->get();
        }

        if (Schema::hasTable('payment_gateways')) {
            $gateways = PaymentGateway::query()
                ->where('is_active', true)
                ->orderByDesc('is_default')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        return view('pages.user-dashboard.upgrade', compact('plans', 'gateways'));
    }
}
