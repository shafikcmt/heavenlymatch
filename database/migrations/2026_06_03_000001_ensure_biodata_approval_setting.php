<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ensures the "Require Admin Approval for Biodata" setting exists.
 *
 * Reuses the existing system_settings table + key (system.profile_approval_required).
 * Idempotent and non-destructive: only inserts the row when it is missing so
 * databases that already hold the key (and any admin-chosen value) are untouched.
 * Default is '1' (enabled) so the admin-approval workflow stays the safe default.
 */
return new class extends Migration
{
    private const KEY = 'system.profile_approval_required';

    public function up(): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        $exists = DB::table('system_settings')->where('key', self::KEY)->exists();

        if (! $exists) {
            DB::table('system_settings')->insert([
                'key'        => self::KEY,
                'value'      => '1',
                'type'       => 'boolean',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // No-op: the setting is shared infrastructure; do not delete on rollback.
    }
};
