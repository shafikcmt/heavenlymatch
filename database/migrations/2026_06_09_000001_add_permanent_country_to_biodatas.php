<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Location step cleanup — Present / Permanent address workflow.
 *
 * Adds a permanent-country column so a permanent address can live abroad too
 * (the present country is already stored in `residing_country`). All other
 * present/permanent fields reuse existing columns.
 *
 * SAFETY: additive, nullable, guarded — existing rows untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('biodatas') && ! Schema::hasColumn('biodatas', 'permanent_country')) {
            Schema::table('biodatas', function (Blueprint $table) {
                $table->string('permanent_country', 60)->nullable()->after('residing_city');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('biodatas') && Schema::hasColumn('biodatas', 'permanent_country')) {
            Schema::table('biodatas', function (Blueprint $table) {
                $table->dropColumn('permanent_country');
            });
        }
    }
};
