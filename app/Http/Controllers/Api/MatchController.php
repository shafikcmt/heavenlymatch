<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\MatchScore;
use App\Services\MatchingEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MatchController extends Controller
{
    public function __construct(private readonly MatchingEngine $engine) {}

    /**
     * GET /api/matches
     * Returns the authenticated user's top matches, served from the pre-computed
     * match_scores table (refreshed daily by ComputeMatchScoresJob).
     * Falls back to live computation if the cache is empty (new users).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $cacheKey = "matches:{$user->registration_id}";

        $matches = Cache::remember($cacheKey, now()->addHours(6), function () use ($user) {
            // Try pre-computed scores first
            $precomputed = MatchScore::with(['candidate.biodata', 'candidate.biodata.registration'])
                ->where('user_id', $user->registration_id)
                ->orderByDesc('total_score')
                ->limit(30)
                ->get();

            if ($precomputed->isNotEmpty()) {
                return $precomputed->map(fn ($ms) => $this->formatMatch($ms->candidate->biodata, $ms->total_score, $ms->score_breakdown));
            }

            // Live fallback for new users
            return $this->engine->topMatches($user, 20)
                ->map(fn ($m) => $this->formatMatch($m['biodata'], $m['total_score'], $m['score_breakdown']));
        });

        return response()->json([
            'data' => $matches,
            'meta' => ['count' => count($matches)],
        ]);
    }

    /**
     * GET /api/matches/search
     * Advanced filtered search — premium members get full filters, free users get basic.
     */
    public function search(Request $request): JsonResponse
    {
        $user = $request->user();
        $isPremium = $user->hasActiveMembership();

        $validated = $request->validate([
            'gender'           => 'nullable|in:male,female',
            'age_min'          => 'nullable|integer|min:18|max:80',
            'age_max'          => 'nullable|integer|min:18|max:80',
            'religion'         => 'nullable|string|max:30',
            'sect'             => 'nullable|string|max:50',
            'marital_status'   => 'nullable|string|max:30',
            'division'         => 'nullable|string|max:60',
            'district'         => 'nullable|string|max:60',
            'residing_country' => 'nullable|string|max:60',
            'height_cm_min'    => 'nullable|integer|min:100|max:250',
            'height_cm_max'    => 'nullable|integer|min:100|max:250',
            // Premium-only filters
            'occupation'       => 'nullable|string|max:60',
            'education'        => 'nullable|string|max:60',
            'income_min'       => 'nullable|integer|min:0',
            'income_max'       => 'nullable|integer|min:0',
            'family_type'      => 'nullable|string|max:20',
            'is_practicing'    => 'nullable|boolean',
            'sort_by'          => 'nullable|in:match_score,newest,last_active,featured',
            'per_page'         => 'nullable|integer|min:6|max:30',
        ]);

        $oppositeGender = $user->gender === 'male' ? 'female' : 'male';

        $query = Biodata::with(['registration'])
            ->where('gender', $validated['gender'] ?? $oppositeGender)
            ->where('status', 'approved')
            ->where('is_completed', true)
            ->whereHas('registration', fn ($q) =>
                $q->where('account_status', 'active')->whereNull('deactivated_at')
            )
            ->where('registration_id', '!=', $user->registration_id);

        // ── Basic Filters (all users) ────────────────────────────────────
        if (! empty($validated['age_min']) || ! empty($validated['age_max'])) {
            $minDate = now()->subYears($validated['age_max'] ?? 80)->toDateString();
            $maxDate = now()->subYears($validated['age_min'] ?? 18)->toDateString();
            $query->whereBetween('birth_date', [$minDate, $maxDate]);
        }

        if (! empty($validated['religion'])) {
            $query->where('religion', $validated['religion']);
        }

        if (! empty($validated['marital_status'])) {
            $query->where('marital_status', $validated['marital_status']);
        }

        if (! empty($validated['division'])) {
            $query->where('division', $validated['division']);
        }

        if (! empty($validated['residing_country'])) {
            $query->where('residing_country', $validated['residing_country']);
        }

        if (! empty($validated['height_cm_min'])) {
            $query->where('height_cm', '>=', $validated['height_cm_min']);
        }

        if (! empty($validated['height_cm_max'])) {
            $query->where('height_cm', '<=', $validated['height_cm_max']);
        }

        // ── Premium-Only Filters ─────────────────────────────────────────
        if ($isPremium) {
            if (! empty($validated['sect'])) {
                $query->where('sect', $validated['sect']);
            }

            if (! empty($validated['district'])) {
                $query->where('district', $validated['district']);
            }

            if (! empty($validated['occupation'])) {
                $query->where('occupation', 'like', "%{$validated['occupation']}%");
            }

            if (! empty($validated['income_min'])) {
                $query->where('monthly_income', '>=', $validated['income_min']);
            }

            if (! empty($validated['income_max'])) {
                $query->where('monthly_income', '<=', $validated['income_max']);
            }

            if (! empty($validated['family_type'])) {
                $query->where('family_type', $validated['family_type']);
            }

            if (isset($validated['is_practicing'])) {
                $query->where('is_practicing', $validated['is_practicing']);
            }
        }

        // ── Sorting ──────────────────────────────────────────────────────
        match ($validated['sort_by'] ?? 'match_score') {
            'newest'      => $query->orderByDesc('created_at'),
            'last_active' => $query->orderByDesc('last_active_at'),
            'featured'    => $query->orderByDesc('is_featured')->orderByDesc('completeness_score'),
            default       => $query->orderByDesc('completeness_score')->orderByDesc('last_active_at'),
        };

        // Boost featured/boosted profiles
        $query->orderByDesc(
            \DB::raw("(SELECT is_boosted FROM registrations WHERE registrations.registration_id = biodatas.registration_id LIMIT 1)")
        );

        $perPage = $validated['per_page'] ?? 12;
        $results = $query->paginate($perPage);

        $seekerBio = $user->biodata;

        $data = collect($results->items())->map(function (Biodata $b) use ($seekerBio) {
            $score = $seekerBio ? $this->engine->score($seekerBio, $b)['total_score'] : null;
            return $this->formatMatch($b, $score, null);
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'total'        => $results->total(),
                'per_page'     => $results->perPage(),
                'current_page' => $results->currentPage(),
                'last_page'    => $results->lastPage(),
                'premium_filters_active' => $isPremium,
            ],
        ]);
    }

    /**
     * GET /api/matches/daily
     * Returns the 5 daily Best Match suggestions — sent via notification daily.
     */
    public function daily(Request $request): JsonResponse
    {
        $user = $request->user();
        $cacheKey = "daily_matches:{$user->registration_id}:" . now()->toDateString();

        $matches = Cache::remember($cacheKey, now()->endOfDay(), function () use ($user) {
            return $this->engine->topMatches($user, 5)
                ->map(fn ($m) => $this->formatMatch($m['biodata'], $m['total_score'], $m['score_breakdown']));
        });

        return response()->json(['data' => $matches]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Shared formatter — respects photo visibility rules
    // ─────────────────────────────────────────────────────────────────────

    private function formatMatch(Biodata $b, ?int $score, ?array $breakdown): array
    {
        $reg = $b->registration;
        $age = $b->birth_date ? now()->diffInYears($b->birth_date) : null;

        return [
            'registration_id'       => $b->registration_id,
            'name'                  => $b->name,
            'age'                   => $age,
            'gender'                => $b->gender,
            'marital_status'        => $b->marital_status,
            'religion'              => $b->religion,
            'sect'                  => $b->sect,
            'district'              => $b->district,
            'division'              => $b->division,
            'residing_country'      => $b->residing_country,
            'occupation'            => $b->occupation,
            'highest_qualification' => $b->highest_qualification,
            'height_cm'             => $b->height_cm,
            'is_featured'           => $b->is_featured,
            'is_boosted'            => $reg?->is_boosted,
            'is_verified'           => $reg?->identity_verification_status === 'verified',
            'platform_mode'         => $reg?->platform_mode,
            'photo'                 => $this->resolvePhoto($b, $reg),
            'completeness_score'    => $b->completeness_score,
            'match_score'           => $score,
            'score_breakdown'       => $breakdown,
            'last_active'           => $b->last_active_at?->diffForHumans(),
        ];
    }

    private function resolvePhoto(Biodata $b, ?Registration $reg): array
    {
        $visibility = $reg?->photo_visibility ?? 'members_only';
        $photos = $b->photos ?? [];
        $primary = collect($photos)->firstWhere('is_primary', true) ?? collect($photos)->first();

        if (! $primary) {
            return ['url' => null, 'blurred' => false, 'placeholder' => true];
        }

        return match ($visibility) {
            'public'       => ['url' => $primary['path'], 'blurred' => false, 'placeholder' => false],
            'blurred'      => ['url' => $primary['path'], 'blurred' => true,  'placeholder' => false],
            // 'members_only' returns the path; frontend decides blur based on connection status
            default        => ['url' => $primary['path'], 'blurred' => null,  'placeholder' => false],
        };
    }
}
