<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
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
            'income_min', 'income_max', 'keyword',
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
            ->where('registration_id', '!=', $user->registration_id);

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

        $perPage = ($user->membership_tier === 'premium') ? 24 : 12;
        $results = $query->orderByDesc('completeness_score')->paginate($perPage)->withQueryString();

        return Inertia::render('Dashboard/Search', [
            'results'        => $results,
            'filters'        => $filters,
            'membershipTier' => $user->membership_tier ?? 'free',
            'platformMode'   => $user->platform_mode,
        ]);
    }
}
