<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biodata extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',

        // General Info
        'marital_status',
        'birth_date',
        'height_cm',
        'weight_kg',
        'complexion',
        'blood_group',
        'about_me',
        'profile_headline',

        // Location
        'nationality',
        'division',
        'district',
        'upazila',
        'permanent_address',
        'village_area',
        'present_address',
        'grew_up_in',
        'residing_country',
        'residing_city',
        'visa_status',
        'is_nrb',
        'mother_tongue',
        // Granular current address (Phase A)
        'current_division',
        'current_district',
        'current_upazila',
        'current_area',
        'same_as_permanent',

        // Religion & Islamic Practice
        'religion',
        'sect',
        'is_practicing',
        'accepts_interfaith',
        'prayers_info',
        'quran_recitation',
        'fiqh',
        'clothing_style',
        'beard_info',
        'hijab_info',
        'is_islamically_educated',
        'beliefs_on_mazar',
        'favorite_scholars',
        'religious_work',
        'wali_approval',
        'sunni_scale',
        // Deen detail (Phase A)
        'prayer_start_age',
        'weekly_missed_prayers',
        'mahram_practice',
        'islamic_books_read',
        'deen_work_details',
        'social_media_usage',
        'beard_since',
        'pants_above_ankle',
        'niqab_since',
        'purdah_details',

        // Education & Professional
        'education_method',
        'education_medium',
        'highest_qualification',
        'education_details',
        'occupation',
        'occupation_category',
        'profession_details',
        'monthly_income',
        'profession_halal_status',
        // Professional detail (Phase A)
        'income_type',
        'income_privacy',
        'workplace_type',
        'future_career_plan',

        // Family
        'father_name',
        'father_alive',
        'father_profession',
        'mother_name',
        'mother_alive',
        'mother_profession',
        'brothers',
        'sisters',
        'brothers_details',
        'sisters_details',
        'family_type',
        'family_financial_status',
        'home_ownership',
        'family_details',
        'family_religious_condition',
        // Family extras (Phase A)
        'uncle_profession',
        'family_assets_details',
        'guardian_name',
        'guardian_whatsapp',

        // Health
        'health_status',
        'health_details',

        // Lifestyle
        'diet',
        'smoking',
        'watch_entertainment',
        'hobbies',
        'special_category',

        // Photos
        'photos',
        'photo_verified',

        // Marriage Info
        'guardian_agree',
        'wife_in_veil',
        'wife_study_allowed',
        'wife_job_allowed',
        'residence_after_marriage',
        'expect_gift_from_bride',
        'post_marriage_plan',
        'polygamy_open',
        'children_count',
        'has_children',
        'children_live_with',
        'children_notes',
        // Marriage thoughts + intentions (Phase A)
        'why_getting_married',
        'marriage_thoughts',
        'marriage_timeline',
        'gift_expectation_details',
        'wants_to_work',
        'continue_study',
        'continue_job',
        'preferred_living',
        // Marital-status conditionals (Phase A)
        'marital_substatus',
        'previous_marriage_date',
        'divorce_date',
        'divorce_reason',
        'spouse_death_date',
        'spouse_death_reason',
        'child_acceptance_expectation',
        'reason_for_second_marriage',
        'current_wife_count',
        'current_family_consent',
        'first_wife_knows',
        'second_marriage_living',

        // Partner Preferences
        'partner_age_min',
        'partner_age_max',
        'partner_height_cm_min',
        'partner_height_cm_max',
        'partner_complexion',
        'partner_marital_status',
        'partner_education',
        'partner_occupation_pref',
        'partner_income_min',
        'partner_income_max',
        'partner_religion',
        'partner_sect',
        'partner_nationality',
        'partner_residing_country',
        'partner_division',
        'partner_district',
        'partner_family_type',
        'partner_expectations',
        // Partner extras (Phase A)
        'partner_economic_status',
        'partner_deen_practice',
        'partner_special_qualities',
        'partner_deal_breakers',
        'partner_districts',

        // Contact / Guardian
        'guardian_mobile',
        'guardian_relationship',
        'guardian_email',
        'whatsapp_number',
        'contact_privacy',
        // Contact / privacy extras (Phase A)
        'contact_person_name',
        'biodata_visibility',
        'allow_shortlist',
        'allow_contact_request',
        // Commitment / Declaration (Phase A)
        'guardian_knows_biodata',
        'info_truthful_confirmed',
        'accept_liability_terms',

        // Admin-governed custom fields (Phase E — values for admin-created fields)
        'custom_fields',

        // Admin & Moderation
        'status',
        'is_completed',
        'completeness_score',
        'admin_note',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'is_featured',
        'featured_at',
        'profile_score',
        'last_active_at',
    ];

    protected $casts = [
        // Booleans
        'is_completed'            => 'boolean',
        'is_featured'             => 'boolean',
        'photo_verified'          => 'boolean',
        'is_practicing'           => 'boolean',
        'accepts_interfaith'      => 'boolean',
        'is_islamically_educated' => 'boolean',
        'wali_approval'           => 'boolean',
        'is_nrb'                  => 'boolean',
        'father_alive'            => 'boolean',
        'mother_alive'            => 'boolean',
        'guardian_agree'          => 'boolean',
        'wife_in_veil'            => 'boolean',
        'wife_study_allowed'      => 'boolean',
        'wife_job_allowed'        => 'boolean',
        'polygamy_open'           => 'boolean',
        'has_children'            => 'boolean',
        // Phase A booleans
        'same_as_permanent'       => 'boolean',
        'pants_above_ankle'       => 'boolean',
        'wants_to_work'           => 'boolean',
        'continue_study'          => 'boolean',
        'continue_job'            => 'boolean',
        'current_family_consent'  => 'boolean',
        'first_wife_knows'        => 'boolean',
        'allow_shortlist'         => 'boolean',
        'allow_contact_request'   => 'boolean',
        'guardian_knows_biodata'  => 'boolean',
        'info_truthful_confirmed' => 'boolean',
        'accept_liability_terms'  => 'boolean',

        // Arrays/JSON
        'photos'            => 'array',
        'education_details' => 'array',
        'brothers_details'  => 'array',
        'sisters_details'   => 'array',
        'partner_districts' => 'array',
        'custom_fields'     => 'array',

        // Integers
        'completeness_score'    => 'integer',
        'height_cm'             => 'integer',
        'weight_kg'             => 'integer',
        'brothers'              => 'integer',
        'sisters'               => 'integer',
        'monthly_income'        => 'integer',
        'partner_age_min'       => 'integer',
        'partner_age_max'       => 'integer',
        'partner_height_cm_min' => 'integer',
        'partner_height_cm_max' => 'integer',
        'partner_income_min'    => 'integer',
        'partner_income_max'    => 'integer',
        'children_count'        => 'integer',
        'sunni_scale'           => 'integer',
        'profile_score'         => 'integer',
        'current_wife_count'    => 'integer',

        // Dates
        'birth_date'             => 'date',
        'previous_marriage_date' => 'date',
        'divorce_date'           => 'date',
        'spouse_death_date'      => 'date',
        'approved_at'    => 'datetime',
        'rejected_at'    => 'datetime',
        'featured_at'    => 'datetime',
        'last_active_at' => 'datetime',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id', 'registration_id');
    }
}
