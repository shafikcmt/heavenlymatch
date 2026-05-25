<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(): Response
    {
        /** @var Registration $user */
        $user = Auth::user();

        return Inertia::render('Dashboard/Settings', [
            'user' => [
                'name'               => $user->name,
                'email'              => $user->email,
                'mobile'             => $user->mobile_number,
                'platform_mode'      => $user->platform_mode,
                'photo_visibility'   => $user->photo_visibility,
                'account_status'     => $user->account_status,
                'registration_id'    => $user->registration_id,
                'preferred_language' => $user->preferred_language ?? 'en',
            ],
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:100'],
            'mobile'             => ['nullable', 'string', 'max:20'],
            'platform_mode'      => ['required', 'in:general,islamic'],
            'photo_visibility'   => ['required', 'in:public,members_only,blurred'],
            'preferred_language' => ['required', 'in:en,bn'],
        ]);

        $user->update([
            'name'               => $validated['name'],
            'mobile_number'      => $validated['mobile'] ?? null,
            'platform_mode'      => $validated['platform_mode'],
            'photo_visibility'   => $validated['photo_visibility'],
            'preferred_language' => $validated['preferred_language'],
        ]);

        $request->session()->put('locale', $validated['preferred_language']);

        return back()->with('success', __('settings.account_saved'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => __('settings.error_current_password')]);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', __('settings.password_updated'));
    }

    public function deleteAccount(Request $request): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => __('settings.error_delete_password')]);
        }

        Auth::logout();

        $user->forceFill(['account_status' => 'deleted'])->save();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', __('settings.account_deleted'));
    }
}
