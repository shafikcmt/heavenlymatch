<?php

namespace App\Jobs;

use App\Models\Biodata;
use App\Models\MatchScore;
use App\Models\Registration;
use App\Services\MatchingEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Runs nightly (02:00 BDT) via the scheduler.
 * Processes users in chunks to stay within memory limits.
 * On a 10K-user platform this completes in ~3-5 minutes on a standard VPS.
 */
class ComputeMatchScoresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;
    public int $tries   = 1;

    public function handle(MatchingEngine $engine): void
    {
        $processedCount = 0;

        Registration::where('account_status', 'active')
            ->whereNull('deactivated_at')
            ->whereHas('biodata', fn ($q) => $q->where('status', 'approved')->where('is_completed', true))
            ->select(['id', 'registration_id', 'gender'])
            ->chunkById(100, function ($users) use ($engine, &$processedCount) {
                foreach ($users as $user) {
                    try {
                        $this->computeForUser($user, $engine);
                        $processedCount++;
                    } catch (\Throwable $e) {
                        Log::warning("MatchScore computation failed for {$user->registration_id}: {$e->getMessage()}");
                    }
                }
            });

        Log::info("ComputeMatchScoresJob completed. Processed: {$processedCount} users.");
    }

    private function computeForUser(Registration $user, MatchingEngine $engine): void
    {
        $topMatches = $engine->topMatches($user, 30);

        // Delete stale scores
        MatchScore::where('user_id', $user->registration_id)->delete();

        if ($topMatches->isEmpty()) {
            return;
        }

        // Bulk upsert — efficient even on 10K rows
        $rows = $topMatches->map(fn ($m) => [
            'user_id'         => $user->registration_id,
            'candidate_id'    => $m['biodata']->registration_id,
            'total_score'     => $m['total_score'],
            'score_breakdown' => json_encode($m['score_breakdown']),
            'computed_at'     => now(),
        ])->all();

        MatchScore::insert($rows);
    }
}
