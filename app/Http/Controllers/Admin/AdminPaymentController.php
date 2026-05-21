<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\UserNotification;
use App\Notifications\HeavenlyMatchNotification;
use App\Services\MembershipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminPaymentController extends Controller
{
    public function __construct(private readonly MembershipService $membership) {}

    public function index(): Response
    {
        $pending = PaymentTransaction::with([
                'registration:registration_id,name,email,mobile_number',
            ])
            ->pendingReview()
            ->orderBy('updated_at')       // oldest first — fair queue
            ->paginate(20)
            ->through(fn ($txn) => [
                'id'                      => $txn->id,
                'transaction_no'          => $txn->transaction_no,
                'plan_name'               => $txn->plan_name,
                'amount'                  => (float) $txn->amount,
                'gateway_name'            => $txn->gateway_name,
                'external_transaction_id' => $txn->external_transaction_id,
                'sender_number'           => $txn->sender_number,
                'screenshot_path'         => $txn->screenshot_path
                    ? asset('storage/' . $txn->screenshot_path)
                    : null,
                'submitted_at'            => $txn->updated_at->toDateTimeString(),
                'user'                    => $txn->registration ? [
                    'registration_id' => $txn->registration->registration_id,
                    'name'            => $txn->registration->name,
                    'email'           => $txn->registration->email,
                    'phone'           => $txn->registration->mobile_number,
                ] : null,
            ]);

        $recent = PaymentTransaction::with([
                'registration:registration_id,name',
            ])
            ->whereIn('status', ['paid', 'failed'])
            ->whereNotNull('reviewed_at')
            ->orderByDesc('reviewed_at')
            ->take(15)
            ->get()
            ->map(fn ($txn) => [
                'id'             => $txn->id,
                'transaction_no' => $txn->transaction_no,
                'plan_name'      => $txn->plan_name,
                'amount'         => (float) $txn->amount,
                'status'         => $txn->status,
                'admin_note'     => $txn->admin_note,
                'reviewed_at'    => $txn->reviewed_at?->toDateTimeString(),
                'user_name'      => $txn->registration?->name,
            ]);

        $stats = [
            'pending_count' => PaymentTransaction::pendingReview()->count(),
            'approved_today' => PaymentTransaction::where('status', 'paid')
                ->whereDate('reviewed_at', today())->count(),
            'rejected_today' => PaymentTransaction::where('status', 'failed')
                ->whereDate('reviewed_at', today())->count(),
        ];

        return Inertia::render('Admin/Payments', [
            'pending' => $pending,
            'recent'  => $recent,
            'stats'   => $stats,
        ]);
    }

    public function approve(Request $request, int $id): RedirectResponse
    {
        $txn = PaymentTransaction::with('registration')->findOrFail($id);

        if ($txn->status !== 'pending') {
            return back()->with('info', 'Payment has already been reviewed.');
        }

        // Mark reviewed before activating (in case activate throws)
        $txn->forceFill([
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ])->save();

        $this->membership->activate($txn->registration, $txn);

        if ($txn->registration) {
            $member = $txn->registration;
            $lang   = $member->preferred_language ?? 'bn';

            UserNotification::send(
                $member->registration_id,
                'membership',
                __('notifications.membership_activated_title', ['plan' => $txn->plan_name]),
                __('notifications.membership_activated_body', [
                    'plan' => $txn->plan_name,
                    'date' => $txn->expires_at?->format('d M Y') ?? '',
                ]),
                ['transaction_no' => $txn->transaction_no],
            );

            $member->notify(new HeavenlyMatchNotification(
                subject: trans('notifications.email_subject_membership', ['plan' => $txn->plan_name], $lang),
                greeting: trans('notifications.email_greeting', ['name' => $member->name], $lang),
                introLines: [
                    trans('notifications.membership_activated_title', ['plan' => $txn->plan_name], $lang),
                    trans('notifications.membership_activated_body', [
                        'plan' => $txn->plan_name,
                        'date' => $txn->expires_at?->format('d M Y') ?? '',
                    ], $lang),
                ],
                actionUrl: url('/dashboard'),
                actionText: trans('notifications.email_action_go_dashboard', [], $lang),
            ));
        }

        return back()->with('success', __('admin.payment_approved'));
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'admin_note' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $txn = PaymentTransaction::with('registration')->findOrFail($id);

        if ($txn->status !== 'pending') {
            return back()->with('info', 'Payment has already been reviewed.');
        }

        $txn->forceFill([
            'status'      => 'failed',
            'admin_note'  => $request->input('admin_note'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ])->save();

        if ($txn->registration) {
            $member = $txn->registration;
            $lang   = $member->preferred_language ?? 'bn';

            UserNotification::send(
                $member->registration_id,
                'membership',
                __('notifications.membership_rejected_title'),
                __('notifications.membership_rejected_body'),
                ['reason' => $request->input('admin_note')],
            );

            $member->notify(new HeavenlyMatchNotification(
                subject: trans('notifications.email_subject_payment_rejected', [], $lang),
                greeting: trans('notifications.email_greeting', ['name' => $member->name], $lang),
                introLines: [
                    trans('notifications.membership_rejected_title', [], $lang),
                    trans('notifications.membership_rejected_body', [], $lang),
                ],
                actionUrl: url('/upgrade/status'),
                actionText: trans('notifications.email_action_check_status', [], $lang),
            ));
        }

        return back()->with('success', __('admin.payment_rejected'));
    }
}
