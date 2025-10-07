<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();

            // Step 1 fields
            $table->string('name');
            $table->enum('gender', ['male', 'female'])->nullable();

            // Step 2 fields (Email Verification)
            $table->string('email')->unique();
            $table->string('email_verification_code')->nullable();
            $table->boolean('is_email_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable(); // ✅ new field added

            // Step 3 fields (Mobile Verification)
            $table->string('country_code', 10)->nullable();
            $table->string('mobile_number', 20)->unique();
            $table->string('mobile_verification_code')->nullable();
            $table->boolean('is_mobile_verified')->default(false);

            // Step 4 fields (Password)
            $table->string('password');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
