<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Registration;
use App\Models\UserNotification;
use App\Notifications\HeavenlyMatchNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Sends a re-engagement in-app notification to users who have been inactive
 * for a configurable number of days and have not been re-engaged recently.
 *
 * "Inactive" = last_login_at is null OR last_login_at < (now - inactive_days).
 * Spam guard = no 'reengagement' notification sent in the last 14 days.
 *
 * Shared-hosting safe: chunkById(100), withoutOverlapping() in schedule.
 * Run manually: php artisan notify:reengagement
 * Scheduled:    weekly on Sunday 04:00 UTC (10:00 BDT) — see routes/console.php
 */
class SendReengagementNotifications extends Command
{
    protected $signature = 'notify:reengagement
                            {--inactive-days=30  : Days since last login to consider user inactive}
                            {--cooldown-days=14  : Minimum days between re-engagement notifications}
                            {--dry-run           : Count eligible users without sending notifications}';

    protected $description = 'Send re-engagement notification to inactive users.';

    public function handle(): int
    {
        $dryRun       = $this->option('dry-run');
        $inactiveDays = (int) $this->option('inactive-days');
        $cooldownDays = (int) $this->option('cooldown-days');
        $notified     = 0;
        $skipped      = 0;
        $failed       = 0;

        $this->info($dryRun
            ? "[DRY RUN] Scanning users inactive for {$inactiveDays}+ days…"
            : "Sending re-engagement notifications (inactive >{$inactiveDays}d, cooldown {$cooldownDays}d)…"
        );

        // Users notified within the cooldown window — skip them
        $recentlySent = DB::table('user_notifications')
            ->where('type', 'reengagement')
            ->where('created_at', '>=', now()->subDays($cooldownDays))
            ->pluck('user_id')
            ->flip()
            ->all();

        Registration::query()
            ->where('account_status', 'active')
            ->where(function ($q) use ($inactiveDays): void {
                $q->whereNull('last_login_at')
                  ->orWhere('last_login_at', '<', now()->subDays($inactiveDays));
            })
            ->select(['id', 'registration_id', 'name', 'email', 'preferred_language'])
            ->chunkById(100, function ($users) use (
                $dryRun, $recentlySent, $inactiveDays, &$notified, &$skipped, &$failed
            ): void {
                foreach ($users as $user) {
                    if (isset($recentlySent[$user->registration_id])) {
                        $skipped++;
                        continue;
                    }

                    if ($dryRun) {
                        $this->line("  Would notify: {$user->registration_id}");
                        $notified++;
                        continue;
                    }

                    try {
                        $lang = $user->preferred_language ?? 'bn';

                        UserNotification::send(
                            $user->registration_id,
                            'reengagement',
                            trans('notifications.reengagement_title', [], $lang),
                            trans('notifications.reengagement_body', [], $lang),
                            ['link' => '/matches'],
                        );

                        $user->notify(new HeavenlyMatchNotification(
                            subject: trans('notifications.email_subject_reengagement', [], $lang),
                            greeting: trans('notifications.email_greeting', ['name' => $user->name], $lang),
                            introLines: [
                                trans('notifications.reengagement_body', [], $lang),
                            ],
                            actionUrl: url('/login'),
                            actionText: trans('notifications.email_action_login', [], $lang),
                        ));

                        $notified++;

                    } catch (Throwable $e) {
                        $failed++;
                        $this->error("  Failed: {$user->registration_id} — {$e->getMessage()}");
                        Log::error('notify:reengagement failed', [
                            'registration_id' => $user->registration_id,
                            'error'           => $e->getMessage(),
                        ]);
                    }
                }
            });

        $summary = $dryRun
            ? "[DRY RUN] {$notified} users would be notified."
            : "Done. Notified: {$notified}, Already-sent: {$skipped}, Failed: {$failed}.";

        $this->info($summary);

        if (! $dryRun) {
            Log::info('notify:reengagement completed', [
                'notified' => $notified,
                'skipped'  => $skipped,
                'failed'   => $failed,
            ]);
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
