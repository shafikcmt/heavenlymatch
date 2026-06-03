<?php

namespace App\Http\Controllers\Biodata;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\Registration;
use App\Models\SystemSetting;
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

            foreach ($required as $field) {
                $existing = $rules[$field] ?? ['nullable'];
                $existing = array_values(array_filter($existing, fn ($r) => $r !== 'nullable'));
                $rules[$field] = array_values(array_unique([...['required'], ...$existing]));
            }
            if ($step === self::PHOTO_STEP) {
                $rules['confirm_correct'] = ['accepted'];
            }
        }

        $validated = $request->validate($rules);
        unset($validated['confirm_correct']); // transient — not a biodata column

        // Normalise + validate the optional WhatsApp number (Bangladesh format).
        if ($step === 9 && ! empty($validated['whatsapp_number'])) {
            $normalized = $this->phone->normalizePhone($validated['whatsapp_number']);
            if ($normalized === null) {
                throw ValidationException::withMessages([
                    'whatsapp_number' => __('biodata.whatsapp_invalid'),
                ]);
            }
            $validated['whatsapp_number'] = $normalized;
        }

        $biodata = Biodata::firstOrNew(['registration_id' => $user->registration_id]);
        $biodata->fill($validated);

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

    /** Required field names per step (enforced on Continue / Submit, skipped for drafts). */
    private function requiredForStep(int $step): array
    {
        return match ($step) {
            1  => ['marital_status', 'birth_date'],
            2  => ['residing_country', 'residing_city', 'division', 'district'],
            3  => ['religion'], // prayers_info added conditionally (Muslims only) in save()
            4  => ['highest_qualification', 'occupation'],
            5  => ['height_cm', 'weight_kg', 'complexion'],
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
                'marital_status'   => ['nullable', 'in:never_married,married,divorced,widowed'],
                'birth_date'       => ['nullable', 'date', 'before:-18 years'],
                'about_me'         => ['nullable', 'string', 'max:1000'],
                'profile_headline' => ['nullable', 'string', 'max:200'],
                'mother_tongue'    => ['nullable', 'string', 'max:50'],
            ],
            2 => [
                'nationality'       => ['nullable', 'string', 'max:60'],
                'division'          => ['nullable', 'string', 'max:60'],
                'district'          => ['nullable', 'string', 'max:60'],
                'upazila'           => ['nullable', 'string', 'max:60'],
                'permanent_address' => ['nullable', 'string', 'max:500'],
                'grew_up_in'        => ['nullable', 'string', 'max:60'],
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
                ...$gender === 'male'
                    ? ['beard_info' => ['nullable', 'string', 'max:50']]
                    : ['hijab_info'  => ['nullable', 'string', 'max:50']],
                'is_islamically_educated' => ['boolean'],
                'beliefs_on_mazar'        => ['nullable', 'string', 'max:500'],
                'favorite_scholars'       => ['nullable', 'string', 'max:300'],
                'wali_approval'           => ['nullable', 'boolean'],
                'sunni_scale'             => ['nullable', 'integer', 'min:1', 'max:10'],
            ],
            4 => [
                'education_method'                        => ['nullable', 'in:general,islamic,both'],
                'highest_qualification'                   => ['nullable', 'in:below_ssc,ssc,hsc,diploma,graduation,post_graduation,phd,hafez,alim,fazil,kamil,other'],
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
            ],
            5 => [
                'height_cm'           => ['nullable', 'integer', 'min:100', 'max:250'],
                'weight_kg'           => ['nullable', 'integer', 'min:20', 'max:200'],
                'complexion'          => ['nullable', 'in:very_fair,fair,wheatish,medium,dark'],
                'blood_group'         => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
                'health_status'       => ['nullable', 'in:healthy,minor_condition,disability,prefer_not_say'],
                'health_details'      => ['nullable', 'string', 'max:500'],
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
                'family_type'              => ['nullable', 'in:joint,nuclear,flexible'],
                'family_financial_status'  => ['nullable', 'in:lower,lower_middle,middle,upper_middle,upper'],
                'home_ownership'           => ['nullable', 'in:own_house,rented,family_house,other'],
                'family_details'           => ['nullable', 'string', 'max:1000'],
                'family_religious_condition' => ['nullable', 'string', 'max:100'],
            ],
            7 => [
                'guardian_agree'           => ['nullable', 'boolean'],
                'wife_in_veil'             => ['nullable', 'boolean'],
                'wife_study_allowed'       => ['nullable', 'boolean'],
                'wife_job_allowed'         => ['nullable', 'boolean'],
                'residence_after_marriage' => ['nullable', 'string', 'max:100'],
                'post_marriage_plan'       => ['nullable', 'string', 'max:100'],
                'polygamy_open'            => ['boolean'],
                'has_children'             => ['nullable', 'boolean'],
                'children_count'           => ['nullable', 'integer', 'min:0', 'max:30'],
                'children_live_with'       => ['nullable', 'string', 'max:100'],
                'children_notes'           => ['nullable', 'string', 'max:500'],
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
                'partner_family_type'       => ['nullable', 'string', 'max:20'],
                'partner_expectations'      => ['nullable', 'string', 'max:1000'],
            ],
            9 => [
                'guardian_mobile'       => ['nullable', 'string', 'max:20'],
                'guardian_relationship' => ['nullable', 'string', 'max:50'],
                'guardian_email'        => ['nullable', 'email', 'max:100'],
                'whatsapp_number'       => ['nullable', 'string', 'max:20'],
                'contact_privacy'       => ['nullable', 'in:private,request_only,matches_only'],
            ],
            10 => [
                // Review step — only a confirmation checkbox (added in save()).
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
