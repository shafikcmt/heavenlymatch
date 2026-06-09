<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BiodataField;
use App\Models\BiodataSection;
use Illuminate\Database\Seeder;

/**
 * PHASE E1 — Seeds the default biodata field registry.
 *
 * IDEMPOTENT + NON-DESTRUCTIVE:
 *   - Sections/fields are created with firstOrCreate (keyed by key/field_key),
 *     so re-running never duplicates rows and never overwrites admin edits to
 *     labels/order/visibility on rows that already exist.
 *   - Mirrors the existing 10-step wizard (ProfileCompletionService::SECTION_STEP)
 *     and the real `biodatas` columns from BiodataWizardController.
 *   - Every seeded field is a "system" field mapped to an existing column
 *     (model_column set) → cannot be hard-deleted by admins, only deactivated.
 *     Admin-created custom fields (model_column NULL) are added later at runtime.
 */
class BiodataFieldSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->sections() as $order => $section) {
            $model = BiodataSection::firstOrCreate(
                ['key' => $section['key']],
                array_merge($section['attributes'], ['sort_order' => $order + 1]),
            );

            foreach (array_values($section['fields']) as $fOrder => $field) {
                $existing = BiodataField::firstOrCreate(
                    ['field_key' => $field['field_key']],
                    array_merge($this->fieldDefaults(), $field, [
                        'section_id' => $model->id,
                        'sort_order' => $fOrder + 1,
                        'is_system'  => true,
                    ]),
                );

                // Reconcile placement: a SYSTEM field may have been seeded under a
                // different section in an earlier release (e.g. the physical fields
                // moved from Lifestyle → Basic Information). Re-attach it to the
                // section its current definition specifies so the admin overlay lines
                // up with the live wizard step. Only placement is corrected — labels,
                // required/active flags and visibility stay as the admin left them.
                if (! $existing->wasRecentlyCreated
                    && $existing->is_system
                    && $existing->section_id !== $model->id) {
                    $existing->update(['section_id' => $model->id, 'sort_order' => $fOrder + 1]);
                }
            }
        }
    }

    /** Attribute defaults so each field definition stays terse. */
    private function fieldDefaults(): array
    {
        return [
            'input_type'      => 'text',
            'is_required'     => false,
            'is_active'       => true,
            'show_in_form'    => true,
            'show_in_profile' => true,
            'show_in_admin'   => true,
            'is_private'      => false,
            'is_searchable'   => false,
            'is_filterable'   => false,
        ];
    }

    /**
     * 10 sections (keys aligned with ProfileCompletionService::SECTION_STEP)
     * each with their mapped real `biodatas` columns.
     */
    private function sections(): array
    {
        return [
            [
                'key' => 'general',
                'attributes' => [
                    'title_en' => 'General Information', 'title_bn' => 'সাধারণ তথ্য',
                    'icon' => 'user', 'step' => 1, 'completion_weight' => 10,
                ],
                'fields' => [
                    ['field_key' => 'marital_status', 'model_column' => 'marital_status', 'label_en' => 'Marital Status', 'label_bn' => 'বৈবাহিক অবস্থা', 'input_type' => 'select', 'is_required' => true, 'is_filterable' => true, 'options_en' => [
                        ['value' => 'never_married', 'label' => 'Never Married'],
                        ['value' => 'married', 'label' => 'Married'],
                        ['value' => 'divorced', 'label' => 'Divorced'],
                        ['value' => 'widowed', 'label' => 'Widowed'],
                    ]],
                    ['field_key' => 'marital_substatus', 'model_column' => 'marital_substatus', 'label_en' => 'Marital Sub-status', 'label_bn' => 'বৈবাহিক উপ-অবস্থা', 'show_in_profile' => false],
                    ['field_key' => 'birth_date', 'model_column' => 'birth_date', 'label_en' => 'Date of Birth', 'label_bn' => 'জন্ম তারিখ', 'input_type' => 'date', 'is_required' => true, 'is_private' => true, 'profile_display_format' => 'age'],
                    // Physical summary lives in Basic Information (moved from Lifestyle).
                    ['field_key' => 'height_cm', 'model_column' => 'height_cm', 'label_en' => 'Height (cm)', 'label_bn' => 'উচ্চতা (সেমি)', 'input_type' => 'number', 'is_required' => true, 'is_filterable' => true, 'profile_display_format' => 'height'],
                    ['field_key' => 'weight_kg', 'model_column' => 'weight_kg', 'label_en' => 'Weight (kg)', 'label_bn' => 'ওজন (কেজি)', 'input_type' => 'number', 'is_required' => true],
                    ['field_key' => 'complexion', 'model_column' => 'complexion', 'label_en' => 'Complexion', 'label_bn' => 'গায়ের রং', 'input_type' => 'select', 'is_required' => true, 'options_en' => [
                        ['value' => 'very_fair', 'label' => 'Very Fair'],
                        ['value' => 'fair', 'label' => 'Fair'],
                        ['value' => 'wheatish', 'label' => 'Wheatish'],
                        ['value' => 'medium', 'label' => 'Medium'],
                        ['value' => 'dark', 'label' => 'Dark'],
                    ]],
                    ['field_key' => 'blood_group', 'model_column' => 'blood_group', 'label_en' => 'Blood Group', 'label_bn' => 'রক্তের গ্রুপ', 'input_type' => 'select', 'options_en' => [
                        ['value' => 'A+', 'label' => 'A+'], ['value' => 'A-', 'label' => 'A-'],
                        ['value' => 'B+', 'label' => 'B+'], ['value' => 'B-', 'label' => 'B-'],
                        ['value' => 'AB+', 'label' => 'AB+'], ['value' => 'AB-', 'label' => 'AB-'],
                        ['value' => 'O+', 'label' => 'O+'], ['value' => 'O-', 'label' => 'O-'],
                    ]],
                    ['field_key' => 'health_status', 'model_column' => 'health_status', 'label_en' => 'Physical Status', 'label_bn' => 'শারীরিক অবস্থা', 'input_type' => 'select', 'options_en' => [
                        ['value' => 'healthy', 'label' => 'Healthy'],
                        ['value' => 'minor_condition', 'label' => 'Minor Condition'],
                        ['value' => 'disability', 'label' => 'Disability'],
                        ['value' => 'prefer_not_say', 'label' => 'Prefer not to say'],
                    ]],
                    ['field_key' => 'health_details', 'model_column' => 'health_details', 'label_en' => 'Health / Disability Details', 'label_bn' => 'স্বাস্থ্য / প্রতিবন্ধকতার বিবরণ', 'input_type' => 'textarea'],
                    // Deactivated by default — removed from the Basic Information form
                    // (columns retained; admin can re-enable).
                    ['field_key' => 'mother_tongue', 'model_column' => 'mother_tongue', 'label_en' => 'Mother Tongue', 'label_bn' => 'মাতৃভাষা', 'is_active' => false, 'show_in_form' => false, 'show_in_profile' => false],
                    ['field_key' => 'profile_headline', 'model_column' => 'profile_headline', 'label_en' => 'Profile Headline', 'label_bn' => 'প্রোফাইল শিরোনাম', 'is_active' => false, 'show_in_form' => false, 'show_in_profile' => false],
                    ['field_key' => 'about_me', 'model_column' => 'about_me', 'label_en' => 'About Me', 'label_bn' => 'আমার সম্পর্কে', 'input_type' => 'textarea', 'is_active' => false, 'show_in_form' => false, 'show_in_profile' => false],
                ],
            ],
            [
                'key' => 'location',
                'attributes' => [
                    'title_en' => 'Address & Location', 'title_bn' => 'ঠিকানা ও অবস্থান',
                    'icon' => 'map-pin', 'step' => 2, 'completion_weight' => 10,
                ],
                // Present address (residing_* / current_*) then Permanent address.
                'fields' => [
                    ['field_key' => 'nationality', 'model_column' => 'nationality', 'label_en' => 'Nationality', 'label_bn' => 'জাতীয়তা'],
                    ['field_key' => 'residing_country', 'model_column' => 'residing_country', 'label_en' => 'Present Country', 'label_bn' => 'বর্তমান দেশ', 'is_required' => true, 'is_filterable' => true],
                    ['field_key' => 'current_division', 'model_column' => 'current_division', 'label_en' => 'Present Division', 'label_bn' => 'বর্তমান বিভাগ'],
                    ['field_key' => 'current_district', 'model_column' => 'current_district', 'label_en' => 'Present District', 'label_bn' => 'বর্তমান জেলা'],
                    ['field_key' => 'current_upazila', 'model_column' => 'current_upazila', 'label_en' => 'Present Upazila / Thana', 'label_bn' => 'বর্তমান উপজেলা / থানা'],
                    ['field_key' => 'current_area', 'model_column' => 'current_area', 'label_en' => 'Present Area / Village / City', 'label_bn' => 'বর্তমান এলাকা / গ্রাম / শহর'],
                    ['field_key' => 'present_address', 'model_column' => 'present_address', 'label_en' => 'Present Address', 'label_bn' => 'বর্তমান ঠিকানা', 'input_type' => 'textarea', 'show_in_profile' => false],
                    ['field_key' => 'residing_city', 'model_column' => 'residing_city', 'label_en' => 'Present City (abroad)', 'label_bn' => 'বর্তমান শহর (বিদেশে)'],
                    ['field_key' => 'permanent_country', 'model_column' => 'permanent_country', 'label_en' => 'Permanent Country', 'label_bn' => 'স্থায়ী দেশ'],
                    ['field_key' => 'division', 'model_column' => 'division', 'label_en' => 'Permanent Division', 'label_bn' => 'স্থায়ী বিভাগ', 'is_filterable' => true],
                    ['field_key' => 'district', 'model_column' => 'district', 'label_en' => 'Permanent District', 'label_bn' => 'স্থায়ী জেলা', 'is_filterable' => true],
                    ['field_key' => 'upazila', 'model_column' => 'upazila', 'label_en' => 'Permanent Upazila / Thana', 'label_bn' => 'স্থায়ী উপজেলা / থানা'],
                    ['field_key' => 'village_area', 'model_column' => 'village_area', 'label_en' => 'Permanent Area / Village / City', 'label_bn' => 'স্থায়ী এলাকা / গ্রাম / শহর'],
                    ['field_key' => 'permanent_address', 'model_column' => 'permanent_address', 'label_en' => 'Permanent Address', 'label_bn' => 'স্থায়ী ঠিকানা', 'input_type' => 'textarea', 'show_in_profile' => false],
                    ['field_key' => 'grew_up_in', 'model_column' => 'grew_up_in', 'label_en' => 'Grew Up In', 'label_bn' => 'যেখানে বড় হয়েছেন'],
                    ['field_key' => 'same_as_permanent', 'model_column' => 'same_as_permanent', 'label_en' => 'Permanent same as present', 'label_bn' => 'স্থায়ী ঠিকানা বর্তমানের মতোই', 'input_type' => 'yes_no', 'show_in_profile' => false],
                    ['field_key' => 'is_nrb', 'model_column' => 'is_nrb', 'label_en' => 'Non-Resident Bangladeshi', 'label_bn' => 'প্রবাসী বাংলাদেশি', 'input_type' => 'yes_no'],
                    ['field_key' => 'visa_status', 'model_column' => 'visa_status', 'label_en' => 'Visa / Residency Status', 'label_bn' => 'ভিসা / রেসিডেন্সি অবস্থা', 'input_type' => 'select', 'show_in_profile' => false, 'options_en' => [
                        ['value' => 'citizen', 'label' => 'Citizen'],
                        ['value' => 'permanent_resident', 'label' => 'Permanent Resident'],
                        ['value' => 'work_visa', 'label' => 'Work Visa'],
                        ['value' => 'student_visa', 'label' => 'Student Visa'],
                    ]],
                ],
            ],
            [
                'key' => 'religion',
                'attributes' => [
                    'title_en' => 'Religion & Deen', 'title_bn' => 'ধর্ম ও দ্বীন',
                    'icon' => 'moon', 'step' => 3, 'completion_weight' => 10,
                ],
                'fields' => [
                    ['field_key' => 'religion', 'model_column' => 'religion', 'label_en' => 'Religion', 'label_bn' => 'ধর্ম', 'is_required' => true, 'is_filterable' => true],
                    ['field_key' => 'sect', 'model_column' => 'sect', 'label_en' => 'Sect / Maslak', 'label_bn' => 'মাযহাব / মাসলাক'],
                    ['field_key' => 'prayers_info', 'model_column' => 'prayers_info', 'label_en' => 'Prayer Habit', 'label_bn' => 'নামাজের অভ্যাস', 'input_type' => 'select', 'options_en' => [
                        ['value' => '5_times', 'label' => '5 times daily'],
                        ['value' => '4_times', 'label' => '4 times'],
                        ['value' => 'sometimes', 'label' => 'Sometimes'],
                        ['value' => 'rarely', 'label' => 'Rarely'],
                        ['value' => 'never', 'label' => 'Never'],
                    ]],
                    ['field_key' => 'quran_recitation', 'model_column' => 'quran_recitation', 'label_en' => 'Quran Recitation', 'label_bn' => 'কুরআন তিলাওয়াত', 'input_type' => 'select', 'options_en' => [
                        ['value' => 'fluent', 'label' => 'Fluent'],
                        ['value' => 'basic', 'label' => 'Basic'],
                        ['value' => 'learning', 'label' => 'Learning'],
                        ['value' => 'no', 'label' => 'Cannot read'],
                    ]],
                    ['field_key' => 'is_practicing', 'model_column' => 'is_practicing', 'label_en' => 'Practicing', 'label_bn' => 'অনুশীলনকারী', 'input_type' => 'yes_no'],
                    ['field_key' => 'fiqh', 'model_column' => 'fiqh', 'label_en' => 'Fiqh', 'label_bn' => 'ফিকহ'],
                    ['field_key' => 'clothing_style', 'model_column' => 'clothing_style', 'label_en' => 'Clothing Style', 'label_bn' => 'পোশাকের ধরন'],
                    ['field_key' => 'beard_info', 'model_column' => 'beard_info', 'label_en' => 'Beard', 'label_bn' => 'দাড়ি', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'male']],
                    ['field_key' => 'beard_since', 'model_column' => 'beard_since', 'label_en' => 'Beard Since', 'label_bn' => 'কত বছর যাবৎ দাড়ি', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'male']],
                    ['field_key' => 'pants_above_ankle', 'model_column' => 'pants_above_ankle', 'label_en' => 'Pants Above Ankle', 'label_bn' => 'টাখনুর উপরে কাপড়', 'input_type' => 'yes_no', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'male']],
                    ['field_key' => 'hijab_info', 'model_column' => 'hijab_info', 'label_en' => 'Hijab', 'label_bn' => 'হিজাব', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'female']],
                    ['field_key' => 'niqab_since', 'model_column' => 'niqab_since', 'label_en' => 'Niqab Since', 'label_bn' => 'কত বছর যাবৎ নিকাব', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'female']],
                    ['field_key' => 'purdah_details', 'model_column' => 'purdah_details', 'label_en' => 'Purdah Details', 'label_bn' => 'পর্দার বিবরণ', 'input_type' => 'textarea', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'female']],
                    ['field_key' => 'prayer_start_age', 'model_column' => 'prayer_start_age', 'label_en' => 'Prayer Start Age', 'label_bn' => 'নামাজ শুরুর বয়স'],
                    ['field_key' => 'weekly_missed_prayers', 'model_column' => 'weekly_missed_prayers', 'label_en' => 'Weekly Missed Prayers', 'label_bn' => 'সাপ্তাহিক ছুটে যাওয়া নামাজ'],
                    ['field_key' => 'mahram_practice', 'model_column' => 'mahram_practice', 'label_en' => 'Mahram Practice', 'label_bn' => 'মাহরাম মেনে চলা', 'input_type' => 'textarea'],
                    ['field_key' => 'islamic_books_read', 'model_column' => 'islamic_books_read', 'label_en' => 'Islamic Books Read', 'label_bn' => 'পঠিত ইসলামিক বই', 'input_type' => 'textarea'],
                    ['field_key' => 'deen_work_details', 'model_column' => 'deen_work_details', 'label_en' => 'Deen Work Details', 'label_bn' => 'দ্বীনি কাজের বিবরণ', 'input_type' => 'textarea'],
                    ['field_key' => 'social_media_usage', 'model_column' => 'social_media_usage', 'label_en' => 'Social Media Usage', 'label_bn' => 'সোশ্যাল মিডিয়া ব্যবহার'],
                    ['field_key' => 'is_islamically_educated', 'model_column' => 'is_islamically_educated', 'label_en' => 'Islamically Educated', 'label_bn' => 'ইসলামিক শিক্ষিত', 'input_type' => 'yes_no'],
                    ['field_key' => 'beliefs_on_mazar', 'model_column' => 'beliefs_on_mazar', 'label_en' => 'Beliefs on Mazar', 'label_bn' => 'মাজার সম্পর্কে বিশ্বাস', 'input_type' => 'textarea'],
                    ['field_key' => 'favorite_scholars', 'model_column' => 'favorite_scholars', 'label_en' => 'Favorite Scholars', 'label_bn' => 'প্রিয় আলেম', 'input_type' => 'textarea'],
                    ['field_key' => 'wali_approval', 'model_column' => 'wali_approval', 'label_en' => 'Wali Approval', 'label_bn' => 'অভিভাবকের সম্মতি', 'input_type' => 'yes_no'],
                    ['field_key' => 'sunni_scale', 'model_column' => 'sunni_scale', 'label_en' => 'Practice Scale (1-10)', 'label_bn' => 'অনুশীলনের মাত্রা (১-১০)', 'input_type' => 'number'],
                ],
            ],
            [
                'key' => 'education',
                'attributes' => [
                    'title_en' => 'Education & Profession', 'title_bn' => 'শিক্ষা ও পেশা',
                    'icon' => 'graduation-cap', 'step' => 4, 'completion_weight' => 10,
                ],
                'fields' => [
                    ['field_key' => 'highest_qualification', 'model_column' => 'highest_qualification', 'label_en' => 'Highest Qualification', 'label_bn' => 'সর্বোচ্চ শিক্ষাগত যোগ্যতা', 'input_type' => 'select', 'is_required' => true, 'is_filterable' => true, 'options_en' => [
                        ['value' => 'below_ssc', 'label' => 'Below SSC'],
                        ['value' => 'ssc', 'label' => 'SSC'],
                        ['value' => 'hsc', 'label' => 'HSC'],
                        ['value' => 'graduation', 'label' => 'Graduation'],
                        ['value' => 'post_graduation', 'label' => 'Post Graduation'],
                        ['value' => 'hafez', 'label' => 'Hafez'],
                        ['value' => 'alim', 'label' => 'Alim'],
                        ['value' => 'fazil', 'label' => 'Fazil'],
                        ['value' => 'kamil', 'label' => 'Kamil'],
                        ['value' => 'other', 'label' => 'Other'],
                    ]],
                    ['field_key' => 'education_medium', 'model_column' => 'education_medium', 'label_en' => 'Education System', 'label_bn' => 'শিক্ষার মাধ্যম', 'input_type' => 'select', 'options_en' => [
                        ['value' => 'general', 'label' => 'General'],
                        ['value' => 'qawmi', 'label' => 'Qawmi'],
                        ['value' => 'alia', 'label' => 'Alia'],
                        ['value' => 'english_medium', 'label' => 'English Medium'],
                        ['value' => 'vocational', 'label' => 'Vocational'],
                        ['value' => 'other', 'label' => 'Other'],
                    ]],
                    // Derived from education_medium on the client — not a direct input.
                    ['field_key' => 'education_method', 'model_column' => 'education_method', 'label_en' => 'Education Method', 'label_bn' => 'শিক্ষার ধরন', 'input_type' => 'select', 'show_in_form' => false],
                    // Composite repeater (education records). Toggling it off hides the
                    // whole detailed-records block in the wizard.
                    ['field_key' => 'education_details', 'model_column' => 'education_details', 'label_en' => 'Education Records', 'label_bn' => 'শিক্ষার রেকর্ড', 'input_type' => 'repeater'],
                    ['field_key' => 'occupation', 'model_column' => 'occupation', 'label_en' => 'Occupation', 'label_bn' => 'পেশা', 'is_required' => true, 'is_filterable' => true],
                    ['field_key' => 'occupation_category', 'model_column' => 'occupation_category', 'label_en' => 'Occupation Category', 'label_bn' => 'পেশার ধরন', 'input_type' => 'select', 'options_en' => [
                        ['value' => 'business', 'label' => 'Business'],
                        ['value' => 'service_govt', 'label' => 'Govt Service'],
                        ['value' => 'service_private', 'label' => 'Private Service'],
                        ['value' => 'student', 'label' => 'Student'],
                        ['value' => 'other', 'label' => 'Other'],
                    ]],
                    ['field_key' => 'monthly_income', 'model_column' => 'monthly_income', 'label_en' => 'Monthly Income', 'label_bn' => 'মাসিক আয়', 'input_type' => 'number', 'is_private' => true, 'profile_display_format' => 'currency'],
                    ['field_key' => 'profession_details', 'model_column' => 'profession_details', 'label_en' => 'Profession Details', 'label_bn' => 'পেশার বিবরণ', 'input_type' => 'textarea'],
                    ['field_key' => 'workplace_type', 'model_column' => 'workplace_type', 'label_en' => 'Workplace Type', 'label_bn' => 'কর্মস্থলের ধরন'],
                    ['field_key' => 'future_career_plan', 'model_column' => 'future_career_plan', 'label_en' => 'Future Career Plan', 'label_bn' => 'ভবিষ্যৎ ক্যারিয়ার পরিকল্পনা', 'input_type' => 'textarea'],
                    ['field_key' => 'income_type', 'model_column' => 'income_type', 'label_en' => 'Income Type', 'label_bn' => 'আয়ের ধরন', 'input_type' => 'select', 'options_en' => [
                        ['value' => 'monthly', 'label' => 'Monthly'],
                        ['value' => 'business', 'label' => 'Business'],
                        ['value' => 'freelance', 'label' => 'Freelance'],
                        ['value' => 'daily', 'label' => 'Daily'],
                        ['value' => 'variable', 'label' => 'Variable'],
                        ['value' => 'private', 'label' => 'Private'],
                    ]],
                    ['field_key' => 'income_privacy', 'model_column' => 'income_privacy', 'label_en' => 'Income Privacy', 'label_bn' => 'আয়ের গোপনীয়তা', 'input_type' => 'select', 'is_private' => true, 'show_in_profile' => false, 'options_en' => [
                        ['value' => 'public', 'label' => 'Public'],
                        ['value' => 'range', 'label' => 'Show as Range'],
                        ['value' => 'members_only', 'label' => 'Members Only'],
                        ['value' => 'private', 'label' => 'Private'],
                    ]],
                    ['field_key' => 'profession_halal_status', 'model_column' => 'profession_halal_status', 'label_en' => 'Halal Status', 'label_bn' => 'হালাল অবস্থা', 'input_type' => 'select', 'options_en' => [
                        ['value' => 'halal', 'label' => 'Halal'],
                        ['value' => 'not_sure', 'label' => 'Not Sure'],
                        ['value' => 'halal_alternative', 'label' => 'Seeking Halal Alternative'],
                    ]],
                ],
            ],
            [
                'key' => 'lifestyle',
                'attributes' => [
                    'title_en' => 'Lifestyle', 'title_bn' => 'জীবনধারা',
                    'icon' => 'heart-pulse', 'step' => 5, 'completion_weight' => 10,
                ],
                // Physical fields (height/weight/complexion/blood_group/health) moved
                // to the general (Basic Information) section.
                'fields' => [
                    ['field_key' => 'hobbies', 'model_column' => 'hobbies', 'label_en' => 'Hobbies', 'label_bn' => 'শখ', 'input_type' => 'textarea'],
                    ['field_key' => 'diet', 'model_column' => 'diet', 'label_en' => 'Diet', 'label_bn' => 'খাদ্যাভ্যাস', 'input_type' => 'select', 'is_active' => false, 'show_in_form' => false, 'options_en' => [
                        ['value' => 'halal_only', 'label' => 'Halal Only'],
                        ['value' => 'vegetarian', 'label' => 'Vegetarian'],
                        ['value' => 'no_restriction', 'label' => 'No Restriction'],
                    ]],
                    ['field_key' => 'smoking', 'model_column' => 'smoking', 'label_en' => 'Smoking', 'label_bn' => 'ধূমপান', 'input_type' => 'select', 'is_active' => false, 'show_in_form' => false, 'options_en' => [
                        ['value' => 'never', 'label' => 'Never'],
                        ['value' => 'occasionally', 'label' => 'Occasionally'],
                        ['value' => 'regularly', 'label' => 'Regularly'],
                    ]],
                    ['field_key' => 'watch_entertainment', 'model_column' => 'watch_entertainment', 'label_en' => 'Entertainment Habit', 'label_bn' => 'বিনোদনের অভ্যাস', 'is_active' => false, 'show_in_form' => false],
                    ['field_key' => 'special_category', 'model_column' => 'special_category', 'label_en' => 'Special Category', 'label_bn' => 'বিশেষ ক্যাটাগরি', 'is_active' => false, 'show_in_form' => false],
                ],
            ],
            [
                'key' => 'family',
                'attributes' => [
                    'title_en' => 'Family Information', 'title_bn' => 'পারিবারিক তথ্য',
                    'icon' => 'users', 'step' => 6, 'completion_weight' => 10,
                ],
                'fields' => [
                    ['field_key' => 'father_name', 'model_column' => 'father_name', 'label_en' => "Father's Name", 'label_bn' => 'পিতার নাম', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'father_profession', 'model_column' => 'father_profession', 'label_en' => "Father's Profession", 'label_bn' => 'পিতার পেশা', 'is_required' => true],
                    ['field_key' => 'father_alive', 'model_column' => 'father_alive', 'label_en' => 'Father Alive', 'label_bn' => 'পিতা জীবিত', 'input_type' => 'yes_no'],
                    ['field_key' => 'mother_name', 'model_column' => 'mother_name', 'label_en' => "Mother's Name", 'label_bn' => 'মাতার নাম', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'mother_profession', 'model_column' => 'mother_profession', 'label_en' => "Mother's Profession", 'label_bn' => 'মাতার পেশা', 'is_required' => true],
                    ['field_key' => 'mother_alive', 'model_column' => 'mother_alive', 'label_en' => 'Mother Alive', 'label_bn' => 'মাতা জীবিত', 'input_type' => 'yes_no'],
                    ['field_key' => 'uncle_profession', 'model_column' => 'uncle_profession', 'label_en' => "Uncle's Profession", 'label_bn' => 'চাচা/মামার পেশা'],
                    ['field_key' => 'brothers', 'model_column' => 'brothers', 'label_en' => 'Number of Brothers', 'label_bn' => 'ভাইয়ের সংখ্যা', 'input_type' => 'number'],
                    ['field_key' => 'sisters', 'model_column' => 'sisters', 'label_en' => 'Number of Sisters', 'label_bn' => 'বোনের সংখ্যা', 'input_type' => 'number'],
                    // Composite repeaters (sibling cards). Toggling off hides the cards.
                    ['field_key' => 'brothers_details', 'model_column' => 'brothers_details', 'label_en' => 'Brother Details', 'label_bn' => 'ভাইয়ের বিবরণ', 'input_type' => 'repeater', 'show_in_profile' => false],
                    ['field_key' => 'sisters_details', 'model_column' => 'sisters_details', 'label_en' => 'Sister Details', 'label_bn' => 'বোনের বিবরণ', 'input_type' => 'repeater', 'show_in_profile' => false],
                    ['field_key' => 'family_type', 'model_column' => 'family_type', 'label_en' => 'Family Type', 'label_bn' => 'পরিবারের ধরন', 'input_type' => 'select', 'is_required' => true, 'options_en' => [
                        ['value' => 'joint', 'label' => 'Joint'],
                        ['value' => 'nuclear', 'label' => 'Nuclear'],
                        ['value' => 'flexible', 'label' => 'Flexible'],
                    ]],
                    ['field_key' => 'family_financial_status', 'model_column' => 'family_financial_status', 'label_en' => 'Family Financial Status', 'label_bn' => 'পারিবারিক আর্থিক অবস্থা', 'input_type' => 'select', 'options_en' => [
                        ['value' => 'lower', 'label' => 'Lower'],
                        ['value' => 'lower_middle', 'label' => 'Lower Middle'],
                        ['value' => 'middle', 'label' => 'Middle'],
                        ['value' => 'upper_middle', 'label' => 'Upper Middle'],
                        ['value' => 'upper', 'label' => 'Upper'],
                    ]],
                    ['field_key' => 'home_ownership', 'model_column' => 'home_ownership', 'label_en' => 'Home Ownership', 'label_bn' => 'বাড়ির মালিকানা', 'input_type' => 'select', 'options_en' => [
                        ['value' => 'own_house', 'label' => 'Own House'],
                        ['value' => 'family_house', 'label' => 'Family House'],
                        ['value' => 'rented', 'label' => 'Rented'],
                        ['value' => 'other', 'label' => 'Other'],
                    ]],
                    ['field_key' => 'family_assets_details', 'model_column' => 'family_assets_details', 'label_en' => 'Family Assets Details', 'label_bn' => 'পারিবারিক সম্পদের বিবরণ', 'input_type' => 'textarea', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'family_religious_condition', 'model_column' => 'family_religious_condition', 'label_en' => 'Family Religious Condition', 'label_bn' => 'পরিবারের ধর্মীয় অবস্থা'],
                    ['field_key' => 'family_details', 'model_column' => 'family_details', 'label_en' => 'Family Details', 'label_bn' => 'পরিবারের বিবরণ', 'input_type' => 'textarea'],
                ],
            ],
            [
                'key' => 'marriage',
                'attributes' => [
                    'title_en' => 'Marriage Information', 'title_bn' => 'বিবাহ সংক্রান্ত তথ্য',
                    'icon' => 'gem', 'step' => 7, 'completion_weight' => 10,
                ],
                'fields' => [
                    ['field_key' => 'residence_after_marriage', 'model_column' => 'residence_after_marriage', 'label_en' => 'Residence After Marriage', 'label_bn' => 'বিবাহের পর বসবাস', 'is_required' => true],
                    ['field_key' => 'why_getting_married', 'model_column' => 'why_getting_married', 'label_en' => 'Why Getting Married', 'label_bn' => 'কেন বিয়ে করতে চান', 'input_type' => 'textarea'],
                    ['field_key' => 'marriage_thoughts', 'model_column' => 'marriage_thoughts', 'label_en' => 'Thoughts on Marriage', 'label_bn' => 'বিবাহ সম্পর্কে ভাবনা', 'input_type' => 'textarea'],
                    ['field_key' => 'guardian_agree', 'model_column' => 'guardian_agree', 'label_en' => 'Guardian Agrees', 'label_bn' => 'অভিভাবক সম্মত', 'input_type' => 'yes_no'],
                    ['field_key' => 'marriage_timeline', 'model_column' => 'marriage_timeline', 'label_en' => 'Marriage Timeline', 'label_bn' => 'বিবাহের সময়সীমা'],
                    // Male-oriented expectations.
                    ['field_key' => 'wife_in_veil', 'model_column' => 'wife_in_veil', 'label_en' => 'Expect Wife in Veil', 'label_bn' => 'স্ত্রীর পর্দা প্রত্যাশা', 'input_type' => 'yes_no', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'male']],
                    ['field_key' => 'wife_study_allowed', 'model_column' => 'wife_study_allowed', 'label_en' => 'Allow Wife to Study', 'label_bn' => 'স্ত্রীর পড়াশোনার অনুমতি', 'input_type' => 'yes_no', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'male']],
                    ['field_key' => 'wife_job_allowed', 'model_column' => 'wife_job_allowed', 'label_en' => 'Allow Wife to Work', 'label_bn' => 'স্ত্রীর চাকরির অনুমতি', 'input_type' => 'yes_no', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'male']],
                    ['field_key' => 'expect_gift_from_bride', 'model_column' => 'expect_gift_from_bride', 'label_en' => 'Expect Gift from Bride', 'label_bn' => 'কনের পক্ষ থেকে উপহার প্রত্যাশা', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'male']],
                    ['field_key' => 'gift_expectation_details', 'model_column' => 'gift_expectation_details', 'label_en' => 'Gift Expectation Details', 'label_bn' => 'উপহার প্রত্যাশার বিবরণ', 'input_type' => 'textarea', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'male']],
                    ['field_key' => 'polygamy_open', 'model_column' => 'polygamy_open', 'label_en' => 'Open to Polygamy', 'label_bn' => 'বহুবিবাহে আগ্রহী', 'input_type' => 'yes_no'],
                    // Female-oriented intentions.
                    ['field_key' => 'wants_to_work', 'model_column' => 'wants_to_work', 'label_en' => 'Wants to Work After Marriage', 'label_bn' => 'বিবাহের পর কাজ করতে চান', 'input_type' => 'yes_no', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'female']],
                    ['field_key' => 'continue_study', 'model_column' => 'continue_study', 'label_en' => 'Continue Study After Marriage', 'label_bn' => 'বিবাহের পর পড়াশোনা চালিয়ে যাওয়া', 'input_type' => 'yes_no', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'female']],
                    ['field_key' => 'continue_job', 'model_column' => 'continue_job', 'label_en' => 'Continue Job After Marriage', 'label_bn' => 'বিবাহের পর চাকরি চালিয়ে যাওয়া', 'input_type' => 'yes_no', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'female']],
                    ['field_key' => 'preferred_living', 'model_column' => 'preferred_living', 'label_en' => 'Preferred Living', 'label_bn' => 'পছন্দের বসবাস', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'female']],
                    ['field_key' => 'post_marriage_plan', 'model_column' => 'post_marriage_plan', 'label_en' => 'Post-Marriage Plan', 'label_bn' => 'বিবাহ পরবর্তী পরিকল্পনা'],
                    // Children (any previously-married status).
                    ['field_key' => 'has_children', 'model_column' => 'has_children', 'label_en' => 'Has Children', 'label_bn' => 'সন্তান আছে', 'input_type' => 'yes_no', 'show_in_profile' => false],
                    ['field_key' => 'children_count', 'model_column' => 'children_count', 'label_en' => 'Number of Children', 'label_bn' => 'সন্তানের সংখ্যা', 'input_type' => 'number', 'show_in_profile' => false],
                    ['field_key' => 'children_live_with', 'model_column' => 'children_live_with', 'label_en' => 'Children Live With', 'label_bn' => 'সন্তান যার সাথে থাকে', 'show_in_profile' => false],
                    ['field_key' => 'children_notes', 'model_column' => 'children_notes', 'label_en' => 'Children Notes', 'label_bn' => 'সন্তান সম্পর্কিত নোট', 'input_type' => 'textarea', 'show_in_profile' => false],
                    ['field_key' => 'child_acceptance_expectation', 'model_column' => 'child_acceptance_expectation', 'label_en' => 'Child Acceptance Expectation', 'label_bn' => 'সন্তান গ্রহণের প্রত্যাশা', 'input_type' => 'textarea', 'show_in_profile' => false],
                    // Divorced-specific.
                    ['field_key' => 'previous_marriage_date', 'model_column' => 'previous_marriage_date', 'label_en' => 'Previous Marriage Date', 'label_bn' => 'পূর্ববর্তী বিবাহের তারিখ', 'input_type' => 'date', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'divorce_date', 'model_column' => 'divorce_date', 'label_en' => 'Divorce Date', 'label_bn' => 'তালাকের তারিখ', 'input_type' => 'date', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'divorce_reason', 'model_column' => 'divorce_reason', 'label_en' => 'Divorce Reason', 'label_bn' => 'তালাকের কারণ', 'input_type' => 'textarea', 'is_private' => true, 'show_in_profile' => false],
                    // Widowed-specific.
                    ['field_key' => 'spouse_death_date', 'model_column' => 'spouse_death_date', 'label_en' => 'Spouse Death Date', 'label_bn' => 'সঙ্গীর মৃত্যুর তারিখ', 'input_type' => 'date', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'spouse_death_reason', 'model_column' => 'spouse_death_reason', 'label_en' => 'Spouse Death Reason', 'label_bn' => 'সঙ্গীর মৃত্যুর কারণ', 'input_type' => 'textarea', 'is_private' => true, 'show_in_profile' => false],
                    // Married / second-marriage-specific.
                    ['field_key' => 'reason_for_second_marriage', 'model_column' => 'reason_for_second_marriage', 'label_en' => 'Reason for Second Marriage', 'label_bn' => 'দ্বিতীয় বিবাহের কারণ', 'input_type' => 'textarea', 'show_in_profile' => false],
                    ['field_key' => 'current_wife_count', 'model_column' => 'current_wife_count', 'label_en' => 'Current Wife Count', 'label_bn' => 'বর্তমান স্ত্রীর সংখ্যা', 'input_type' => 'number', 'show_in_profile' => false],
                    ['field_key' => 'current_family_consent', 'model_column' => 'current_family_consent', 'label_en' => 'Current Family Consent', 'label_bn' => 'বর্তমান পরিবারের সম্মতি', 'input_type' => 'yes_no', 'show_in_profile' => false],
                    ['field_key' => 'first_wife_knows', 'model_column' => 'first_wife_knows', 'label_en' => 'First Wife Knows', 'label_bn' => 'প্রথম স্ত্রী জানেন', 'input_type' => 'yes_no', 'show_in_profile' => false],
                    ['field_key' => 'second_marriage_living', 'model_column' => 'second_marriage_living', 'label_en' => 'Second Marriage Living Arrangement', 'label_bn' => 'দ্বিতীয় বিবাহের বসবাস ব্যবস্থা', 'show_in_profile' => false],
                ],
            ],
            [
                'key' => 'partner',
                'attributes' => [
                    'title_en' => 'Partner Preferences', 'title_bn' => 'সঙ্গীর পছন্দ',
                    'icon' => 'search-heart', 'step' => 8, 'completion_weight' => 10,
                ],
                'fields' => [
                    ['field_key' => 'partner_age_min', 'model_column' => 'partner_age_min', 'label_en' => 'Min Age', 'label_bn' => 'সর্বনিম্ন বয়স', 'input_type' => 'number', 'is_required' => true],
                    ['field_key' => 'partner_age_max', 'model_column' => 'partner_age_max', 'label_en' => 'Max Age', 'label_bn' => 'সর্বোচ্চ বয়স', 'input_type' => 'number', 'is_required' => true],
                    ['field_key' => 'partner_height_cm_min', 'model_column' => 'partner_height_cm_min', 'label_en' => 'Min Height (cm)', 'label_bn' => 'সর্বনিম্ন উচ্চতা', 'input_type' => 'number'],
                    ['field_key' => 'partner_height_cm_max', 'model_column' => 'partner_height_cm_max', 'label_en' => 'Max Height (cm)', 'label_bn' => 'সর্বোচ্চ উচ্চতা', 'input_type' => 'number'],
                    ['field_key' => 'partner_education', 'model_column' => 'partner_education', 'label_en' => 'Preferred Education', 'label_bn' => 'পছন্দের শিক্ষা', 'is_required' => true],
                    ['field_key' => 'partner_division', 'model_column' => 'partner_division', 'label_en' => 'Preferred Division', 'label_bn' => 'পছন্দের বিভাগ', 'is_required' => true],
                    ['field_key' => 'partner_marital_status', 'model_column' => 'partner_marital_status', 'label_en' => 'Preferred Marital Status', 'label_bn' => 'পছন্দের বৈবাহিক অবস্থা'],
                    ['field_key' => 'partner_complexion', 'model_column' => 'partner_complexion', 'label_en' => 'Preferred Complexion', 'label_bn' => 'পছন্দের গায়ের রং'],
                    ['field_key' => 'partner_district', 'model_column' => 'partner_district', 'label_en' => 'Preferred District', 'label_bn' => 'পছন্দের জেলা', 'is_filterable' => true],
                    ['field_key' => 'partner_districts', 'model_column' => 'partner_districts', 'label_en' => 'Additional Preferred Districts', 'label_bn' => 'অতিরিক্ত পছন্দের জেলা', 'input_type' => 'multi_select'],
                    ['field_key' => 'partner_occupation_pref', 'model_column' => 'partner_occupation_pref', 'label_en' => 'Preferred Occupation', 'label_bn' => 'পছন্দের পেশা'],
                    ['field_key' => 'partner_income_min', 'model_column' => 'partner_income_min', 'label_en' => 'Min Income', 'label_bn' => 'সর্বনিম্ন আয়', 'input_type' => 'number', 'show_in_profile' => false],
                    ['field_key' => 'partner_income_max', 'model_column' => 'partner_income_max', 'label_en' => 'Max Income', 'label_bn' => 'সর্বোচ্চ আয়', 'input_type' => 'number', 'show_in_profile' => false],
                    ['field_key' => 'partner_family_type', 'model_column' => 'partner_family_type', 'label_en' => 'Preferred Family Type', 'label_bn' => 'পছন্দের পরিবারের ধরন'],
                    ['field_key' => 'partner_economic_status', 'model_column' => 'partner_economic_status', 'label_en' => 'Preferred Economic Status', 'label_bn' => 'পছন্দের অর্থনৈতিক অবস্থা'],
                    ['field_key' => 'partner_deen_practice', 'model_column' => 'partner_deen_practice', 'label_en' => 'Preferred Deen Practice', 'label_bn' => 'পছন্দের দ্বীন চর্চা'],
                    ['field_key' => 'partner_special_qualities', 'model_column' => 'partner_special_qualities', 'label_en' => 'Special Qualities Wanted', 'label_bn' => 'কাঙ্ক্ষিত বিশেষ গুণাবলী', 'input_type' => 'textarea'],
                    ['field_key' => 'partner_deal_breakers', 'model_column' => 'partner_deal_breakers', 'label_en' => 'Deal Breakers', 'label_bn' => 'অগ্রহণযোগ্য বিষয়', 'input_type' => 'textarea'],
                    ['field_key' => 'partner_expectations', 'model_column' => 'partner_expectations', 'label_en' => 'Expectations', 'label_bn' => 'প্রত্যাশা', 'input_type' => 'textarea'],
                ],
            ],
            [
                'key' => 'contact',
                'attributes' => [
                    'title_en' => 'Contact & Guardian', 'title_bn' => 'যোগাযোগ ও অভিভাবক',
                    'icon' => 'phone', 'step' => 9, 'completion_weight' => 10,
                ],
                'fields' => [
                    ['field_key' => 'contact_privacy', 'model_column' => 'contact_privacy', 'label_en' => 'Contact Privacy', 'label_bn' => 'যোগাযোগ গোপনীয়তা', 'input_type' => 'select', 'is_required' => true, 'show_in_profile' => false, 'options_en' => [
                        ['value' => 'private', 'label' => 'Private'],
                        ['value' => 'request_only', 'label' => 'On Request'],
                        ['value' => 'matches_only', 'label' => 'Matches Only'],
                    ]],
                    ['field_key' => 'contact_person_name', 'model_column' => 'contact_person_name', 'label_en' => 'Contact Person Name', 'label_bn' => 'যোগাযোগকারীর নাম', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'guardian_name', 'model_column' => 'guardian_name', 'label_en' => 'Guardian Name', 'label_bn' => 'অভিভাবকের নাম', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'guardian_mobile', 'model_column' => 'guardian_mobile', 'label_en' => 'Guardian Mobile', 'label_bn' => 'অভিভাবকের মোবাইল', 'input_type' => 'phone', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'guardian_relationship', 'model_column' => 'guardian_relationship', 'label_en' => 'Guardian Relationship', 'label_bn' => 'অভিভাবকের সম্পর্ক', 'show_in_profile' => false],
                    ['field_key' => 'guardian_email', 'model_column' => 'guardian_email', 'label_en' => 'Guardian Email', 'label_bn' => 'অভিভাবকের ইমেইল', 'input_type' => 'email', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'guardian_whatsapp', 'model_column' => 'guardian_whatsapp', 'label_en' => 'Guardian WhatsApp', 'label_bn' => 'অভিভাবকের হোয়াটসঅ্যাপ', 'input_type' => 'phone', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'whatsapp_number', 'model_column' => 'whatsapp_number', 'label_en' => 'WhatsApp Number', 'label_bn' => 'হোয়াটসঅ্যাপ নম্বর', 'input_type' => 'phone', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'biodata_visibility', 'model_column' => 'biodata_visibility', 'label_en' => 'Biodata Visibility', 'label_bn' => 'বায়োডাটা দৃশ্যমানতা', 'input_type' => 'select', 'show_in_profile' => false, 'options_en' => [
                        ['value' => 'public', 'label' => 'Public'],
                        ['value' => 'private', 'label' => 'Private'],
                        ['value' => 'admin_approved_only', 'label' => 'Admin Approved Only'],
                    ]],
                    ['field_key' => 'allow_shortlist', 'model_column' => 'allow_shortlist', 'label_en' => 'Allow Shortlisting', 'label_bn' => 'শর্টলিস্ট করার অনুমতি', 'input_type' => 'yes_no', 'show_in_profile' => false],
                    ['field_key' => 'allow_contact_request', 'model_column' => 'allow_contact_request', 'label_en' => 'Allow Contact Requests', 'label_bn' => 'যোগাযোগের অনুরোধের অনুমতি', 'input_type' => 'yes_no', 'show_in_profile' => false],
                ],
            ],
            [
                'key' => 'review',
                'attributes' => [
                    'title_en' => 'Photos & Declaration', 'title_bn' => 'ছবি ও ঘোষণা',
                    'icon' => 'shield-check', 'step' => 10, 'completion_weight' => 10,
                ],
                'fields' => [
                    ['field_key' => 'photos', 'model_column' => 'photos', 'label_en' => 'Photos', 'label_bn' => 'ছবি', 'input_type' => 'file', 'is_private' => true],
                    ['field_key' => 'guardian_knows_biodata', 'model_column' => 'guardian_knows_biodata', 'label_en' => 'Guardian Knows About This Biodata', 'label_bn' => 'অভিভাবক এই বায়োডাটা সম্পর্কে জানেন', 'input_type' => 'yes_no', 'show_in_profile' => false],
                    ['field_key' => 'info_truthful_confirmed', 'model_column' => 'info_truthful_confirmed', 'label_en' => 'Information is Truthful', 'label_bn' => 'তথ্য সত্য', 'input_type' => 'yes_no', 'show_in_profile' => false],
                    ['field_key' => 'accept_liability_terms', 'model_column' => 'accept_liability_terms', 'label_en' => 'Accept Terms', 'label_bn' => 'শর্তাবলী গ্রহণ', 'input_type' => 'yes_no', 'show_in_profile' => false],
                ],
            ],
        ];
    }
}
