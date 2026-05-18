<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('duration_months')->default(3);
            $table->decimal('price', 12, 2)->default(0);
            $table->string('currency', 10)->default('BDT');
            $table->json('features')->nullable();
            $table->string('badge')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['duration_months', 'is_active', 'sort_order']);
        });

        $now = now();
        $rows = [
            ['Gold', 3, 3900, ['Unlimited messages and chat online', '40 verified mobile numbers'], null, false, 1],
            ['Diamond', 3, 6900, ['Priority messages and family support', '100 verified mobile numbers'], 'Most popular', true, 2],
            ['Platinum', 3, 9900, ['Profile boost and premium placement', 'Unlimited verified contacts'], null, false, 3],
            ['Gold', 6, 6900, ['Unlimited messages and chat online', '85 verified mobile numbers', 'Save more with 6 months access'], null, false, 4],
            ['Diamond', 6, 11900, ['Priority messages and family support', '220 verified mobile numbers', 'Better value for serious search'], 'Best value', true, 5],
            ['Platinum', 6, 16900, ['Profile boost and premium placement', 'Unlimited verified contacts', 'Family support for 6 months'], null, false, 6],
            ['Gold', 12, 11900, ['Unlimited messages and chat online', '180 verified mobile numbers', 'Full year contact access'], null, false, 7],
            ['Diamond', 12, 19900, ['Priority messages and family support', '500 verified mobile numbers', 'Premium help for 1 year'], 'Best value', true, 8],
            ['Platinum', 12, 29900, ['Profile boost and premium placement', 'Unlimited verified contacts', 'Top profile priority for 1 year'], null, false, 9],
        ];

        DB::table('membership_plans')->insert(array_map(function ($row) use ($now) {
            [$name, $duration, $price, $features, $badge, $popular, $order] = $row;

            return [
                'name' => $name,
                'slug' => Str::slug($name . '-' . $duration . '-months'),
                'duration_months' => $duration,
                'price' => $price,
                'currency' => 'BDT',
                'features' => json_encode($features),
                'badge' => $badge,
                'is_popular' => $popular,
                'is_active' => true,
                'sort_order' => $order,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $rows));
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};
