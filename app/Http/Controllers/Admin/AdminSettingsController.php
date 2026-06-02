<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AdminSettingsController extends Controller
{
    private const VALID_MEDIA_KEYS = ['hero', 'success', 'testimonial'];

    private const EDITABLE_KEYS = [
        'general.site_name',
        'general.maintenance_mode',
        'general.support_email',
        'notification.mail_from_name',
        'notification.mail_from_address',
        'social.google_enabled',
        'social.facebook_enabled',
        // Biodata Approval Control — '1' requires admin approval, '0' auto-approves.
        'system.profile_approval_required',
    ];

    public function index(): Response
    {
        $settings = [];
        foreach (self::EDITABLE_KEYS as $key) {
            // Biodata approval defaults to enabled ('1') so the safe workflow stays on.
            $default = $key === 'system.profile_approval_required' ? '1' : '';
            $settings[$key] = SystemSetting::get($key, $default);
        }

        $gateways = PaymentGateway::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'merchant_id', 'type']);

        $mediaKeys = ['hero', 'success', 'testimonial'];
        $mediaUrls = [];
        foreach ($mediaKeys as $mk) {
            $path = SystemSetting::get("marketing.{$mk}_image", '');
            $mediaUrls[$mk] = $path ? Storage::disk('public')->url($path) : null;
        }

        return Inertia::render('Admin/Settings', [
            'settings'  => $settings,
            'gateways'  => $gateways,
            'mediaUrls' => $mediaUrls,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings'                          => 'array',
            'settings.general.site_name'        => 'nullable|string|max:100',
            'settings.general.maintenance_mode' => 'nullable|string|in:0,1',
            'settings.general.support_email'    => 'nullable|email|max:150',
            'settings.notification.mail_from_name'    => 'nullable|string|max:100',
            'settings.notification.mail_from_address' => 'nullable|email|max:150',
            'settings.social.google_enabled'          => 'nullable|string|in:0,1',
            'settings.social.facebook_enabled'        => 'nullable|string|in:0,1',
            'settings.system.profile_approval_required' => 'nullable|string|in:0,1',
            'gateways'                          => 'array',
            'gateways.*.id'                     => 'required|integer|exists:payment_gateways,id',
            'gateways.*.merchant_id'            => 'nullable|string|max:100',
        ]);

        // Save system settings
        if (! empty($validated['settings'])) {
            $flat = [];
            foreach ($validated['settings'] as $group => $pairs) {
                foreach ($pairs as $subkey => $value) {
                    $flat["{$group}.{$subkey}"] = (string) ($value ?? '');
                }
            }
            SystemSetting::setMany($flat);
        }

        // Update gateway merchant numbers
        if (! empty($validated['gateways'])) {
            foreach ($validated['gateways'] as $gwData) {
                PaymentGateway::where('id', $gwData['id'])
                    ->update(['merchant_id' => $gwData['merchant_id'] ?? null]);
            }
        }

        return back()->with('success', __('admin.settings_saved'));
    }

    public function uploadMedia(Request $request, string $key): RedirectResponse
    {
        if (! in_array($key, self::VALID_MEDIA_KEYS, true)) {
            abort(422, 'Invalid media key.');
        }

        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048']);

        $oldPath = SystemSetting::get("marketing.{$key}_image", '');
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('image')->store('marketing', 'public');
        SystemSetting::setValue("marketing.{$key}_image", $path, 'string');

        return back()->with('success', __('admin.image_uploaded'));
    }

    public function removeMedia(Request $request, string $key): RedirectResponse
    {
        if (! in_array($key, self::VALID_MEDIA_KEYS, true)) {
            abort(422, 'Invalid media key.');
        }

        $path = SystemSetting::get("marketing.{$key}_image", '');
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        SystemSetting::setValue("marketing.{$key}_image", '', 'string');

        return back()->with('success', __('admin.image_removed'));
    }
}
