<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\EmailOtpMail;
use App\Models\EmailVerificationCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Handles registration email OTP: sending (Laravel Mail) and verification.
 * Codes are stored hashed and pruned after use. Mirrors PhoneOtpService.
 *
 * Result shape returned by sendOtp()/verifyOtp():
 *   ['ok' => bool, 'message' => string, 'retry_after' => int|null]
 */
class EmailOtpService
{
    public const CODE_LENGTH          = 6;
    public const EXPIRY_MINUTES       = 5;
    public const MAX_ATTEMPTS         = 5;
    public const RESEND_SECONDS       = 60;
    public const MAX_SENDS_PER_WINDOW = 3;
    public const SEND_WINDOW_MINUTES  = 10;
    // A verified OTP stays valid this long to finish the registration form.
    public const VERIFIED_TTL_MINUTES = 30;

    public function generateOtp(): string
    {
        return (string) random_int(100000, 999999);
    }

    public function hashOtp(string $code): string
    {
        return Hash::make($code);
    }

    /**
     * Generate, store (hashed) and email a fresh OTP for the given address.
     */
    public function sendOtp(string $email, ?string $ip = null, ?string $userAgent = null): array
    {
        $email = mb_strtolower(trim($email));

        // ── Resend cooldown (60s between sends per email) ──────────────────────
        $latest = EmailVerificationCode::where('email', $email)
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

        // ── Per-email abuse cap (max sends per rolling window) ──────────────────
        $recentSends = EmailVerificationCode::where('email', $email)
            ->where('created_at', '>=', now()->subMinutes(self::SEND_WINDOW_MINUTES))
            ->count();

        if ($recentSends >= self::MAX_SENDS_PER_WINDOW) {
            return $this->fail(trans('auth.otp_too_many'));
        }

        $code = $this->generateOtp();

        $record = EmailVerificationCode::create([
            'email'      => $email,
            'code_hash'  => $this->hashOtp($code),
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
            'attempts'   => 0,
            'ip_address' => $ip,
            'user_agent' => $userAgent ? substr($userAgent, 0, 255) : null,
        ]);

        if (! $this->dispatchEmail($email, $code)) {
            // Allow an immediate retry — drop the undeliverable code.
            $record->delete();
            return $this->fail(trans('auth.email_otp_send_failed'));
        }

        return ['ok' => true, 'message' => trans('auth.email_otp_sent'), 'retry_after' => self::RESEND_SECONDS];
    }

    /**
     * Verify a submitted OTP against the latest unused code for the email.
     */
    public function verifyOtp(string $email, string $otp): array
    {
        $email = mb_strtolower(trim($email));

        $record = EmailVerificationCode::where('email', $email)
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

        return ['ok' => true, 'message' => trans('auth.email_otp_verified'), 'retry_after' => null];
    }

    /**
     * Has this email been OTP-verified recently enough to complete registration?
     */
    public function isEmailVerified(string $email): bool
    {
        $email = mb_strtolower(trim($email));

        return EmailVerificationCode::where('email', $email)
            ->whereNotNull('verified_at')
            ->where('verified_at', '>=', now()->subMinutes(self::VERIFIED_TTL_MINUTES))
            ->exists();
    }

    /**
     * Remove all codes for an email once registration is complete.
     */
    public function clearForEmail(string $email): void
    {
        EmailVerificationCode::where('email', mb_strtolower(trim($email)))->delete();
    }

    // ── Internals ─────────────────────────────────────────────────────────────

    /**
     * Send the OTP email. On failure, log it; in local/testing also log the code
     * so development can proceed without a working mailer. Returns delivery status.
     */
    private function dispatchEmail(string $email, string $code): bool
    {
        try {
            Mail::to($email)->send(new EmailOtpMail($code, self::EXPIRY_MINUTES));
            return true;
        } catch (\Throwable $e) {
            Log::error('Email OTP send failed: ' . $e->getMessage());

            if (app()->environment('production')) {
                return false;
            }

            // Local/testing fallback so devs can read the code from the log.
            Log::info("[EmailOtpService] OTP for {$email} is {$code} (mail failed — local fallback).");
            return true;
        }
    }

    private function fail(string $message, ?int $retryAfter = null): array
    {
        return ['ok' => false, 'message' => $message, 'retry_after' => $retryAfter];
    }
}
