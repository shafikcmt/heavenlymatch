<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Career step (Step 4) UX improvement — guided "Is your income halal?" field.
 *
 * Widens the profession_halal_status enum to add a 'prefer_not_say' option so the
 * field can be answered honestly without pressure. Purely additive: the existing
 * values (halal / not_sure / halal_alternative) and all stored rows are kept.
 *
 * SAFETY: ALTER ... MODIFY only ADDS an allowed value; no data is dropped.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('biodatas', 'profession_halal_status')) {
            return;
        }
        DB::statement(
            "ALTER TABLE `biodatas` MODIFY COLUMN `profession_halal_status` "
            . "ENUM('halal', 'not_sure', 'halal_alternative', 'prefer_not_say') NULL"
        );
    }

    public function down(): void
    {
        if (! Schema::hasColumn('biodatas', 'profession_halal_status')) {
            return;
        }
        // Fold any prefer_not_say rows back to not_sure before narrowing the enum
        // so the column constraint stays valid (no data lost, just remapped).
        DB::table('biodatas')
            ->where('profession_halal_status', 'prefer_not_say')
            ->update(['profession_halal_status' => 'not_sure']);

        DB::statement(
            "ALTER TABLE `biodatas` MODIFY COLUMN `profession_halal_status` "
            . "ENUM('halal', 'not_sure', 'halal_alternative') NULL"
        );
    }
};
