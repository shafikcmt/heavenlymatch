<?php

declare(strict_types=1);

/**
 * Biodata wizard field labels, section headers, hints, and options.
 */
return [

    // ── Wizard navigation ─────────────────────────────────────────────────────
    'wizard_title'      => 'Complete Your Biodata',
    'wizard_subtitle'   => 'Step :step of :total',
    'wizard_save_draft' => 'Save Draft',
    'wizard_complete'   => 'Complete & Submit',
    'wizard_next'       => 'Save & Continue',
    'wizard_progress'   => ':percent% Complete',
    'step_labels' => [
        1  => 'Basic Information',
        2  => 'Location',
        3  => 'Religious Practice',
        4  => 'Education & Career',
        5  => 'Physical & Lifestyle',
        6  => 'Family Background',
        7  => 'Marriage Preferences',
        8  => 'Partner Preferences',
        9  => 'Contact & Privacy',
        10 => 'Profile Photo & Review',
    ],

    // ── Step helper text ──────────────────────────────────────────────────────
    'step_helper' => [
        1  => 'Tell us about yourself — marital status, date of birth and a short intro.',
        2  => 'Where are you from, and where do you currently live?',
        3  => 'Share your religious beliefs and daily practice.',
        4  => 'Your education background and current occupation.',
        5  => 'Your physical details, health and lifestyle habits.',
        6  => 'Help us understand your family background.',
        7  => 'Marriage expectations and after-marriage plans.',
        8  => 'What qualities are you looking for in a life partner?',
        9  => 'Guardian contact, WhatsApp and who can see your details.',
        10 => 'Add a profile photo and confirm your information.',
    ],

    // ── Completion section labels (for dashboard checklist) ───────────────────
    'section_label' => [
        'general'   => 'General Information',
        'location'  => 'Location',
        'religion'  => 'Religion & Practice',
        'education' => 'Education & Career',
        'family'    => 'Family Background',
        'lifestyle' => 'Lifestyle & Health',
        'marriage'  => 'Marriage Preferences',
        'partner'   => 'Partner Preferences',
        'contact'   => 'Contact & Privacy',
        'review'    => 'Photo & Review',
        'photos'    => 'Profile Photo',
    ],

    'completion_cta'     => 'Continue completing your profile',
    'completion_benefit' => 'Profiles above 80% get 3× more matches',

    // ── Section headers ───────────────────────────────────────────────────────
    'section_general'    => 'General Information',
    'section_location'   => 'Location',
    'section_religion'   => 'Religion & Practice',
    'section_education'  => 'Education',
    'section_family'     => 'Family Background',
    'section_profession' => 'Profession & Income',
    'section_lifestyle'  => 'Lifestyle',
    'section_marriage'   => 'Marriage Preferences',
    'section_partner'    => 'Preferred Partner',
    'section_photos'     => 'Photos',
    'section_contact'    => 'Contact & Guardian',

    // ── General fields ────────────────────────────────────────────────────────
    'marital_status'       => 'Marital Status',
    'birth_date'           => 'Date of Birth',
    'age'                  => 'Age',
    'height'               => 'Height',
    'weight'               => 'Weight',
    'complexion'           => 'Complexion',
    'blood_group'          => 'Blood Group',
    'about_me'             => 'About Me',
    'profile_headline'     => 'Profile Headline',
    'profile_created_for'  => 'Profile Created For',

    // Marital status options
    'never_married' => 'Never Married',
    'divorced'      => 'Divorced',
    'widowed'       => 'Widowed',

    // Complexion options
    'very_fair'  => 'Very Fair',
    'fair'       => 'Fair',
    'wheatish'   => 'Wheatish',
    'medium'     => 'Medium',
    'dark'       => 'Dark',

    // Profile created for options
    'for_self'    => 'Myself',
    'for_son'     => 'Son',
    'for_daughter'=> 'Daughter',
    'for_brother' => 'Brother',
    'for_sister'  => 'Sister',
    'for_relative'=> 'Relative',

    // ── Location fields ───────────────────────────────────────────────────────
    'nationality'       => 'Nationality',
    'division'          => 'Division',
    'district'          => 'District',
    'upazila'           => 'Upazila / Thana',
    'present_address'   => 'Present Address',
    'permanent_address' => 'Permanent Address',
    'village_area'      => 'Village / Area',
    'grew_up_in'        => 'Grew Up In',
    'residing_country'  => 'Currently Residing In',
    'residing_city'     => 'City',
    'is_nrb'            => 'Non-Resident Bangladeshi (NRB)',
    'mother_tongue'     => 'Mother Tongue',
    'visa_status'       => 'Visa / Residence Status',

    // Visa status options
    'visa_citizen'            => 'Citizen',
    'visa_permanent_resident' => 'Permanent Resident',
    'visa_work_visa'          => 'Work Visa',
    'visa_student_visa'       => 'Student Visa',

    // ── Religion & practice ───────────────────────────────────────────────────
    'religion'                => 'Religion',
    'sect'                    => 'Sect / Madhhab',
    'is_practicing'           => 'Practicing Muslim',
    'prayers_info'            => 'Prayer Habits',
    'quran_recitation'        => 'Quran Recitation',
    'clothing_style'          => 'Clothing Style',
    'beard_info'              => 'Beard',
    'hijab_info'              => 'Hijab / Niqab',
    'is_islamically_educated' => 'Islamic Education',
    'wali_approval'           => 'Guardian (Wali) Involved',
    'beliefs_on_mazar'        => 'View on Mazar / Shrine',
    'favorite_scholars'       => 'Favourite Islamic Scholars',
    'religious_work'          => 'Engaged in Islamic Work',
    'sunni_scale'             => 'Islamic Practice Level (1–10)',
    'fiqh'                    => 'Fiqh / School of Thought',

    // Prayer options
    'prayers_5_times'   => '5 Times Daily',
    'prayers_4_times'   => '4 Times Daily',
    'prayers_sometimes' => 'Sometimes',
    'prayers_rarely'    => 'Rarely',
    'prayers_never'     => 'Never',

    // Quran options
    'quran_fluent'   => 'Fluent',
    'quran_basic'    => 'Basic',
    'quran_learning' => 'Currently Learning',
    'quran_no'       => 'Cannot Recite',

    // Hijab options
    'hijab_wears_niqab' => 'Wears Niqab',
    'hijab_wears_hijab' => 'Wears Hijab',
    'hijab_trying'      => 'Trying to Wear',
    'hijab_no_hijab'    => 'Does Not Wear',

    // ── Education ─────────────────────────────────────────────────────────────
    'education_method'       => 'Education System',
    'highest_qualification'  => 'Highest Qualification',

    // Education method options
    'edu_method_general' => 'General',
    'edu_method_islamic' => 'Islamic (Madrasa)',
    'edu_method_both'    => 'Both General & Islamic',
    'education_details'      => 'Education Details',
    'institution'            => 'Institution / University',
    'passing_year'           => 'Passing Year',

    // Qualification options
    'qual_below_ssc'      => 'Below SSC',
    'qual_ssc'            => 'SSC / O-Level',
    'qual_hsc'            => 'HSC / A-Level',
    'qual_diploma'        => 'Diploma',
    'qual_graduation'     => 'Graduation (Bachelor\'s)',
    'qual_post_graduation'=> 'Post-Graduation (Master\'s)',
    'qual_phd'            => 'PhD / Doctorate',
    'qual_hafez'          => 'Hafez-e-Quran',
    'qual_alim'           => 'Alim',
    'qual_fazil'          => 'Fazil',
    'qual_kamil'          => 'Kamil',

    // ── Family ────────────────────────────────────────────────────────────────
    'father_name'           => 'Father\'s Name',
    'father_alive'          => 'Father is Alive',
    'father_profession'     => 'Father\'s Profession',
    'mother_name'           => 'Mother\'s Name',
    'mother_alive'          => 'Mother is Alive',
    'mother_profession'     => 'Mother\'s Profession',
    'brothers'              => 'Number of Brothers',
    'sisters'               => 'Number of Sisters',
    'family_type'           => 'Family Type',
    'family_financial_status' => 'Family Financial Status',
    'home_ownership'        => 'Home Ownership',
    'family_details'        => 'About the Family',
    'family_religious_condition' => 'Family Religious Environment',

    // Family type options
    'family_joint'    => 'Joint Family',
    'family_nuclear'  => 'Nuclear Family',
    'family_flexible' => 'Flexible',

    // Financial status
    'finance_lower'        => 'Lower Class',
    'finance_lower_middle' => 'Lower Middle Class',
    'finance_middle'       => 'Middle Class',
    'finance_upper_middle' => 'Upper Middle Class',
    'finance_upper'        => 'Upper Class',

    // Home ownership options
    'home_own_house'    => 'Own House',
    'home_family_house' => 'Family House',
    'home_rented'       => 'Rented',

    // ── Profession ────────────────────────────────────────────────────────────
    'occupation'              => 'Occupation',
    'occupation_category'     => 'Occupation Category',
    'profession_details'      => 'Details About Profession',
    'monthly_income'          => 'Monthly Income (BDT)',
    'profession_halal_status' => 'Is Profession Halal?',

    // Occupation category options
    'occ_business'        => 'Business / Entrepreneur',
    'occ_service_govt'    => 'Government Job',
    'occ_service_private' => 'Private Job',
    'occ_education'       => 'Education / Teacher',
    'occ_medical'         => 'Medical / Healthcare',
    'occ_engineering'     => 'Engineering',
    'occ_it'              => 'IT / Tech',
    'occ_abroad_job'      => 'Working Abroad',
    'occ_student'         => 'Student',
    'occ_housewife'       => 'Housewife',
    'occ_agriculture'     => 'Agriculture',
    'occ_other'           => 'Other',

    // ── Lifestyle ─────────────────────────────────────────────────────────────
    'diet'               => 'Diet',
    'smoking'            => 'Smoking',
    'watch_entertainment'=> 'Entertainment Preferences',
    'hobbies'            => 'Hobbies & Interests',
    'health_status'      => 'Health Status',
    'health_details'     => 'Health Details',
    'special_category'   => 'Special Category',

    // Health status options
    'health_healthy'         => 'Healthy',
    'health_minor_condition' => 'Minor Condition',
    'health_disability'      => 'Disability',
    'health_prefer_not_say'  => 'Prefer Not to Say',

    // Diet options
    'diet_halal_only'     => 'Halal Only',
    'diet_vegetarian'     => 'Vegetarian',
    'diet_no_restriction' => 'No Restriction',

    // Smoking options
    'smoking_never'        => 'Never',
    'smoking_occasionally' => 'Occasionally',
    'smoking_regularly'    => 'Regularly',

    // ── Marriage ─────────────────────────────────────────────────────────────
    'guardian_agree'          => 'Guardian is Aware & Agreed',
    'wife_in_veil'            => 'Expects Wife to Observe Pardah',
    'wife_study_allowed'      => 'Wife Can Continue Studies',
    'wife_job_allowed'        => 'Wife Can Work',
    'residence_after_marriage'=> 'Residence After Marriage',
    'expect_gift_from_bride'  => 'Expects Gift from Bride (Mehr)',
    'post_marriage_plan'      => 'Post-Marriage Plan',
    'polygamy_open'           => 'Open to Polygamy',
    'children_count'          => 'Number of Children',

    // ── Partner preferences ───────────────────────────────────────────────────
    'partner_age_range'        => 'Preferred Age Range',
    'partner_age_min'          => 'Minimum Age',
    'partner_age_max'          => 'Maximum Age',
    'partner_height_range'     => 'Preferred Height Range',
    'partner_complexion'       => 'Preferred Complexion',
    'partner_marital_status'   => 'Preferred Marital Status',
    'partner_education'        => 'Preferred Education',
    'partner_occupation_pref'  => 'Preferred Occupation',
    'partner_income_min'       => 'Minimum Income Preferred',
    'partner_religion'         => 'Religion of Partner',
    'partner_sect'             => 'Sect Preference',
    'partner_division'         => 'Preferred Division',
    'partner_district'         => 'Preferred District',
    'partner_family_type'      => 'Preferred Family Type',
    'partner_expectations'     => 'Special Expectations from Partner',

    // ── Contact / Guardian ────────────────────────────────────────────────────
    'guardian_mobile'       => 'Guardian Mobile Number',
    'guardian_relationship' => 'Relationship with Guardian',
    'guardian_email'        => 'Guardian Email (Optional)',

    // ── Photos ────────────────────────────────────────────────────────────────
    'photos_title'              => 'Profile Photos',
    'photos_hint'               => 'Upload a clear, recent photo of yourself. Maximum :max photos.',
    'photo_upload_btn'          => 'Upload Photo',
    'photo_visibility'          => 'Photo Visibility',
    'photo_public'              => 'Visible to All',
    'photo_members'             => 'Members Only',
    'photo_blurred'             => 'Blurred (Request Required)',
    'photo_request'             => 'Request Photo Access',
    'photo_requested'           => 'Request Sent',
    'photo_grant'               => 'Allow Photo Access',
    'photo_deny'                => 'Deny Photo Access',
    'photo_set_primary'         => 'Set as Primary',
    'photo_primary_badge'       => 'Primary',
    'photo_delete'              => 'Delete Photo',
    'photo_delete_confirm'      => 'Delete this photo?',
    'photo_count'               => ':count / :max photos',
    'photo_limit_reached'       => 'You have reached the maximum of :max photos.',
    'photo_no_biodata'          => 'Please complete your biodata before uploading photos.',
    'photo_uploaded_success'    => 'Photo uploaded successfully.',
    'photo_deleted_success'     => 'Photo deleted.',
    'photo_primary_set_success' => 'Primary photo updated.',
    'photo_visibility_updated'  => 'Photo visibility updated.',
    'photo_no_photos'           => 'No photos uploaded yet.',
    'photo_visibility_hint'     => 'Controls who can see your photos on your profile.',
    'photo_vis_public_desc'     => 'Anyone — including guests — can see your photos',
    'photo_vis_members_desc'    => 'Only registered and logged-in members can see clearly',
    'photo_vis_blurred_desc'    => 'Photos are blurred for everyone until you approve their request',
    'photo_privacy_islamic_notice' => 'Islamic mode recommends "Blurred" visibility to preserve modesty (purdah).',
    'photo_file_hint'           => 'JPG, PNG, WebP · max 4 MB · min 200×200 px',
    'photo_uploading'           => 'Uploading…',
    'photo_too_large'           => 'File is too large. Maximum allowed size is 4 MB.',
    'photo_preview_label'       => 'Preview',
    'photo_confirm_upload'      => 'Upload This Photo',
    'photo_cancel_preview'      => 'Cancel',
    'photo_not_found'           => 'Photo not found.',
    'photo_access_granted'      => 'Photo access approved.',
    'photo_access_denied'       => 'Photo access request declined.',

    // ── Completion score ─────────────────────────────────────────────────────
    'completion_score'     => 'Profile :percent% Complete',
    'completion_incomplete'=> 'Complete your biodata to find better matches',
    'completion_good'      => 'Good profile! Add more details to improve matches',
    'completion_excellent' => 'Excellent profile!',

    // ── Status messages ───────────────────────────────────────────────────────
    'draft_saved'       => 'Draft saved successfully.',
    'submitted'         => 'Biodata submitted for review.',
    'approved'          => 'Your biodata has been approved.',
    'rejected'          => 'Your biodata needs corrections.',
    'pending_review'    => 'Under Review',

    // ── Wizard misc ───────────────────────────────────────────────────────────
    'profile_completion'      => 'Profile Completion',
    'wizard_review_notice'    => 'Your biodata will be reviewed before going live. Usually takes 24 hours.',
    'about_me_tip'            => 'Tip: profiles with 100+ characters in About Me get a 10% completeness bonus.',
    'after_marriage_section'  => 'After Marriage',
    'guardian_contact_section'=> 'Guardian Contact',
    'practicing_scale_min'    => '1 – Minimal',
    'practicing_scale_max'    => '10 – Devout',
    'married'                 => 'Married',
    'partner_height_cm_min'   => 'Min Height (cm)',
    'partner_height_cm_max'   => 'Max Height (cm)',
    'partner_income_max'      => 'Maximum Income Preferred',
    'partner_income_range'    => 'Preferred Income Range',
    'any'                     => 'Any',
    'no_preference'           => 'No Preference',
    'any_division'            => 'Any Division',

    // ── Wizard step 9 content ─────────────────────────────────────────────────
    'step9_title'          => 'Upload Your Profile Photo',
    'step9_desc'           => 'Profiles with a photo get 3× more match requests. Your privacy settings control who can see it.',
    'step9_optional_badge' => 'Optional — but strongly recommended',
    'step9_note'           => 'Photo is optional. You can also manage all photos from your Profile page anytime.',
    'step9_submit_hint'    => 'Click "Complete & Submit" below to finalize your biodata.',

    // ── Step 5: Physical & Lifestyle ──────────────────────────────────────────
    'section_physical'        => 'Physical Attributes',

    // ── Step 9: Contact & Privacy ─────────────────────────────────────────────
    'contact_intro'           => 'Add contact details for your guardian. These stay private and follow your privacy preference below.',
    'whatsapp_number'         => 'WhatsApp Number',
    'whatsapp_hint'           => 'Bangladesh format: 01XXXXXXXXX or +8801XXXXXXXXX. Never shown publicly.',
    'whatsapp_invalid'        => 'Please enter a valid Bangladeshi WhatsApp number.',
    'contact_privacy_section' => 'Contact Privacy',
    'contact_privacy'         => 'Who can see your contact details',
    'contact_privacy_private' => 'Private — only admin can see',
    'contact_privacy_request' => 'Request only — shown after you approve a request',
    'contact_privacy_matches' => 'Approved matches only',
    'contact_privacy_note'    => 'Your WhatsApp and guardian contact are never shown on your public profile.',

    // ── Step 8 / Step 10 misc ─────────────────────────────────────────────────
    'partner_location_required' => 'Preferred Location (Bangladesh)',
    'confirm_correct'           => 'I confirm that the information provided is correct and accurate.',
    'complete_required_first'   => 'Please complete all required fields before submitting your biodata.',

    // ── Phase C: extended fields ──────────────────────────────────────────────
    // Step 1 — marital sub-status
    'marital_substatus'      => 'Detailed Status',
    'sub_divorced'           => 'Divorced',
    'sub_separated'          => 'Separated',
    'sub_widow'              => 'Widow',
    'sub_widower'            => 'Widower',
    'sub_second_marriage'    => 'Seeking second marriage',

    // Step 2 — address
    'permanent_area'          => 'Permanent Area / Village',
    'permanent_area_ph'       => 'e.g. Banani, Village name',
    'grew_up_in'              => 'Grew Up In',
    'grew_up_in_ph'           => 'e.g. Dhaka City, Village, Abroad',
    'current_address_section' => 'Current Address',
    'same_as_permanent'       => 'Current address is same as permanent',
    'current_area'            => 'Current Area',
    'current_area_ph'         => 'e.g. Uttara, Mirpur',

    // Step 3 — deen detail
    'deen_detail_section'        => 'Religious Practice Detail',
    'prayer_start_age'           => 'Started Praying (age/year)',
    'prayer_start_age_ph'        => 'e.g. Since age 15 / 2015',
    'weekly_missed_prayers'      => 'Weekly Missed Prayers',
    'weekly_missed_prayers_ph'   => 'e.g. None, 1-2',
    'mahram_practice'            => 'Mahram / Non-Mahram Practice',
    'mahram_practice_ph'         => 'Briefly describe your observance',
    'islamic_books_read'         => 'Islamic Books You Read',
    'islamic_books_read_ph'      => 'e.g. Riyad us-Saliheen',
    'deen_work_details'          => 'Involvement in Deen Work',
    'deen_work_details_ph'       => 'e.g. Dawah, teaching, none',
    'social_media_usage'         => 'Social Media Usage',
    'social_media_usage_ph'      => 'e.g. Limited, for work only',
    'beard_since'                => 'Beard Since',
    'niqab_since'                => 'Niqab Since',
    'since_ph'                   => 'e.g. 3 years / 2020',
    'pants_above_ankle'          => 'Wears trousers above the ankle',
    'purdah_details'             => 'Purdah Practice',
    'purdah_details_ph'          => 'Briefly describe your purdah observance',

    // Step 4 — education medium + professional
    'education_medium'        => 'Education Medium',
    'edu_medium_general'      => 'General / National',
    'edu_medium_qawmi'        => 'Qawmi Madrasa',
    'edu_medium_alia'         => 'Alia Madrasa',
    'edu_medium_english'      => 'English Medium',
    'edu_medium_vocational'   => 'Vocational / Technical',
    'edu_medium_other'        => 'Other',
    'income_type'             => 'Income Type',
    'income_type_monthly'     => 'Monthly',
    'income_type_yearly'      => 'Yearly',
    'income_type_variable'    => 'Variable',
    'income_type_private'     => 'Prefer not to disclose',
    'income_privacy'          => 'Income Visibility',
    'income_privacy_public'   => 'Public',
    'income_privacy_members'  => 'Members only',
    'income_privacy_private'  => 'Private',
    'profession_halal_status'  => 'Is your income halal?',
    'halal_status_halal'       => 'Yes, halal',
    'halal_status_alternative' => 'Mostly halal / moving towards it',
    'halal_status_not_sure'    => 'Not sure',
    'workplace_type'           => 'Workplace Type',
    'workplace_type_ph'        => 'e.g. Office, Home-based, Remote',
    'future_career_plan'       => 'Future Career Plan',
    'future_career_plan_ph'    => 'Your career goals after marriage...',

    // Step 6 — family extras
    'uncle_profession'         => "Uncle's / Maternal Uncle's Profession",
    'uncle_profession_ph'      => 'e.g. Doctor, Businessman',
    'family_assets_details'    => 'Family Assets',
    'family_assets_details_ph' => 'Briefly describe land, home, business (optional)',

    // Step 7 — marriage thoughts + conditionals
    'marriage_thoughts_section'  => 'About This Marriage',
    'why_getting_married'        => 'Why are you getting married?',
    'why_getting_married_ph'     => 'Share your intention for marriage...',
    'marriage_thoughts'          => 'Your Thoughts on Marriage',
    'marriage_thoughts_ph'       => 'What does marriage mean to you?',
    'marriage_timeline'          => 'Expected Marriage Timeline',
    'marriage_timeline_ph'       => 'e.g. Within 6 months, This year',
    'expect_gift_from_bride'     => "Expectation of gift from bride's family",
    'expect_gift_ph'             => 'e.g. No expectation, As per sunnah',
    'gift_expectation_details'   => 'Gift Expectation Details',
    'gift_expectation_details_ph' => 'Please specify (optional)',
    'female_intentions_section'  => 'After Marriage (Your Preferences)',
    'wants_to_work'              => 'I would like to work after marriage',
    'continue_study'             => 'I would like to continue studying',
    'continue_job'               => 'I would like to continue my current job',
    'preferred_living'           => 'Preferred Living After Marriage',
    'preferred_living_ph'        => "e.g. With husband's family, Separate",
    'sensitive_note'             => 'This section is private and handled with care. Share only what you are comfortable with.',
    'divorce_section'            => 'Divorce Information',
    'previous_marriage_date'     => 'Previous Marriage Date',
    'divorce_date'               => 'Divorce Date',
    'divorce_reason'             => 'Reason for Divorce',
    'divorce_reason_ph'          => 'Brief reason (optional)',
    'widowed_section'            => 'Widowhood Information',
    'spouse_death_date'          => "Spouse's Date of Passing",
    'spouse_death_reason'        => 'Cause / Details',
    'spouse_death_reason_ph'     => 'Brief details (optional)',
    'child_acceptance_expectation' => 'Expectation about Child Acceptance',
    'child_acceptance_expectation_ph' => 'e.g. Partner should accept my children',
    'second_marriage_section'    => 'Marriage / Second Marriage Information',
    'reason_for_second_marriage' => 'Reason for Second Marriage',
    'reason_for_second_marriage_ph' => 'Please explain (optional)',
    'current_wife_count'         => 'Current Number of Wives',
    'second_marriage_living'     => 'Living Arrangement After Marriage',
    'second_marriage_living_ph'  => 'e.g. Separate home for each',
    'current_family_consent'     => 'Current family consents to this marriage',
    'first_wife_knows'           => 'First wife knows and agrees',

    // Step 8 — partner extras
    'partner_economic_status'    => 'Preferred Economic Status',
    'partner_deen_practice'      => 'Preferred Religious Practice',
    'partner_deen_practice_ph'   => 'e.g. Practicing, Prays 5 times',
    'partner_districts'          => 'Additional Preferred Districts',
    'partner_districts_ph'       => 'Search and add districts...',
    'partner_special_qualities'  => 'Special Qualities You Want',
    'partner_special_qualities_ph' => 'Qualities most important to you...',
    'partner_deal_breakers'      => 'Deal-breakers',
    'partner_deal_breakers_ph'   => 'Things you cannot accept (optional)',

    // Step 9 — contact extras
    'contact_person_name'        => 'Contact Person Name',
    'contact_person_name_ph'     => 'Who should be contacted',
    'guardian_name'              => 'Guardian Name',
    'guardian_name_ph'           => "Guardian's full name",
    'guardian_whatsapp'          => 'Guardian WhatsApp',
    'biodata_visibility'         => 'Biodata Visibility',
    'visibility_public'          => 'Public',
    'visibility_approved_only'   => 'Approved members only',
    'visibility_private'         => 'Private',
    'allow_shortlist'            => 'Allow others to shortlist my biodata',
    'allow_contact_request'      => 'Allow others to send contact requests',

    // Step 10 — declaration
    'declaration_section'        => 'Declaration',
    'declare_guardian_knows'     => 'My guardian knows about this biodata.',
    'declare_info_truthful'      => 'All information provided is true to the best of my knowledge.',
    'declare_accept_terms'       => 'I accept responsibility for the accuracy of this information and the platform terms.',

    // Admin-defined custom fields (Phase E3)
    'custom_fields_heading'      => 'Additional Information',
    'additional_info'            => 'Additional Information',

];
