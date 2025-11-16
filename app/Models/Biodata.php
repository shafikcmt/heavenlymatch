<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biodata extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',

        // Step 1: General Info
        'marital_status',
        'birth_date',
        'height',
        'complexion',
        'weight',
        'blood_group',
        'nationality',

        // Step 2: Address
        'permanent_address',
        'village_area',
        'present_address',
        'grew_up',

        // Step 3: Education
        'education_method',
        'highest_qualification',
        'other_education',
        'ssc_year',
        'ssc_group',
        'hsc_year',
        'hsc_group',
        'diploma_subject',
        'diploma_medium',
        'diploma_institution',
        'diploma_year',
        'graduation_subject',
        'graduation_institution',
        'graduation_year',
        'postgraduation_subject',
        'postgraduation_institution',
        'postgraduation_year',
        'islamic_titles',
        'islamic_institution',
        'islamic_year',

        // Step 4: Family
        'father_name',
        'father_alive',
        'father_profession',
        'mother_name',
        'mother_alive',
        'mother_profession',
        'brothers',
        'sisters',
        'uncle_profession',
        'family_financial_status',
        'family_details',
        'family_religious_condition',

        // Step 5: Personal Info
        'clothing_style',
        'beard_info',
        'clothes_above_ankles',
        'niqab_since',
        'prays_five_times',
        'prayers_info',
        'mahram_nonmahram',
        'quran_recitation',
        'fiqh',
        'watch_entertainment',
        'diseases',
        'beliefs_on_mazar',
        'books_read',
        'special_category',
        'hobbies',
        'groom_photo',

        // Step 6: Occupation
        'occupation',
        'profession_details',
        'monthly_income',

        // Step 7: Marriage Info
        'guardian_agree',
        'wife_in_veil',
        'wife_study_allowed',
        'wife_job_allowed',
        'residence_after_marriage',
        'expect_gift_from_bride',

        // Step 8: Expected Partner
        'partner_age',
        'partner_complexion',
        'partner_height',
        'partner_education',
        'partner_district',
        'partner_marital_status',
        'partner_profession',
        'partner_financial_condition',
        'partner_expectations',

        // Step 9: Pledge
        'parents_know',
        'truth_testify',
        'responsibility',

        // Step 10: Contact
        'guardian_mobile',
        'guardian_relationship',
        'guardian_email',
        'is_completed',
    ];

    /**
     * Relationship: Biodata belongs to one Registration
     */
        public function registration()
        {
            return $this->belongsTo(Registration::class, 'registration_id', 'registration_id');
        }
}
