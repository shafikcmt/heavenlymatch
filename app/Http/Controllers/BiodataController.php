<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\Biodata;

class BiodataController extends Controller
{
   public function create(Request $request, $step = 1)
    {
        // Load biodata from session if exists
        $biodata = $request->session()->get('biodata', []);

        $maxStep = 10; // <-- declare total steps here

        return view('pages.user-dashboard.create_biodatas', [
            'step' => $step,
            'biodata' => $biodata,
            'maxStep' => $maxStep, // <-- pass it to Blade
        ]);
    }

    public function store(Request $request, $step)
    {
        $maxStep = 10;
        $rules = [];

        switch ($step) {
            case 1: // General Info
                $rules = [
                    'marital_status' => 'required|string',
                    'birth_date'     => 'required|date',
                    'height'         => 'required|string|max:10',
                    'weight'         => 'required|string|max:10',
                    'complexion'     => 'required|string',
                    'blood_group'    => 'required|string',
                    'nationality'    => 'nullable|string|max:50',
                ];
                break;

            case 2: // Address
                $rules = [
                    'permanent_address' => 'required|string|max:255',
                    'village_area'      => 'nullable|string|max:255',
                    'present_address'   => 'required|string|max:255',
                    'grew_up'           => 'required|string|max:100',
                ];
                break;

            case 3: // Education
                $rules = [
        'education_method'       => 'required|string|in:General,Islamic,Both',
        'education_type'   => 'required|array',
        'education_type.*' => 'required|string|max:255',

        'ssc_year'               => 'nullable|array',
        'ssc_year.*'             => 'nullable|digits:4',
        'ssc_group'              => 'nullable|array',
        'ssc_group.*'            => 'nullable|string|max:100',

        'diploma_subject'        => 'nullable|array',
        'diploma_subject.*'      => 'nullable|string|max:255',
        'diploma_medium'         => 'nullable|array',
        'diploma_medium.*'       => 'nullable|string|max:100',
        'diploma_institution'    => 'nullable|array',
        'diploma_institution.*'  => 'nullable|string|max:255',
        'diploma_year'           => 'nullable|array',
        'diploma_year.*'         => 'nullable|digits:4',

        'graduation_subject'     => 'nullable|array',
        'graduation_subject.*'   => 'nullable|string|max:255',
        'graduation_institution' => 'nullable|array',
        'graduation_institution.*' => 'nullable|string|max:255',
        'graduation_year'        => 'nullable|array',
        'graduation_year.*'      => 'nullable|digits:4',

        'postgraduation_subject' => 'nullable|array',
        'postgraduation_subject.*' => 'nullable|string|max:255',
        'postgraduation_institution' => 'nullable|array',
        'postgraduation_institution.*' => 'nullable|string|max:255',
        'postgraduation_year'    => 'nullable|array',
        'postgraduation_year.*'  => 'nullable|digits:4',

        'islamic_institution'    => 'nullable|array',
        'islamic_institution.*'  => 'nullable|string|max:255',
        'islamic_year'           => 'nullable|array',
        'islamic_year.*'         => 'nullable|digits:4',

        'other_education'        => 'nullable|string|max:500',
    ];
                break;

            case 4: // Family
                $rules = [
                    'father_name'      => 'required|string|max:255',
                    'father_alive'     => 'required|boolean',
                    'father_profession'=> 'nullable|string|max:500',
                    'mother_name'      => 'required|string|max:255',
                    'mother_alive'     => 'required|boolean',
                    'mother_profession'=> 'nullable|string|max:500',
                    'brothers'         => 'required|integer|min:0',
                    'sisters'          => 'required|integer|min:0',
                    'uncle_profession' => 'nullable|string|max:500',
                    'family_financial_status' => 'required|string',
                    'family_details'   => 'nullable|string|max:1000',
                    'family_religious_condition' => 'nullable|string|max:1000',
                ];
                break;

            case 5: // Personal Info
                $rules = [
                    'clothing_style'     => 'required|string',
                    'beard_info'         => 'required|string',
                    'clothes_above_ankles' => 'required|boolean',
                    'prayers_info'       => 'nullable|string',
                    'mahram_nonmahram'   => 'required|boolean',
                    'quran_recitation' => 'required|string|in:Yes,No',
                    'fiqh'               => 'required|string|in:Hanafi,Shafi,Maliki,Hanbali',
                    'watch_entertainment'=> 'required|boolean',
                    'diseases' => 'required|string|in:Yes,No',
                    'beliefs_on_mazar'   => 'nullable|string',
                    'books_read'         => 'nullable|string',
                    'special_category'   => 'nullable|array',
                    'special_category.*' => 'string|in:Disabled,Infertile,Converted Muslim,Orphan,Interested in becoming a second wife,Tablig',
                    'hobbies'            => 'nullable|string',
                    'groom_mobile' => ['required', 'string', 'regex:/^01[3-9]\d{8}$/', 'unique:biodatas,groom_mobile'],
                    'groom_photo'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                ];
                break;

            case 6: // Occupation
                $rules = [
                    'occupation'           => 'required|string|max:255',
                    'profession_details'   => 'required|string',
                    'monthly_income'       => 'required|numeric|min:0',
                ];
                break;

            case 7: // Marriage Info
                $rules = [
                    'guardian_agree' => 'required|string|in:Yes,No',
                    'wife_in_veil' => 'required|string|in:Yes,No,InshaAllah',
                    'wife_study_allowed' => 'required|string|in:Yes,No',
                    'wife_job_allowed'   => 'required|string|in:Yes,No',
                     'residence_after_marriage' => 'required|string|in:Own House,Wife’s House,Rented House,Other',
                    'expect_gift_from_bride'  => 'required|string|in:Yes,No',
                ];
                break;

           case 8: // Expected Partner
                $rules = [
                    'partner_age' => ['required', 'string', 'regex:/^(1[8-9]|2[0-9]|3[0-9]|4[0-9]|5[0-5])\-(2[3-9]|[3-5][0-9]|60)$/'],
                    'partner_complexion'   => 'required|array|min:1', // must select at least one
                    'partner_complexion.*' => 'string|in:Dark,Brown,Bright Brown,Fair,Bright Fair',
                    'partner_height' => ['required', 'string', 'regex:/^(4\.5|4\.6|4\.7|4\.8|4\.9|5\.0|5\.1|5\.2|5\.3|5\.4|5\.5|5\.6|5\.7|5\.8|5\.9|6\.0|6\.1|6\.2|6\.3|6\.4|6\.5)\-(4\.5|4\.6|4\.7|4\.8|4\.9|5\.0|5\.1|5\.2|5\.3|5\.4|5\.5|5\.6|5\.7|5\.8|5\.9|6\.0|6\.1|6\.2|6\.3|6\.4|6\.5)$/'],
                    'partner_education' => 'required|string|in:SSC,HSC,Diploma,Graduation,Post Graduation,Hafez,Others',
                    'partner_district'            => 'required|string|max:255',
                    'partner_marital_status'   => 'required|array|min:1',
                    'partner_marital_status.*' => 'string|in:Never Married,Divorced,Widow',
                    'partner_profession' => 'required|string|in:Engineer,Doctor,Teacher,Business,Government Employee,Private Job,Farmer,Others',
                    'partner_financial_condition' => 'required|string|in:Poor,Average,Good,Very Good,Wealthy',
                    'partner_expectations'        => 'required|string|max:2000',
                ];
                break;

                $rules = [
                'parents_know' => 'required|string',
                'truth_testify' => 'required|string',
                'responsibility' => 'required|string',
                ];
                break;

            case 10: // Contact
                $rules = [
                    'groom_name'           => 'required|string|max:255',
                    'guardian_mobile'      => 'required|string|max:20',
                    'guardian_relationship'=> 'required|string|max:50',
                    'guardian_email'       => 'required|email|max:255',
                ];
                break;

            default:
                $rules = [];
                break;
        }

        $validated = $request->validate($rules);

        // Step 5 extra handling for photo upload
        if ($step == 5) {
            if ($request->hasFile('groom_photo')) {
                $path = $request->file('groom_photo')->store('groom_photos', 'public');
                $validated['groom_photo'] = $path; // store path
            } else {
                $oldData = $request->session()->get("biodata.step_5", []);
                if (isset($oldData['groom_photo'])) {
                    $validated['groom_photo'] = $oldData['groom_photo'];
                }
            }
        } 

        if (!empty($validated['education_type'])) {
            $validated['highest_qualification'] = implode(',', $validated['education_type']);
        }

        // Save validated data (not raw request) in session
        $biodata = $request->session()->get('biodata', []);
        $biodata["step_$step"] = $validated; // <-- use $validated instead of $request->except(...)
        $request->session()->put('biodata', $biodata);



        // Final Step Save to DB
        if ($step == $maxStep && !$request->has('next')) {
    // Merge all step data into one array
    $allData = array_merge(...array_values($biodata));

    // ✅ Convert array fields to comma-separated string
    foreach ($allData as $key => $value) {
        if (is_array($value)) {
            $allData[$key] = implode(',', $value);
        }
    }

    // Create a new Biodata record
    $biodataRecord = Biodata::create($allData);

    // Clear biodata session
    $request->session()->forget('biodata');

    return redirect()
        ->route('biodata.create')
        ->with('success', 'Biodata saved successfully!');
}


        // Navigate Steps
        if ($request->input('next')) {
            return redirect()->route('biodata.create', $step + 1);
        }
        if ($request->input('back')) {
            return redirect()->route('biodata.create', $step - 1);
        }

        return view('pages.user-dashboard.create_biodatas', [
            'step' => $step,
            'biodata' => $biodata,
            'maxStep' => $maxStep, // <-- pass here too
        ]);
    }
}