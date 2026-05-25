<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table): void {
            // Generic social-provider fields — supports any Socialite driver
            $table->string('provider_name')->nullable()->after('google_id');
            $table->string('provider_id')->nullable()->after('provider_name');
            $table->string('avatar_url', 500)->nullable()->after('provider_id');

            // Fast lookup when user re-authenticates via same provider
            $table->index(['provider_name', 'provider_id'], 'idx_social_provider');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table): void {
            $table->dropIndex('idx_social_provider');
            $table->dropColumn(['provider_name', 'provider_id', 'avatar_url']);
        });
    }
};
