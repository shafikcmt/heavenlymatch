<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Registration;
use App\Models\UserNotification;
use App\Notifications\HeavenlyMatchNotification;
use App\Services\MembershipService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Scans all active memberships whose expiry date has passed and marks them expired.
 *
 * Shared-hosting safe:
 *  - chunkById(100) avoids loading all rows into memory at once.
 *  - withoutOverlapping() in the schedule prevents parallel runs.
 *  - Each record is expired individually so a single failure doesn't abort the batch.
 *
 * Run manually: php artisan memberships:expire
 * Scheduled:    daily at 18:00 UTC (midnight BDT, UTC+6) — see routes/console.php
 * cPanel cron:  * * * * * /usr/local/bin/php /home/USERNAME/public_html/artisan schedule:run >> /dev/null 2>&1
 */
class ExpireMemberships extends Command
{
    protected $signature = 'memberships:expire
                            {--dry-run : List candidates without making any changes}';

    protected $description = 'Expire active memberships whose expiry date has passed.';

    public function __construct(private readonly MembershipService $membership)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun   = $this->option('dry-run');
        $expired  = 0;
        $failed   = 0;

        $this->info($dryRun ? '[DRY RUN] Scanning for expired memberships…' : 'Scanning for expired memberships…');

        Registration::query()
            ->where('membership_status', 'active')
            ->where('membership_expires_at', '<=', now())
            ->select(['id', 'registration_id', 'name', 'email', 'preferred_language', 'membership_plan_name', 'membership_expires_at'])
            ->chunkById(100, function ($users) use ($dryRun, &$expired, &$failed): void {
                foreach ($users as $user) {
                    $label = "{$user->registration_id} (plan: {$user->membership_plan_name}, expired: {$user->membership_expires_at})";

                    if ($dryRun) {
                        $this->line("  Would expire: {$label}");
                        $expired++;
                        continue;
                    }

                    try {
                        $planName = $user->membership_plan_name ?? 'Premium';

                        $this->membership->expire($user);

                        $lang = $user->preferred_language ?? 'bn';

                        UserNotification::send(
                            $user->registration_id,
                            'membership_expired',
                            trans('notifications.membership_expired_title', [], $lang),
                            trans('notifications.membership_expired_body', ['plan' => $planName], $lang),
                        );

                        $user->notify(new HeavenlyMatchNotification(
                            subject: trans('notifications.email_subject_membership_expired', [], $lang),
                            greeting: trans('notifications.email_greeting', ['name' => $user->name], $lang),
                            introLines: [
                                trans('notifications.membership_expired_title', [], $lang),
                                trans('notifications.membership_expired_body', ['plan' => $planName], $lang),
                            ],
                            actionUrl: url('/upgrade'),
                            actionText: trans('notifications.email_action_renew', [], $lang),
                        ));

                        $this->line("  Expired: {$label}");
                        $expired++;

                    } catch (Throwable $e) {
                        $failed++;
                        $this->error("  Failed: {$label} — {$e->getMessage()}");
                        Log::error('memberships:expire failed for user', [
                            'registration_id' => $user->registration_id,
                            'error'           => $e->getMessage(),
                        ]);
                    }
                }
            });

        $summary = $dryRun
            ? "[DRY RUN] {$expired} memberships would be expired."
            : "Done. Expired: {$expired}, Failed: {$failed}.";

        $this->info($summary);

        if (! $dryRun) {
            Log::info('memberships:expire completed', [
                'expired' => $expired,
                'failed'  => $failed,
            ]);
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
