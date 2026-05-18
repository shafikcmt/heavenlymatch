<?php

namespace App\Services;

use App\Mail\EmailVerificationMail;
use App\Models\Registration;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailVerificationSender
{
    public function createCode(Registration $user): array
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(64);

        $user->forceFill([
            'email_verification_code' => $code,
            'email_verification_token' => $token,
            'email_verification_sent_at' => now(),
        ])->save();

        return [$code, $token];
    }

    public function send(Registration $user, ?string $code = null, ?string $token = null): bool
    {
        if (! $code || ! $token) {
            [$code, $token] = $this->createCode($user);
        }

        $this->ensureDefaultFromAddress();

        try {
            Mail::to($user->email)->send(new EmailVerificationMail($user->name, $code, $token));
            return true;
        } catch (\Throwable $mailException) {
            Log::warning('Laravel mail verification send failed', [
                'email' => $user->email,
                'message' => $mailException->getMessage(),
            ]);
        }

        try {
            $subject = 'Verify your HeavenlyMatch email';
            $verifyUrl = url('/verify-email/' . $token);
            $body = "Hello {$user->name},\n\nYour HeavenlyMatch verification code is: {$code}\n\nVerification link: {$verifyUrl}\n\nThis code expires in 24 hours.";
            $headers = 'From: ' . config('mail.from.name', 'HeavenlyMatch') . ' <' . config('mail.from.address', 'no-reply@heavenlymatch.net') . '>';

            if (@mail($user->email, $subject, $body, $headers)) {
                return true;
            }
        } catch (\Throwable $nativeException) {
            Log::warning('Native PHP mail verification send failed', [
                'email' => $user->email,
                'message' => $nativeException->getMessage(),
            ]);
        }

        return false;
    }

    private function ensureDefaultFromAddress(): void
    {
        $fromAddress = SystemSetting::get('notification.mail_from_email', env('MAIL_FROM_ADDRESS', 'no-reply@heavenlymatch.net'));
        $fromName = SystemSetting::get('notification.mail_from_name', SystemSetting::get('general.site_name', 'HeavenlyMatch'));

        if ($fromAddress) {
            config(['mail.from.address' => $fromAddress]);
        }

        if ($fromName) {
            config(['mail.from.name' => $fromName]);
        }
    }
}
