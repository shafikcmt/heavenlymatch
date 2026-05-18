<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $code;
    public ?string $token;

    public function __construct(string $name, string $code, ?string $token = null)
    {
        $this->name = $name;
        $this->code = $code;
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Verify your HeavenlyMatch email')
            ->view('emails.verify-email')
            ->with([
                'name' => $this->name,
                'code' => $this->code,
                'token' => $this->token,
                'verifyUrl' => $this->token ? url('/verify-email/' . $this->token) : null,
            ]);
    }
}
