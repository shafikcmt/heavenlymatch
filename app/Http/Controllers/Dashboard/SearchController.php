<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\Registration;
use App\Services\PhotoPrivacyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function __construct(private PhotoPrivacyService $photoPrivacy) {}

    public function index(Request $request): Response
    {
        /** @var Registration $user */
        $user    = Auth::user();
        $filters = $request->only([
            'age_min', 'age_max', 'height_cm_min', 'height_cm_max',
            'division', 'district', 'residing_country',
            'marital_status', 'education', 'occupation_category',
            'religion', 'sect', 'is_practicing',
            'complexion', 'family_type', 'platform_mode',
            'income_min', 'income_max', 'keyword', 'sort',
        ]);

        $looking_for = $user->looking_for ?? 'any';
        $genderFilter = match ($looking_for) {
            'bride'  => 'female',
            'groom'  => 'male',
            default  => null,
        };

        $query = Biodata::with('registration')
            ->where('status', 'approved')
            ->where('is_completed', true)
            ->where('registration_id', '!=', $user->registration_id)
            ->whereHas('registration', fn($q) => $q->where('account_status', 'active'));

        if ($genderFilter) {
            $query->whereHas('registration', fn($q) => $q->where('gender', $genderFilter));
        }

        if ($user->platform_mode === 'islamic') {
            $query->whereHas('registration', fn($q) => $q->where('platform_mode', 'islamic'));
        }

        // Age filter via birth_date
        if (!empty($filters['age_min'])) {
            $query->where('birth_date', '<=', now()->subYears((int)$filters['age_min'])->format('Y-m-d'));
        }
        if (!empty($filters['age_max'])) {
            $query->where('birth_date', '>=', now()->subYears((int)$filters['age_max'])->format('Y-m-d'));
        }

        if (!empty($filters['height_cm_min'])) {
            $query->where('height_cm', '>=', $filters['height_cm_min']);
        }
        if (!empty($filters['height_cm_max'])) {
            $query->where('height_cm', '<=', $filters['height_cm_max']);
        }

        foreach (['division', 'district', 'residing_country', 'marital_status', 'complexion', 'family_type'] as $f) {
            if (!empty($filters[$f])) {
                $query->where($f, $filters[$f]);
            }
        }

        if (!empty($filters['occupation_category'])) {
            $query->where('occupation_category', $filters['occupation_category']);
        }

        if (!empty($filters['education'])) {
            $query->where('highest_qualification', $filters['education']);
        }

        if (!empty($filters['income_min'])) {
            $query->where('monthly_income', '>=', $filters['income_min']);
        }
        if (!empty($filters['income_max'])) {
            $query->where('monthly_income', '<=', $filters['income_max']);
        }

        if (!empty($filters['keyword'])) {
            $kw = '%' . $filters['keyword'] . '%';
            $query->where(function ($q) use ($kw) {
                $q->where('about_me', 'like', $kw)
                  ->orWhere('occupation', 'like', $kw)
                  ->orWhere('district', 'like', $kw);
            });
        }

        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        match ($sort) {
            'last_active' => $query->orderByDesc('last_active_at'),
            'featured'    => $query->orderByDesc('is_featured')->orderByDesc('completeness_score'),
            'score'       => $query->orderByDesc('completeness_score'),
            default       => $query->orderByDesc('biodatas.created_at'),
        };

        $perPage = $user->hasActiveMembership() ? 24 : 12;
        $results = $query->paginate($perPage)->withQueryString();

        // Transform Biodata records into ProfileCard-shaped objects
        $results->through(function (Biodata $biodata) use ($user) {
            $reg    = $biodata->registration;
            $photos = $biodata->photos ?? [];

            $photoUrl = !empty($photos)
                ? $this->photoPrivacy->photoUrl($biodata->registration_id, 0, $user->registration_id)
                : null;

            return [
                'registration_id'       => $biodata->registration_id,
                'name'                  => $reg?->name ?? '',
                'gender'                => $reg?->gender ?? 'male',
                'age'                   => $biodata->birth_date ? (int) now()->diffInYears($biodata->birth_date) : null,
                'marital_status'        => $biodata->marital_status,
                'religion'              => $biodata->religion ?? 'Islam',
                'sect'                  => $biodata->sect,
                'highest_qualification' => $biodata->highest_qualification,
                'occupation'            => $biodata->occupation,
                'occupation_category'   => $biodata->occupation_category,
                'district'              => $biodata->district,
                'division'              => $biodata->division,
                'residing_country'      => $biodata->residing_country ?? 'Bangladesh',
                'height_cm'             => $biodata->height_cm,
                'is_featured'           => (bool) $biodata->is_featured,
                'is_verified'           => $reg?->identity_verification_status === 'verified',
                'is_premium'            => $reg?->hasActiveMembership() ?? false,
                'is_boosted'            => false,
                'platform_mode'         => $reg?->platform_mode ?? 'general',
                'photo_visibility'      => $reg?->photo_visibility ?? 'members_only',
                'has_photo'             => !empty($photos),
                'photo_url'             => $photoUrl,
                'blurred'               => $reg ? $this->photoPrivacy->shouldBlur($reg, $user) : true,
                'completeness_score'    => $biodata->completeness_score ?? 0,
                'last_active_at'        => $biodata->last_active_at?->toISOString(),
                'match_score'           => null,
                'score_breakdown'       => null,
            ];
        });

        return Inertia::render('Dashboard/Search', [
            'results'        => $results,
            'filters'        => $filters,
            'membershipTier' => $user->hasActiveMembership() ? 'premium' : 'free',
            'platformMode'   => $user->platform_mode,
        ]);
    }
}
