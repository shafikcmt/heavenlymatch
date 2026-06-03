<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ensures the "User Verification Control" settings exist:
 *   - system.require_email_verification
 *   - system.require_phone_verification
 *
 * Reuses the existing system_settings table. Idempotent and non-destructive:
 * only inserts a row when missing, so any admin-chosen value is preserved.
 * Default is '1' (enabled) so the safe verification workflow stays on.
 */
return new class extends Migration
{
    private const KEYS = [
        'system.require_email_verification',
        'system.require_phone_verification',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        foreach (self::KEYS as $key) {
            $exists = DB::table('system_settings')->where('key', $key)->exists();

            if (! $exists) {
                DB::table('system_settings')->insert([
                    'key'        => $key,
                    'value'      => '1',
                    'type'       => 'boolean',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // No-op: shared settings infrastructure; do not delete on rollback.
    }
};
