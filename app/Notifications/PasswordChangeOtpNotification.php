<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangeOtpNotification extends Notification
{
    public string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your password change verification code')
            ->greeting('Hello!')
            ->line('We received a request to change the password on your account.')
            ->line('Enter this verification code to confirm the change:')
            ->line('**'.$this->code.'**')
            ->line('This code expires in 10 minutes.')
            ->line('If you did not request this, ignore this email — your password will stay the same.');
    }
}
