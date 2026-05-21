<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Permanently deletes soft-deleted messages older than --days (default 90).
 *
 * Messages are soft-deleted by users (deleted_at is set). This command
 * hard-deletes them after the retention window so the table stays lean.
 * Conversations and connection records are NOT touched.
 *
 * Shared-hosting safe: chunkById(200), withoutOverlapping() in schedule.
 * Run manually: php artisan messages:purge
 *               php artisan messages:purge --days=30
 * Scheduled:    weekly — see routes/console.php
 */
class PurgeOldMessages extends Command
{
    protected $signature = 'messages:purge
                            {--days=90  : Permanently delete soft-deleted messages older than this many days}
                            {--dry-run  : Count purgeable messages without deleting them}';

    protected $description = 'Permanently delete soft-deleted messages older than --days days.';

    public function handle(): int
    {
        $days   = max(1, (int) $this->option('days'));
        $dryRun = $this->option('dry-run');
        $purged = 0;
        $failed = 0;

        $cutoff = now()->subDays($days);

        $this->info($dryRun
            ? "[DRY RUN] Messages soft-deleted before {$cutoff}…"
            : "Purging messages soft-deleted before {$cutoff}…"
        );

        // withTrashed() + whereNotNull('deleted_at') to target only soft-deleted rows
        Message::withTrashed()
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '<=', $cutoff)
            ->select(['id', 'conversation_id', 'deleted_at'])
            ->chunkById(200, function ($messages) use ($dryRun, &$purged, &$failed): void {
                foreach ($messages as $message) {
                    if ($dryRun) {
                        $purged++;
                        continue;
                    }

                    try {
                        $message->forceDelete();
                        $purged++;
                    } catch (Throwable $e) {
                        $failed++;
                        $this->error("  Failed message#{$message->id}: {$e->getMessage()}");
                        Log::error('messages:purge failed', [
                            'message_id' => $message->id,
                            'error'      => $e->getMessage(),
                        ]);
                    }
                }

                if (! $dryRun) {
                    $this->line("  Purged batch of {$messages->count()} messages.");
                }
            });

        $summary = $dryRun
            ? "[DRY RUN] {$purged} messages would be purged."
            : "Done. Purged: {$purged}, Failed: {$failed}.";

        $this->info($summary);

        if (! $dryRun) {
            Log::info('messages:purge completed', [
                'days'   => $days,
                'purged' => $purged,
                'failed' => $failed,
            ]);
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
