<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Registration;

class ProfileCompletionService
{
    /**
     * Each section maps to the wizard step number and the key fields
     * that must have at least one non-null value for the section to count as "started".
     */
    private const SECTION_STEP = [
        'general'   => 1,
        'location'  => 2,
        'religion'  => 3,
        'education' => 4,
        'family'    => 5,
        'lifestyle' => 6,
        'marriage'  => 7,
        'partner'   => 8,
        'photos'    => 9,
    ];

    private const SECTION_FIELDS = [
        'general'   => ['marital_status', 'birth_date', 'height_cm'],
        'location'  => ['residing_country', 'division', 'district'],
        'religion'  => ['religion', 'prayers_info'],
        'education' => ['highest_qualification', 'occupation'],
        'family'    => ['family_type'],
        'lifestyle' => ['diet', 'health_status'],
        'marriage'  => ['guardian_agree', 'residence_after_marriage'],
        'partner'   => ['partner_age_min', 'partner_age_max'],
    ];

    /** Fields used for computing the overall completeness percentage. */
    private const SCORED_FIELDS = [
        'marital_status', 'birth_date', 'height_cm', 'about_me',
        'division', 'district', 'residing_country',
        'religion', 'is_practicing', 'prayers_info',
        'highest_qualification', 'occupation',
        'family_type', 'brothers', 'sisters',
        'health_status', 'diet',
        'partner_age_min', 'partner_age_max', 'partner_expectations',
    ];

    /**
     * Compute completion data for a user.
     *
     * Returns:
     *  - percentage             (0–100, stored completeness_score or computed)
     *  - completed_sections     list of done sections
     *  - missing_sections       list of sections still needed
     *  - next_step              wizard step number of first missing section
     *  - next_step_url          URL to that wizard step
     *  - can_send_interest      true if percentage >= 30
     *  - can_be_publicly_listed true if percentage >= 60
     *  - has_photo              whether any photo is uploaded
     */
    public static function compute(Registration $user): array
    {
        $biodata = $user->biodata;

        if (! $biodata) {
            return [
                'percentage'             => 0,
                'completed_sections'     => [],
                'missing_sections'       => array_keys(self::SECTION_STEP),
                'next_step'              => 1,
                'next_step_url'          => route('biodata.wizard', ['step' => 1]),
                'can_send_interest'      => false,
                'can_be_publicly_listed' => false,
                'has_photo'              => false,
            ];
        }

        $completed = [];
        $missing   = [];

        foreach (self::SECTION_FIELDS as $section => $fields) {
            $started = collect($fields)->some(
                fn ($f) => ! is_null($biodata->$f) && $biodata->$f !== '' && $biodata->$f !== false
            );
            if ($started) {
                $completed[] = $section;
            } else {
                $missing[] = $section;
            }
        }

        // Photos section
        $hasPhoto = ! empty($biodata->photos);
        if ($hasPhoto) {
            $completed[] = 'photos';
        } else {
            $missing[] = 'photos';
        }

        // Use stored completeness_score if saved, otherwise compute from fields
        $percentage = ($biodata->completeness_score > 0)
            ? $biodata->completeness_score
            : self::computePercentage($biodata);

        // Next step = first missing section's wizard step
        $nextSection = count($missing) > 0 ? $missing[0] : null;
        $nextStep    = $nextSection ? (self::SECTION_STEP[$nextSection] ?? 1) : 9;

        return [
            'percentage'             => $percentage,
            'completed_sections'     => $completed,
            'missing_sections'       => $missing,
            'next_step'              => $nextStep,
            'next_step_url'          => route('biodata.wizard', ['step' => $nextStep]),
            'can_send_interest'      => $percentage >= 30,
            'can_be_publicly_listed' => $percentage >= 60,
            'has_photo'              => $hasPhoto,
        ];
    }

    /** Recompute percentage from raw fields (used when completeness_score is 0/null). */
    public static function computePercentage(object $biodata): int
    {
        $total  = count(self::SCORED_FIELDS);
        $filled = collect(self::SCORED_FIELDS)
            ->filter(fn ($f) => ! is_null($biodata->$f) && $biodata->$f !== '')
            ->count();

        $base  = (int) round(($filled / $total) * 80);
        $bonus = 0;

        if (! empty($biodata->about_me) && strlen((string) $biodata->about_me) > 100) {
            $bonus += 10;
        }
        if (! empty($biodata->photos)) {
            $bonus += 10;
        }

        return min(100, $base + $bonus);
    }
}
