<?php

declare(strict_types=1);

/**
 * Notification messages — used in both database notifications and emails.
 * Use :placeholder syntax for dynamic values.
 */
return [

    // ── Interest / connection ─────────────────────────────────────────────────
    'interest_received_title'   => ':name sent you an interest',
    'interest_received_body'    => ':name is interested in your profile. View their biodata and respond.',
    'interest_accepted_title'   => ':name accepted your interest',
    'interest_accepted_body'    => 'Great news! :name accepted your interest. You can now message each other.',
    'interest_declined_title'   => ':name declined your interest',
    'interest_declined_body'    => ':name has declined your interest.',
    'guardian_pending_title'    => 'Awaiting guardian approval',
    'guardian_pending_body'     => 'The interest request is awaiting approval from :name\'s guardian.',

    // ── Photo access ──────────────────────────────────────────────────────────
    'photo_requested_title'     => ':name requested your photo',
    'photo_requested_body'      => ':name has requested access to your photos. View their profile to decide.',
    'photo_granted_title'       => ':name granted photo access',
    'photo_granted_body'        => ':name has approved your photo request. Visit their profile to see photos.',
    'photo_denied_title'        => 'Photo request not approved',
    'photo_denied_body'         => ':name has declined your photo request.',

    // ── Messaging ─────────────────────────────────────────────────────────────
    'message_received_title'    => 'New message from :name',
    'message_received_body'     => ':name sent you a message.',

    // ── Membership / payment ──────────────────────────────────────────────────
    'payment_received_title'    => 'Payment received',
    'payment_received_body'     => 'We received your :plan payment. It is under review and will be confirmed within 24 hours.',
    'membership_activated_title'=> ':plan membership activated',
    'membership_activated_body' => 'Congratulations! Your :plan membership is now active until :date.',
    'membership_rejected_title' => 'Payment not verified',
    'membership_rejected_body'  => 'We could not verify your payment. Please contact support or resubmit.',
    'membership_expiring_title' => 'Membership expiring soon',
    'membership_expiring_body'  => 'Your :plan membership expires on :date. Renew now to stay connected.',
    'membership_expired_title'  => 'Membership expired',
    'membership_expired_body'   => 'Your :plan membership has expired. Renew to continue accessing premium features.',

    // ── Biodata / verification ────────────────────────────────────────────────
    'biodata_approved_title'    => 'Biodata approved',
    'biodata_approved_body'     => 'Your biodata has been approved and is now visible to members.',
    'biodata_rejected_title'    => 'Biodata needs corrections',
    'biodata_rejected_body'     => 'Your biodata was not approved. Reason: :reason. Please update and resubmit.',
    'identity_verified_title'   => 'Identity verified',
    'identity_verified_body'    => 'Your identity has been verified. Your profile now shows a Verified badge.',

    // ── Daily matches ─────────────────────────────────────────────────────────
    'daily_match_title'         => ':count new match suggestions for you',
    'daily_match_title_generic' => 'New match suggestions are ready',
    'daily_match_body'          => 'Visit your Matches page to see today\'s suggestions.',

    // ── Re-engagement ─────────────────────────────────────────────────────────
    'reengagement_title'        => 'We miss you on HeavenlyMatch',
    'reengagement_body'         => 'New profiles are waiting. Log in to see your latest match suggestions.',

    // ── Profile views ─────────────────────────────────────────────────────────
    'profile_viewed_title'      => 'Someone viewed your profile',
    'profile_viewed_body'       => ':count people viewed your profile today.',

    // ── Reports ───────────────────────────────────────────────────────────────
    'report_resolved_title'     => 'Report reviewed',
    'report_resolved_body'      => 'Your report has been reviewed by our team. Thank you for helping keep HeavenlyMatch safe.',

    // ── Admin / system ────────────────────────────────────────────────────────
    'account_warning_title'     => 'Account Warning',
    'account_warning_body'      => 'Your account has received a warning: :reason',
    'account_suspended_title'   => 'Account Suspended',
    'account_suspended_body'    => 'Your account has been suspended. Reason: :reason. Contact support to appeal.',

    // ── Email subjects (used in Mailable subject lines) ───────────────────────
    'email_subject_verify'       => 'Verify your HeavenlyMatch email address',
    'email_subject_reset'        => 'Reset your HeavenlyMatch password',
    'email_subject_interest'     => ':name sent you a marriage interest',
    'email_subject_accepted'     => 'Your interest has been accepted',
    'email_subject_membership'   => 'Your HeavenlyMatch :plan membership is active',
    'email_subject_biodata'      => 'Your biodata status update',
    'email_subject_daily'        => 'Your daily match suggestions are ready',

];
