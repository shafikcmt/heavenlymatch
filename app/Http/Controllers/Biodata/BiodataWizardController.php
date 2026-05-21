<?php

namespace App\Http\Controllers\Biodata;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class BiodataWizardController extends Controller
{
    private const STEPS = [
        1 => 'general',
        2 => 'location',
        3 => 'religion',
        4 => 'education',
        5 => 'family',
        6 => 'lifestyle',
        7 => 'marriage',
        8 => 'partner',
        9 => 'photos',
    ];

    public function show(int $step = 1): Response|RedirectResponse
    {
        if (!array_key_exists($step, self::STEPS)) {
            return redirect()->route('biodata.wizard', ['step' => 1]);
        }

        /** @var Registration $user */
        $user = Auth::user();
        $biodata = $user->biodata ?? new Biodata();

        return Inertia::render('Biodata/Wizard', [
            'step'    => $step,
            'steps'   => self::STEPS,
            'biodata' => $biodata,
            'user'    => [
                'name'   => $user->name,
                'gender' => $user->gender,
                'mode'   => $user->platform_mode,
            ],
        ]);
    }

    public function save(Request $request, int $step): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();
        $rules = $this->rulesForStep($step, $user->gender ?? 'male');

        $validated = $request->validate($rules);

        $biodata = Biodata::firstOrNew(['registration_id' => $user->registration_id]);
        $biodata->fill($validated);
        $biodata->completeness_score = $this->computeCompleteness($biodata);

        if ($step === count(self::STEPS)) {
            $biodata->is_completed = true;
            $biodata->status = 'pending';
        }

        $biodata->save();

        $nextStep = $step + 1;

        if ($nextStep > count(self::STEPS)) {
            return redirect()->route('dashboard')->with('success', 'Your biodata has been submitted for review!');
        }

        return redirect()->route('biodata.wizard', ['step' => $nextStep]);
    }

    private function rulesForStep(int $step, string $gender): array
    {
        return match ($step) {
            1 => [
                'marital_status'   => ['nullable', 'in:never_married,married,divorced,widowed'],
                'birth_date'       => ['nullable', 'date', 'before:-18 years'],
                'height_cm'        => ['nullable', 'integer', 'min:100', 'max:250'],
                'weight_kg'        => ['nullable', 'integer', 'min:30', 'max:200'],
                'complexion'       => ['nullable', 'in:very_fair,fair,wheatish,medium,dark'],
                'blood_group'      => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
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
                'education_method'      => ['nullable', 'in:general,islamic,both'],
                'highest_qualification' => ['nullable', 'in:below_ssc,ssc,hsc,diploma,graduation,post_graduation,phd,hafez,alim,fazil,kamil,other'],
                'education_details'     => ['nullable', 'array'],
                'occupation'            => ['nullable', 'string', 'max:100'],
                'occupation_category'   => ['nullable', 'in:business,service_govt,service_private,education,medical,engineering,agriculture,student,housewife,ngo,it,abroad_job,other'],
                'profession_details'    => ['nullable', 'string', 'max:500'],
                'monthly_income'        => ['nullable', 'integer', 'min:0'],
            ],
            5 => [
                'father_name'              => ['nullable', 'string', 'max:100'],
                'father_alive'             => ['nullable', 'boolean'],
                'father_profession'        => ['nullable', 'string', 'max:100'],
                'mother_name'              => ['nullable', 'string', 'max:100'],
                'mother_alive'             => ['nullable', 'boolean'],
                'mother_profession'        => ['nullable', 'string', 'max:100'],
                'brothers'                 => ['nullable', 'integer', 'min:0', 'max:20'],
                'sisters'                  => ['nullable', 'integer', 'min:0', 'max:20'],
                'family_type'              => ['nullable', 'in:joint,nuclear,flexible'],
                'family_financial_status'  => ['nullable', 'in:lower,lower_middle,middle,upper_middle,upper'],
                'home_ownership'           => ['nullable', 'in:own_house,rented,family_house,other'],
                'family_details'           => ['nullable', 'string', 'max:1000'],
                'family_religious_condition' => ['nullable', 'string', 'max:100'],
            ],
            6 => [
                'health_status'       => ['nullable', 'in:healthy,minor_condition,disability,prefer_not_say'],
                'health_details'      => ['nullable', 'string', 'max:500'],
                'diet'                => ['nullable', 'in:halal_only,vegetarian,no_restriction'],
                'smoking'             => ['nullable', 'in:never,occasionally,regularly'],
                'hobbies'             => ['nullable', 'string', 'max:500'],
                'watch_entertainment' => ['nullable', 'string', 'max:50'],
                'special_category'    => ['nullable', 'string', 'max:100'],
            ],
            7 => [
                'guardian_agree'           => ['nullable', 'boolean'],
                'wife_in_veil'             => ['nullable', 'boolean'],
                'wife_study_allowed'       => ['nullable', 'boolean'],
                'wife_job_allowed'         => ['nullable', 'boolean'],
                'residence_after_marriage' => ['nullable', 'string', 'max:100'],
                'post_marriage_plan'       => ['nullable', 'string', 'max:100'],
                'polygamy_open'            => ['boolean'],
                'children_count'           => ['nullable', 'integer', 'min:0', 'max:30'],
                'guardian_mobile'          => ['nullable', 'string', 'max:20'],
                'guardian_relationship'    => ['nullable', 'string', 'max:50'],
                'guardian_email'           => ['nullable', 'email', 'max:100'],
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
                // Photos handled via separate upload endpoint; step 9 just marks completion
            ],
            default => [],
        };
    }

    private function computeCompleteness(Biodata $biodata): int
    {
        $fields = [
            'marital_status', 'birth_date', 'height_cm', 'about_me',
            'division', 'district', 'residing_country',
            'religion', 'is_practicing', 'prayers_info',
            'highest_qualification', 'occupation',
            'family_type', 'brothers', 'sisters',
            'health_status', 'diet',
            'partner_age_min', 'partner_age_max', 'partner_expectations',
        ];

        $filled = collect($fields)
            ->filter(fn($f) => !is_null($biodata->$f) && $biodata->$f !== '')
            ->count();

        $base = (int) round(($filled / count($fields)) * 80);

        $bonus = 0;
        if (!empty($biodata->about_me) && strlen($biodata->about_me) > 100) $bonus += 10;
        if (!empty($biodata->photos)) $bonus += 10;

        return min(100, $base + $bonus);
    }
}
