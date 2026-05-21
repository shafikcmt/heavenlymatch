<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\SystemSetting;
use App\Models\UserNotification;
use App\Notifications\HeavenlyMatchNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    // GET /upgrade
    public function plans(): Response
    {
        /** @var \App\Models\Registration $user */
        $user = Auth::user();

        $plans = MembershipPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get([
                'id', 'name', 'slug', 'duration_months', 'price', 'currency',
                'features', 'badge', 'color_hex', 'is_popular',
                'contact_view_limit', 'message_limit', 'priority_placement', 'family_support',
            ]);

        $gateways = PaymentGateway::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'type', 'merchant_id', 'instructions']);

        $pendingTxn = PaymentTransaction::where('registration_id', $user->registration_id)
            ->where('status', 'pending')
            ->latest()
            ->first(['id', 'transaction_no', 'plan_name', 'amount', 'external_transaction_id']);

        return Inertia::render('Upgrade/Plans', [
            'plans'             => $plans,
            'gateways'          => $gateways,
            'currentPlan'       => $user->membership_plan_name,
            'membershipStatus'  => $user->membership_status,
            'membershipExpires' => $user->membership_expires_at?->toDateString(),
            'pendingPayment'    => $pendingTxn ? [
                'transaction_no'          => $pendingTxn->transaction_no,
                'plan_name'               => $pendingTxn->plan_name,
                'amount'                  => $pendingTxn->amount,
                'is_submitted'            => (bool) $pendingTxn->external_transaction_id,
            ] : null,
        ]);
    }

    // POST /upgrade/checkout
    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id'    => ['required', 'integer', 'exists:membership_plans,id'],
            'gateway_id' => ['required', 'integer', 'exists:payment_gateways,id'],
        ]);

        /** @var \App\Models\Registration $user */
        $user = Auth::user();

        // Guard: only one pending payment at a time
        $existing = PaymentTransaction::where('registration_id', $user->registration_id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return redirect()->route('upgrade.manual', $existing->transaction_no)
                ->with('info', __('pricing.payment_pending'));
        }

        $plan    = MembershipPlan::findOrFail($validated['plan_id']);
        $gateway = PaymentGateway::findOrFail($validated['gateway_id']);

        $months    = (int) $plan->duration_months;
        $planLabel = $plan->name . ' — ' . $months . ($months === 1 ? ' month' : ' months');

        $txn = PaymentTransaction::create([
            'registration_id'    => $user->registration_id,
            'membership_plan_id' => $plan->id,
            'payment_gateway_id' => $gateway->id,
            'transaction_no'     => 'HMT' . date('YmdHis') . strtoupper(Str::random(4)),
            'plan_name'          => $planLabel,
            'gateway_name'       => $gateway->name,
            'duration_months'    => $plan->duration_months,
            'amount'             => $plan->price,
            'currency'           => $plan->currency ?? 'BDT',
            'status'             => 'pending',
            'customer_name'      => $user->name,
            'customer_email'     => $user->email,
            'customer_phone'     => $user->mobile_number,
        ]);

        return redirect()->route('upgrade.manual', $txn->transaction_no);
    }

    // GET /upgrade/manual/{txn}
    public function manualForm(string $txnNo): Response|RedirectResponse
    {
        /** @var \App\Models\Registration $user */
        $user = Auth::user();

        $txn = PaymentTransaction::with('gateway')
            ->where('transaction_no', $txnNo)
            ->where('registration_id', $user->registration_id)
            ->firstOrFail();

        if ($txn->status !== 'pending') {
            return redirect()->route('upgrade.status');
        }

        // If already submitted, redirect to status
        if ($txn->external_transaction_id) {
            return redirect()->route('upgrade.status')
                ->with('info', __('pricing.payment_pending'));
        }

        $merchantNumber = $txn->gateway?->merchant_id
            ?? SystemSetting::get('payment.merchant_number', '');

        return Inertia::render('Upgrade/ManualPayment', [
            'transaction' => [
                'transaction_no'  => $txn->transaction_no,
                'plan_name'       => $txn->plan_name,
                'amount'          => (float) $txn->amount,
                'gateway_name'    => $txn->gateway_name,
                'gateway_type'    => $txn->gateway?->type ?? 'manual',
                'merchant_number' => $merchantNumber,
                'instructions'    => $txn->gateway?->instructions ?? '',
            ],
        ]);
    }

    // POST /upgrade/manual/{txn}
    public function manualSubmit(Request $request, string $txnNo): RedirectResponse
    {
        $validated = $request->validate([
            'sender_number'           => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
            'external_transaction_id' => ['required', 'string', 'min:8', 'max:50'],
            'screenshot'              => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'sender_number.regex'              => 'Please enter a valid Bangladesh mobile number (01XXXXXXXXX).',
            'external_transaction_id.required' => 'Transaction ID is required.',
            'external_transaction_id.min'      => 'Transaction ID must be at least 8 characters.',
        ]);

        /** @var \App\Models\Registration $user */
        $user = Auth::user();

        $txn = PaymentTransaction::where('transaction_no', $txnNo)
            ->where('registration_id', $user->registration_id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Guard: transaction ID must be unique across all submissions
        $duplicate = PaymentTransaction::where('external_transaction_id', $validated['external_transaction_id'])
            ->where('id', '!=', $txn->id)
            ->exists();

        if ($duplicate) {
            return back()->withErrors([
                'external_transaction_id' => 'This transaction ID has already been submitted.',
            ]);
        }

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')
                ->store("payment-screenshots/{$user->registration_id}", 'public');
        }

        $txn->update([
            'external_transaction_id' => $validated['external_transaction_id'],
            'sender_number'           => $validated['sender_number'],
            'screenshot_path'         => $screenshotPath,
        ]);

        $lang = $user->preferred_language ?? 'bn';

        UserNotification::send(
            $user->registration_id,
            'payment',
            __('notifications.payment_received_title'),
            __('notifications.payment_received_body', ['plan' => $txn->plan_name]),
        );

        $user->notify(new HeavenlyMatchNotification(
            subject: trans('notifications.email_subject_payment_submitted', [], $lang),
            greeting: trans('notifications.email_greeting', ['name' => $user->name], $lang),
            introLines: [
                trans('notifications.payment_received_title', [], $lang),
                trans('notifications.payment_received_body', ['plan' => $txn->plan_name], $lang),
            ],
            actionUrl: url('/upgrade/status'),
            actionText: trans('notifications.email_action_check_status', [], $lang),
        ));

        return redirect()->route('upgrade.status')
            ->with('success', __('pricing.payment_submitted'));
    }

    // GET /upgrade/status
    public function status(): Response
    {
        /** @var \App\Models\Registration $user */
        $user = Auth::user();

        $latest = PaymentTransaction::where('registration_id', $user->registration_id)
            ->orderByDesc('created_at')
            ->first(['transaction_no', 'plan_name', 'amount', 'status', 'created_at', 'external_transaction_id', 'admin_note']);

        return Inertia::render('Upgrade/PaymentStatus', [
            'membershipStatus'  => $user->membership_status,
            'membershipPlan'    => $user->membership_plan_name,
            'membershipExpires' => $user->membership_expires_at?->toDateString(),
            'latestTransaction' => $latest ? [
                'transaction_no' => $latest->transaction_no,
                'plan_name'      => $latest->plan_name,
                'amount'         => (float) $latest->amount,
                'status'         => $latest->status,
                'is_submitted'   => (bool) $latest->external_transaction_id,
                'admin_note'     => $latest->admin_note,
                'created_at'     => $latest->created_at->toDateTimeString(),
            ] : null,
        ]);
    }

    // Legacy route stubs (kept so existing named routes don't 404)
    public function callback(Request $request): RedirectResponse
    {
        return redirect()->route('upgrade.status');
    }

    public function success(Request $request): RedirectResponse
    {
        return redirect()->route('upgrade.status');
    }
}
