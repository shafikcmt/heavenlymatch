<?php

/**
 * LEGACY — No active routes reference this controller.
 * Superseded by App\Http\Controllers\Biodata\BiodataWizardController (Inertia).
 * Broken route refs: route('biodata.create'), route('myhome') — both defunct.
 * Safe to delete after confirming no session/Blade view depends on it.
 */

namespace App\Http\Controllers\Legacy;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\SystemSetting;
use App\Models\UserAttribute;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BiodataController extends Controller
{
    private int $maxStep = 10;

    public function create(Request $request, $step = 1)
    {
        $step = max(1, min((int) $step, $this->maxStep));

        return view('pages.user-dashboard.create_biodatas', [
            'step' => $step,
            'maxStep' => $this->maxStep,
            'draft' => $request->session()->get('biodata', []),
            'savedBiodata' => $request->user()->biodata,
            'religionOptions' => UserAttribute::optionsFor('religion'),
            'bloodGroupOptions' => UserAttribute::optionsFor('blood-group'),
            'maritalStatusOptions' => UserAttribute::optionsFor('marital-status'),
        ]);
    }

    public function store(Request $request, $step)
    {
        $step = max(1, min((int) $step, $this->maxStep));

        if ($request->has('back')) {
            return redirect()->route('biodata.create', max(1, $step - 1));
        }

        if ($request->has('draft')) {
            $draftData = $request->except(['_token', 'back', 'next', 'complete', 'draft']);

            if ($step === 5 && $request->hasFile('groom_photo')) {
                $draftData['groom_photo'] = $request->file('groom_photo')->store('groom_photos', 'public');
            }

            $draft = $request->session()->get('biodata', []);
            $draft["step_{$step}"] = array_merge($draft["step_{$step}"] ?? [], $draftData);
            $request->session()->put('biodata', $draft);

            $partialData = $this->normaliseForDatabase($draftData);
            $partialData['registration_id'] = $request->user()->registration_id;
            $partialData['completion_status'] = 'draft';
            $partialData['approval_status'] = optional($request->user()->biodata)->approval_status ?? (SystemSetting::bool('system.profile_approval_required', true) ? 'pending' : 'approved');

            Biodata::updateOrCreate(
                ['registration_id' => $request->user()->registration_id],
                $this->filterFillable($partialData)
            );

            return redirect()->route('biodata.create', $step)->with('success', 'Draft saved successfully.');
        }

        $validated = $request->validate($this->rulesForStep($step), $this->messages());

        if ($step === 5 && $request->hasFile('groom_photo')) {
            $validated['groom_photo'] = $request->file('groom_photo')->store('groom_photos', 'public');
        }

        $draft = $request->session()->get('biodata', []);
        $draft["step_{$step}"] = $validated;
        $request->session()->put('biodata', $draft);

        $partialData = $this->normaliseForDatabase($validated);
        $partialData['registration_id'] = $request->user()->registration_id;
        $partialData['completion_status'] = 'draft';
        $partialData['approval_status'] = optional($request->user()->biodata)->approval_status ?? (SystemSetting::bool('system.profile_approval_required', true) ? 'pending' : 'approved');

        Biodata::updateOrCreate(
            ['registration_id' => $request->user()->registration_id],
            $this->filterFillable($partialData)
        );

        if ($step === $this->maxStep) {
            $allData = [];
            foreach ($draft as $data) {
                $allData = array_merge($allData, $data);
            }

            $allData = $this->normaliseForDatabase($allData);
            $allData['registration_id'] = $request->user()->registration_id;
            $allData['is_completed'] = true;
            $allData['completion_status'] = 'completed';
            $allData['approval_status'] = optional($request->user()->biodata)->approval_status ?? (SystemSetting::bool('system.profile_approval_required', true) ? 'pending' : 'approved');

            Biodata::updateOrCreate(
                ['registration_id' => $request->user()->registration_id],
                $this->filterFillable($allData)
            );

            $request->session()->forget('biodata');

            return redirect()->route('myhome')->with('success', 'Biodata saved successfully. Your profile is now ready for review.');
        }

        return redirect()->route('biodata.create', min($this->maxStep, $step + 1));
    }

    private function rulesForStep(int $step): array
    {
        $adultDate = now()->subYears(18)->format('Y-m-d');

        return match ($step) {
            1 => [
                'biodata_type' => 'required|string|in:groom,bride',
                'religion' => 'nullable|string|max:120',
                'marital_status' => 'required|string|max:120',
                'previous_marriage_details' => 'nullable|required_unless:marital_status,Single,Never Married|string|max:1200',
                'children_count' => 'nullable|integer|min:0|max:20',
                'birth_date' => "required|date|before_or_equal:{$adultDate}",
                'height' => 'required|string|max:20',
                'weight' => 'required|string|max:20',
                'complexion' => 'required|string|max:60',
                'blood_group' => 'required|string|max:20',
                'nationality' => 'required|string|max:50',
            ],
            2 => [
                'permanent_address' => 'required|string|max:255',
                'village_area' => 'nullable|string|max:255',
                'present_address' => 'required|string|max:255',
                'grew_up' => 'required|string|max:255',
            ],
            3 => [
                'education_method' => 'required|string|in:General,Qawmi,Alia,General + Islamic,Other',
                'highest_qualification' => 'required|string|max:255',
                'ssc_year' => 'nullable|string|max:80',
                'ssc_group' => 'nullable|string|max:120',
                'diploma_subject' => 'nullable|string|max:255',
                'diploma_medium' => 'nullable|string|max:120',
                'diploma_institution' => 'nullable|string|max:255',
                'diploma_year' => 'nullable|string|max:80',
                'graduation_subject' => 'nullable|string|max:255',
                'graduation_institution' => 'nullable|string|max:255',
                'graduation_year' => 'nullable|string|max:80',
                'postgraduation_subject' => 'nullable|string|max:255',
                'postgraduation_institution' => 'nullable|string|max:255',
                'postgraduation_year' => 'nullable|string|max:80',
                'islamic_titles' => 'nullable|array|max:8',
                'islamic_titles.*' => 'string|max:80',
                'islamic_institution' => 'nullable|string|max:255',
                'islamic_year' => 'nullable|string|max:80',
                'other_education' => 'nullable|string|max:1500',
            ],
            4 => [
                'father_name' => 'required|string|max:255',
                'father_alive' => 'required|string|in:Yes,No',
                'father_profession' => 'required|string|max:700',
                'mother_name' => 'required|string|max:255',
                'mother_alive' => 'required|string|in:Yes,No',
                'mother_profession' => 'required|string|max:700',
                'brothers' => 'required|integer|min:0|max:20',
                'brothers_info' => 'nullable|string|max:1500',
                'sisters' => 'required|integer|min:0|max:20',
                'sisters_info' => 'nullable|string|max:1500',
                'uncle_profession' => 'nullable|string|max:1000',
                'family_financial_status' => 'required|string|max:120',
                'home_ownership' => 'required|string|max:1200',
                'family_details' => 'required|string|max:1500',
                'family_religious_condition' => 'required|string|max:1500',
            ],
            5 => [
                'clothing_style' => 'required|string|max:1000',
                'niqab_since' => 'nullable|string|max:500',
                'beard_info' => 'nullable|string|max:500',
                'clothes_above_ankles' => 'nullable|string|max:500',
                'prayers_info' => 'required|string|max:700',
                'prayers_qaza_weekly' => 'required|string|max:255',
                'mahram_nonmahram' => 'required|string|max:700',
                'quran_recitation' => 'required|string|max:255',
                'fiqh' => 'required|string|max:80',
                'watch_entertainment' => 'required|string|max:700',
                'diseases' => 'required|string|max:700',
                'religious_work' => 'nullable|string|max:700',
                'beliefs_on_mazar' => 'required|string|max:700',
                'books_read' => 'required|string|max:700',
                'favorite_scholars' => 'required|string|max:700',
                'special_category' => 'nullable|array|max:8',
                'special_category.*' => 'string|max:100',
                'hobbies' => 'nullable|string|max:1000',
                'groom_mobile' => 'nullable|string|max:20',
                'groom_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ],
            6 => [
                'occupation' => 'required|string|max:255',
                'profession_details' => 'required|string|max:1200',
                'profession_halal_status' => 'required|string|max:700',
                'monthly_income' => 'nullable|numeric|min:0|max:999999999',
            ],
            7 => [
                'guardian_agree' => 'required|string|in:Yes,No,Need to discuss',
                'wife_in_veil' => 'nullable|string|max:255',
                'wife_study_allowed' => 'nullable|string|max:255',
                'wife_job_allowed' => 'nullable|string|max:255',
                'residence_after_marriage' => 'required|string|max:500',
                'expect_gift_from_bride' => 'required|string|in:Yes,No',
                'marriage_plan' => 'required|string|max:1200',
            ],
            8 => [
                'partner_age' => 'required|string|max:80',
                'partner_complexion' => 'nullable|array|max:5',
                'partner_complexion.*' => 'string|max:80',
                'partner_height' => 'nullable|string|max:100',
                'partner_education' => 'nullable|string|max:255',
                'partner_district' => 'nullable|string|max:255',
                'partner_marital_status' => 'nullable|array|max:5',
                'partner_marital_status.*' => 'string|max:80',
                'partner_profession' => 'nullable|string|max:255',
                'partner_financial_condition' => 'nullable|string|max:120',
                'partner_expectations' => 'required|string|max:2500',
            ],
            9 => [
                'parents_know' => 'required|string|in:Yes,No',
                'truth_testify' => 'required|accepted',
                'responsibility' => 'required|accepted',
                'privacy_consent' => 'required|accepted',
            ],
            10 => [
                'groom_name' => 'required|string|max:255',
                'guardian_mobile' => 'required|string|max:20',
                'guardian_relationship' => 'required|string|max:100',
                'guardian_email' => 'nullable|email|max:255',
            ],
            default => [],
        };
    }

    private function messages(): array
    {
        return [
            'birth_date.before_or_equal' => 'The candidate must be at least 18 years old.',
            'previous_marriage_details.required_unless' => 'Please add previous marriage details for married/divorced/widow/widower status.',
            'truth_testify.accepted' => 'Please confirm that the biodata information is true.',
            'responsibility.accepted' => 'Please accept responsibility for the submitted information.',
            'privacy_consent.accepted' => 'Please accept the privacy and contact sharing consent.',
        ];
    }

    private function normaliseForDatabase(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = implode(', ', array_filter($value));
            }
            if (is_bool($value)) {
                $data[$key] = $value ? 'Yes' : 'No';
            }
        }

        return $data;
    }

    private function filterFillable(array $data): array
    {
        $fillable = (new Biodata())->getFillable();
        return Arr::only($data, $fillable);
    }

    public function updateGeneralInfo(Request $request, $id)
    {
        $validated = $request->validate([
            'biodata_type' => 'nullable|string|max:30',
            'religion' => 'nullable|string|max:120',
            'marital_status' => 'nullable|string|max:120',
            'previous_marriage_details' => 'nullable|string|max:1200',
            'children_count' => 'nullable|integer|min:0|max:20',
            'birth_date' => 'nullable|date',
            'height' => 'nullable|string|max:50',
            'complexion' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:30',
            'nationality' => 'nullable|string|max:50',
        ]);
        Biodata::findOrFail($id)->update($validated);
        return back()->with('success', 'General information updated successfully.');
    }

    public function updateAddress(Request $request, $id)
    {
        Biodata::findOrFail($id)->update($request->only(['present_address', 'village_area', 'permanent_address', 'grew_up']));
        return back()->with('success', 'Address updated successfully.');
    }

    public function updateEducation(Request $request, $id)
    {
        $data = $this->normaliseForDatabase($request->only([
            'education_method', 'highest_qualification', 'other_education', 'ssc_year', 'ssc_group', 'diploma_subject',
            'diploma_medium', 'diploma_institution', 'diploma_year', 'graduation_subject', 'graduation_institution',
            'graduation_year', 'postgraduation_subject', 'postgraduation_institution', 'postgraduation_year',
            'islamic_titles', 'islamic_institution', 'islamic_year'
        ]));
        Biodata::findOrFail($id)->update($this->filterFillable($data));
        return back()->with('success', 'Educational information updated successfully.');
    }

    public function updateFamily(Request $request, $id)
    {
        Biodata::findOrFail($id)->update($request->only([
            'father_name', 'father_alive', 'father_profession', 'mother_name', 'mother_alive', 'mother_profession',
            'brothers', 'brothers_info', 'sisters', 'sisters_info', 'uncle_profession', 'family_financial_status',
            'home_ownership', 'family_details', 'family_religious_condition'
        ]));
        return back()->with('success', 'Family information updated successfully.');
    }

    public function updatePersonal(Request $request, $id)
    {
        $data = $this->normaliseForDatabase($request->only([
            'clothing_style', 'niqab_since', 'beard_info', 'clothes_above_ankles', 'prayers_info', 'prayers_qaza_weekly',
            'mahram_nonmahram', 'quran_recitation', 'fiqh', 'watch_entertainment', 'diseases', 'religious_work',
            'beliefs_on_mazar', 'books_read', 'favorite_scholars', 'special_category', 'hobbies', 'groom_mobile'
        ]));
        Biodata::findOrFail($id)->update($this->filterFillable($data));
        return back()->with('success', 'Personal information updated successfully.');
    }

    public function updateOccupation(Request $request, $id)
    {
        Biodata::findOrFail($id)->update($request->only(['occupation', 'profession_details', 'profession_halal_status', 'monthly_income']));
        return back()->with('success', 'Occupational information updated successfully.');
    }

    public function updateMarriage(Request $request, $id)
    {
        Biodata::findOrFail($id)->update($request->only([
            'guardian_agree', 'wife_in_veil', 'wife_study_allowed', 'wife_job_allowed', 'residence_after_marriage',
            'expect_gift_from_bride', 'marriage_plan'
        ]));
        return back()->with('success', 'Marriage information updated successfully.');
    }

    public function updatePartner(Request $request, $id)
    {
        $data = $this->normaliseForDatabase($request->only([
            'partner_age', 'partner_complexion', 'partner_height', 'partner_education', 'partner_district',
            'partner_marital_status', 'partner_profession', 'partner_financial_condition', 'partner_expectations'
        ]));
        Biodata::findOrFail($id)->update($this->filterFillable($data));
        return back()->with('success', 'Expected partner details updated successfully.');
    }

    public function updatePledge(Request $request, $id)
    {
        Biodata::findOrFail($id)->update($request->only(['parents_know', 'truth_testify', 'responsibility', 'privacy_consent']));
        return back()->with('success', 'Pledge information updated successfully.');
    }

    public function updateContact(Request $request, $id)
    {
        $request->validate([
            'groom_name' => 'nullable|string|max:255',
            'guardian_mobile' => 'nullable|string|max:20',
            'guardian_relationship' => 'nullable|string|max:100',
            'guardian_email' => 'nullable|email|max:255',
        ]);
        Biodata::findOrFail($id)->update($request->only(['groom_name', 'guardian_mobile', 'guardian_relationship', 'guardian_email']));
        return back()->with('success', 'Contact information updated successfully.');
    }

    public function downloadPdf($id)
    {
        $biodata = Biodata::findOrFail($id);
        $pdf = Pdf::loadView('biodata.pdf', compact('biodata'));
        session()->flash('success', 'Biodata PDF generated successfully.');
        return $pdf->download('Biodata_' . $biodata->id . '.pdf');
    }
}
