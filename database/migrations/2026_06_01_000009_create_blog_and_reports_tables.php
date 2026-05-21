<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SEO Blog + User Reports tables.
 * Blog drives organic traffic via Islamic marriage tips, halal dating guides.
 * Reports enable community self-moderation.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─── Blog Posts ───────────────────────────────────────────────────────
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('registration_id', 20)->nullable(); // null = system/admin
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('featured_image')->nullable();
            $table->json('tags')->nullable();
            $table->string('category', 80)->default('general'); // general/islamic/advice/success-stories
            $table->string('meta_title', 200)->nullable();
            $table->text('meta_description')->nullable();
            $table->json('og_data')->nullable();               // {title, description, image}
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at'], 'idx_blog_status_pub');
            $table->index(['category', 'status'], 'idx_blog_cat_status');
            $table->fullText('title', 'ft_blog_title');
        });

        // ─── Profile Reports ──────────────────────────────────────────────────
        Schema::create('profile_reports', function (Blueprint $table) {
            $table->id();
            $table->string('reporter_id', 20);
            $table->string('reported_id', 20);
            $table->enum('reason', [
                'fake_profile', 'inappropriate_photos', 'harassment',
                'spam', 'scam', 'underage', 'other',
            ]);
            $table->text('details')->nullable();
            $table->json('evidence')->nullable();               // screenshot paths
            $table->enum('status', ['open', 'reviewing', 'resolved', 'dismissed'])->default('open');
            $table->string('resolved_by', 20)->nullable();     // admin registration_id
            $table->text('resolution_note')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['reported_id', 'status'], 'idx_report_profile_status');
            $table->index(['reporter_id'], 'idx_report_reporter');
            $table->index(['status', 'created_at'], 'idx_report_status_date');

            $table->foreign('reporter_id')->references('registration_id')->on('registrations')->onDelete('cascade');
            $table->foreign('reported_id')->references('registration_id')->on('registrations')->onDelete('cascade');
        });

        // ─── OTP / Verification Codes (unified) ───────────────────────────────
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->string('registration_id', 20)->nullable();
            $table->string('mobile')->nullable();              // for SMS OTP without account
            $table->string('email')->nullable();               // for email OTP
            $table->enum('type', ['email', 'mobile', 'login_2fa', 'guardian']);
            $table->string('code', 10);
            $table->string('token', 100)->nullable()->unique(); // for link-based verification
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['registration_id', 'type'], 'idx_otp_reg_type');
            $table->index(['expires_at', 'verified_at'], 'idx_otp_expiry');
        });

        // ─── Newsletter Subscribers ───────────────────────────────────────────
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 180)->unique();
            $table->string('name', 100)->nullable();
            $table->string('token', 80)->unique();             // unsubscribe token
            $table->boolean('is_active')->default(true);
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('unsubscribed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('verification_codes');
        Schema::dropIfExists('profile_reports');
        Schema::dropIfExists('blog_posts');
    }
};
