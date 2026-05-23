<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ensures photo-related columns exist in biodatas.
 *
 * SAFE FOR FRESH INSTALL: skips when biodatas does not yet exist —
 * the clean 2026_06_01_000003 migration already includes all these columns.
 *
 * SAFE FOR EXISTING DATABASES: hasColumn() guards prevent duplicate-column errors.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('biodatas')) {
            // Fresh install — 2026_06_01_000003 already defines all these columns.
            return;
        }

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
        // Intentionally empty — do not drop columns that may have pre-existed this migration.
    }
};
