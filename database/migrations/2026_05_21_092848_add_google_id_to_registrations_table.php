<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds google_id to registrations so Google OAuth accounts can be linked.
 * Also relaxes gender and looking_for to nullable so OAuth registrations
 * can be created before the user sets their profile preferences in the wizard.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Google OAuth link — null = email/password account
            $table->string('google_id', 100)->nullable()->unique()->after('registration_id');

            // Denormalized plan name so dashboard queries avoid a JOIN to membership_plans.
            // Written by PaymentController when a payment is approved.
            $table->string('membership_plan_name', 50)->nullable()->after('membership_plan_id');

            // Allow null so Google OAuth can create an account before the user
            // fills in gender/preferences in the biodata wizard.
            $table->enum('gender', ['male', 'female'])->nullable()->change();
            $table->enum('looking_for', ['bride', 'groom'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropUnique(['google_id']);
            $table->dropColumn('google_id');
            $table->dropColumn('membership_plan_name');
            $table->enum('gender', ['male', 'female'])->nullable(false)->change();
            $table->enum('looking_for', ['bride', 'groom'])->nullable(false)->change();
        });
    }
};
