<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasProviderName = Schema::hasColumn('registrations', 'provider_name');
        $hasProviderId   = Schema::hasColumn('registrations', 'provider_id');
        $hasAvatarUrl    = Schema::hasColumn('registrations', 'avatar_url');

        Schema::table('registrations', function (Blueprint $table) use ($hasProviderName, $hasProviderId, $hasAvatarUrl): void {
            // VARCHAR(32) is sufficient for provider names (google, facebook, etc.)
            if ($hasProviderName) {
                $table->string('provider_name', 32)->nullable()->change();
            } else {
                $table->string('provider_name', 32)->nullable()->after('google_id');
            }

            // VARCHAR(191) fits the 767-byte InnoDB index limit on utf8mb4
            if ($hasProviderId) {
                $table->string('provider_id', 191)->nullable()->change();
            } else {
                $table->string('provider_id', 191)->nullable()->after('provider_name');
            }

            // TEXT: no length cap, no index — safe for any OAuth avatar URL
            if (! $hasAvatarUrl) {
                $table->text('avatar_url')->nullable()->after('provider_id');
            }
        });

        // Add composite index only after column lengths are safe
        $indexExists = collect(DB::select("
            SELECT INDEX_NAME
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'registrations'
              AND INDEX_NAME   = 'idx_social_provider'
        "))->isNotEmpty();

        if (! $indexExists) {
            Schema::table('registrations', function (Blueprint $table): void {
                $table->index(['provider_name', 'provider_id'], 'idx_social_provider');
            });
        }
    }

    public function down(): void
    {
        $indexExists = collect(DB::select("
            SELECT INDEX_NAME
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'registrations'
              AND INDEX_NAME   = 'idx_social_provider'
        "))->isNotEmpty();

        if ($indexExists) {
            Schema::table('registrations', function (Blueprint $table): void {
                $table->dropIndex('idx_social_provider');
            });
        }

        Schema::table('registrations', function (Blueprint $table): void {
            foreach (['provider_name', 'provider_id', 'avatar_url'] as $col) {
                if (Schema::hasColumn('registrations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
