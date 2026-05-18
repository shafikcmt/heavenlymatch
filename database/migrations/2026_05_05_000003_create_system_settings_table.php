<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->string('type')->default('string');
                $table->timestamps();
            });
        }

        $now = now();
        $defaults = [
            ['key' => 'general.site_name', 'value' => 'HeavenlyMatch', 'type' => 'string'],
            ['key' => 'general.site_tagline', 'value' => 'The ultimate matrimony platform', 'type' => 'string'],
            ['key' => 'general.primary_email', 'value' => config('mail.from.address'), 'type' => 'string'],
            ['key' => 'general.currency', 'value' => 'BDT', 'type' => 'string'],
            ['key' => 'general.timezone', 'value' => config('app.timezone', 'UTC'), 'type' => 'string'],
            ['key' => 'system.enable_registration', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'system.email_verification_required', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'system.enable_membership_payment', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'system.phone_verification_required', 'value' => '0', 'type' => 'boolean'],
            ['key' => 'system.profile_approval_required', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'system.default_user_status', 'value' => 'pending', 'type' => 'string'],
            ['key' => 'system.free_profile_show_limit', 'value' => '10', 'type' => 'string'],
            ['key' => 'notification.mail_from_name', 'value' => 'HeavenlyMatch', 'type' => 'string'],
            ['key' => 'notification.mail_from_email', 'value' => config('mail.from.address'), 'type' => 'string'],
            ['key' => 'seo.meta_title', 'value' => 'HeavenlyMatch', 'type' => 'string'],
            ['key' => 'seo.meta_description', 'value' => 'Find trusted matrimonial matches with HeavenlyMatch.', 'type' => 'string'],
            ['key' => 'frontend.hero_title', 'value' => 'Begin Your Search for an Ideal Match', 'type' => 'string'],
            ['key' => 'frontend.hero_subtitle', 'value' => 'Find verified profiles and meaningful connections.', 'type' => 'string'],
            ['key' => 'maintenance.enabled', 'value' => '0', 'type' => 'boolean'],
            ['key' => 'gdpr.enabled', 'value' => '0', 'type' => 'boolean'],
            ['key' => 'gdpr.button_text', 'value' => 'Accept', 'type' => 'string'],
            ['key' => 'robots.txt', 'value' => "User-agent: *\nAllow: /", 'type' => 'code'],
            ['key' => 'sitemap.xml', 'value' => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n</urlset>", 'type' => 'code'],
        ];

        foreach ($defaults as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, ['updated_at' => $now, 'created_at' => $now])
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
