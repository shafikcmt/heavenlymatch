<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CLEAN REPLACEMENT for all previous registrations-related migrations.
 * Supersedes: 2025_09_09_*, 2026_05_03_*, 2026_05_04_000004_*, 2026_05_20_000001_*
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {

            // ── Core Identity ─────────────────────────────────────────────
            $table->id();
            $table->string('registration_id', 20)->unique();      // HM000001
            $table->string('name', 100);
            $table->enum('gender', ['male', 'female']);
            $table->enum('profile_created_for', ['self', 'son', 'daughter', 'brother', 'sister', 'relative'])->default('self');
            $table->enum('looking_for', ['bride', 'groom']);

            // ── Email Auth ────────────────────────────────────────────────
            $table->string('email', 180)->unique();
            $table->string('email_verification_code', 10)->nullable();
            $table->string('email_verification_token', 100)->nullable()->unique();
            $table->timestamp('email_verification_sent_at')->nullable();
            $table->boolean('is_email_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();

            // ── Mobile Auth ───────────────────────────────────────────────
            $table->string('country_code', 10)->default('+880');
            $table->string('mobile_number', 20)->nullable()->unique();
            $table->string('mobile_verification_code', 10)->nullable();
            $table->boolean('is_mobile_verified')->default(false);

            // ── Password & Session ────────────────────────────────────────
            $table->string('password');
            $table->rememberToken();

            // ── Platform Mode ─────────────────────────────────────────────
            // 'general' = Shaadi-style | 'islamic' = strict halal/Ordeekdin mode
            $table->enum('platform_mode', ['general', 'islamic'])->default('general');
            $table->string('preferred_language', 10)->default('bn');

            // ── Membership ────────────────────────────────────────────────
            $table->unsignedBigInteger('membership_plan_id')->nullable();
            $table->enum('membership_status', ['free', 'active', 'expired', 'cancelled'])->default('free');
            $table->timestamp('membership_started_at')->nullable();
            $table->timestamp('membership_expires_at')->nullable();

            // ── Roles & Account Status ────────────────────────────────────
            $table->string('role', 30)->default('user');
            $table->boolean('is_admin')->default(false);
            $table->enum('account_status', ['active', 'inactive', 'suspended', 'banned'])->default('active');
            $table->timestamp('blocked_at')->nullable();
            $table->text('blocked_reason')->nullable();

            // ── Identity Verification ─────────────────────────────────────
            $table->string('nid_number', 20)->nullable();
            $table->string('nid_image_front')->nullable();
            $table->string('nid_image_back')->nullable();
            $table->string('passport_number', 20)->nullable();
            $table->string('passport_image')->nullable();
            $table->enum('identity_verification_status', ['unverified', 'pending_review', 'verified', 'rejected'])->default('unverified');
            $table->timestamp('identity_verified_at')->nullable();
            $table->unsignedBigInteger('identity_verified_by')->nullable();
            $table->text('identity_rejection_reason')->nullable();

            // ── Photo Privacy ─────────────────────────────────────────────
            // 'public' = all | 'members_only' = accepted connections | 'blurred' = always blurred
            $table->enum('photo_visibility', ['public', 'members_only', 'blurred'])->default('members_only');

            // ── Boost (Monetization) ──────────────────────────────────────
            $table->boolean('is_boosted')->default(false);
            $table->timestamp('boost_expires_at')->nullable();
            $table->unsignedInteger('profile_views_count')->default(0);

            // ── 2FA Security ──────────────────────────────────────────────
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();

            // ── Account Lifecycle ─────────────────────────────────────────
            $table->timestamp('terms_accepted_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamp('deletion_requested_at')->nullable();

            $table->timestamps();

            // ── Strategic Indexes ─────────────────────────────────────────
            $table->index(['account_status', 'platform_mode'], 'idx_reg_status_mode');
            $table->index(['identity_verification_status'], 'idx_reg_id_verify');
            $table->index(['is_boosted', 'boost_expires_at'], 'idx_reg_boost');
            $table->index(['membership_status', 'membership_expires_at'], 'idx_reg_membership');
            $table->index(['role', 'is_admin'], 'idx_reg_role');
            $table->index(['gender', 'account_status'], 'idx_reg_gender_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
