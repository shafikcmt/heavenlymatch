<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds google_id and membership_plan_name to registrations, and makes gender/looking_for nullable.
 *
 * SAFE FOR FRESH INSTALL: skips entirely when the registrations table does not yet exist —
 * the clean 2026_06_01_000001 migration already includes these columns and nullable definitions.
 *
 * SAFE FOR EXISTING DATABASES: runs normally; hasColumn() guards prevent duplicate-column errors.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('registrations')) {
            // Fresh install — 2026_06_01_000001 already defines all these columns.
            return;
        }

        Schema::table('registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('registrations', 'google_id')) {
                $table->string('google_id', 100)->nullable()->unique()->after('registration_id');
            }

            if (! Schema::hasColumn('registrations', 'membership_plan_name')) {
                $table->string('membership_plan_name', 50)->nullable()->after('membership_plan_id');
            }

            // Make nullable only if current definition is NOT NULL.
            // The ->change() call is safe when doctrine/dbal is present (Laravel 11 includes it).
            // Wrapped in hasColumn guards to be idempotent.
            if (Schema::hasColumn('registrations', 'gender')) {
                $table->enum('gender', ['male', 'female'])->nullable()->change();
            }

            if (Schema::hasColumn('registrations', 'looking_for')) {
                $table->enum('looking_for', ['bride', 'groom'])->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('registrations')) {
            return;
        }

        Schema::table('registrations', function (Blueprint $table) {
            if (Schema::hasColumn('registrations', 'google_id')) {
                $table->dropUnique(['google_id']);
                $table->dropColumn('google_id');
            }

            if (Schema::hasColumn('registrations', 'membership_plan_name')) {
                $table->dropColumn('membership_plan_name');
            }
        });
    }
};
