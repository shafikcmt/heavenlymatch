<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\Registration;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::allSettings();
        $settingsCards = $this->settingsCards($settings);

        return view('admin.settings.index', compact('settings', 'settingsCards'));
    }

    public function packages()
    {
        $plans = Schema::hasTable('membership_plans')
            ? MembershipPlan::orderBy('sort_order')->orderBy('id')->get()
            : collect();

        return view('admin.packages', compact('plans'));
    }

    public function edit(string $section)
    {
        $section = Str::slug($section);
        $settings = SystemSetting::allSettings();
        $settingsCards = $this->settingsCards($settings);

        abort_unless(array_key_exists($section, $settingsCards), 404);

        $sectionMeta = $settingsCards[$section];
        $fieldDefinitions = $this->settingFields()[$section] ?? [];
        $gateways = collect();
        $automaticGateways = collect();
        $manualGateways = collect();
        $payments = collect();
        $systemFeatureRows = collect();
        $activeGatewayTab = request('tab') === 'manual' ? 'manual' : 'automatic';
        $gatewaySearch = trim((string) request('search', ''));

        if ($section === 'payment-gateways') {
            $gatewayQuery = Schema::hasTable('payment_gateways')
                ? PaymentGateway::query()->orderByDesc('is_default')->orderBy('sort_order')->orderBy('name')
                : null;

            if ($gatewayQuery && $gatewaySearch !== '') {
                $gatewayQuery->where('name', 'like', '%' . $gatewaySearch . '%');
            }

            $gateways = $gatewayQuery ? $gatewayQuery->get() : collect();
            $automaticGateways = $gateways->filter(fn ($gateway) => $gateway->type !== 'manual')->values();
            $manualGateways = $gateways->filter(fn ($gateway) => $gateway->type === 'manual')->values();

            $payments = Schema::hasTable('payment_transactions')
                ? PaymentTransaction::with(['registration', 'plan', 'gateway'])->latest('id')->take(20)->get()
                : collect();
        }

        if ($section === 'system-configuration') {
            $systemFeatureRows = collect($this->systemFeatureRows($settings));
        }

        return view('admin.settings.section', compact(
            'section',
            'settings',
            'settingsCards',
            'sectionMeta',
            'fieldDefinitions',
            'gateways',
            'automaticGateways',
            'manualGateways',
            'payments',
            'systemFeatureRows',
            'activeGatewayTab',
            'gatewaySearch'
        ));
    }

    public function update(Request $request, string $section)
    {
        $section = Str::slug($section);
        $fields = $this->settingFields()[$section] ?? null;

        abort_unless($fields !== null, 404);

        $rules = $this->settingsValidationRules($fields);
        $data = $request->validate($rules);
        $payload = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $key = $field['key'];
            $type = $field['type'] ?? 'text';

            if ($type === 'file') {
                if ($request->hasFile($name)) {
                    $payload[$key] = [
                        'value' => $this->storeUploadedSettingFile($request->file($name), $section),
                        'type' => 'file',
                    ];
                }
                continue;
            }

            if ($type === 'checkbox') {
                $payload[$key] = [
                    'value' => $request->boolean($name) ? '1' : '0',
                    'type' => 'boolean',
                ];
                continue;
            }

            $payload[$key] = [
                'value' => $data[$name] ?? '',
                'type' => in_array($type, ['textarea', 'code', 'json'], true) ? $type : 'string',
            ];
        }

        SystemSetting::setMany($payload);
        $this->writeGeneratedPublicFiles($payload);

        return redirect()->route('admin.settings.edit', $section)->with('success', $this->settingsCards()[$section]['title'] . ' updated successfully.');
    }

    public function toggleSystemFeature(Request $request, string $feature)
    {
        $feature = Str::slug($feature);
        $row = collect($this->systemFeatureRows(SystemSetting::allSettings()))->firstWhere('slug', $feature);

        abort_unless($row, 404);

        $current = SystemSetting::bool($row['key'], (bool) ($row['default'] ?? false));
        $nextValue = $current ? '0' : '1';

        SystemSetting::setValue($row['key'], $nextValue, 'boolean');

        return back()->with('success', $row['title'] . ' ' . ($nextValue === '1' ? 'enabled' : 'disabled') . ' successfully.');
    }

    public function storePlan(Request $request)
    {
        $data = $this->preparePlanData($request);
        MembershipPlan::create($data);

        return back()->with('success', 'Membership package added successfully.');
    }

    public function updatePlan(Request $request, MembershipPlan $plan)
    {
        $data = $this->preparePlanData($request, $plan);
        $plan->update($data);

        return back()->with('success', 'Membership package updated successfully.');
    }

    public function destroyPlan(MembershipPlan $plan)
    {
        $plan->delete();

        return back()->with('success', 'Membership plan deleted.');
    }

    public function togglePlanStatus(MembershipPlan $plan)
    {
        $plan->update(['is_active' => ! $plan->is_active]);

        return back()->with('success', 'Membership package status updated.');
    }

    public function storeGateway(Request $request)
    {
        $data = $this->validateGateway($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_default'] = $request->boolean('is_default');
        $data['sandbox'] = $request->boolean('sandbox');
        $data['slug'] = $this->makeGatewaySlug($data['name']);
        $data['config'] = $this->gatewayConfig($request);

        DB::transaction(function () use ($data) {
            if ($data['is_default']) {
                PaymentGateway::query()->update(['is_default' => false]);
            }
            PaymentGateway::create($data);
        });

        return back()->with('success', 'Payment gateway added successfully.');
    }

    public function updateGateway(Request $request, PaymentGateway $gateway)
    {
        $data = $this->validateGateway($request, $gateway);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_default'] = $request->boolean('is_default');
        $data['sandbox'] = $request->boolean('sandbox');
        $data['config'] = $this->gatewayConfig($request);

        if ($gateway->name !== $data['name']) {
            $data['slug'] = $this->makeGatewaySlug($data['name'], $gateway->id);
        }

        if (! filled($request->input('secret_key')) && filled($gateway->secret_key)) {
            unset($data['secret_key']);
        }

        DB::transaction(function () use ($gateway, $data) {
            if ($data['is_default']) {
                PaymentGateway::query()->whereKeyNot($gateway->id)->update(['is_default' => false]);
            }
            $gateway->update($data);
        });

        return back()->with('success', 'Payment gateway updated successfully.');
    }

    public function destroyGateway(PaymentGateway $gateway)
    {
        $gateway->delete();

        return back()->with('success', 'Payment gateway deleted.');
    }

    public function updatePaymentStatus(Request $request, PaymentTransaction $payment)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'submitted', 'paid', 'failed', 'cancelled', 'refunded'])],
            'external_transaction_id' => ['nullable', 'string', 'max:120'],
        ]);

        DB::transaction(function () use ($payment, $data) {
            $payment->update([
                'status' => $data['status'],
                'external_transaction_id' => $data['external_transaction_id'] ?? $payment->external_transaction_id,
                'paid_at' => $data['status'] === 'paid' ? ($payment->paid_at ?: now()) : $payment->paid_at,
            ]);

            if ($data['status'] === 'paid') {
                $this->activateMembership($payment->fresh());
            }
        });

        return back()->with('success', 'Payment status updated.');
    }

    private function settingsCards(array $settings = []): array
    {
        $gatewayCount = Schema::hasTable('payment_gateways') ? PaymentGateway::count() : 0;
        $activeGateways = Schema::hasTable('payment_gateways') ? PaymentGateway::where('is_active', true)->count() : 0;

        return [
            'general-setting' => [
                'title' => 'General Setting',
                'description' => 'Configure the fundamental information of the site.',
                'icon' => 'gear',
                'summary' => $settings['general.site_name'] ?? config('app.name', 'HeavenlyMatch'),
            ],
            'logo-favicon' => [
                'title' => 'Logo and Favicon',
                'description' => 'Upload your logo and favicon here.',
                'icon' => 'image',
                'summary' => ! empty($settings['media.logo']) ? 'Logo uploaded' : 'Default branding',
            ],
            'system-configuration' => [
                'title' => 'System Configuration',
                'description' => 'Control all of the basic modules of the system.',
                'icon' => 'sliders',
                'summary' => ($settings['system.enable_registration'] ?? '1') ? 'Registration enabled' : 'Registration disabled',
            ],
            'notification-setting' => [
                'title' => 'Notification Setting',
                'description' => 'Control and configure overall notification elements of the system.',
                'icon' => 'bell',
                'summary' => $settings['notification.admin_email'] ?? config('mail.from.address'),
            ],
            'payment-gateways' => [
                'title' => 'Payment Gateways',
                'description' => 'Configure automatic or manual payment gateways to accept payment from users.',
                'icon' => 'credit-card',
                'summary' => $activeGateways . ' active / ' . $gatewayCount . ' total',
                'highlight' => true,
            ],
            'seo-configuration' => [
                'title' => 'SEO Configuration',
                'description' => 'Configure meta title, meta description and keywords to make the system SEO-friendly.',
                'icon' => 'globe',
                'summary' => $settings['seo.meta_title'] ?? 'SEO ready',
            ],
            'manage-frontend' => [
                'title' => 'Manage Frontend',
                'description' => 'Control all of the frontend contents of the system.',
                'icon' => 'frontend',
                'summary' => $settings['frontend.hero_title'] ?? 'Hero content',
            ],
            'manage-pages' => [
                'title' => 'Manage Pages',
                'description' => 'Control dynamic and static pages of the system.',
                'icon' => 'pages',
                'summary' => 'About, FAQ, Guide, Contact',
            ],
            'kyc-setting' => [
                'title' => 'KYC Setting',
                'description' => 'Configure dynamic input fields to collect client information.',
                'icon' => 'user-check',
                'summary' => ($settings['kyc.enabled'] ?? '0') ? 'Enabled' : 'Disabled',
            ],
            'social-login-setting' => [
                'title' => 'Social Login Setting',
                'description' => 'Provide required information to use the login system by social media.',
                'icon' => 'user-circle',
                'summary' => 'Google / Facebook',
            ],
            'language' => [
                'title' => 'Language',
                'description' => 'Configure required languages and keywords to localize the system.',
                'icon' => 'language',
                'summary' => $settings['language.default_locale'] ?? app()->getLocale(),
            ],
            'extensions' => [
                'title' => 'Extensions',
                'description' => 'Manage extensions of the system to extend extra features.',
                'icon' => 'extension',
                'summary' => 'Captcha, SMS, chat',
            ],
            'policy-pages' => [
                'title' => 'Policy Pages',
                'description' => 'Configure your policy and terms of the system here.',
                'icon' => 'shield',
                'summary' => 'Terms and privacy',
            ],
            'maintenance-mode' => [
                'title' => 'Maintenance Mode',
                'description' => 'Enable or disable maintenance mode of the system when required.',
                'icon' => 'robot',
                'summary' => ($settings['maintenance.enabled'] ?? '0') ? 'Enabled' : 'Disabled',
            ],
            'gdpr-cookie' => [
                'title' => 'GDPR Cookie',
                'description' => 'Set GDPR cookie policy. Visitors will be asked to accept it when enabled.',
                'icon' => 'cookie',
                'summary' => ($settings['gdpr.enabled'] ?? '0') ? 'Enabled' : 'Disabled',
            ],
            'custom-css' => [
                'title' => 'Custom CSS',
                'description' => 'Write custom CSS to modify frontend styles if you need to.',
                'icon' => 'css',
                'summary' => ! empty($settings['custom.css']) ? 'Custom CSS saved' : 'No custom CSS',
            ],
            'sitemap-xml' => [
                'title' => 'Sitemap XML',
                'description' => 'Insert the sitemap XML to enhance SEO performance.',
                'icon' => 'sitemap',
                'summary' => 'public/sitemap.xml',
            ],
            'robots-txt' => [
                'title' => 'Robots txt',
                'description' => 'Insert robots.txt content to instruct bot web crawlers.',
                'icon' => 'robot',
                'summary' => 'public/robots.txt',
            ],
        ];
    }

    private function settingFields(): array
    {
        return [
            'general-setting' => [
                ['name' => 'site_name', 'key' => 'general.site_name', 'label' => 'Site name', 'type' => 'text', 'required' => true],
                ['name' => 'site_tagline', 'key' => 'general.site_tagline', 'label' => 'Site tagline', 'type' => 'text'],
                ['name' => 'primary_email', 'key' => 'general.primary_email', 'label' => 'Primary email', 'type' => 'email'],
                ['name' => 'support_email', 'key' => 'general.support_email', 'label' => 'Support email', 'type' => 'email'],
                ['name' => 'phone', 'key' => 'general.phone', 'label' => 'Phone number', 'type' => 'text'],
                ['name' => 'address', 'key' => 'general.address', 'label' => 'Address', 'type' => 'textarea'],
                ['name' => 'timezone', 'key' => 'general.timezone', 'label' => 'Timezone', 'type' => 'select', 'options' => $this->timezoneOptions()],
                ['name' => 'currency', 'key' => 'general.currency', 'label' => 'Default currency', 'type' => 'select', 'options' => ['BDT' => 'BDT - Bangladeshi Taka', 'USD' => 'USD - US Dollar', 'INR' => 'INR - Indian Rupee']],
                ['name' => 'date_format', 'key' => 'general.date_format', 'label' => 'Date format', 'type' => 'select', 'options' => ['d M Y' => '05 May 2026', 'Y-m-d' => '2026-05-05', 'm/d/Y' => '05/05/2026']],
            ],
            'logo-favicon' => [
                ['name' => 'logo', 'key' => 'media.logo', 'label' => 'Main logo', 'type' => 'file', 'help' => 'PNG, JPG, WEBP or SVG'],
                ['name' => 'admin_logo', 'key' => 'media.admin_logo', 'label' => 'Admin logo', 'type' => 'file', 'help' => 'Optional sidebar logo'],
                ['name' => 'favicon', 'key' => 'media.favicon', 'label' => 'Favicon', 'type' => 'file', 'help' => 'ICO, PNG, JPG or SVG'],
                ['name' => 'login_background', 'key' => 'media.login_background', 'label' => 'Login background image', 'type' => 'file'],
            ],
            'system-configuration' => [
                ['name' => 'enable_registration', 'key' => 'system.enable_registration', 'label' => 'Enable user registration', 'type' => 'checkbox'],
                ['name' => 'email_verification_required', 'key' => 'system.email_verification_required', 'label' => 'Require email verification', 'type' => 'checkbox'],
                ['name' => 'phone_verification_required', 'key' => 'system.phone_verification_required', 'label' => 'Require phone verification', 'type' => 'checkbox'],
                ['name' => 'enable_membership_payment', 'key' => 'system.enable_membership_payment', 'label' => 'Enable membership payments', 'type' => 'checkbox'],
                ['name' => 'profile_approval_required', 'key' => 'system.profile_approval_required', 'label' => 'Require profile approval before publish', 'type' => 'checkbox'],
                ['name' => 'default_user_status', 'key' => 'system.default_user_status', 'label' => 'Default user status', 'type' => 'select', 'options' => ['active' => 'Active', 'pending' => 'Pending', 'blocked' => 'Blocked']],
                ['name' => 'profile_show_limit', 'key' => 'system.free_profile_show_limit', 'label' => 'Free profile show limit', 'type' => 'number'],
            ],
            'notification-setting' => [
                ['name' => 'mail_from_name', 'key' => 'notification.mail_from_name', 'label' => 'Mail from name', 'type' => 'text'],
                ['name' => 'mail_from_email', 'key' => 'notification.mail_from_email', 'label' => 'Mail from email', 'type' => 'email'],
                ['name' => 'admin_email', 'key' => 'notification.admin_email', 'label' => 'Admin notification email', 'type' => 'email'],
                ['name' => 'welcome_email_enabled', 'key' => 'notification.welcome_email_enabled', 'label' => 'Send welcome email', 'type' => 'checkbox'],
                ['name' => 'payment_email_enabled', 'key' => 'notification.payment_email_enabled', 'label' => 'Send payment status email', 'type' => 'checkbox'],
                ['name' => 'sms_gateway_url', 'key' => 'notification.sms_gateway_url', 'label' => 'SMS gateway URL', 'type' => 'url'],
                ['name' => 'sms_sender_id', 'key' => 'notification.sms_sender_id', 'label' => 'SMS sender ID', 'type' => 'text'],
            ],
            'payment-gateways' => [],
            'seo-configuration' => [
                ['name' => 'meta_title', 'key' => 'seo.meta_title', 'label' => 'Meta title', 'type' => 'text'],
                ['name' => 'meta_description', 'key' => 'seo.meta_description', 'label' => 'Meta description', 'type' => 'textarea'],
                ['name' => 'meta_keywords', 'key' => 'seo.meta_keywords', 'label' => 'Meta keywords', 'type' => 'textarea'],
                ['name' => 'canonical_url', 'key' => 'seo.canonical_url', 'label' => 'Canonical URL', 'type' => 'url'],
                ['name' => 'og_image', 'key' => 'seo.og_image', 'label' => 'Open graph image', 'type' => 'file'],
                ['name' => 'google_analytics_id', 'key' => 'seo.google_analytics_id', 'label' => 'Google Analytics ID', 'type' => 'text'],
            ],
            'manage-frontend' => [
                ['name' => 'hero_title', 'key' => 'frontend.hero_title', 'label' => 'Hero title', 'type' => 'text'],
                ['name' => 'hero_subtitle', 'key' => 'frontend.hero_subtitle', 'label' => 'Hero subtitle', 'type' => 'textarea'],
                ['name' => 'hero_background', 'key' => 'frontend.hero_background', 'label' => 'Hero background image', 'type' => 'file'],
                ['name' => 'cta_text', 'key' => 'frontend.cta_text', 'label' => 'CTA button text', 'type' => 'text'],
                ['name' => 'cta_url', 'key' => 'frontend.cta_url', 'label' => 'CTA button URL', 'type' => 'url'],
                ['name' => 'footer_about', 'key' => 'frontend.footer_about', 'label' => 'Footer about text', 'type' => 'textarea'],
                ['name' => 'facebook_url', 'key' => 'frontend.facebook_url', 'label' => 'Facebook URL', 'type' => 'url'],
                ['name' => 'instagram_url', 'key' => 'frontend.instagram_url', 'label' => 'Instagram URL', 'type' => 'url'],
                ['name' => 'youtube_url', 'key' => 'frontend.youtube_url', 'label' => 'YouTube URL', 'type' => 'url'],
            ],
            'manage-pages' => [
                ['name' => 'about_title', 'key' => 'pages.about_title', 'label' => 'About page title', 'type' => 'text'],
                ['name' => 'about_content', 'key' => 'pages.about_content', 'label' => 'About page content', 'type' => 'textarea'],
                ['name' => 'contact_intro', 'key' => 'pages.contact_intro', 'label' => 'Contact page intro', 'type' => 'textarea'],
                ['name' => 'faq_intro', 'key' => 'pages.faq_intro', 'label' => 'FAQ page intro', 'type' => 'textarea'],
                ['name' => 'guide_intro', 'key' => 'pages.guide_intro', 'label' => 'Guide page intro', 'type' => 'textarea'],
            ],
            'kyc-setting' => [
                ['name' => 'enabled', 'key' => 'kyc.enabled', 'label' => 'Enable KYC module', 'type' => 'checkbox'],
                ['name' => 'required_for_profile', 'key' => 'kyc.required_for_profile', 'label' => 'Require KYC before showing profile publicly', 'type' => 'checkbox'],
                ['name' => 'intro', 'key' => 'kyc.intro', 'label' => 'KYC instruction text', 'type' => 'textarea'],
                ['name' => 'fields', 'key' => 'kyc.fields', 'label' => 'Dynamic fields', 'type' => 'json', 'help' => 'One field per line or JSON. Example: National ID|required'],
            ],
            'social-login-setting' => [
                ['name' => 'google_enabled', 'key' => 'social.google_enabled', 'label' => 'Enable Google login', 'type' => 'checkbox'],
                ['name' => 'google_client_id', 'key' => 'social.google_client_id', 'label' => 'Google client ID', 'type' => 'text'],
                ['name' => 'google_client_secret', 'key' => 'social.google_client_secret', 'label' => 'Google client secret', 'type' => 'text'],
                ['name' => 'facebook_enabled', 'key' => 'social.facebook_enabled', 'label' => 'Enable Facebook login', 'type' => 'checkbox'],
                ['name' => 'facebook_client_id', 'key' => 'social.facebook_client_id', 'label' => 'Facebook app ID', 'type' => 'text'],
                ['name' => 'facebook_client_secret', 'key' => 'social.facebook_client_secret', 'label' => 'Facebook app secret', 'type' => 'text'],
            ],
            'language' => [
                ['name' => 'default_locale', 'key' => 'language.default_locale', 'label' => 'Default language', 'type' => 'select', 'options' => ['en' => 'English', 'bn' => 'Bangla', 'hi' => 'Hindi', 'ar' => 'Arabic']],
                ['name' => 'enabled_locales', 'key' => 'language.enabled_locales', 'label' => 'Enabled locales', 'type' => 'text', 'help' => 'Comma separated: en,bn,hi'],
                ['name' => 'rtl_enabled', 'key' => 'language.rtl_enabled', 'label' => 'Enable RTL layout', 'type' => 'checkbox'],
            ],
            'extensions' => [
                ['name' => 'captcha_enabled', 'key' => 'extensions.captcha_enabled', 'label' => 'Enable captcha', 'type' => 'checkbox'],
                ['name' => 'recaptcha_site_key', 'key' => 'extensions.recaptcha_site_key', 'label' => 'reCAPTCHA site key', 'type' => 'text'],
                ['name' => 'recaptcha_secret_key', 'key' => 'extensions.recaptcha_secret_key', 'label' => 'reCAPTCHA secret key', 'type' => 'text'],
                ['name' => 'newsletter_enabled', 'key' => 'extensions.newsletter_enabled', 'label' => 'Enable newsletter', 'type' => 'checkbox'],
                ['name' => 'live_chat_script', 'key' => 'extensions.live_chat_script', 'label' => 'Live chat script', 'type' => 'code'],
            ],
            'policy-pages' => [
                ['name' => 'terms', 'key' => 'policy.terms', 'label' => 'Terms and conditions', 'type' => 'code'],
                ['name' => 'privacy', 'key' => 'policy.privacy', 'label' => 'Privacy policy', 'type' => 'code'],
                ['name' => 'refund', 'key' => 'policy.refund', 'label' => 'Refund policy', 'type' => 'code'],
                ['name' => 'cookie', 'key' => 'policy.cookie', 'label' => 'Cookie policy', 'type' => 'code'],
            ],
            'maintenance-mode' => [
                ['name' => 'enabled', 'key' => 'maintenance.enabled', 'label' => 'Enable maintenance mode', 'type' => 'checkbox'],
                ['name' => 'title', 'key' => 'maintenance.title', 'label' => 'Maintenance title', 'type' => 'text'],
                ['name' => 'message', 'key' => 'maintenance.message', 'label' => 'Maintenance message', 'type' => 'textarea'],
                ['name' => 'allowed_ips', 'key' => 'maintenance.allowed_ips', 'label' => 'Allowed IP addresses', 'type' => 'textarea', 'help' => 'One IP per line. Admin routes remain accessible.'],
            ],
            'gdpr-cookie' => [
                ['name' => 'enabled', 'key' => 'gdpr.enabled', 'label' => 'Enable GDPR cookie notice', 'type' => 'checkbox'],
                ['name' => 'message', 'key' => 'gdpr.message', 'label' => 'Cookie message', 'type' => 'textarea'],
                ['name' => 'button_text', 'key' => 'gdpr.button_text', 'label' => 'Button text', 'type' => 'text'],
                ['name' => 'policy_url', 'key' => 'gdpr.policy_url', 'label' => 'Cookie policy URL', 'type' => 'url'],
            ],
            'custom-css' => [
                ['name' => 'css', 'key' => 'custom.css', 'label' => 'Custom CSS', 'type' => 'code'],
            ],
            'sitemap-xml' => [
                ['name' => 'xml', 'key' => 'sitemap.xml', 'label' => 'Sitemap XML', 'type' => 'code'],
            ],
            'robots-txt' => [
                ['name' => 'txt', 'key' => 'robots.txt', 'label' => 'Robots.txt content', 'type' => 'code'],
            ],
        ];
    }

    private function systemFeatureRows(array $settings = []): array
    {
        $rows = [
            [
                'slug' => 'user-registration',
                'key' => 'system.enable_registration',
                'title' => 'User Registration',
                'description' => 'If you disable this module, no one can register on this system.',
                'default' => true,
            ],
            [
                'slug' => 'force-ssl',
                'key' => 'system.force_ssl',
                'title' => 'Force SSL',
                'description' => 'By enabling <strong>Force SSL (Secure Sockets Layer)</strong> the system will force every visitor to use secure mode. Otherwise, the site can load without secure mode.',
                'default' => false,
            ],
            [
                'slug' => 'agree-policy',
                'key' => 'system.agree_policy',
                'title' => 'Agree Policy',
                'description' => 'If you enable this module, users must agree with your system\'s <a href="' . route('admin.settings.edit', 'policy-pages') . '">policies</a> during registration.',
                'default' => true,
            ],
            [
                'slug' => 'force-secure-password',
                'key' => 'system.force_secure_password',
                'title' => 'Force Secure Password',
                'description' => 'By enabling this module, users must set a secure password while signing up or changing the password.',
                'default' => false,
            ],
            [
                'slug' => 'kyc-verification',
                'key' => 'kyc.enabled',
                'title' => 'KYC Verification',
                'description' => 'If you enable <strong>KYC (Know Your Client)</strong> module, users must submit <a href="' . route('admin.settings.edit', 'kyc-setting') . '">the required data</a>. Otherwise, restricted actions can be prevented by this system.',
                'default' => false,
            ],
            [
                'slug' => 'email-verification',
                'key' => 'system.email_verification_required',
                'title' => 'Email Verification',
                'description' => 'If you enable <strong>Email Verification</strong>, users have to verify their email to access the dashboard. A 6-digit verification code will be sent to their email.<br><em>Note: Make sure that the <strong>Email Notification</strong> module is enabled</em>',
                'default' => true,
            ],
            [
                'slug' => 'email-notification',
                'key' => 'notification.email_enabled',
                'title' => 'Email Notification',
                'description' => 'If you enable this module, the system will send emails to users where needed. Otherwise, no email will be sent. <code>So be sure before disabling this module that, the system doesn\'t need to send any emails.</code>',
                'default' => true,
            ],
            [
                'slug' => 'mobile-verification',
                'key' => 'system.phone_verification_required',
                'title' => 'Mobile Verification',
                'description' => 'If you enable <strong>Mobile Verification</strong>, users have to verify their mobile to access the dashboard. A 6-digit verification code will be sent to their mobile.<br><em>Note: Make sure that the <strong>SMS Notification</strong> module is enabled</em>',
                'default' => false,
            ],
            [
                'slug' => 'sms-notification',
                'key' => 'notification.sms_enabled',
                'title' => 'SMS Notification',
                'description' => 'If you enable this module, the system will send SMS to users where needed. Otherwise, no SMS will be sent. <code>So be sure before disabling this module that, the system doesn\'t need to send any SMS.</code>',
                'default' => false,
            ],
            [
                'slug' => 'push-notification',
                'key' => 'notification.push_enabled',
                'title' => 'Push Notification',
                'description' => 'If you enable this module, the system will send push notifications to users. Otherwise, no push notification will be sent. <a href="' . route('admin.settings.edit', 'notification-setting') . '">Setting here</a>',
                'default' => false,
            ],
            [
                'slug' => 'language-option',
                'key' => 'language.option_enabled',
                'title' => 'Language Option',
                'description' => 'If you enable this module, users can change the language according to their needs.',
                'default' => true,
            ],
            [
                'slug' => 'chat-attachment',
                'key' => 'system.chat_attachment_enabled',
                'title' => 'Chat Attachment',
                'description' => 'If you enable this, users can send files while chatting.',
                'default' => true,
            ],
        ];

        return array_map(function (array $row) use ($settings) {
            $raw = array_key_exists($row['key'], $settings) ? $settings[$row['key']] : ($row['default'] ? '1' : '0');
            $row['enabled'] = filter_var($raw, FILTER_VALIDATE_BOOLEAN);
            return $row;
        }, $rows);
    }

    private function settingsValidationRules(array $fields): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';
            $base = ! empty($field['required']) ? ['required'] : ['nullable'];

            $rules[$name] = match ($type) {
                'email' => array_merge($base, ['email', 'max:190']),
                'url' => array_merge($base, ['url', 'max:500']),
                'number' => array_merge($base, ['numeric']),
                'select' => array_merge($base, ['string', 'max:190']),
                'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg,ico', 'max:4096'],
                'checkbox' => ['nullable'],
                'textarea', 'code', 'json' => array_merge($base, ['string', 'max:65000']),
                default => array_merge($base, ['string', 'max:500']),
            };
        }

        return $rules;
    }

    private function storeUploadedSettingFile($file, string $section): string
    {
        $directory = public_path('uploads/settings/' . $section);
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'setting-file';
        $filename = $name . '-' . time() . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/settings/' . $section . '/' . $filename;
    }

    private function writeGeneratedPublicFiles(array $payload): void
    {
        if (isset($payload['robots.txt']['value'])) {
            File::put(public_path('robots.txt'), $payload['robots.txt']['value']);
        }

        if (isset($payload['sitemap.xml']['value'])) {
            File::put(public_path('sitemap.xml'), $payload['sitemap.xml']['value']);
        }
    }

    private function timezoneOptions(): array
    {
        return [
            'Asia/Dhaka' => 'Asia/Dhaka',
            'Asia/Kolkata' => 'Asia/Kolkata',
            'Asia/Dubai' => 'Asia/Dubai',
            'Europe/London' => 'Europe/London',
            'America/New_York' => 'America/New_York',
            'UTC' => 'UTC',
        ];
    }

    private function preparePlanData(Request $request, ?MembershipPlan $plan = null): array
    {
        $data = $this->validatePlan($request);
        $data['currency'] = SystemSetting::get('general.currency', 'BDT');
        $data['features'] = $this->planFeatures(
            (int) $data['interest_express_limit'],
            (int) $data['profile_show_limit'],
            (int) $data['image_upload_limit'],
            (int) $data['validity_days'],
            $request->input('features_text')
        );
        $data['is_active'] = $request->boolean('is_active');
        $data['is_popular'] = $request->boolean('is_popular');
        $data['duration_months'] = $this->durationMonthsFromValidity((int) $data['validity_days']);

        $needsNewSlug = ! $plan
            || $plan->name !== $data['name']
            || (int) $plan->duration_months !== (int) $data['duration_months'];

        if ($needsNewSlug) {
            $data['slug'] = $this->makePlanSlug($data['name'], $data['duration_months'], $plan?->id);
        }

        return $data;
    }

    private function validatePlan(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'interest_express_limit' => ['required', 'integer', 'min:-1', 'max:999999'],
            'profile_show_limit' => ['required', 'integer', 'min:-1', 'max:999999'],
            'image_upload_limit' => ['required', 'integer', 'min:-1', 'max:999999'],
            'validity_days' => ['required', 'integer', 'min:-1', 'max:99999'],
            'price' => ['required', 'numeric', 'min:0'],
            'badge' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'features_text' => ['nullable', 'string', 'max:3000'],
        ]);
    }

    private function validateGateway(Request $request, ?PaymentGateway $gateway = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(['manual', 'redirect', 'sslcommerz', 'bkash', 'nagad'])],
            'checkout_url' => ['nullable', 'url', 'max:500'],
            'merchant_id' => ['nullable', 'string', 'max:190'],
            'public_key' => ['nullable', 'string', 'max:500'],
            'secret_key' => ['nullable', 'string', 'max:1000'],
            'instructions' => ['nullable', 'string', 'max:3000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
    }

    private function planFeatures(int $interestLimit, int $profileLimit, int $imageLimit, int $validityDays, ?string $featuresText): array
    {
        return collect([
            'Interest Express: ' . $this->limitLabel($interestLimit),
            'Profile show limit: ' . $this->limitLabel($profileLimit),
            'Image upload limit: ' . $this->limitLabel($imageLimit),
            'Validity: ' . ($validityDays === -1 ? 'Unlimited' : ($validityDays . ' days')),
        ])
            ->merge(collect(preg_split('/\r\n|\r|\n/', (string) $featuresText))
                ->map(fn ($line) => trim($line))
                ->filter())
            ->values()
            ->all();
    }

    private function limitLabel(int $value): string
    {
        return $value === -1 ? 'Unlimited' : (string) $value;
    }

    private function durationMonthsFromValidity(int $validityDays): int
    {
        if ($validityDays === -1) {
            return 12;
        }

        if ($validityDays <= 0) {
            return 1;
        }

        if ($validityDays >= 365) {
            return 12;
        }

        return max(1, (int) ceil($validityDays / 30));
    }

    private function gatewayConfig(Request $request): array
    {
        return [
            'api_version' => $request->input('api_version'),
            'success_note' => $request->input('success_note'),
        ];
    }

    private function makePlanSlug(string $name, int $duration, ?int $ignoreId = null): string
    {
        $base = Str::slug($name . '-' . $duration . '-months') ?: 'membership-plan';
        $slug = $base;
        $counter = 2;

        while (MembershipPlan::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    private function makeGatewaySlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'payment-gateway';
        $slug = $base;
        $counter = 2;

        while (PaymentGateway::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    private function activateMembership(PaymentTransaction $payment): void
    {
        if (! $payment->registration_id) {
            return;
        }

        $user = Registration::find($payment->registration_id);
        if (! $user) {
            return;
        }

        $startsAt = now();
        $currentExpiry = $user->membership_expires_at instanceof Carbon ? $user->membership_expires_at : null;
        if ($currentExpiry && $currentExpiry->isFuture()) {
            $startsAt = $currentExpiry;
        }

        $user->forceFill([
            'membership_plan_id' => $payment->membership_plan_id,
            'membership_plan_name' => $payment->plan_name,
            'membership_status' => 'active',
            'membership_started_at' => now(),
            'membership_expires_at' => (clone $startsAt)->addMonths((int) $payment->duration_months),
        ])->save();
    }
}
