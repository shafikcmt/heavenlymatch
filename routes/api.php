<?php

use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\PhotoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());

// Photo token issuance (guest = token for public profiles; auth = personalised)
Route::post('/photo/token', [PhotoController::class, 'issueToken']);

// Signed photo serving — no auth required; visibility enforced inside the method
Route::get('/photo/{registrationId}/{photoIndex?}', [PhotoController::class, 'serve'])
    ->where('photoIndex', '[0-9]+');

// ── Authenticated ─────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'verified.user'])->group(function () {

    // ── Matching & Search ───────────────────────────────────────────────
    Route::prefix('matches')->group(function () {
        Route::get('/',        [MatchController::class, 'index']);   // pre-computed top matches
        Route::get('/search',  [MatchController::class, 'search']);  // advanced filter search
        Route::get('/daily',   [MatchController::class, 'daily']);   // 5 best-match suggestions
    });

    // ── Photo Access (Islamic mode) ─────────────────────────────────────
    Route::post('/photo/request-access/{registrationId}', [PhotoController::class, 'requestAccess']);
    Route::post('/photo/respond-access/{requestId}',      [PhotoController::class, 'respondAccess']);
});
