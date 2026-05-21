<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\Registration;
use Illuminate\Support\Carbon;

class MembershipService
{
    /**
     * Activate membership for a user based on an approved transaction.
     * Uses forceFill because membership fields must never be mass-assignable.
     */
    public function activate(Registration $user, PaymentTransaction $txn): void
    {
        $expiresAt = Carbon::now()->addMonths((int) $txn->duration_months);

        $user->forceFill([
            'membership_plan_id'    => $txn->membership_plan_id,
            'membership_plan_name'  => $txn->plan_name,
            'membership_status'     => 'active',
            'membership_started_at' => now(),
            'membership_expires_at' => $expiresAt,
        ])->save();

        $txn->forceFill([
            'status'     => 'paid',
            'paid_at'    => now(),
            'expires_at' => $expiresAt,
        ])->save();
    }

    /**
     * Expire a user's membership (called by a scheduled command).
     */
    public function expire(Registration $user): void
    {
        $user->forceFill([
            'membership_status'     => 'expired',
            'membership_plan_id'    => null,
            'membership_plan_name'  => null,
            'membership_expires_at' => null,
        ])->save();
    }
}
