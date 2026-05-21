<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Safe migration: adds photo-related columns to the biodatas table only if
 * they don't already exist. The clean schema (2026_06_01_000003) includes
 * these columns; this migration covers installations that ran the older
 * incremental migrations instead of the clean rewrite.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biodatas', function (Blueprint $table) {
            if (! Schema::hasColumn('biodatas', 'photos')) {
                $table->json('photos')->nullable()->after('special_category');
            }

            if (! Schema::hasColumn('biodatas', 'photo_verified')) {
                $table->boolean('photo_verified')->default(false)->after('photos');
            }

            if (! Schema::hasColumn('biodatas', 'completeness_score')) {
                $table->unsignedTinyInteger('completeness_score')->default(0)->after('photo_verified');
            }

            if (! Schema::hasColumn('biodatas', 'last_active_at')) {
                $table->timestamp('last_active_at')->nullable()->after('completeness_score');
            }
        });
    }

    public function down(): void
    {
        // Intentionally empty — do not drop columns that may have existed before this migration.
    }
};
