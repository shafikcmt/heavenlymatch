<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * CLEAN REPLACEMENT for 2026_05_04_000001_* and 2026_05_04_000005_*
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('slug', 100)->unique();
            $table->unsignedSmallInteger('duration_months')->default(3);
            $table->decimal('price', 12, 2)->default(0);
            $table->string('currency', 10)->default('BDT');
            $table->json('features')->nullable();

            // ── Limits & Quotas ───────────────────────────────────────────
            $table->unsignedInteger('contact_view_limit')->default(0);       // 0 = unlimited
            $table->unsignedInteger('message_limit')->default(0);
            $table->unsignedInteger('shortlist_limit')->default(50);
            $table->unsignedInteger('profile_boost_hours')->default(0);
            $table->boolean('can_see_photos')->default(true);
            $table->boolean('can_send_interest')->default(true);
            $table->boolean('priority_placement')->default(false);
            $table->boolean('family_support')->default(false);

            // ── Display ───────────────────────────────────────────────────
            $table->string('badge', 50)->nullable();
            $table->string('color_hex', 10)->default('#4F46E5');
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['is_active', 'sort_order'], 'idx_plan_active_order');
        });

        // Seed default plans
        $now  = now();
        $plans = [
            // [name, months, price, contacts, messages, boost_hrs, priority, family_support, badge, popular, order]
            ['Gold',     3,  3900,  40,  200,  0,  false, false, null,           false, 1],
            ['Diamond',  3,  6900, 100,    0,  24, true,  true,  'Most Popular', true,  2],
            ['Platinum', 3,  9900,   0,    0,  72, true,  true,  null,           false, 3],
            ['Gold',     6,  6900,  85,  400,  0,  false, false, null,           false, 4],
            ['Diamond',  6, 11900, 220,    0,  48, true,  true,  'Best Value',   true,  5],
            ['Platinum', 6, 16900,   0,    0, 144, true,  true,  null,           false, 6],
            ['Gold',    12, 11900, 180,  900,  0,  false, false, null,           false, 7],
            ['Diamond', 12, 19900, 500,    0,  96, true,  true,  'Best Value',   true,  8],
            ['Platinum',12, 29900,   0,    0, 288, true,  true,  null,           false, 9],
        ];

        foreach ($plans as [$name, $months, $price, $contacts, $msgs, $boost, $priority, $family, $badge, $popular, $order]) {
            DB::table('membership_plans')->insert([
                'name'                 => $name,
                'slug'                 => Str::slug("{$name}-{$months}-months"),
                'duration_months'      => $months,
                'price'                => $price,
                'currency'             => 'BDT',
                'contact_view_limit'   => $contacts,
                'message_limit'        => $msgs,
                'shortlist_limit'      => 50,
                'profile_boost_hours'  => $boost,
                'priority_placement'   => $priority,
                'family_support'       => $family,
                'badge'                => $badge,
                'is_popular'           => $popular,
                'is_active'            => true,
                'sort_order'           => $order,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};
