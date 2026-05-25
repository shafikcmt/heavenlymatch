<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ConnectionRequest;
use App\Models\MatchScore;
use App\Models\ProfileView;
use App\Models\Registration;
use App\Services\MatchingEngine;
use App\Services\PhotoPrivacyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly MatchingEngine $engine,
        private readonly PhotoPrivacyService $photoService,
    ) {}

    public function index(Request $request): Response
    {
        /** @var Registration $user */
        $user = $request->user();
        $regId = $user->registration_id;

        // ── Stats (cached 15 min) ─────────────────────────────────────────────
        $stats = Cache::remember("dashboard_stats:{$regId}", 900, function () use ($regId) {
            return [
                'matches_today'      => MatchScore::where('user_id', $regId)
                    ->where('computed_at', '>=', now()->startOfDay())->count(),
                'interests_received' => ConnectionRequest::where('receiver_id', $regId)
                    ->where('status', 'pending')->count(),
                'interests_sent'     => ConnectionRequest::where('sender_id', $regId)
                    ->where('status', 'pending')->count(),
                'profile_views'      => \App\Models\ProfileView::where('profile_id', $regId)
                    ->where('viewed_at', '>=', now()->subDays(7))->count(),
                'messages_unread'    => \App\Models\Message::whereHas('conversation', fn ($q) =>
                        $q->where('user_a_id', $regId)->orWhere('user_b_id', $regId)
                    )
                    ->where('sender_id', '!=', $regId)
                    ->whereNull('read_at')
                    ->count(),
                'shortlisted_count'  => \Illuminate\Support\Facades\DB::table('shortlists')
                    ->where('user_id', $regId)->count(),
            ];
        });

        // ── Daily picks (5 best AI matches, cached until midnight) ───────────
        $dailyPicks = Cache::remember(
            "daily_picks:{$regId}:" . now()->toDateString(),
            (int) now()->diffInSeconds(now()->endOfDay()),
            function () use ($user, $regId) {
                $bio = $user->biodata;
                if (! $bio) return [];

                $precomputed = MatchScore::where('user_id', $regId)
                    ->orderByDesc('total_score')
                    ->limit(5)
                    ->with('candidate.biodata')
                    ->get();

                if ($precomputed->isNotEmpty()) {
                    return $precomputed->map(fn ($ms) => $this->formatProfile($ms->candidate, $ms->total_score, $ms->score_breakdown, $user))->filter()->values()->all();
                }

                return $this->engine->topMatchesForUser($user, 5)
                    ->map(fn ($m) => $this->formatProfile($m['biodata']->registration, $m['total_score'], $m['score_breakdown'], $user))
                    ->filter()->values()->all();
            }
        );

        // ── Recent visitors (premium sees names, free sees blurred count) ─────
        $recentVisitors = \App\Models\ProfileView::where('profile_id', $regId)
            ->whereNotNull('viewer_id')
            ->where('viewer_id', '!=', $regId)
            ->where('viewed_at', '>=', now()->subDays(30))
            ->selectRaw('viewer_id, MAX(viewed_at) as last_view')
            ->groupBy('viewer_id')
            ->orderByDesc('last_view')
            ->limit($user->hasActiveMembership() ? 10 : 3)
            ->pluck('viewer_id')
            ->mapWithKeys(fn ($id) => [$id => Registration::where('registration_id', $id)->select('registration_id', 'name', 'gender')->first()])
            ->filter()
            ->values()
            ->map(fn ($r) => [
                'registration_id' => $r->registration_id,
                'name'            => $r->name,
                'gender'          => $r->gender,
                'photo_url'       => null,
                'has_photo'       => false,
            ])
            ->all();

        // ── Biodata completeness ──────────────────────────────────────────────
        $completeness = $user->biodata?->completeness_score ?? 0;

        return Inertia::render('Dashboard/Index', [
            'stats'               => $stats,
            'daily_picks'         => $dailyPicks,
            'biodata_completeness'=> $completeness,
            'biodata_status'      => $user->biodata?->status,
            'rejection_reason'    => $user->biodata?->status === 'rejected' ? $user->biodata?->admin_note : null,
            'recent_visitors'     => $recentVisitors,
            'is_verified'         => $user->identity_verification_status === 'verified',
        ]);
    }

    private function formatProfile(Registration $reg, int $score, ?array $breakdown, Registration $viewer): ?array
    {
        $bio = $reg->biodata;
        if (! $bio) return null;

        return [
            'registration_id'       => $reg->registration_id,
            'name'                  => $reg->name,
            'gender'                => $reg->gender,
            'age'                   => $bio->birth_date ? now()->diffInYears($bio->birth_date) : null,
            'marital_status'        => $bio->marital_status,
            'religion'              => $bio->religion,
            'sect'                  => $bio->sect,
            'highest_qualification' => $bio->highest_qualification,
            'occupation'            => $bio->occupation,
            'occupation_category'   => $bio->occupation_category,
            'district'              => $bio->district,
            'division'              => $bio->division,
            'residing_country'      => $bio->residing_country ?? 'Bangladesh',
            'height_cm'             => $bio->height_cm,
            'is_featured'           => $bio->is_featured,
            'is_verified'           => $reg->identity_verification_status === 'verified',
            'is_premium'            => $reg->hasActiveMembership(),
            'is_boosted'            => $reg->is_boosted,
            'platform_mode'         => $reg->platform_mode,
            'photo_visibility'      => $reg->photo_visibility,
            'has_photo'             => ! empty($bio->photos),
            'photo_url'             => null,
            'blurred'               => $this->photoService->shouldBlur($reg, $viewer),
            'completeness_score'    => $bio->completeness_score,
            'last_active_at'        => $bio->last_active_at?->toISOString(),
            'match_score'           => $score,
            'score_breakdown'       => $breakdown,
        ];
    }
}
