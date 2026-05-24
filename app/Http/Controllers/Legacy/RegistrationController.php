<?php

/**
 * LEGACY — No active routes reference this controller.
 * Superseded by App\Http\Controllers\Auth\RegisterController (Inertia).
 * Broken route refs: route('email.verify.notice') — now route('verification.notice').
 */

namespace App\Http\Controllers\Legacy;

use App\Models\Registration;
use App\Models\SystemSetting;
use App\Models\UserAttribute;
use App\Services\EmailVerificationSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function showForm()
    {
        if (! SystemSetting::bool('system.enable_registration', true)) {
            return redirect()->route('login')->with('error', 'Registration is currently disabled. Please contact support.');
        }

        return view('auth.registration', [
            'religionOptions' => UserAttribute::optionsFor('religion'),
            'bloodGroupOptions' => UserAttribute::optionsFor('blood-group'),
            'maritalStatusOptions' => UserAttribute::optionsFor('marital-status'),
        ]);
    }

    public function store(Request $request, EmailVerificationSender $verificationSender)
    {
        if (! SystemSetting::bool('system.enable_registration', true)) {
            return redirect()->route('login')->with('error', 'Registration is currently disabled. Please contact support.');
        }

        $request->merge([
            'email' => strtolower((string) $request->input('email')),
        ]);

        $validated = $request->validate([
            'profile_for' => 'required|string|in:self,son,daughter,brother,sister,relative,friend',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'religion' => 'nullable|string|max:120',
            'marital_status' => 'nullable|string|max:120',
            'blood_group' => 'nullable|string|max:30',
            'preferred_language' => 'required|in:bn,en',
            'email' => 'required|email|unique:registrations,email',
            'country_code' => 'required|string|max:10',
            'mobile_number' => ['required', 'string', 'max:20', 'regex:/^[0-9]{8,15}$/', 'unique:registrations,mobile_number'],
            'password' => 'required|string|confirmed|min:8|max:64',
            'terms' => 'accepted',
        ], [
            'mobile_number.regex' => 'Use only digits for the mobile number. Do not include country code here.',
            'terms.accepted' => 'You must accept the privacy and matrimony-use agreement.',
        ]);

        $emailVerificationRequired = SystemSetting::bool('system.email_verification_required', true);
        $defaultUserStatus = SystemSetting::get('system.default_user_status', 'pending');

        $user = Registration::create([
            'profile_for' => $validated['profile_for'],
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'religion' => $validated['religion'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'blood_group' => $validated['blood_group'] ?? null,
            'preferred_language' => $validated['preferred_language'],
            'email' => $validated['email'],
            'country_code' => $validated['country_code'],
            'mobile_number' => $validated['mobile_number'],
            'password' => Hash::make($validated['password']),
            'is_email_verified' => ! $emailVerificationRequired,
            'is_mobile_verified' => false,
            'status' => in_array($defaultUserStatus, ['active', 'pending', 'blocked'], true) ? $defaultUserStatus : 'pending',
            'terms_accepted_at' => now(),
        ]);

        if (! $emailVerificationRequired) {
            return redirect()->route('login')->with('success', 'Account created. You can login now.');
        }

        [$code, $token] = $verificationSender->createCode($user);
        $sent = $verificationSender->send($user, $code, $token);

        $redirect = redirect()->route('email.verify.notice', ['email' => $user->email])
            ->with('email', $user->email);

        if (! $sent) {
            return $redirect
                ->with('dev_code', app()->environment(['local', 'development']) ? $code : null)
                ->with('error', 'Account created, but email sending failed. Please fix MAIL settings, then resend the code.');
        }

        return $redirect->with('success', 'Account created. We sent a verification code to your email.');
    }
}
