<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\PhoneOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON endpoints used by the registration form (Step 2) to send and verify
 * a mobile OTP before the account is created. The OTP itself is never returned.
 */
class PhoneVerificationController extends Controller
{
    public function __construct(private readonly PhoneOtpService $otp)
    {
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'mobile_number' => 'required|string|max:20',
        ]);

        $normalized = $this->otp->normalizePhone($request->input('mobile_number'));
        if ($normalized === null) {
            return response()->json(['ok' => false, 'message' => trans('auth.otp_invalid_phone')], 422);
        }

        // Don't send OTPs to numbers that already belong to an account.
        if (Registration::where('mobile_number', $normalized)->exists()) {
            return response()->json(['ok' => false, 'message' => trans('auth.otp_phone_taken')], 422);
        }

        $result = $this->otp->sendOtp(
            $normalized,
            $request->ip(),
            (string) $request->userAgent(),
        );

        return response()->json($result, $result['ok'] ? 200 : 429);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'mobile_number' => 'required|string|max:20',
            'otp'           => 'required|string',
        ]);

        $result = $this->otp->verifyOtp(
            (string) $request->input('mobile_number'),
            (string) $request->input('otp'),
        );

        return response()->json($result, $result['ok'] ? 200 : 422);
    }
}
