<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ProfileBoost;
use App\Models\Registration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Deactivates profile boosts whose expiry time has passed.
 *
 * Also syncs is_boosted / boost_expires_at on registrations so the
 * in-app boost indicator stays accurate.
 *
 * Shared-hosting safe: chunkById(100), withoutOverlapping() in schedule.
 * Run manually: php artisan boosts:expire
 * Scheduled:    every 30 minutes — see routes/console.php
 */
class ExpireBoosts extends Command
{
    protected $signature = 'boosts:expire
                            {--dry-run : List candidates without making any changes}';

    protected $description = 'Deactivate profile boosts whose expires_at has passed.';

    public function handle(): int
    {
        $dryRun  = $this->option('dry-run');
        $expired = 0;
        $failed  = 0;

        $this->info($dryRun ? '[DRY RUN] Scanning for expired boosts…' : 'Scanning for expired boosts…');

        ProfileBoost::query()
            ->where('is_active', true)
            ->where('expires_at', '<=', now())
            ->select(['id', 'user_id', 'expires_at'])
            ->chunkById(100, function ($boosts) use ($dryRun, &$expired, &$failed): void {
                foreach ($boosts as $boost) {
                    $label = "boost#{$boost->id} user:{$boost->user_id} expired:{$boost->expires_at}";

                    if ($dryRun) {
                        $this->line("  Would expire: {$label}");
                        $expired++;
                        continue;
                    }

                    try {
                        DB::transaction(function () use ($boost): void {
                            $boost->update(['is_active' => false]);

                            // Only clear the registration flag if no other active boost remains
                            $hasOtherActive = ProfileBoost::query()
                                ->where('user_id', $boost->user_id)
                                ->where('is_active', true)
                                ->where('id', '!=', $boost->id)
                                ->exists();

                            if (! $hasOtherActive) {
                                Registration::where('registration_id', $boost->user_id)
                                    ->update([
                                        'is_boosted'       => false,
                                        'boost_expires_at' => null,
                                    ]);
                            }
                        });

                        $this->line("  Expired: {$label}");
                        $expired++;

                    } catch (Throwable $e) {
                        $failed++;
                        $this->error("  Failed: {$label} — {$e->getMessage()}");
                        Log::error('boosts:expire failed', [
                            'boost_id' => $boost->id,
                            'user_id'  => $boost->user_id,
                            'error'    => $e->getMessage(),
                        ]);
                    }
                }
            });

        $summary = $dryRun
            ? "[DRY RUN] {$expired} boosts would be expired."
            : "Done. Expired: {$expired}, Failed: {$failed}.";

        $this->info($summary);

        if (! $dryRun) {
            Log::info('boosts:expire completed', ['expired' => $expired, 'failed' => $failed]);
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
