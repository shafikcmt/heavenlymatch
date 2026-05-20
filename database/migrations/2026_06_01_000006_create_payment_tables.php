<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Commerce & Monetization tables.
 * Supersedes: 2026_05_04_000002_*, 2026_05_04_000003_*
 *
 * Tables: payment_gateways, payment_transactions, biodata_unlocks, profile_boosts
 *
 * FIX: payment_transactions now uses registration_id (varchar HM000001)
 *      instead of the Laravel foreignId integer inconsistency.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─── Payment Gateways (admin-configured) ──────────────────────────
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('slug', 100)->unique();
            $table->string('type', 30)->default('manual'); // manual/sslcommerz/bkash/nagad/stripe
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

            $table->index(['is_active', 'is_default', 'sort_order'], 'idx_gw_active_default');
        });

        // ─── Payment Transactions ─────────────────────────────────────────
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('registration_id', 20)->nullable();        // HM000001 format
            $table->unsignedBigInteger('membership_plan_id')->nullable();
            $table->unsignedBigInteger('payment_gateway_id')->nullable();
            $table->string('transaction_no', 60)->unique();
            $table->string('external_transaction_id', 100)->nullable(); // gateway ref
            $table->string('plan_name', 80);
            $table->string('gateway_name', 80)->nullable();
            $table->unsignedSmallInteger('duration_months')->default(3);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('BDT');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_email', 180)->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->text('reference_note')->nullable();
            $table->json('payload')->nullable();                       // full gateway response
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['registration_id', 'status'], 'idx_txn_reg_status');
            $table->index(['status', 'paid_at'], 'idx_txn_status_paid');

            $table->foreign('registration_id')
                ->references('registration_id')
                ->on('registrations')
                ->nullOnDelete();

            $table->foreign('membership_plan_id')
                ->references('id')
                ->on('membership_plans')
                ->nullOnDelete();

            $table->foreign('payment_gateway_id')
                ->references('id')
                ->on('payment_gateways')
                ->nullOnDelete();
        });

        // ─── Biodata Unlock Log (pay-per-contact) ─────────────────────────
        Schema::create('biodata_unlocks', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 20);
            $table->string('unlocked_profile_id', 20);
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->timestamp('unlocked_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();             // null = permanent

            $table->unique(['user_id', 'unlocked_profile_id'], 'uq_unlock_pair');
            $table->index('user_id', 'idx_unlock_user');

            $table->foreign('user_id')->references('registration_id')->on('registrations')->onDelete('cascade');
        });

        // ─── Profile Boost Purchases ──────────────────────────────────────
        Schema::create('profile_boosts', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 20);
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedSmallInteger('duration_hours')->default(24);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_active', 'expires_at'], 'idx_boost_user_active');

            $table->foreign('user_id')->references('registration_id')->on('registrations')->onDelete('cascade');
        });

        // ─── Seed default gateways ─────────────────────────────────────────
        $now = now();
        DB::table('payment_gateways')->insert([
            ['name' => 'Manual Payment', 'slug' => 'manual-payment', 'type' => 'manual',
             'instructions' => 'Send payment to the configured merchant number, then submit your transaction ID. Admin verifies from Settings > Payments.',
             'is_active' => true,  'is_default' => true,  'sandbox' => false, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'bKash',          'slug' => 'bkash',          'type' => 'bkash',
             'instructions' => 'Add bKash merchant credentials in Admin > Settings.',
             'is_active' => false, 'is_default' => false, 'sandbox' => true,  'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Nagad',          'slug' => 'nagad',          'type' => 'nagad',
             'instructions' => 'Add Nagad merchant credentials in Admin > Settings.',
             'is_active' => false, 'is_default' => false, 'sandbox' => true,  'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'SSLCommerz',     'slug' => 'sslcommerz',     'type' => 'sslcommerz',
             'instructions' => 'Add SSLCommerz Store ID and Store Password in Admin > Settings.',
             'is_active' => false, 'is_default' => false, 'sandbox' => true,  'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_boosts');
        Schema::dropIfExists('biodata_unlocks');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payment_gateways');
    }
};
