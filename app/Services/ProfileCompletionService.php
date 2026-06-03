<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Biodata;
use App\Models\Registration;

class ProfileCompletionService
{
    /**
     * The 10 wizard sections, each worth 10% of completion.
     * Section key => wizard step number.
     */
    public const SECTION_STEP = [
        'general'   => 1,
        'location'  => 2,
        'religion'  => 3,
        'education' => 4,
        'lifestyle' => 5,
        'family'    => 6,
        'marriage'  => 7,
        'partner'   => 8,
        'contact'   => 9,
        'review'    => 10,
    ];

    /**
     * Required fields that must all be filled for a section to count as complete.
     * The "review" section (step 10) is completed via the is_completed flag, not fields.
     */
    public const SECTION_REQUIRED = [
        'general'   => ['marital_status', 'birth_date'],
        'location'  => ['residing_country', 'residing_city', 'division', 'district'],
        'religion'  => ['religion', 'prayers_info'],
        'education' => ['highest_qualification', 'occupation'],
        'lifestyle' => ['height_cm', 'weight_kg', 'complexion'],
        'family'    => ['father_profession', 'mother_profession', 'family_type'],
        'marriage'  => ['residence_after_marriage'],
        'partner'   => ['partner_age_min', 'partner_age_max', 'partner_education', 'partner_division'],
        'contact'   => ['contact_privacy'],
    ];

    /**
     * Compute completion data for a user. Each completed section = 10%.
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

        ['completed' => $completed, 'missing' => $missing] = self::sectionStatus($biodata);

        $percentage = count($completed) * 10;

        $nextSection = $missing[0] ?? 'review';
        $nextStep    = self::SECTION_STEP[$nextSection] ?? 10;

        return [
            'percentage'             => $percentage,
            'completed_sections'     => $completed,
            'missing_sections'       => $missing,
            'next_step'              => $nextStep,
            'next_step_url'          => route('biodata.wizard', ['step' => $nextStep]),
            'can_send_interest'      => $percentage >= 30,
            'can_be_publicly_listed' => $percentage >= 60,
            'has_photo'              => ! empty($biodata->photos),
        ];
    }

    /**
     * Determine which sections are complete / missing for a biodata.
     *
     * @return array{completed: array<int,string>, missing: array<int,string>}
     */
    public static function sectionStatus(Biodata $biodata): array
    {
        $completed = [];
        $missing   = [];

        foreach (self::SECTION_REQUIRED as $section => $fields) {
            $allFilled = collect($fields)->every(fn ($f) => self::filled($biodata->$f));
            $allFilled ? $completed[] = $section : $missing[] = $section;
        }

        // Photos/review section completes once the wizard is finished.
        if ($biodata->is_completed) {
            $completed[] = 'review';
        } else {
            $missing[] = 'review';
        }

        return ['completed' => $completed, 'missing' => $missing];
    }

    /** Step-based percentage (completed sections × 10). */
    public static function computePercentage(Biodata $biodata): int
    {
        $completed = self::sectionStatus($biodata)['completed'];

        return count($completed) * 10;
    }

    /** Required content sections (excluding review) still missing — used to gate final submit. */
    public static function missingRequiredSections(Biodata $biodata): array
    {
        return array_values(array_filter(
            self::sectionStatus($biodata)['missing'],
            fn ($s) => $s !== 'review',
        ));
    }

    /** A value counts as filled when it is not null and not an empty string. */
    private static function filled($value): bool
    {
        return ! is_null($value) && $value !== '';
    }
}
