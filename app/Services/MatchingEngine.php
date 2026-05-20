<?php

namespace App\Services;

use App\Models\Biodata;
use App\Models\Registration;
use Illuminate\Support\Collection;

/**
 * HeavenlyMatch Scoring Engine
 *
 * Produces a 0-100 compatibility score between two biodatas.
 * Weights are tuned for the Bangladeshi matrimony context.
 * Adjust WEIGHTS to A/B test scoring strategies.
 */
class MatchingEngine
{
    // ── Scoring weights (must sum to 100) ─────────────────────────────────
    private const WEIGHTS = [
        'age'          => 18,
        'location'     => 16,
        'religion'     => 15,
        'education'    => 12,
        'occupation'   => 10,
        'height'       => 8,
        'lifestyle'    => 8,
        'family'       => 7,
        'activity'     => 6,
    ];

    public function score(Biodata $seeker, Biodata $candidate): array
    {
        $breakdown = [
            'age'        => $this->scoreAge($seeker, $candidate),
            'location'   => $this->scoreLocation($seeker, $candidate),
            'religion'   => $this->scoreReligion($seeker, $candidate),
            'education'  => $this->scoreEducation($seeker, $candidate),
            'occupation' => $this->scoreOccupation($seeker, $candidate),
            'height'     => $this->scoreHeight($seeker, $candidate),
            'lifestyle'  => $this->scoreLifestyle($seeker, $candidate),
            'family'     => $this->scoreFamily($seeker, $candidate),
            'activity'   => $this->scoreActivity($candidate),
        ];

        $total = 0;
        foreach (self::WEIGHTS as $key => $weight) {
            $total += ($breakdown[$key] / 100) * $weight;
        }

        return [
            'total_score'     => (int) round($total),
            'score_breakdown' => $breakdown,
        ];
    }

    /**
     * Compute top-N matches for a given user from the active biodata pool.
     * Returns a Collection ordered by total_score descending.
     */
    public function topMatches(Registration $user, int $limit = 20): Collection
    {
        $seekerBio = $user->biodata;
        if (! $seekerBio) {
            return collect();
        }

        $oppositeGender = $user->gender === 'male' ? 'female' : 'male';

        // Base query: approved, completed, active, opposite gender
        $candidates = Biodata::with('registration')
            ->where('gender', $oppositeGender)
            ->where('status', 'approved')
            ->where('is_completed', true)
            ->whereHas('registration', fn ($q) =>
                $q->where('account_status', 'active')
                  ->whereNull('deactivated_at')
            )
            ->where('registration_id', '!=', $user->registration_id)
            ->get();

        return $candidates
            ->map(function (Biodata $candidate) use ($seekerBio) {
                $result = $this->score($seekerBio, $candidate);
                return [
                    'biodata'         => $candidate,
                    'total_score'     => $result['total_score'],
                    'score_breakdown' => $result['score_breakdown'],
                ];
            })
            ->sortByDesc('total_score')
            ->take($limit)
            ->values();
    }

    // ─────────────────────────────────────────────────────────────────────
    // Individual Scoring Functions (each returns 0-100)
    // ─────────────────────────────────────────────────────────────────────

    private function scoreAge(Biodata $seeker, Biodata $candidate): int
    {
        if (! $seeker->birth_date || ! $candidate->birth_date) {
            return 50; // neutral if missing
        }

        $candidateAge = now()->diffInYears($candidate->birth_date);
        $minAge = $seeker->partner_age_min ?? 18;
        $maxAge = $seeker->partner_age_max ?? 55;

        if ($candidateAge >= $minAge && $candidateAge <= $maxAge) {
            // Bonus for being in the sweet-spot (middle 60% of the range)
            $midPoint  = ($minAge + $maxAge) / 2;
            $deviation = abs($candidateAge - $midPoint) / max(($maxAge - $minAge) / 2, 1);
            return (int) round(100 - ($deviation * 25)); // max 25 point deduction
        }

        // Partial credit for being within 3 years outside range
        $outsideBy = max(0, $minAge - $candidateAge, $candidateAge - $maxAge);
        return $outsideBy <= 3 ? (int) round(60 - ($outsideBy * 15)) : 0;
    }

    private function scoreLocation(Biodata $seeker, Biodata $candidate): int
    {
        $score = 0;

        // Same country preference
        $preferredCountry = $seeker->partner_residing_country;
        if ($preferredCountry && $preferredCountry !== 'Any') {
            $score += $candidate->residing_country === $preferredCountry ? 40 : 0;
        } else {
            $score += 40; // no preference = full credit
        }

        // Same division in Bangladesh
        if ($seeker->partner_division) {
            $score += $candidate->division === $seeker->partner_division ? 35 : 0;
        } else {
            $score += 25;
        }

        // Same district bonus
        if ($seeker->district && $candidate->district === $seeker->district) {
            $score += 25;
        } else {
            $score += 10; // partial credit
        }

        return min($score, 100);
    }

    private function scoreReligion(Biodata $seeker, Biodata $candidate): int
    {
        // Hard requirement: if seeker specifies partner religion, must match
        if ($seeker->partner_religion && $seeker->partner_religion !== 'Any') {
            if ($candidate->religion !== $seeker->partner_religion) {
                return 0;
            }
        }

        $score = $candidate->religion === $seeker->religion ? 60 : 30;

        // Sect compatibility (only within Islam)
        if ($seeker->religion === 'Islam' && $seeker->partner_sect) {
            if ($candidate->sect === $seeker->partner_sect) {
                $score += 40;
            } elseif ($this->isCompatibleSect($seeker->partner_sect, $candidate->sect ?? '')) {
                $score += 20;
            }
        } else {
            $score += 40; // no sect preference = full credit
        }

        return min($score, 100);
    }

    private function isCompatibleSect(string $preferred, string $actual): bool
    {
        // Sunni schools that are broadly compatible with each other
        $sunniSchools = ['Hanafi', 'Shafi', 'Maliki', 'Hanbali', 'Ahle Sunnah'];
        return in_array($preferred, $sunniSchools) && in_array($actual, $sunniSchools);
    }

    private function scoreEducation(Biodata $seeker, Biodata $candidate): int
    {
        $hierarchy = [
            'Primary'         => 1,
            'JSC'             => 2,
            'SSC'             => 3,
            'HSC'             => 4,
            'Diploma'         => 4,
            'Graduation'      => 5,
            'Post Graduation' => 6,
            'PhD'             => 7,
        ];

        $seekerLevel    = $hierarchy[$seeker->highest_qualification ?? ''] ?? 0;
        $candidateLevel = $hierarchy[$candidate->highest_qualification ?? ''] ?? 0;
        $preferredLevel = $hierarchy[$seeker->partner_education ?? ''] ?? 0;

        if ($preferredLevel === 0) {
            return 70; // no preference = good score
        }

        if ($candidateLevel >= $preferredLevel) {
            return 100;
        }

        $diff = $preferredLevel - $candidateLevel;
        return max(0, 100 - ($diff * 25));
    }

    private function scoreOccupation(Biodata $seeker, Biodata $candidate): int
    {
        $preferred = $seeker->partner_profession;
        if (! $preferred || $preferred === 'Any') {
            return 70;
        }

        if ($candidate->occupation === $preferred) {
            return 100;
        }

        // Group-level match (e.g., seeker wants 'Engineer', candidate is 'Software Engineer')
        $groups = [
            'Business'  => ['Business', 'Entrepreneur', 'Self Employed'],
            'Service'   => ['Government Job', 'Bank Job', 'Private Job'],
            'Education' => ['Teacher', 'Professor', 'Lecturer', 'Researcher'],
            'Medical'   => ['Doctor', 'Nurse', 'Pharmacist'],
            'Engineer'  => ['Engineer', 'Software Engineer', 'IT Professional'],
        ];

        foreach ($groups as $group => $roles) {
            if (in_array($preferred, $roles) && in_array($candidate->occupation, $roles)) {
                return 70;
            }
        }

        return 20;
    }

    private function scoreHeight(Biodata $seeker, Biodata $candidate): int
    {
        $minCm = $seeker->partner_height_cm_min;
        $maxCm = $seeker->partner_height_cm_max;
        $candidateCm = $candidate->height_cm;

        if (! $minCm && ! $maxCm) {
            return 80; // no preference
        }
        if (! $candidateCm) {
            return 50; // candidate hasn't provided height
        }

        if (($minCm === null || $candidateCm >= $minCm) && ($maxCm === null || $candidateCm <= $maxCm)) {
            return 100;
        }

        return 30; // outside preference
    }

    private function scoreLifestyle(Biodata $seeker, Biodata $candidate): int
    {
        $score = 0;
        $checks = 0;

        // Prayer consistency
        if ($seeker->prayers_info && $candidate->prayers_info) {
            $score += $seeker->prayers_info === $candidate->prayers_info ? 30 : 10;
            $checks++;
        }

        // Diet
        if ($seeker->diet && $candidate->diet) {
            $score += $seeker->diet === $candidate->diet ? 35 : 10;
            $checks++;
        }

        // Smoking
        if ($candidate->smoking && $candidate->smoking !== 'No') {
            $score -= 20; // penalty for smoker
        }

        // Family type
        if ($seeker->family_type && $candidate->family_type) {
            $score += $seeker->family_type === $candidate->family_type ? 35 : 15;
            $checks++;
        }

        return $checks > 0 ? min(100, max(0, (int) round($score))) : 60;
    }

    private function scoreFamily(Biodata $seeker, Biodata $candidate): int
    {
        $score = 0;

        // Financial status compatibility
        $statusMap = ['lower' => 1, 'lower_middle' => 2, 'middle' => 3, 'upper_middle' => 4, 'upper' => 5];
        $seekerStatus    = $statusMap[$seeker->financial_status ?? ''] ?? 0;
        $candidateStatus = $statusMap[$candidate->family_financial_status ?? ''] ?? 0;

        if ($seekerStatus && $candidateStatus) {
            $diff = abs($seekerStatus - $candidateStatus);
            $score += max(0, 50 - ($diff * 15));
        } else {
            $score += 30;
        }

        // Guardian agree (Islamic mode)
        if ($candidate->guardian_agree === 'Yes') {
            $score += 25;
        }

        // Partner income range
        $minIncome = $seeker->partner_income_min;
        $maxIncome = $seeker->partner_income_max;
        $candidateIncome = $candidate->monthly_income;

        if ($candidateIncome && ($minIncome || $maxIncome)) {
            $inRange = (! $minIncome || $candidateIncome >= $minIncome)
                    && (! $maxIncome || $candidateIncome <= $maxIncome);
            $score += $inRange ? 25 : 0;
        } else {
            $score += 15;
        }

        return min($score, 100);
    }

    private function scoreActivity(Biodata $candidate): int
    {
        if (! $candidate->last_active_at) {
            return 40;
        }

        $daysSinceActive = now()->diffInDays($candidate->last_active_at);

        return match (true) {
            $daysSinceActive <= 1  => 100,
            $daysSinceActive <= 7  => 85,
            $daysSinceActive <= 30 => 60,
            $daysSinceActive <= 90 => 35,
            default                => 10,
        };
    }
}
