<?php

namespace App\Providers;

use App\Contracts\MatchingScorerInterface;
use App\Services\MatchingEngine;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind the matching scorer interface to the local weighted engine.
        // To swap in AWS SageMaker ML scoring, replace MatchingEngine::class
        // with App\Services\AwsMlScorer::class here (no other code changes needed).
        $this->app->bind(MatchingScorerInterface::class, MatchingEngine::class);
    }

    public function boot(): void
    {
        //
    }
}
