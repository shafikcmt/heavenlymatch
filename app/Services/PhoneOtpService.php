<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PhoneVerificationCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Handles registration phone OTP: normalization, sending (Twilio or local log),
 * and verification. Codes are stored hashed and pruned after use.
 *
 * Result shape returned by sendOtp()/verifyOtp():
 *   ['ok' => bool, 'message' => string, 'retry_after' => int|null]
 */
class PhoneOtpService
{
    public const CODE_LENGTH          = 6;
    public const EXPIRY_MINUTES       = 5;
    public const MAX_ATTEMPTS         = 5;
    public const RESEND_SECONDS       = 60;
    public const MAX_SENDS_PER_WINDOW = 3;
    public const SEND_WINDOW_MINUTES  = 10;
    // A verified OTP stays valid this long to finish the registration form.
    public const VERIFIED_TTL_MINUTES = 30;

    /**
     * Normalize a Bangladeshi mobile number to E.164 (+8801XXXXXXXXX).
     * Returns null when the number is not a valid BD mobile.
     *
     *   01768987779    → +8801768987779
     *   8801768987779  → +8801768987779
     *   +8801768987779 → +8801768987779
     *   1768987779     → +8801768987779
     */
    public function normalizePhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        // Strip everything except digits and a leading +
        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '880')) {
            $national = substr($digits, 3);            // drop country code
        } elseif (str_starts_with($digits, '0')) {
            $national = substr($digits, 1);            // drop leading 0
        } else {
            $national = $digits;                       // assume already national (1XXXXXXXXX)
        }

        $normalized = '+880' . $national;

        // BD mobile: +880 1[3-9] + 8 digits
        if (! preg_match('/^\+8801[3-9]\d{8}$/', $normalized)) {
            return null;
        }

        return $normalized;
    }

    /**
     * Generate, store (hashed) and dispatch a fresh OTP for the given phone.
     */
    public function sendOtp(string $phone, ?string $ip = null, ?string $userAgent = null): array
    {
        $normalized = $this->normalizePhone($phone);
        if ($normalized === null) {
            return $this->fail(trans('auth.otp_invalid_phone'));
        }

        // ── Resend cooldown (60s between sends per phone) ──────────────────────
        $latest = PhoneVerificationCode::where('phone', $normalized)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($latest) {
            $elapsed = $latest->created_at->diffInSeconds(now());
            if ($elapsed < self::RESEND_SECONDS) {
                $retry = self::RESEND_SECONDS - $elapsed;
                return $this->fail(trans('auth.otp_resend_wait', ['seconds' => $retry]), $retry);
            }
        }

        // ── Per-phone abuse cap (max sends per rolling window) ─────────────────
        $recentSends = PhoneVerificationCode::where('phone', $normalized)
            ->where('created_at', '>=', now()->subMinutes(self::SEND_WINDOW_MINUTES))
            ->count();

        if ($recentSends >= self::MAX_SENDS_PER_WINDOW) {
            return $this->fail(trans('auth.otp_too_many'));
        }

        $code = (string) random_int(100000, 999999);

        $record = PhoneVerificationCode::create([
            'phone'      => $normalized,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
            'attempts'   => 0,
            'ip_address' => $ip,
            'user_agent' => $userAgent ? substr($userAgent, 0, 255) : null,
        ]);

        $delivered = $this->dispatchSms($normalized, $code);

        if (! $delivered) {
            // Allow an immediate retry — drop the undeliverable code.
            $record->delete();
            return $this->fail(trans('auth.otp_send_failed'));
        }

        return ['ok' => true, 'message' => trans('auth.otp_sent'), 'retry_after' => self::RESEND_SECONDS];
    }

    /**
     * Verify a submitted OTP against the latest unused code for the phone.
     */
    public function verifyOtp(string $phone, string $otp): array
    {
        $normalized = $this->normalizePhone($phone);
        if ($normalized === null) {
            return $this->fail(trans('auth.otp_invalid_phone'));
        }

        $record = PhoneVerificationCode::where('phone', $normalized)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $record) {
            return $this->fail(trans('auth.otp_no_code'));
        }

        if ($record->isExpired()) {
            return $this->fail(trans('auth.otp_expired'));
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            return $this->fail(trans('auth.otp_too_many'));
        }

        $record->increment('attempts');

        if (! Hash::check($otp, $record->code_hash)) {
            return $this->fail(trans('auth.otp_invalid'));
        }

        $record->forceFill(['verified_at' => now()])->save();

        return ['ok' => true, 'message' => trans('auth.otp_verified'), 'retry_after' => null];
    }

    /**
     * Has this phone been OTP-verified recently enough to complete registration?
     */
    public function isPhoneVerified(string $phone): bool
    {
        $normalized = $this->normalizePhone($phone);
        if ($normalized === null) {
            return false;
        }

        return PhoneVerificationCode::where('phone', $normalized)
            ->whereNotNull('verified_at')
            ->where('verified_at', '>=', now()->subMinutes(self::VERIFIED_TTL_MINUTES))
            ->exists();
    }

    /**
     * Remove all codes for a phone once registration is complete.
     */
    public function clearForPhone(string $phone): void
    {
        $normalized = $this->normalizePhone($phone);
        if ($normalized !== null) {
            PhoneVerificationCode::where('phone', $normalized)->delete();
        }
    }

    // ── Internals ─────────────────────────────────────────────────────────────

    /**
     * Send the SMS via Twilio when configured; otherwise log it (local only).
     * Returns whether the OTP is considered "delivered" for the flow to proceed.
     */
    private function dispatchSms(string $phone, string $code): bool
    {
        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from  = config('services.twilio.from');

        $message = "Your HeavenlyMatch verification code is {$code}. It expires in "
            . self::EXPIRY_MINUTES . ' minutes.';

        // No credentials configured.
        if (! $sid || ! $token || ! $from) {
            if (app()->environment('production')) {
                Log::error('Phone OTP could not be sent: Twilio credentials are not configured.');
                return false;
            }

            // Local/testing: surface the code in the log so devs can verify.
            Log::info("[PhoneOtpService] OTP for {$phone} is {$code} (Twilio not configured — local fallback).");
            return true;
        }

        try {
            $client = new \Twilio\Rest\Client($sid, $token);
            $client->messages->create($phone, ['from' => $from, 'body' => $message]);
            return true;
        } catch (\Throwable $e) {
            // Never expose Twilio internals to the user.
            Log::error('Twilio OTP send failed: ' . $e->getMessage());
            return false;
        }
    }

    private function fail(string $message, ?int $retryAfter = null): array
    {
        return ['ok' => false, 'message' => $message, 'retry_after' => $retryAfter];
    }
}
