<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\EmailOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON endpoints used by the registration form (Step 1) to send and verify
 * an email OTP before the account is created. The OTP itself is never returned.
 */
class EmailVerificationController extends Controller
{
    public function __construct(private readonly EmailOtpService $otp)
    {
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:180',
        ]);

        // Don't send OTPs to addresses that already belong to an account.
        if (Registration::where('email', $validated['email'])->exists()) {
            return response()->json(['ok' => false, 'message' => trans('auth.email_taken')], 422);
        }

        $result = $this->otp->sendOtp(
            $validated['email'],
            $request->ip(),
            (string) $request->userAgent(),
        );

        return response()->json($result, $result['ok'] ? 200 : 429);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:180',
            'otp'   => 'required|string',
        ]);

        $result = $this->otp->verifyOtp($validated['email'], $validated['otp']);

        return response()->json($result, $result['ok'] ? 200 : 422);
    }
}
