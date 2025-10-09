<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('biodatas', function (Blueprint $table) {
            $table->id(); 
            $table->string('registration_id')->unique(); 
           // Optional: add foreign key to registrations.registration_id
            $table->foreign('registration_id')
                ->references('registration_id')
                ->on('registrations')
                ->onDelete('cascade');

            // Step 1: General Info
            $table->string('marital_status')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('height')->nullable();
            $table->string('complexion')->nullable();
            $table->string('weight')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('nationality')->default('Bangladeshi');

            // Step 2: Address
            $table->string('permanent_address')->nullable();
            $table->string('village_area')->nullable();
            $table->string('present_address')->nullable();
            $table->string('grew_up')->nullable();

            // Step 3: Education
            $table->string('education_method')->nullable();   // Stores General / Islamic / Both
            $table->string('highest_qualification')->nullable(); // Stores the selected top qualification (SSC, Diploma, Hafez, etc.)
            $table->text('other_education')->nullable();      // Stores other educational qualifications entered in the text input
            $table->text('ssc_year')->nullable();             // Optional: for SSC passing year
            $table->text('ssc_group')->nullable();            // Optional: for SSC group
            $table->text('diploma_subject')->nullable();      // Optional: Diploma subject
            $table->text('diploma_medium')->nullable();       // Optional: medium studied after SSC
            $table->text('diploma_institution')->nullable();  // Optional: Diploma institution
            $table->text('diploma_year')->nullable();         // Optional: Diploma passing year
            $table->text('graduation_subject')->nullable();   // Optional: Bachelor subject
            $table->text('graduation_institution')->nullable();
            $table->text('graduation_year')->nullable();
            $table->text('postgraduation_subject')->nullable();
            $table->text('postgraduation_institution')->nullable();
            $table->text('postgraduation_year')->nullable();
            $table->text('islamic_titles')->nullable();      // Stores comma-separated Islamic titles if selected
            $table->text('islamic_institution')->nullable(); // Madrasa/Institution name for Islamic titles
            $table->text('islamic_year')->nullable();        // Passing year for Islamic titles

            // Step 4: Family
            $table->string('father_name')->nullable();
            $table->string('father_alive')->nullable();
            $table->text('father_profession')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_alive')->nullable();
            $table->text('mother_profession')->nullable();
            $table->integer('brothers')->default(0);
            $table->integer('sisters')->default(0);
            $table->text('uncle_profession')->nullable();
            $table->string('family_financial_status')->nullable();
            $table->text('family_details')->nullable();
            $table->text('family_religious_condition')->nullable();

            // Step 5: Personal Info
            $table->text('clothing_style')->nullable();
            $table->text('beard_info')->nullable();
            $table->string('clothes_above_ankles')->nullable();
            $table->string('prayers_info')->nullable();
            $table->string('mahram_nonmahram')->nullable();
            $table->string('quran_recitation')->nullable();
            $table->string('fiqh')->nullable();
            $table->string('watch_entertainment')->nullable();
            $table->text('diseases')->nullable();
            $table->text('beliefs_on_mazar')->nullable();
            $table->text('books_read')->nullable();
            $table->string('special_category')->nullable();
            $table->text('hobbies')->nullable();
            $table->string('groom_mobile')->nullable();
            $table->string('groom_photo')->nullable();

            // Step 6: Occupation
            $table->string('occupation')->nullable();
            $table->text('profession_details')->nullable();
            $table->integer('monthly_income')->nullable();

            // Step 7: Marriage Info
            $table->string('guardian_agree')->nullable();
            $table->string('wife_in_veil')->nullable();
            $table->string('wife_study_allowed')->nullable();
            $table->string('wife_job_allowed')->nullable();
            $table->string('residence_after_marriage')->nullable();
            $table->string('expect_gift_from_bride')->nullable();

            // Step 8: Expected Partner
            $table->string('partner_age')->nullable();
            $table->string('partner_complexion')->nullable();
            $table->string('partner_height')->nullable();
            $table->string('partner_education')->nullable();
            $table->string('partner_district')->nullable();
            $table->string('partner_marital_status')->nullable();
            $table->string('partner_profession')->nullable();
            $table->string('partner_financial_condition')->nullable();
            $table->text('partner_expectations')->nullable();

            // Step 9: Pledge
            $table->string('parents_know')->nullable();
            $table->string('truth_testify')->nullable();
            $table->string('responsibility')->nullable();

            // Step 10: Contact
            $table->string('groom_name')->nullable();
            $table->string('guardian_mobile')->nullable();
            $table->string('guardian_relationship')->nullable();
            $table->string('guardian_email')->nullable();
            $table->boolean('is_completed')->default(false);


            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('biodatas');
    }
};
