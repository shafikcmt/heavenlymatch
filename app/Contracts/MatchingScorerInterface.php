<?php

namespace App\Contracts;

use App\Models\Biodata;
use Illuminate\Support\Collection;

/**
 * Abstraction layer for the matching scorer.
 * Current implementation: MatchingEngine (weighted rules, runs locally).
 * Future implementation: AwsMlScorer (calls SageMaker endpoint).
 *
 * Swap the binding in AppServiceProvider when AWS ML is ready:
 *   $this->app->bind(MatchingScorerInterface::class, AwsMlScorer::class);
 */
interface MatchingScorerInterface
{
    /**
     * Compute a 0–100 compatibility score between two biodatas.
     *
     * @return array{total_score: int, score_breakdown: array<string, int>}
     */
    public function score(Biodata $seeker, Biodata $candidate): array;

    /**
     * Return top-N matches for a seeker's biodata.
     *
     * @return Collection<int, array{biodata: Biodata, total_score: int, score_breakdown: array}>
     */
    public function topMatches(Biodata $seekerBio, int $limit = 20): Collection;
}
