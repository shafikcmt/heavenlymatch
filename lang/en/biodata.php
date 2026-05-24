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
        1 => 'General',
        2 => 'Location',
        3 => 'Religion',
        4 => 'Education',
        5 => 'Family',
        6 => 'Lifestyle',
        7 => 'Marriage',
        8 => 'Partner',
        9 => 'Photos',
    ],

    // ── Step helper text ──────────────────────────────────────────────────────
    'step_helper' => [
        1 => 'Tell us about yourself — age, marital status, and personal details.',
        2 => 'Where are you from, and where do you currently live?',
        3 => 'Share your religious beliefs and daily practice.',
        4 => 'Your education background and current occupation.',
        5 => 'Help us understand your family background.',
        6 => 'Your lifestyle, health, and daily habits.',
        7 => 'Marriage expectations and guardian information.',
        8 => 'What qualities are you looking for in a life partner?',
        9 => 'A photo increases your match rate by 3× — add one now.',
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

    // ── Education ─────────────────────────────────────────────────────────────
    'education_method'       => 'Education System',
    'highest_qualification'  => 'Highest Qualification',
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

    // ── Profession ────────────────────────────────────────────────────────────
    'occupation'              => 'Occupation',
    'occupation_category'     => 'Occupation Category',
    'profession_details'      => 'Details About Profession',
    'monthly_income'          => 'Monthly Income (BDT)',
    'profession_halal_status' => 'Is Profession Halal?',

    // ── Lifestyle ─────────────────────────────────────────────────────────────
    'diet'               => 'Diet',
    'smoking'            => 'Smoking',
    'watch_entertainment'=> 'Entertainment Preferences',
    'hobbies'            => 'Hobbies & Interests',
    'health_status'      => 'Health Status',
    'health_details'     => 'Health Details',
    'special_category'   => 'Special Category',

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

    // ── Wizard step 9 content ─────────────────────────────────────────────────
    'step9_title'       => 'Upload Your Profile Photo',
    'step9_desc'        => 'Profiles with a photo get 3× more match requests. Your privacy settings control who can see it.',
    'step9_note'        => 'You can upload photos from your Profile page after completing this step.',
    'step9_submit_hint' => 'Click "Complete & Submit" below to finish — then visit your profile to upload photos.',

];
