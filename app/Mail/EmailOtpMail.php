<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public int $expiryMinutes,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your HeavenlyMatch Email Verification Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.email-otp',
            with: [
                'code'          => $this->code,
                'expiryMinutes' => $this->expiryMinutes,
            ],
        );
    }
}
