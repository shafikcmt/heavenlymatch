<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a timestamp recording WHEN a mobile number was OTP-verified.
 * The registrations table already has `mobile_number`, `country_code`,
 * `mobile_verification_code` and the `is_mobile_verified` boolean flag —
 * this only adds the missing timestamp. Nullable, so existing users are untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('registrations', 'mobile_verified_at')) {
                $table->timestamp('mobile_verified_at')->nullable()->after('is_mobile_verified');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (Schema::hasColumn('registrations', 'mobile_verified_at')) {
                $table->dropColumn('mobile_verified_at');
            }
        });
    }
};
