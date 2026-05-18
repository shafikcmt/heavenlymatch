<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\Registration;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function start(Request $request)
    {
        if (! SystemSetting::bool('system.enable_membership_payment', true)) {
            return redirect()->route('myhome')->with('warning', 'Membership payment is currently disabled.');
        }

        $data = $request->validate([
            'membership_plan_id' => ['required', 'exists:membership_plans,id'],
            'payment_gateway_id' => ['required', 'exists:payment_gateways,id'],
        ]);

        $plan = MembershipPlan::query()
            ->where('is_active', true)
            ->findOrFail($data['membership_plan_id']);

        $gateway = PaymentGateway::query()
            ->where('is_active', true)
            ->findOrFail($data['payment_gateway_id']);

        /** @var \App\Models\Registration|null $user */
        $user = $request->user();

        $payment = PaymentTransaction::create([
            'registration_id' => $user?->id,
            'registration_code' => $user?->registration_id,
            'membership_plan_id' => $plan->id,
            'payment_gateway_id' => $gateway->id,
            'transaction_no' => $this->makeTransactionNo(),
            'plan_name' => $plan->name,
            'gateway_name' => $gateway->name,
            'duration_months' => $plan->duration_months,
            'amount' => $plan->price,
            'currency' => $plan->currency ?: 'BDT',
            'status' => 'pending',
            'customer_name' => $user?->name,
            'customer_email' => $user?->email,
            'customer_phone' => trim(($user?->country_code ? $user->country_code . ' ' : '') . ($user?->mobile_number ?? '')),
            'expires_at' => now()->addMinutes(45),
        ]);

        if ($gateway->type !== 'manual' && filled($gateway->checkout_url)) {
            $params = [
                'tran_id' => $payment->transaction_no,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'customer_name' => $payment->customer_name,
                'customer_email' => $payment->customer_email,
                'customer_phone' => $payment->customer_phone,
                'success_url' => route('payments.success', $payment),
                'fail_url' => route('payments.fail', $payment),
                'cancel_url' => route('payments.cancel', $payment),
                'callback_url' => route('payments.success', $payment),
            ];

            $separator = str_contains($gateway->checkout_url, '?') ? '&' : '?';
            $redirectUrl = $gateway->checkout_url . $separator . http_build_query($params);

            $payment->update([
                'redirect_url' => $redirectUrl,
                'payload' => [
                    'gateway_type' => $gateway->type,
                    'gateway_sandbox' => (bool) $gateway->sandbox,
                    'checkout_payload' => $params,
                ],
            ]);

            return redirect()->away($redirectUrl);
        }

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment request created. Follow the gateway instructions below.');
    }

    public function show(PaymentTransaction $payment)
    {
        $this->authorizePaymentOwner($payment);

        $gateway = $payment->gateway;

        return view('pages.user-dashboard.payment-show', compact('payment', 'gateway'));
    }

    public function manualSubmitted(Request $request, PaymentTransaction $payment)
    {
        $this->authorizePaymentOwner($payment);

        $data = $request->validate([
            'external_transaction_id' => ['nullable', 'string', 'max:120'],
            'reference_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $payment->update([
            'external_transaction_id' => $data['external_transaction_id'] ?? $payment->external_transaction_id,
            'reference_note' => $data['reference_note'] ?? $payment->reference_note,
            'status' => 'submitted',
        ]);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment proof submitted. Admin will verify and activate your membership.');
    }

    public function success(Request $request, PaymentTransaction $payment)
    {
        $this->authorizePaymentOwner($payment, allowGatewayReturn: true);

        DB::transaction(function () use ($payment, $request) {
            $payment->update([
                'status' => 'paid',
                'external_transaction_id' => $request->input('bank_tran_id', $request->input('payment_id', $payment->external_transaction_id)),
                'payload' => array_merge($payment->payload ?: [], ['gateway_return' => $request->all()]),
                'paid_at' => now(),
            ]);

            $this->activateMembership($payment);
        });

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment successful. Your membership is now active.');
    }

    public function fail(Request $request, PaymentTransaction $payment)
    {
        $this->authorizePaymentOwner($payment, allowGatewayReturn: true);

        $payment->update([
            'status' => 'failed',
            'payload' => array_merge($payment->payload ?: [], ['gateway_fail' => $request->all()]),
        ]);

        return redirect()->route('payments.show', $payment)
            ->with('error', 'Payment failed. Please try again or choose another payment method.');
    }

    public function cancel(Request $request, PaymentTransaction $payment)
    {
        $this->authorizePaymentOwner($payment, allowGatewayReturn: true);

        $payment->update([
            'status' => 'cancelled',
            'payload' => array_merge($payment->payload ?: [], ['gateway_cancel' => $request->all()]),
        ]);

        return redirect()->route('payments.show', $payment)
            ->with('warning', 'Payment cancelled. You can start a new payment anytime.');
    }

    private function makeTransactionNo(): string
    {
        do {
            $transactionNo = 'HM' . now()->format('ymdHis') . strtoupper(Str::random(5));
        } while (PaymentTransaction::where('transaction_no', $transactionNo)->exists());

        return $transactionNo;
    }

    private function authorizePaymentOwner(PaymentTransaction $payment, bool $allowGatewayReturn = false): void
    {
        $user = auth()->user();

        if (! $user && $allowGatewayReturn) {
            return;
        }

        abort_unless($user && ((int) $payment->registration_id === (int) $user->id || method_exists($user, 'isAdmin') && $user->isAdmin()), 403);
    }

    private function activateMembership(PaymentTransaction $payment): void
    {
        if (! $payment->registration_id) {
            return;
        }

        $user = Registration::find($payment->registration_id);
        if (! $user) {
            return;
        }

        $startsAt = now();
        $currentExpiry = $user->membership_expires_at instanceof Carbon ? $user->membership_expires_at : null;
        if ($currentExpiry && $currentExpiry->isFuture()) {
            $startsAt = $currentExpiry;
        }

        $expiresAt = (clone $startsAt)->addMonths((int) $payment->duration_months);

        $user->forceFill([
            'membership_plan_id' => $payment->membership_plan_id,
            'membership_plan_name' => $payment->plan_name,
            'membership_status' => 'active',
            'membership_started_at' => now(),
            'membership_expires_at' => $expiresAt,
        ])->save();
    }
}
