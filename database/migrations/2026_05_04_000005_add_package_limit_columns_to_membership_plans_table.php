<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->integer('validity_days')->default(90)->after('duration_months');
            $table->integer('interest_express_limit')->default(-1)->after('validity_days');
            $table->integer('profile_show_limit')->default(-1)->after('interest_express_limit');
            $table->integer('image_upload_limit')->default(-1)->after('profile_show_limit');
        });

        DB::table('membership_plans')->orderBy('id')->get()->each(function ($plan) {
            $validity = ((int) $plan->duration_months === 12) ? 365 : ((int) $plan->duration_months * 30);
            [$interest, $profile, $image] = match (strtolower((string) $plan->name)) {
                'gold' => [100, 100, 50],
                'diamond' => [150, 150, 100],
                'platinum' => [-1, -1, -1],
                default => [50, 50, 20],
            };

            DB::table('membership_plans')->where('id', $plan->id)->update([
                'validity_days' => $validity,
                'interest_express_limit' => $interest,
                'profile_show_limit' => $profile,
                'image_upload_limit' => $image,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn(['validity_days', 'interest_express_limit', 'profile_show_limit', 'image_upload_limit']);
        });
    }
};
