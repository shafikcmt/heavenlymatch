<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Registration;
use App\Models\SystemSetting;

/**
 * Computes the feature-access state for a user based on their biodata
 * completion + approval status. This is the single source of truth used by:
 *   - HandleInertiaRequests (shared `access` prop for conditional UI)
 *   - CheckBiodataCompletion middleware (server-side route protection)
 *
 * Access rule: matching features (matches / search / interests / inbox /
 * shortlist) are unlocked ONLY when the biodata exists, is completed and its
 * status is `approved`. When admin approval is disabled the wizard already
 * stamps completed biodata as `approved`, so this rule covers that case too.
 */
class UserAccessService
{
    /**
     * @return array{
     *   has_biodata: bool,
     *   is_completed: bool,
     *   completion_percentage: int,
     *   biodata_status: string|null,
     *   approval_required: bool,
     *   state: string,
     *   can_access_matches: bool,
     *   can_access_search: bool,
     *   can_send_interest: bool,
     *   missing_sections: array<int, string>,
     *   next_step_url: string
     * }
     */
    public static function compute(Registration $user): array
    {
        $biodata    = $user->biodata;
        $completion = ProfileCompletionService::compute($user);

        $hasBiodata  = $biodata !== null;
        $status      = $biodata?->status;
        $isCompleted = (bool) ($biodata?->is_completed);

        // One canonical state string the frontend can switch on.
        $state = match (true) {
            ! $hasBiodata          => 'incomplete',
            $status === 'approved' => 'approved',
            $status === 'pending'  => 'pending',
            $status === 'rejected' => 'rejected',
            $status === 'hidden'   => 'hidden',
            default                => 'incomplete', // draft / unknown
        };

        $fullAccess = $state === 'approved';

        return [
            'has_biodata'           => $hasBiodata,
            'is_completed'          => $isCompleted,
            'completion_percentage' => (int) $completion['percentage'],
            'biodata_status'        => $status,
            'approval_required'     => SystemSetting::bool('system.profile_approval_required', true),
            'state'                 => $state,
            'can_access_matches'    => $fullAccess,
            'can_access_search'     => $fullAccess,
            'can_send_interest'     => $fullAccess,
            'missing_sections'      => $completion['missing_sections'],
            'next_step_url'         => $completion['next_step_url'],
        ];
    }

    /**
     * The flash message shown when a user is redirected away from a gated
     * feature, keyed off their current biodata state.
     */
    public static function gateMessage(?string $status): string
    {
        return match ($status) {
            'pending'  => __('dashboard.access_msg_pending'),
            'rejected' => __('dashboard.access_msg_rejected'),
            'hidden'   => __('dashboard.access_msg_hidden'),
            default    => __('dashboard.access_msg_incomplete'),
        };
    }
}
