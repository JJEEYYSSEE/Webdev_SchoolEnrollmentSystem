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
            ->subject('Your password change code — '.config('school.short'))
            ->markdown('emails.otp', ['code' => $this->code])
            ->withSymfonyMessage(function (\Symfony\Component\Mime\Email $message) {
                $message->embedFromPath(public_path('images/logo.png'), 'logo');
            });
    }
}
