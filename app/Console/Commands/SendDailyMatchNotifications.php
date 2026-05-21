<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Registration;
use App\Models\UserNotification;
use App\Notifications\HeavenlyMatchNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Sends a daily "new matches available" in-app notification to active users
 * who have an approved biodata and have not been notified yet today.
 *
 * Match scores are pre-computed nightly by ComputeMatchScoresJob. If no scores
 * exist yet for a user, the notification still fires (it links to the /matches
 * page where the user can browse manually).
 *
 * Spam guard: skips users who already received a 'daily_match' notification
 * in the last 20 hours (so back-to-back runs are safe).
 *
 * Shared-hosting safe: chunkById(100), withoutOverlapping() in schedule.
 * Run manually: php artisan notify:daily-matches
 * Scheduled:    daily at 03:00 UTC (09:00 BDT) — see routes/console.php
 */
class SendDailyMatchNotifications extends Command
{
    protected $signature = 'notify:daily-matches
                            {--dry-run : Count eligible users without sending notifications}';

    protected $description = 'Send daily match notification to active users with approved biodata.';

    public function handle(): int
    {
        $dryRun   = $this->option('dry-run');
        $notified = 0;
        $skipped  = 0;
        $failed   = 0;

        $this->info($dryRun ? '[DRY RUN] Counting eligible users…' : 'Sending daily match notifications…');

        // Users who already got a daily_match notification in the last 20 hours
        $alreadyNotified = DB::table('user_notifications')
            ->where('type', 'daily_match')
            ->where('created_at', '>=', now()->subHours(20))
            ->pluck('user_id')
            ->flip()
            ->all();

        Registration::query()
            ->where('account_status', 'active')
            ->whereHas('biodata', fn ($q) => $q->where('status', 'approved'))
            ->select(['id', 'registration_id', 'name', 'email', 'preferred_language'])
            ->chunkById(100, function ($users) use (
                $dryRun, $alreadyNotified, &$notified, &$skipped, &$failed
            ): void {
                foreach ($users as $user) {
                    if (isset($alreadyNotified[$user->registration_id])) {
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

                        // Count pre-computed matches in the user's language context
                        $matchCount = DB::table('match_scores')
                            ->where('user_id', $user->registration_id)
                            ->where('total_score', '>=', 50)
                            ->count();

                        $title = $matchCount > 0
                            ? trans('notifications.daily_match_title', ['count' => $matchCount], $lang)
                            : trans('notifications.daily_match_title_generic', [], $lang);

                        $body = trans('notifications.daily_match_body', [], $lang);

                        UserNotification::send(
                            $user->registration_id,
                            'daily_match',
                            $title,
                            $body,
                            ['match_count' => $matchCount, 'link' => '/matches'],
                        );

                        $user->notify(new HeavenlyMatchNotification(
                            subject: trans('notifications.email_subject_daily', [], $lang),
                            greeting: trans('notifications.email_greeting', ['name' => $user->name], $lang),
                            introLines: [$body],
                            actionUrl: url('/matches'),
                            actionText: trans('notifications.email_action_view_matches', [], $lang),
                        ));

                        $notified++;

                    } catch (Throwable $e) {
                        $failed++;
                        $this->error("  Failed: {$user->registration_id} — {$e->getMessage()}");
                        Log::error('notify:daily-matches failed', [
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
            Log::info('notify:daily-matches completed', [
                'notified' => $notified,
                'skipped'  => $skipped,
                'failed'   => $failed,
            ]);
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
