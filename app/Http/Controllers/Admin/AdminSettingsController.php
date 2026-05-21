<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminSettingsController extends Controller
{
    private const EDITABLE_KEYS = [
        'general.site_name',
        'general.maintenance_mode',
        'general.support_email',
        'notification.mail_from_name',
        'notification.mail_from_address',
    ];

    public function index(): Response
    {
        $settings = [];
        foreach (self::EDITABLE_KEYS as $key) {
            $settings[$key] = SystemSetting::get($key, '');
        }

        $gateways = PaymentGateway::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'merchant_id', 'type']);

        return Inertia::render('Admin/Settings', [
            'settings' => $settings,
            'gateways' => $gateways,
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
}
