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

        // Education & Professional
        'education_method',
        'highest_qualification',
        'education_details',
        'occupation',
        'occupation_category',
        'profession_details',
        'monthly_income',
        'profession_halal_status',

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

        // Contact / Guardian
        'guardian_mobile',
        'guardian_relationship',
        'guardian_email',
        'whatsapp_number',
        'contact_privacy',

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

        // Arrays/JSON
        'photos'            => 'array',
        'education_details' => 'array',
        'brothers_details'  => 'array',
        'sisters_details'   => 'array',

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

        // Dates
        'birth_date'     => 'date',
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
