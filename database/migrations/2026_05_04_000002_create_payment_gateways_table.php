<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('manual');
            $table->string('checkout_url')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('public_key')->nullable();
            $table->text('secret_key')->nullable();
            $table->boolean('sandbox')->default(true);
            $table->text('instructions')->nullable();
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'is_default', 'sort_order']);
        });

        DB::table('payment_gateways')->insert([
            [
                'name' => 'Manual Payment',
                'slug' => 'manual-payment',
                'type' => 'manual',
                'checkout_url' => null,
                'instructions' => 'Send payment to your configured merchant number, then submit the transaction ID from this page. Admin can verify and approve it from Settings > Recent payments.',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SSLCommerz',
                'slug' => 'sslcommerz',
                'type' => 'sslcommerz',
                'checkout_url' => null,
                'instructions' => 'Add your live or sandbox checkout URL, store ID and store password in admin settings to enable redirect checkout.',
                'is_active' => false,
                'is_default' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'bKash',
                'slug' => 'bkash',
                'type' => 'bkash',
                'checkout_url' => null,
                'instructions' => 'Add your bKash checkout URL and merchant credentials in admin settings to enable this gateway.',
                'is_active' => false,
                'is_default' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nagad',
                'slug' => 'nagad',
                'type' => 'nagad',
                'checkout_url' => null,
                'instructions' => 'Add your Nagad checkout URL and merchant credentials in admin settings to enable this gateway.',
                'is_active' => false,
                'is_default' => false,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
