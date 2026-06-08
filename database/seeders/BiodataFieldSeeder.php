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
                BiodataField::firstOrCreate(
                    ['field_key' => $field['field_key']],
                    array_merge($this->fieldDefaults(), $field, [
                        'section_id' => $model->id,
                        'sort_order' => $fOrder + 1,
                        'is_system'  => true,
                    ]),
                );
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
                    ['field_key' => 'birth_date', 'model_column' => 'birth_date', 'label_en' => 'Date of Birth', 'label_bn' => 'জন্ম তারিখ', 'input_type' => 'date', 'is_required' => true, 'is_private' => true, 'profile_display_format' => 'age'],
                    ['field_key' => 'mother_tongue', 'model_column' => 'mother_tongue', 'label_en' => 'Mother Tongue', 'label_bn' => 'মাতৃভাষা'],
                    ['field_key' => 'profile_headline', 'model_column' => 'profile_headline', 'label_en' => 'Profile Headline', 'label_bn' => 'প্রোফাইল শিরোনাম'],
                    ['field_key' => 'about_me', 'model_column' => 'about_me', 'label_en' => 'About Me', 'label_bn' => 'আমার সম্পর্কে', 'input_type' => 'textarea'],
                ],
            ],
            [
                'key' => 'location',
                'attributes' => [
                    'title_en' => 'Address & Location', 'title_bn' => 'ঠিকানা ও অবস্থান',
                    'icon' => 'map-pin', 'step' => 2, 'completion_weight' => 10,
                ],
                'fields' => [
                    ['field_key' => 'residing_country', 'model_column' => 'residing_country', 'label_en' => 'Residing Country', 'label_bn' => 'বসবাসের দেশ', 'is_required' => true, 'is_filterable' => true],
                    ['field_key' => 'residing_city', 'model_column' => 'residing_city', 'label_en' => 'Residing City', 'label_bn' => 'বসবাসের শহর', 'is_required' => true],
                    ['field_key' => 'division', 'model_column' => 'division', 'label_en' => 'Division', 'label_bn' => 'বিভাগ', 'is_required' => true, 'is_filterable' => true],
                    ['field_key' => 'district', 'model_column' => 'district', 'label_en' => 'District', 'label_bn' => 'জেলা', 'is_required' => true, 'is_filterable' => true],
                    ['field_key' => 'upazila', 'model_column' => 'upazila', 'label_en' => 'Upazila', 'label_bn' => 'উপজেলা'],
                    ['field_key' => 'permanent_address', 'model_column' => 'permanent_address', 'label_en' => 'Permanent Address', 'label_bn' => 'স্থায়ী ঠিকানা', 'input_type' => 'textarea', 'is_private' => true],
                    ['field_key' => 'current_division', 'model_column' => 'current_division', 'label_en' => 'Current Division', 'label_bn' => 'বর্তমান বিভাগ'],
                    ['field_key' => 'current_district', 'model_column' => 'current_district', 'label_en' => 'Current District', 'label_bn' => 'বর্তমান জেলা'],
                    ['field_key' => 'is_nrb', 'model_column' => 'is_nrb', 'label_en' => 'Non-Resident Bangladeshi', 'label_bn' => 'প্রবাসী বাংলাদেশি', 'input_type' => 'yes_no'],
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
                    ['field_key' => 'beard_info', 'model_column' => 'beard_info', 'label_en' => 'Beard', 'label_bn' => 'দাড়ি', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'male']],
                    ['field_key' => 'hijab_info', 'model_column' => 'hijab_info', 'label_en' => 'Hijab', 'label_bn' => 'হিজাব', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'female']],
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
                    ['field_key' => 'education_medium', 'model_column' => 'education_medium', 'label_en' => 'Education Medium', 'label_bn' => 'শিক্ষার মাধ্যম', 'input_type' => 'select', 'options_en' => [
                        ['value' => 'general', 'label' => 'General'],
                        ['value' => 'qawmi', 'label' => 'Qawmi'],
                        ['value' => 'alia', 'label' => 'Alia'],
                        ['value' => 'english_medium', 'label' => 'English Medium'],
                        ['value' => 'vocational', 'label' => 'Vocational'],
                        ['value' => 'other', 'label' => 'Other'],
                    ]],
                    ['field_key' => 'occupation', 'model_column' => 'occupation', 'label_en' => 'Occupation', 'label_bn' => 'পেশা', 'is_required' => true, 'is_filterable' => true],
                    ['field_key' => 'occupation_category', 'model_column' => 'occupation_category', 'label_en' => 'Occupation Category', 'label_bn' => 'পেশার ধরন', 'input_type' => 'select', 'options_en' => [
                        ['value' => 'business', 'label' => 'Business'],
                        ['value' => 'service_govt', 'label' => 'Govt Service'],
                        ['value' => 'service_private', 'label' => 'Private Service'],
                        ['value' => 'student', 'label' => 'Student'],
                        ['value' => 'other', 'label' => 'Other'],
                    ]],
                    ['field_key' => 'monthly_income', 'model_column' => 'monthly_income', 'label_en' => 'Monthly Income', 'label_bn' => 'মাসিক আয়', 'input_type' => 'number', 'is_private' => true, 'profile_display_format' => 'currency'],
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
                    'title_en' => 'Lifestyle & Health', 'title_bn' => 'জীবনধারা ও স্বাস্থ্য',
                    'icon' => 'heart-pulse', 'step' => 5, 'completion_weight' => 10,
                ],
                'fields' => [
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
                    ['field_key' => 'health_status', 'model_column' => 'health_status', 'label_en' => 'Health Status', 'label_bn' => 'স্বাস্থ্য অবস্থা', 'input_type' => 'select', 'options_en' => [
                        ['value' => 'healthy', 'label' => 'Healthy'],
                        ['value' => 'minor_condition', 'label' => 'Minor Condition'],
                        ['value' => 'disability', 'label' => 'Disability'],
                        ['value' => 'prefer_not_say', 'label' => 'Prefer not to say'],
                    ]],
                    ['field_key' => 'hobbies', 'model_column' => 'hobbies', 'label_en' => 'Hobbies', 'label_bn' => 'শখ', 'input_type' => 'textarea'],
                ],
            ],
            [
                'key' => 'family',
                'attributes' => [
                    'title_en' => 'Family Information', 'title_bn' => 'পারিবারিক তথ্য',
                    'icon' => 'users', 'step' => 6, 'completion_weight' => 10,
                ],
                'fields' => [
                    ['field_key' => 'father_profession', 'model_column' => 'father_profession', 'label_en' => "Father's Profession", 'label_bn' => 'পিতার পেশা', 'is_required' => true],
                    ['field_key' => 'mother_profession', 'model_column' => 'mother_profession', 'label_en' => "Mother's Profession", 'label_bn' => 'মাতার পেশা', 'is_required' => true],
                    ['field_key' => 'brothers', 'model_column' => 'brothers', 'label_en' => 'Number of Brothers', 'label_bn' => 'ভাইয়ের সংখ্যা', 'input_type' => 'number'],
                    ['field_key' => 'sisters', 'model_column' => 'sisters', 'label_en' => 'Number of Sisters', 'label_bn' => 'বোনের সংখ্যা', 'input_type' => 'number'],
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
                    ['field_key' => 'wife_in_veil', 'model_column' => 'wife_in_veil', 'label_en' => 'Expect Wife in Veil', 'label_bn' => 'স্ত্রীর পর্দা প্রত্যাশা', 'input_type' => 'yes_no', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'male']],
                    ['field_key' => 'wants_to_work', 'model_column' => 'wants_to_work', 'label_en' => 'Wants to Work After Marriage', 'label_bn' => 'বিবাহের পর কাজ করতে চান', 'input_type' => 'yes_no', 'conditional_logic' => ['field' => 'gender', 'operator' => '=', 'value' => 'female']],
                    ['field_key' => 'polygamy_open', 'model_column' => 'polygamy_open', 'label_en' => 'Open to Polygamy', 'label_bn' => 'বহুবিবাহে আগ্রহী', 'input_type' => 'yes_no'],
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
                    ['field_key' => 'guardian_name', 'model_column' => 'guardian_name', 'label_en' => 'Guardian Name', 'label_bn' => 'অভিভাবকের নাম', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'guardian_mobile', 'model_column' => 'guardian_mobile', 'label_en' => 'Guardian Mobile', 'label_bn' => 'অভিভাবকের মোবাইল', 'input_type' => 'phone', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'guardian_relationship', 'model_column' => 'guardian_relationship', 'label_en' => 'Guardian Relationship', 'label_bn' => 'অভিভাবকের সম্পর্ক', 'show_in_profile' => false],
                    ['field_key' => 'whatsapp_number', 'model_column' => 'whatsapp_number', 'label_en' => 'WhatsApp Number', 'label_bn' => 'হোয়াটসঅ্যাপ নম্বর', 'input_type' => 'phone', 'is_private' => true, 'show_in_profile' => false],
                    ['field_key' => 'biodata_visibility', 'model_column' => 'biodata_visibility', 'label_en' => 'Biodata Visibility', 'label_bn' => 'বায়োডাটা দৃশ্যমানতা', 'input_type' => 'select', 'show_in_profile' => false, 'options_en' => [
                        ['value' => 'public', 'label' => 'Public'],
                        ['value' => 'private', 'label' => 'Private'],
                        ['value' => 'admin_approved_only', 'label' => 'Admin Approved Only'],
                    ]],
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
