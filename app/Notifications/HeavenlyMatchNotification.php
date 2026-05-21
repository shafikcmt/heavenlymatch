<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * General-purpose email notification for HeavenlyMatch.
 *
 * Database notifications are handled separately by UserNotification::send().
 * This class covers only the mail channel.
 *
 * ShouldQueue works with QUEUE_CONNECTION=sync, database, or redis.
 * No Redis required — sync fires immediately, database queues the job.
 *
 * All translations are pre-computed at the call site using trans($key, [], $lang)
 * with the recipient's preferred_language, so this class stays locale-agnostic.
 */
class HeavenlyMatchNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param string      $subject     Translated email subject line.
     * @param string      $greeting    Translated greeting, e.g. "Hello, Fatima!".
     * @param string[]    $introLines  Translated body lines (one paragraph each).
     * @param string|null $actionUrl   CTA button URL — omit for no button.
     * @param string|null $actionText  Translated CTA button label.
     * @param string[]    $outroLines  Optional lines rendered after the action button.
     */
    public function __construct(
        private readonly string $subject,
        private readonly string $greeting,
        private readonly array $introLines,
        private readonly ?string $actionUrl = null,
        private readonly ?string $actionText = null,
        private readonly array $outroLines = [],
    ) {}

    /** @param \App\Models\Registration $notifiable */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /** @param \App\Models\Registration $notifiable */
    public function toMail($notifiable): MailMessage
    {
        $msg = (new MailMessage)
            ->subject($this->subject)
            ->greeting($this->greeting);

        foreach ($this->introLines as $line) {
            $msg->line($line);
        }

        if ($this->actionUrl && $this->actionText) {
            $msg->action($this->actionText, $this->actionUrl);
        }

        foreach ($this->outroLines as $line) {
            $msg->line($line);
        }

        return $msg->salutation(config('app.name') . ' Team');
    }
}
