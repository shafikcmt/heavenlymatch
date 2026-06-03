<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\SystemSetting;
use App\Services\PhoneOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function __construct(private readonly PhoneOtpService $otp)
    {
    }

    public function show(): Response
    {
        return Inertia::render('Auth/Register', [
            // Drive the registration UI from the admin verification settings.
            'requireEmailVerification' => SystemSetting::bool('system.require_email_verification', true),
            'requirePhoneVerification' => SystemSetting::bool('system.require_phone_verification', true),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $requireEmail = SystemSetting::bool('system.require_email_verification', true);
        $requirePhone = SystemSetting::bool('system.require_phone_verification', true);

        $validated = $request->validate([
            'name'                => 'required|string|max:100',
            'email'               => 'required|email|max:180|unique:registrations,email',
            'password'            => 'required|confirmed|min:8',
            'gender'              => 'required|in:male,female',
            'profile_created_for' => 'required|in:self,son,daughter,brother,sister,relative',
            // Phone is only mandatory when phone verification is enabled.
            'mobile_number'       => ($requirePhone ? 'required' : 'nullable') . '|string|max:20',
            'platform_mode'       => 'required|in:general,islamic',
            'terms_accepted'      => 'accepted',
        ]);

        // Resolve the phone number when one was provided (always required if $requirePhone).
        $mobile         = null;
        $mobileVerified = false;

        if (! empty($validated['mobile_number'])) {
            $mobile = $this->otp->normalizePhone($validated['mobile_number']);
            if ($mobile === null) {
                throw ValidationException::withMessages([
                    'mobile_number' => trans('auth.otp_invalid_phone'),
                ]);
            }

            if (Registration::where('mobile_number', $mobile)->exists()) {
                throw ValidationException::withMessages([
                    'mobile_number' => trans('auth.otp_phone_taken'),
                ]);
            }

            if ($requirePhone) {
                // Enforce a completed OTP verification for this number.
                if (! $this->otp->isPhoneVerified($mobile)) {
                    throw ValidationException::withMessages([
                        'mobile_number' => trans('auth.otp_not_verified'),
                    ]);
                }
                $mobileVerified = true;
            } else {
                // OTP skipped — honor a verification only if one happened to occur.
                $mobileVerified = $this->otp->isPhoneVerified($mobile);
            }
        }

        $reg = Registration::create([
            'name'                => $validated['name'],
            'email'               => $validated['email'],
            'password'            => Hash::make($validated['password']),
            'gender'              => $validated['gender'],
            'looking_for'         => $validated['gender'] === 'male' ? 'bride' : 'groom',
            'profile_created_for' => $validated['profile_created_for'],
            'country_code'        => '+880',
            'mobile_number'       => $mobile,
            'is_mobile_verified'  => $mobileVerified,
            'mobile_verified_at'  => $mobileVerified ? now() : null,
            'platform_mode'       => $validated['platform_mode'],
            'photo_visibility'    => $validated['platform_mode'] === 'islamic' ? 'blurred' : 'members_only',
            'terms_accepted_at'   => now(),
            // account_status and role intentionally omitted — migration defaults apply:
            // account_status = 'active',  role = 'user'
        ]);

        // OTP fulfilled its purpose — remove all codes for this number.
        if ($mobile !== null) {
            $this->otp->clearForPhone($mobile);
        }

        Auth::login($reg);
        $request->session()->regenerate();

        // Email verification enabled → keep the standard verify-email flow.
        if ($requireEmail) {
            $reg->sendEmailVerificationNotification();

            return redirect()->route('verification.notice')
                ->with('status', trans('auth.register_success'));
        }

        // Email verification disabled → straight into onboarding, no blocking notice.
        return redirect()->route('dashboard')
            ->with('success', trans('auth.register_success'));
    }
}
