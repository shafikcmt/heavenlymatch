<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CLEAN REPLACEMENT for all previous biodatas-related migrations.
 * Supersedes: 2025_09_11_*, 2026_05_03_000001_(biodatas)_, 2026_05_03_000002_*, 2026_05_20_000002_*
 *
 * PRUNING SUMMARY (35% column reduction):
 *   REMOVED: name, gender, profile_created_for (dup from registrations)
 *   REMOVED: height(str), weight(str)  →  height_cm, weight_kg (numeric)
 *   REMOVED: disease_description + diseases  →  health_status + health_details
 *   REMOVED: uncle_profession (zero matching value)
 *   REMOVED: clothes_above_ankles + mahram_nonmahram  →  clothing_style/hijab_info
 *   REMOVED: 20 education sub-columns  →  education_details JSON
 *   REMOVED: truth_testify, responsibility, parents_know (pledge UI, not data)
 *   REMOVED: groom_photo (string)  →  photos JSON
 *   REMOVED: partner_age, partner_height (strings)  →  numeric range fields
 *   REMOVED: biodata_type, completion_status, approval_status, privacy_consent (duplicates)
 *   REMOVED: brothers_info, sisters_info, prayers_qaza_weekly (rolled into related fields)
 *
 *   ADDED: about_me, profile_headline, is_nrb, mother_tongue
 *   ADDED: hijab_info, is_islamically_educated, wali_approval, sunni_scale
 *   ADDED: occupation_category, home_ownership, post_marriage_plan, polygamy_open
 *   ADDED: partner_district, partner_family_type, health_status, health_details
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biodatas', function (Blueprint $table) {

            // ── FK ────────────────────────────────────────────────────────
            $table->id();
            $table->string('registration_id', 20)->unique();
            $table->foreign('registration_id')
                ->references('registration_id')
                ->on('registrations')
                ->onDelete('cascade');

            // ── General Info ──────────────────────────────────────────────
            $table->enum('marital_status', ['never_married', 'married', 'divorced', 'widowed'])->nullable();
            $table->date('birth_date')->nullable();
            $table->unsignedSmallInteger('height_cm')->nullable();   // e.g. 165 = 5′5″
            $table->unsignedSmallInteger('weight_kg')->nullable();
            $table->enum('complexion', ['very_fair', 'fair', 'wheatish', 'medium', 'dark'])->nullable();
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->text('about_me')->nullable();                     // NEW: self-description
            $table->string('profile_headline', 200)->nullable();      // NEW: short tagline

            // ── Location ──────────────────────────────────────────────────
            $table->string('nationality', 60)->default('Bangladeshi');
            $table->string('division', 60)->nullable();
            $table->string('district', 60)->nullable();
            $table->string('upazila', 60)->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('village_area', 100)->nullable();
            $table->text('present_address')->nullable();
            $table->string('grew_up_in', 60)->nullable();            // e.g. 'Dhaka City', 'Village', 'Abroad'
            $table->string('residing_country', 60)->default('Bangladesh');
            $table->string('residing_city', 80)->nullable();
            $table->enum('visa_status', ['citizen', 'permanent_resident', 'work_visa', 'student_visa'])->nullable();
            $table->boolean('is_nrb')->default(false);               // NEW: non-resident Bangladeshi
            $table->string('mother_tongue', 50)->nullable();          // NEW: Bangla/Sylheti/Chittagong

            // ── Religion & Islamic Practice ───────────────────────────────
            $table->string('religion', 30)->default('Islam');
            $table->string('sect', 50)->nullable();                   // Hanafi/Shafi/Ahle Hadith/etc.
            $table->boolean('is_practicing')->default(true);
            $table->boolean('accepts_interfaith')->default(false);
            $table->enum('prayers_info', ['5_times', '4_times', 'sometimes', 'rarely', 'never'])->nullable();
            $table->enum('quran_recitation', ['fluent', 'basic', 'learning', 'no'])->nullable();
            $table->string('fiqh', 50)->nullable();
            $table->string('clothing_style', 100)->nullable();        // Niqab/Hijab/Traditional/etc.
            $table->string('beard_info', 50)->nullable();             // for males
            $table->string('hijab_info', 50)->nullable();             // NEW: for females (wears_niqab/hijab/no_hijab)
            $table->boolean('is_islamically_educated')->default(false); // NEW
            $table->text('beliefs_on_mazar')->nullable();
            $table->text('favorite_scholars')->nullable();
            $table->string('religious_work', 100)->nullable();
            $table->boolean('wali_approval')->nullable();             // NEW: Islamic mode
            $table->unsignedTinyInteger('sunni_scale')->nullable();   // NEW: 1-10 practicing scale

            // ── Education (collapsed from 20+ fields into 3) ──────────────
            $table->enum('education_method', ['general', 'islamic', 'both'])->nullable();
            $table->enum('highest_qualification', [
                'below_ssc', 'ssc', 'hsc', 'diploma', 'graduation',
                'post_graduation', 'phd', 'hafez', 'alim', 'fazil', 'kamil', 'other',
            ])->nullable();
            // JSON: {ssc:{year,group}, hsc:{year,group}, graduation:{subject,institution,year}, ...}
            $table->json('education_details')->nullable();

            // ── Professional ──────────────────────────────────────────────
            $table->string('occupation', 100)->nullable();
            $table->enum('occupation_category', [                     // NEW: for better search
                'business', 'service_govt', 'service_private', 'education',
                'medical', 'engineering', 'agriculture', 'student',
                'housewife', 'ngo', 'it', 'abroad_job', 'other',
            ])->nullable();
            $table->text('profession_details')->nullable();
            $table->unsignedInteger('monthly_income')->nullable();    // in BDT
            $table->enum('profession_halal_status', ['halal', 'not_sure', 'halal_alternative'])->nullable();

            // ── Family ────────────────────────────────────────────────────
            $table->string('father_name', 100)->nullable();
            $table->boolean('father_alive')->nullable();
            $table->string('father_profession', 100)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->boolean('mother_alive')->nullable();
            $table->string('mother_profession', 100)->nullable();
            $table->unsignedTinyInteger('brothers')->default(0);
            $table->unsignedTinyInteger('sisters')->default(0);
            $table->enum('family_type', ['joint', 'nuclear', 'flexible'])->nullable();
            $table->enum('family_financial_status', ['lower', 'lower_middle', 'middle', 'upper_middle', 'upper'])->nullable();
            $table->enum('home_ownership', ['own_house', 'rented', 'family_house', 'other'])->nullable(); // NEW
            $table->text('family_details')->nullable();
            $table->string('family_religious_condition', 100)->nullable();

            // ── Health ────────────────────────────────────────────────────
            // REPLACES disease_description + diseases (2 columns → 2 cleaner columns)
            $table->enum('health_status', ['healthy', 'minor_condition', 'disability', 'prefer_not_say'])->default('healthy');
            $table->text('health_details')->nullable();

            // ── Lifestyle ────────────────────────────────────────────────
            $table->enum('diet', ['halal_only', 'vegetarian', 'no_restriction'])->nullable();
            $table->enum('smoking', ['never', 'occasionally', 'regularly'])->default('never');
            $table->string('watch_entertainment', 50)->nullable();
            $table->text('hobbies')->nullable();
            $table->string('special_category', 100)->nullable();

            // ── Photos ────────────────────────────────────────────────────
            // JSON: [{path, is_primary, visibility:'public'|'members_only'|'blurred', uploaded_at}]
            $table->json('photos')->nullable();
            $table->boolean('photo_verified')->default(false);

            // ── Marriage Info ─────────────────────────────────────────────
            $table->boolean('guardian_agree')->nullable();
            $table->boolean('wife_in_veil')->nullable();
            $table->boolean('wife_study_allowed')->nullable();
            $table->boolean('wife_job_allowed')->nullable();
            $table->string('residence_after_marriage', 100)->nullable();
            $table->string('expect_gift_from_bride', 50)->nullable(); // mehr/no/flexible
            $table->string('post_marriage_plan', 100)->nullable();    // NEW: stay_bd/go_abroad/flexible
            $table->boolean('polygamy_open')->default(false);          // NEW: for males
            $table->unsignedTinyInteger('children_count')->nullable(); // 0 for never married

            // ── Partner Preferences (structured for matching engine) ───────
            $table->unsignedTinyInteger('partner_age_min')->nullable();
            $table->unsignedTinyInteger('partner_age_max')->nullable();
            $table->unsignedSmallInteger('partner_height_cm_min')->nullable();
            $table->unsignedSmallInteger('partner_height_cm_max')->nullable();
            $table->string('partner_complexion', 30)->nullable();
            $table->string('partner_marital_status', 30)->nullable();
            $table->string('partner_education', 60)->nullable();
            $table->string('partner_occupation_pref', 100)->nullable();
            $table->unsignedInteger('partner_income_min')->nullable();
            $table->unsignedInteger('partner_income_max')->nullable();
            $table->string('partner_religion', 30)->nullable();
            $table->string('partner_sect', 50)->nullable();
            $table->string('partner_nationality', 60)->nullable();
            $table->string('partner_residing_country', 60)->nullable();
            $table->string('partner_division', 60)->nullable();
            $table->string('partner_district', 60)->nullable();        // NEW
            $table->string('partner_family_type', 20)->nullable();     // NEW
            $table->text('partner_expectations')->nullable();

            // ── Contact / Guardian Reference ──────────────────────────────
            $table->string('guardian_mobile', 20)->nullable();
            $table->string('guardian_relationship', 50)->nullable();
            $table->string('guardian_email', 100)->nullable();

            // ── Admin & Moderation ────────────────────────────────────────
            $table->boolean('is_completed')->default(false);
            $table->unsignedTinyInteger('completeness_score')->default(0); // 0-100, auto-computed
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'hidden'])->default('draft');
            $table->text('admin_note')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('featured_at')->nullable();
            $table->unsignedTinyInteger('profile_score')->default(0);  // admin-assigned quality score

            // ── Activity Tracking ─────────────────────────────────────────
            $table->timestamp('last_active_at')->nullable();

            $table->timestamps();

            // ── Strategic Indexes (tuned to prevent shared-hosting CPU spikes) ──
            $table->index(['status', 'is_completed', 'is_featured'], 'idx_bio_status_complete');
            $table->index(['district', 'division', 'residing_country'], 'idx_bio_location');
            $table->index(['religion', 'sect', 'is_practicing'], 'idx_bio_religion');
            $table->index(['occupation_category', 'monthly_income'], 'idx_bio_occupation');
            $table->index(['marital_status', 'height_cm'], 'idx_bio_physical');
            $table->index(['completeness_score', 'last_active_at'], 'idx_bio_activity');
            $table->index(['is_nrb', 'residing_country'], 'idx_bio_nrb');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biodatas');
    }
};
