<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Biodata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PublicProfileController extends Controller
{
    // ── Public search index ───────────────────────────────────────────────────

    public function index(Request $request): Response
    {
        $filters = $request->only([
            'looking_for', 'age_min', 'age_max',
            'division', 'district', 'upazila',
            'marital_status', 'sect',
        ]);

        $query = Biodata::query()
            ->where('status', 'approved')
            ->where('is_completed', true)
            ->with('registration:registration_id,name,gender,identity_verification_status,account_status')
            ->whereHas('registration', fn ($q) => $q->where('account_status', 'active'));

        // Exclude own profile when logged in
        if (Auth::check()) {
            $query->where('registration_id', '!=', Auth::id());
        }

        // Gender via looking_for
        if (!empty($filters['looking_for'])) {
            $gender = $filters['looking_for'] === 'bride' ? 'female' : 'male';
            $query->whereHas('registration', fn ($q) => $q->where('gender', $gender));
        }

        // Age → birth_date range
        if (!empty($filters['age_min'])) {
            $query->where('birth_date', '<=', now()->subYears((int) $filters['age_min'])->format('Y-m-d'));
        }
        if (!empty($filters['age_max'])) {
            $query->where('birth_date', '>=', now()->subYears((int) $filters['age_max'])->format('Y-m-d'));
        }

        // Location + attribute filters (column names match biodata schema exactly)
        foreach (['division', 'district', 'upazila', 'marital_status', 'sect'] as $field) {
            if (!empty($filters[$field])) {
                try {
                    $query->where($field, $filters[$field]);
                } catch (\Throwable) {
                    // Gracefully skip if column does not exist
                }
            }
        }

        $results = $query
            ->orderByDesc('completeness_score')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Biodata $bio) => [
                'id'                    => $bio->registration_id,
                'gender'                => $bio->registration?->gender ?? 'male',
                'age'                   => $bio->birth_date ? (int) now()->diffInYears($bio->birth_date) : null,
                'height_cm'             => $bio->height_cm,
                'marital_status'        => $bio->marital_status,
                'district'              => $bio->district,
                'division'              => $bio->division,
                'occupation'            => $bio->occupation,
                'highest_qualification' => $bio->highest_qualification,
                'about_me'              => $bio->about_me ? Str::limit($bio->about_me, 100) : null,
                'religion'              => $bio->religion,
                'sect'                  => $bio->sect,
                'is_verified'           => ($bio->registration?->identity_verification_status ?? '') === 'verified',
                'avatar_num'            => abs(crc32((string) $bio->registration_id)) % 4 + 1,
            ]);

        return Inertia::render('Public/Profiles', [
            'results' => $results,
            'filters' => $filters,
        ]);
    }

    // ── Public profile detail ─────────────────────────────────────────────────

    public function show(string $registrationId): Response|RedirectResponse
    {
        $bio = Biodata::where('registration_id', $registrationId)
            ->where('status', 'approved')
            ->where('is_completed', true)
            ->with('registration:registration_id,name,gender,identity_verification_status,account_status,platform_mode')
            ->whereHas('registration', fn ($q) => $q->where('account_status', 'active'))
            ->firstOrFail();

        // Authenticated users get the full dashboard profile view
        if (Auth::check()) {
            return redirect()->route('profile.show', $registrationId);
        }

        return Inertia::render('Public/ProfileShow', [
            'profile' => [
                'id'                    => $bio->registration_id,
                'gender'                => $bio->registration?->gender ?? 'male',
                'age'                   => $bio->birth_date ? (int) now()->diffInYears($bio->birth_date) : null,
                'height_cm'             => $bio->height_cm,
                'weight_kg'             => $bio->weight_kg,
                'complexion'            => $bio->complexion,
                'blood_group'           => $bio->blood_group,
                'mother_tongue'         => $bio->mother_tongue,
                'marital_status'        => $bio->marital_status,
                'division'              => $bio->division,
                'district'              => $bio->district,
                'upazila'               => $bio->upazila,
                'residing_country'      => $bio->residing_country,
                'religion'              => $bio->religion,
                'sect'                  => $bio->sect,
                'is_practicing'         => $bio->is_practicing,
                'prayers_info'          => $bio->prayers_info,
                'hijab_info'            => $bio->hijab_info,
                'beard_info'            => $bio->beard_info,
                'highest_qualification' => $bio->highest_qualification,
                'occupation'            => $bio->occupation,
                'occupation_category'   => $bio->occupation_category,
                'about_me'              => $bio->about_me,
                'profile_headline'      => $bio->profile_headline,
                'family_type'           => $bio->family_type,
                'family_financial_status' => $bio->family_financial_status,
                'home_ownership'        => $bio->home_ownership,
                'health_status'         => $bio->health_status,
                'diet'                  => $bio->diet,
                'partner_age_min'       => $bio->partner_age_min    ?? null,
                'partner_age_max'       => $bio->partner_age_max    ?? null,
                'partner_division'      => $bio->partner_division   ?? null,
                'partner_marital_status'=> $bio->partner_marital_status ?? null,
                'partner_education'     => $bio->partner_education  ?? null,
                'partner_expectations'  => $bio->partner_expectations ?? null,
                'is_verified'           => ($bio->registration?->identity_verification_status ?? '') === 'verified',
                'avatar_num'            => abs(crc32((string) $bio->registration_id)) % 4 + 1,
                'platform_mode'         => $bio->registration?->platform_mode ?? 'general',
                // name, phone, email, guardian_*, permanent_address intentionally excluded
            ],
        ]);
    }
}
