<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $code;
    public $token;

    /**
     * Create a new message instance.
     *
     * @param string $name  User's name
     * @param string $code  6-digit verification code
     * @param string|null $token Unique verification link token
     */
    public function __construct($name, $code, $token = null)
    {
        $this->name = $name;
        $this->code = $code;
        $this->token = $token;
    }

    /**
     * Build the message.
     */
        public function build()
        {
            $mail = $this->subject('Verify Your Email | HeavenlyMatch 💌')
                        ->view('emails.verify-email')
                        ->with([
                            'name'  => $this->name,
                            'code'  => $this->code,
                        ]);

            // Include token only if it exists
            if ($this->token) {
                $mail->with(['token' => $this->token]);
            }

            return $mail;
        }
}
