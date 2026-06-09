<?php

namespace App\Http\Controllers\Biodata;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\Registration;
use App\Models\SystemSetting;
use App\Services\BiodataFieldService;
use App\Services\PhoneOtpService;
use App\Services\PhotoPrivacyService;
use App\Services\ProfileCompletionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class BiodataWizardController extends Controller
{
    public function __construct(
        private PhotoPrivacyService $photoPrivacy,
        private PhoneOtpService $phone,
        private BiodataFieldService $fields,
    ) {}

    /** 10-step wizard. Each step counts as 10% of completion. */
    private const STEPS = [
        1  => 'general',
        2  => 'location',
        3  => 'religion',
        4  => 'education',
        5  => 'lifestyle',   // physical + lifestyle / health
        6  => 'family',
        7  => 'marriage',
        8  => 'partner',
        9  => 'contact',     // guardian contact, WhatsApp, privacy
        10 => 'review',      // photo + final confirmation
    ];

    private const PHOTO_STEP = 10;

    public function show(int $step = 1): Response|RedirectResponse
    {
        if (!array_key_exists($step, self::STEPS)) {
            return redirect()->route('biodata.wizard', ['step' => 1]);
        }

        /** @var Registration $user */
        $user = Auth::user();
        $biodata = $user->biodata ?? new Biodata();

        // Serialize to array and normalise birth_date to Y-m-d so <input type="date"> pre-fills correctly.
        $biodataData = $biodata->toArray();
        $biodataData['birth_date'] = $biodata->birth_date?->format('Y-m-d');
        $biodataData['completeness_score'] = $biodata->completeness_score ?? 0;

        $photoData = [];
        if ($step === self::PHOTO_STEP) {
            $photos    = $biodata->photos ?? [];
            $photoUrls = array_map(
                fn (int $i) => $this->photoPrivacy->photoUrl($user->registration_id, $i, $user->registration_id),
                array_keys($photos),
            );
            $photoData = [
                'photos'    => array_values($photos),
                'photoUrls' => array_values($photoUrls),
                'maxPhotos' => 6,
            ];
        }

        return Inertia::render('Biodata/Wizard', [
            'step'    => $step,
            'steps'   => self::STEPS,
            'biodata' => $biodataData,
            'user'    => [
                'name'   => $user->name,
                'gender' => $user->gender,
                'mode'   => $user->platform_mode,
            ],
            // Admin-defined custom fields (Phase E3) — appended to their section's step.
            'customFields' => $this->fields->customWizardFields(),
            ...$photoData,
        ]);
    }

    public function save(Request $request, int $step): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();
        $isDraft = $request->boolean('save_draft');

        // Draft → lenient (everything optional). Continue / Submit → enforce this step's required fields.
        $rules = $this->baseRulesForStep($step, $user->gender ?? 'male');
        if (! $isDraft) {
            $required = $this->requiredForStep($step);

            // Prayer practice is only relevant (and only shown) for Muslims.
            if ($step === 3) {
                $religion = strtolower((string) $request->input('religion'));
                if ($religion === '' || $religion === 'islam') {
                    $required[] = 'prayers_info';
                }
            }

            // Location: required fields depend on the chosen country, and the
            // permanent block is skipped when "same as present" is checked.
            if ($step === 2) {
                $isBd = fn (?string $c) => $c === null || strtolower(trim($c)) === '' || strtolower(trim($c)) === 'bangladesh';

                // Present address.
                if ($isBd($request->input('residing_country'))) {
                    $required[] = 'current_division';
                    $required[] = 'current_district';
                } else {
                    $required[] = 'residing_city';
                }

                // Permanent address (unless mirrored from present).
                if (! $request->boolean('same_as_permanent')) {
                    $required[] = 'permanent_country';
                    if ($isBd($request->input('permanent_country'))) {
                        $required[] = 'division';
                        $required[] = 'district';
                    } else {
                        $required[] = 'district'; // abroad: district column holds the city
                    }
                }
            }

            foreach ($required as $field) {
                $existing = $rules[$field] ?? ['nullable'];
                $existing = array_values(array_filter($existing, fn ($r) => $r !== 'nullable'));
                $rules[$field] = array_values(array_unique([...['required'], ...$existing]));
            }
            if ($step === self::PHOTO_STEP) {
                // Declaration / commitment gate — all three must be accepted to submit.
                $rules['guardian_knows_biodata']  = ['accepted'];
                $rules['info_truthful_confirmed'] = ['accepted'];
                $rules['accept_liability_terms']  = ['accepted'];
            }
        }

        $validated = $request->validate($rules);
        unset($validated['confirm_correct']); // legacy transient — not a biodata column

        // Education integrity (Phase: education workflow fix): a record's level may
        // not rank above the chosen highest qualification within the same system.
        // Unknown/legacy/free-text level strings are tolerated (skipped).
        if ($step === 4) {
            $this->validateEducationConsistency($validated);
        }

        // Normalise + validate the optional contact numbers (Bangladesh format).
        // guardian_mobile / guardian_whatsapp / whatsapp_number all share one rule.
        if ($step === 9) {
            foreach (['whatsapp_number', 'guardian_mobile', 'guardian_whatsapp'] as $phoneField) {
                if (empty($validated[$phoneField])) {
                    continue;
                }
                $normalized = $this->phone->normalizePhone($validated[$phoneField]);
                if ($normalized === null) {
                    throw ValidationException::withMessages([
                        $phoneField => __('biodata.whatsapp_invalid'),
                    ]);
                }
                $validated[$phoneField] = $normalized;
            }
        }

        // "Permanent same as present": mirror the present fields into the permanent
        // ones so the data stays consistent even if the client didn't copy them.
        if ($step === 2 && $request->boolean('same_as_permanent')) {
            $validated['permanent_country'] = $validated['residing_country'] ?? null;
            $validated['division']          = $validated['current_division'] ?? null;
            $validated['district']          = $validated['current_district'] ?? null;
            $validated['upazila']           = $validated['current_upazila'] ?? null;
            $validated['village_area']      = $validated['current_area'] ?? null;
            $validated['permanent_address'] = $validated['present_address'] ?? null;
        }

        $biodata = Biodata::firstOrNew(['registration_id' => $user->registration_id]);
        $biodata->fill($validated);

        // Persist admin-defined custom fields for this step into custom_fields JSON.
        $this->persistCustomFields($request, $biodata, $step, $isDraft);

        // Final submit: enforce that every required content section is complete.
        if ($step === self::PHOTO_STEP && ! $isDraft) {
            $missing = ProfileCompletionService::missingRequiredSections($biodata);
            if (! empty($missing)) {
                $firstStep = ProfileCompletionService::SECTION_STEP[$missing[0]] ?? 1;
                return redirect()->route('biodata.wizard', ['step' => $firstStep])
                    ->with('error', __('biodata.complete_required_first'));
            }
            $biodata->is_completed = true;
        }

        $biodata->completeness_score = ProfileCompletionService::computePercentage($biodata);

        // Apply the Biodata Approval Control workflow once the biodata is complete.
        // Drafts keep their current status; admin-hidden profiles are never auto-changed.
        if ($biodata->is_completed && $biodata->status !== 'hidden') {
            $this->applyApprovalStatus($biodata);
        }

        $biodata->save();

        if ($isDraft) {
            return redirect()->route('biodata.wizard', ['step' => $step])
                ->with('success', __('biodata.draft_saved'));
        }

        $nextStep = $step + 1;

        if ($nextStep > count(self::STEPS)) {
            return redirect()->route('dashboard')->with('success', __('biodata.submitted'));
        }

        return redirect()->route('biodata.wizard', ['step' => $nextStep]);
    }

    /**
     * Per-system education level ladders (rank ascending). Mirrors the frontend
     * model in resources/js/lib/education.ts. `other` system = free text, no ladder.
     *
     * @var array<string,array<string,int>>
     */
    private const EDU_SYSTEM_LEVELS = [
        'general' => [
            'below_class5' => 1, 'class5' => 2, 'class8' => 3, 'ssc' => 4, 'hsc' => 5,
            'diploma' => 6, 'bachelor' => 7, 'masters' => 8, 'phd' => 9, 'other' => 99,
        ],
        'qawmi' => [
            'hifz' => 1, 'maktab' => 2, 'mutawassitah' => 3, 'sanawiyah' => 4,
            'fazilat' => 5, 'takmil' => 6, 'ifta' => 7, 'other' => 99,
        ],
        'alia' => [
            'ebtedayee' => 1, 'jdc' => 2, 'dakhil' => 3, 'alim' => 4, 'fazil' => 5,
            'kamil' => 6, 'other' => 99,
        ],
        'english_medium' => [
            'class5' => 2, 'class8' => 3, 'o_level' => 4, 'a_level' => 5, 'diploma' => 6,
            'bachelor' => 7, 'masters' => 8, 'phd' => 9, 'other' => 99,
        ],
        'vocational' => [
            'class8' => 3, 'ssc_voc' => 4, 'hsc_voc' => 5, 'diploma' => 6,
            'bachelor' => 7, 'other' => 99,
        ],
        'other' => [],
    ];

    /**
     * Reject education records whose KNOWN level ranks above the chosen highest
     * qualification within the selected system. Legacy / free-text / unknown
     * level strings are skipped so old rows and the free-text `other` system keep
     * saving. Backend safety net behind the frontend's submit guard.
     *
     * @param  array<string,mixed>  $validated
     */
    private function validateEducationConsistency(array $validated): void
    {
        $system  = $validated['education_medium'] ?? null;
        $highest = $validated['highest_qualification'] ?? null;
        $records = $validated['education_details'] ?? [];

        $ladder = self::EDU_SYSTEM_LEVELS[$system] ?? null;
        if (! $ladder || ! is_array($records) || $records === []) {
            return; // unknown system, free-text `other`, or nothing to check
        }

        $cap = $highest !== null ? ($ladder[$highest] ?? null) : null;
        if ($cap === null) {
            return; // unknown/unset highest → nothing to enforce against
        }

        foreach ($records as $i => $record) {
            $level = $record['level'] ?? null;
            if (! is_string($level) || $level === '') {
                continue;
            }
            $rank = $ladder[$level] ?? null;
            if ($rank !== null && $rank > $cap) {
                throw ValidationException::withMessages([
                    "education_details.$i.level" => __('biodata.edu_level_too_high'),
                ]);
            }
        }
    }

    /**
     * Validate + merge admin-defined custom fields (Phase E3) for this step into
     * biodatas.custom_fields. Only keys belonging to this step are touched, so a
     * draft on one step never wipes another step's custom values.
     */
    private function persistCustomFields(Request $request, Biodata $biodata, int $step, bool $isDraft): void
    {
        $fields = $this->fields->customFieldsForStep($step);
        if ($fields->isEmpty()) {
            return;
        }

        $rules = [];
        foreach ($fields as $field) {
            $key   = "custom_fields.{$field->field_key}";
            $parts = [($field->is_required && ! $isDraft) ? 'required' : 'nullable'];
            if (in_array($field->input_type, ['multi_select'], true)) {
                $parts[] = 'array';
            }
            if ($field->validation_rules) {
                $parts[] = $field->validation_rules;
            }
            $rules[$key] = implode('|', $parts);
        }

        $validated = $request->validate($rules);
        $incoming  = $validated['custom_fields'] ?? [];
        $existing  = $biodata->custom_fields ?? [];

        foreach ($fields as $field) {
            if (array_key_exists($field->field_key, $incoming)) {
                $existing[$field->field_key] = $incoming[$field->field_key];
            }
        }

        $biodata->custom_fields = $existing !== [] ? $existing : null;
    }

    /** Required field names per step (enforced on Continue / Submit, skipped for drafts). */
    private function requiredForStep(int $step): array
    {
        return match ($step) {
            1  => ['marital_status', 'birth_date', 'height_cm', 'weight_kg', 'complexion'],
            // Present country always required; the rest is country-conditional (see save()).
            2  => ['residing_country'],
            3  => ['religion'], // prayers_info added conditionally (Muslims only) in save()
            4  => ['highest_qualification', 'occupation'],
            5  => [], // physical fields enforced in step 1 now; lifestyle is optional

            6  => ['father_profession', 'mother_profession', 'family_type'],
            7  => ['residence_after_marriage'],
            8  => ['partner_age_min', 'partner_age_max', 'partner_education', 'partner_division'],
            9  => ['contact_privacy'],
            default => [],
        };
    }

    /** Type/format rules (all nullable). Required-ness is layered on in save(). */
    private function baseRulesForStep(int $step, string $gender): array
    {
        return match ($step) {
            1 => [
                'marital_status'    => ['nullable', 'in:never_married,married,divorced,widowed'],
                // Free-form nuance under the 4-value enum: separated/widow/widower/second_marriage.
                'marital_substatus' => ['nullable', 'string', 'max:30'],
                // DOB is collected as month + year on the client and composed into
                // birth_date (day = 01) — birth_date stays the single source of truth.
                'birth_date'        => ['nullable', 'date', 'before:-18 years'],
                // Physical summary — moved here from step 5 (Basic Information cleanup).
                'height_cm'         => ['nullable', 'integer', 'min:100', 'max:250'],
                'weight_kg'         => ['nullable', 'integer', 'min:20', 'max:200'],
                'complexion'        => ['nullable', 'in:very_fair,fair,wheatish,medium,dark'],
                'blood_group'       => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
                'health_status'     => ['nullable', 'in:healthy,minor_condition,disability,prefer_not_say'],
                'health_details'    => ['nullable', 'string', 'max:500'],
                // about_me / profile_headline / mother_tongue removed from this step
                // (columns retained; data preserved; no longer collected here).
            ],
            2 => [
                'nationality'       => ['nullable', 'string', 'max:60'],
                // Permanent address (Bangladesh cascade or abroad free text).
                'permanent_country' => ['nullable', 'string', 'max:60'],
                'division'          => ['nullable', 'string', 'max:60'],
                'district'          => ['nullable', 'string', 'max:60'],
                'upazila'           => ['nullable', 'string', 'max:60'],
                'permanent_address' => ['nullable', 'string', 'max:500'],
                'village_area'      => ['nullable', 'string', 'max:100'],
                'grew_up_in'        => ['nullable', 'string', 'max:60'],
                // Present address + "permanent same as present" toggle.
                'same_as_permanent' => ['nullable', 'boolean'],
                'current_division'  => ['nullable', 'string', 'max:60'],
                'current_district'  => ['nullable', 'string', 'max:60'],
                'current_upazila'   => ['nullable', 'string', 'max:60'],
                'current_area'      => ['nullable', 'string', 'max:100'],
                'present_address'   => ['nullable', 'string', 'max:500'],
                'residing_country'  => ['nullable', 'string', 'max:60'],
                'residing_city'     => ['nullable', 'string', 'max:80'],
                'is_nrb'            => ['boolean'],
                'visa_status'       => ['nullable', 'in:citizen,permanent_resident,work_visa,student_visa'],
            ],
            3 => [
                'religion'                 => ['nullable', 'string', 'max:30'],
                'sect'                     => ['nullable', 'string', 'max:50'],
                'is_practicing'            => ['boolean'],
                'prayers_info'             => ['nullable', 'in:5_times,4_times,sometimes,rarely,never'],
                'quran_recitation'         => ['nullable', 'in:fluent,basic,learning,no'],
                'fiqh'                     => ['nullable', 'string', 'max:50'],
                'clothing_style'           => ['nullable', 'string', 'max:100'],
                // Deeper deen practice detail (Phase B).
                'prayer_start_age'      => ['nullable', 'string', 'max:50'],
                'weekly_missed_prayers' => ['nullable', 'string', 'max:50'],
                'mahram_practice'       => ['nullable', 'string', 'max:500'],
                'islamic_books_read'    => ['nullable', 'string', 'max:500'],
                'deen_work_details'     => ['nullable', 'string', 'max:500'],
                'social_media_usage'    => ['nullable', 'string', 'max:150'],
                // Gender-specific appearance/sunnah fields.
                ...$gender === 'male'
                    ? [
                        'beard_info'        => ['nullable', 'string', 'max:50'],
                        'beard_since'       => ['nullable', 'string', 'max:50'],
                        'pants_above_ankle' => ['nullable', 'boolean'],
                    ]
                    : [
                        'hijab_info'     => ['nullable', 'string', 'max:50'],
                        'niqab_since'    => ['nullable', 'string', 'max:50'],
                        'purdah_details' => ['nullable', 'string', 'max:500'],
                    ],
                'is_islamically_educated' => ['boolean'],
                'beliefs_on_mazar'        => ['nullable', 'string', 'max:500'],
                'favorite_scholars'       => ['nullable', 'string', 'max:300'],
                'wali_approval'           => ['nullable', 'boolean'],
                'sunni_scale'             => ['nullable', 'integer', 'min:1', 'max:10'],
            ],
            4 => [
                'education_method'                        => ['nullable', 'in:general,islamic,both'],
                // Wider medium beside legacy education_method (Phase B).
                'education_medium'                        => ['nullable', 'in:general,qawmi,alia,english_medium,vocational,other'],
                // System-scoped qualification keys (Phase: education workflow fix)
                // plus legacy keys kept for backward compatibility with old rows.
                'highest_qualification'                   => ['nullable', 'in:below_class5,class5,class8,ssc,hsc,diploma,bachelor,masters,phd,o_level,a_level,ssc_voc,hsc_voc,hifz,maktab,mutawassitah,sanawiyah,fazilat,takmil,ifta,ebtedayee,jdc,dakhil,alim,fazil,kamil,other,below_ssc,graduation,post_graduation,hafez'],
                'education_details'                       => ['nullable', 'array'],
                'education_details.*'                     => ['nullable', 'array'],
                'education_details.*.level'               => ['nullable', 'string', 'max:100'],
                'education_details.*.edu_type'            => ['nullable', 'string', 'max:50'],
                'education_details.*.subject'             => ['nullable', 'string', 'max:100'],
                'education_details.*.institute'           => ['nullable', 'string', 'max:200'],
                'education_details.*.board_university'    => ['nullable', 'string', 'max:200'],
                'education_details.*.passing_year'        => ['nullable', 'string', 'max:10'],
                'education_details.*.result_type'         => ['nullable', 'string', 'max:50'],
                'education_details.*.result_value'        => ['nullable', 'string', 'max:100'],
                'education_details.*.is_current'          => ['nullable', 'boolean'],
                'education_details.*.note'                => ['nullable', 'string', 'max:300'],
                'occupation'            => ['nullable', 'string', 'max:100'],
                'occupation_category'   => ['nullable', 'in:business,service_govt,service_private,education,medical,engineering,agriculture,student,housewife,ngo,it,abroad_job,other'],
                'profession_details'    => ['nullable', 'string', 'max:500'],
                'monthly_income'        => ['nullable', 'integer', 'min:0'],
                'profession_halal_status' => ['nullable', 'in:halal,not_sure,halal_alternative,prefer_not_say'],
                // Income framing + privacy + career (Phase B). `yearly` kept for
                // legacy rows; `business/freelance/daily` + `range` are the newer
                // guided options surfaced in the wizard.
                'income_type'           => ['nullable', 'in:monthly,yearly,variable,private,business,freelance,daily'],
                'income_privacy'        => ['nullable', 'in:public,private,members_only,range'],
                'workplace_type'        => ['nullable', 'string', 'max:100'],
                'future_career_plan'    => ['nullable', 'string', 'max:500'],
            ],
            5 => [
                // Physical fields (height/weight/complexion/blood_group/health) now
                // live in step 1; step 5 keeps lifestyle only.
                'diet'                => ['nullable', 'in:halal_only,vegetarian,no_restriction'],
                'smoking'             => ['nullable', 'in:never,occasionally,regularly'],
                'hobbies'             => ['nullable', 'string', 'max:500'],
                'watch_entertainment' => ['nullable', 'string', 'max:50'],
                'special_category'    => ['nullable', 'string', 'max:100'],
            ],
            6 => [
                'father_name'              => ['nullable', 'string', 'max:100'],
                'father_alive'             => ['nullable', 'boolean'],
                'father_profession'        => ['nullable', 'string', 'max:100'],
                'mother_name'              => ['nullable', 'string', 'max:100'],
                'mother_alive'             => ['nullable', 'boolean'],
                'mother_profession'        => ['nullable', 'string', 'max:100'],
                'brothers'                 => ['nullable', 'integer', 'min:0', 'max:20'],
                'sisters'                  => ['nullable', 'integer', 'min:0', 'max:20'],
                'brothers_details'                        => ['nullable', 'array'],
                'brothers_details.*.position'             => ['nullable', 'string', 'max:20'],
                'brothers_details.*.marital_status'       => ['nullable', 'string', 'max:30'],
                'brothers_details.*.education'            => ['nullable', 'string', 'max:100'],
                'brothers_details.*.profession'           => ['nullable', 'string', 'max:100'],
                'brothers_details.*.location'             => ['nullable', 'string', 'max:100'],
                'brothers_details.*.note'                 => ['nullable', 'string', 'max:300'],
                'sisters_details'                         => ['nullable', 'array'],
                'sisters_details.*.position'              => ['nullable', 'string', 'max:20'],
                'sisters_details.*.marital_status'        => ['nullable', 'string', 'max:30'],
                'sisters_details.*.education'             => ['nullable', 'string', 'max:100'],
                'sisters_details.*.profession'            => ['nullable', 'string', 'max:100'],
                'sisters_details.*.location'              => ['nullable', 'string', 'max:100'],
                'sisters_details.*.note'                  => ['nullable', 'string', 'max:300'],
                'uncle_profession'         => ['nullable', 'string', 'max:150'],
                'family_type'              => ['nullable', 'in:joint,nuclear,flexible'],
                'family_financial_status'  => ['nullable', 'in:lower,lower_middle,middle,upper_middle,upper'],
                'home_ownership'           => ['nullable', 'in:own_house,rented,family_house,other'],
                'family_assets_details'    => ['nullable', 'string', 'max:1000'],
                'family_details'           => ['nullable', 'string', 'max:1000'],
                'family_religious_condition' => ['nullable', 'string', 'max:100'],
            ],
            7 => [
                'guardian_agree'           => ['nullable', 'boolean'],
                'why_getting_married'      => ['nullable', 'string', 'max:1000'],
                'marriage_thoughts'        => ['nullable', 'string', 'max:1000'],
                'marriage_timeline'        => ['nullable', 'string', 'max:60'],
                // Male-oriented expectations.
                'wife_in_veil'             => ['nullable', 'boolean'],
                'wife_study_allowed'       => ['nullable', 'boolean'],
                'wife_job_allowed'         => ['nullable', 'boolean'],
                'residence_after_marriage' => ['nullable', 'string', 'max:100'],
                'post_marriage_plan'       => ['nullable', 'string', 'max:100'],
                'expect_gift_from_bride'   => ['nullable', 'string', 'max:50'],
                'gift_expectation_details' => ['nullable', 'string', 'max:500'],
                'polygamy_open'            => ['boolean'],
                // Female-oriented intentions.
                'wants_to_work'            => ['nullable', 'boolean'],
                'continue_study'           => ['nullable', 'boolean'],
                'continue_job'             => ['nullable', 'boolean'],
                'preferred_living'         => ['nullable', 'string', 'max:100'],
                // Children (any previously-married status).
                'has_children'             => ['nullable', 'boolean'],
                'children_count'           => ['nullable', 'integer', 'min:0', 'max:30'],
                'children_live_with'       => ['nullable', 'string', 'max:100'],
                'children_notes'           => ['nullable', 'string', 'max:500'],
                // Divorced-specific.
                'previous_marriage_date'   => ['nullable', 'date'],
                'divorce_date'             => ['nullable', 'date'],
                'divorce_reason'           => ['nullable', 'string', 'max:1000'],
                // Widowed-specific.
                'spouse_death_date'        => ['nullable', 'date'],
                'spouse_death_reason'      => ['nullable', 'string', 'max:1000'],
                'child_acceptance_expectation' => ['nullable', 'string', 'max:1000'],
                // Married / second-marriage-specific.
                'reason_for_second_marriage' => ['nullable', 'string', 'max:1000'],
                'current_wife_count'       => ['nullable', 'integer', 'min:0', 'max:4'],
                'current_family_consent'   => ['nullable', 'boolean'],
                'first_wife_knows'         => ['nullable', 'boolean'],
                'second_marriage_living'   => ['nullable', 'string', 'max:150'],
            ],
            8 => [
                'partner_age_min'           => ['nullable', 'integer', 'min:18', 'max:80'],
                'partner_age_max'           => ['nullable', 'integer', 'min:18', 'max:80'],
                'partner_height_cm_min'     => ['nullable', 'integer', 'min:100', 'max:250'],
                'partner_height_cm_max'     => ['nullable', 'integer', 'min:100', 'max:250'],
                'partner_complexion'        => ['nullable', 'string', 'max:30'],
                'partner_marital_status'    => ['nullable', 'string', 'max:30'],
                'partner_education'         => ['nullable', 'string', 'max:60'],
                'partner_occupation_pref'   => ['nullable', 'string', 'max:100'],
                'partner_income_min'        => ['nullable', 'integer', 'min:0'],
                'partner_income_max'        => ['nullable', 'integer', 'min:0'],
                'partner_division'          => ['nullable', 'string', 'max:60'],
                'partner_district'          => ['nullable', 'string', 'max:60'],
                // Multi-select districts beside legacy single partner_district (Phase B).
                'partner_districts'         => ['nullable', 'array'],
                'partner_districts.*'       => ['nullable', 'string', 'max:60'],
                'partner_family_type'       => ['nullable', 'string', 'max:20'],
                'partner_economic_status'   => ['nullable', 'string', 'max:60'],
                'partner_deen_practice'     => ['nullable', 'string', 'max:100'],
                'partner_special_qualities' => ['nullable', 'string', 'max:1000'],
                'partner_deal_breakers'     => ['nullable', 'string', 'max:1000'],
                'partner_expectations'      => ['nullable', 'string', 'max:1000'],
            ],
            9 => [
                'contact_person_name'   => ['nullable', 'string', 'max:100'],
                'guardian_name'         => ['nullable', 'string', 'max:100'],
                'guardian_mobile'       => ['nullable', 'string', 'max:20'],
                'guardian_relationship' => ['nullable', 'string', 'max:50'],
                'guardian_email'        => ['nullable', 'email', 'max:100'],
                'guardian_whatsapp'     => ['nullable', 'string', 'max:20'],
                'whatsapp_number'       => ['nullable', 'string', 'max:20'],
                'contact_privacy'       => ['nullable', 'in:private,request_only,matches_only'],
                'biodata_visibility'    => ['nullable', 'in:public,private,admin_approved_only'],
                'allow_shortlist'       => ['nullable', 'boolean'],
                'allow_contact_request' => ['nullable', 'boolean'],
            ],
            10 => [
                // Review step — persisted declaration flags (Phase B). The blocking
                // gate is still `confirm_correct` (added in save()) until the Phase C
                // UI sends these three checkboxes; they are stored when present.
                'guardian_knows_biodata'  => ['nullable', 'boolean'],
                'info_truthful_confirmed' => ['nullable', 'boolean'],
                'accept_liability_terms'  => ['nullable', 'boolean'],
            ],
            default => [],
        };
    }

    /**
     * Decide the biodata status based on the "Require Admin Approval for Biodata"
     * setting (system.profile_approval_required, default enabled).
     */
    private function applyApprovalStatus(Biodata $biodata): void
    {
        $approvalRequired = SystemSetting::bool('system.profile_approval_required', true);

        if ($approvalRequired) {
            $biodata->status      = 'pending';
            $biodata->approved_at = null;
            $biodata->approved_by = null;

            return;
        }

        // System auto-approval: no admin actor recorded in approved_by.
        $biodata->status      = 'approved';
        $biodata->approved_at = $biodata->approved_at ?? now();
        $biodata->rejected_at = null;
        $biodata->rejected_by = null;
    }
}
