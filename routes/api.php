<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BiodataController;
use App\Http\Controllers\Api\ConnectionController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\PhotoController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────────────

// Authentication
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// Photo serving — signed token enforces privacy server-side; no auth required
Route::post('/photo/token', [PhotoController::class, 'issueToken']);
Route::get('/photo/{registrationId}/{photoIndex?}', [PhotoController::class, 'serve'])
    ->where('photoIndex', '[0-9]+')
    ->name('api.photo.serve');

// Public biodata profile view (approved only)
Route::get('/profile/{registrationId}', [BiodataController::class, 'profile']);

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // Biodata (own profile)
    Route::prefix('biodata')->group(function () {
        Route::get('/me',  [BiodataController::class, 'show']);
        Route::put('/me',  [BiodataController::class, 'update']);
    });

    // Matches & Search
    Route::prefix('matches')->group(function () {
        Route::get('/',       [MatchController::class, 'index']);   // pre-computed top matches
        Route::get('/search', [MatchController::class, 'search']);  // filtered search
        Route::get('/daily',  [MatchController::class, 'daily']);   // 5 daily suggestions
    });

    // Connection requests
    Route::prefix('connections')->group(function () {
        Route::post('/',             [ConnectionController::class, 'send']);
        Route::post('/{id}/respond', [ConnectionController::class, 'respond']);
        Route::get('/received',      [ConnectionController::class, 'received']);
        Route::get('/sent',          [ConnectionController::class, 'sent']);
        Route::delete('/{id}',       [ConnectionController::class, 'withdraw']);
    });

    // Photo access (Islamic mode)
    Route::prefix('photo')->group(function () {
        Route::post('/request-access/{registrationId}', [PhotoController::class, 'requestAccess']);
        Route::post('/respond-access/{requestId}',      [PhotoController::class, 'respondAccess']);
    });
});
