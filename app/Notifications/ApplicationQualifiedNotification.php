<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationQualifiedNotification extends Notification
{
    public string $schoolId;
    public string $defaultPassword;

    public function __construct(string $schoolId, string $defaultPassword)
    {
        $this->schoolId = $schoolId;
        $this->defaultPassword = $defaultPassword;
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
            ->subject('You are qualified to enroll — '.config('school.short'))
            ->markdown('emails.qualified', [
                'schoolId' => $this->schoolId,
                'password' => $this->defaultPassword,
                'loginUrl' => route('login'),
            ])
            ->withSymfonyMessage(function (\Symfony\Component\Mime\Email $message) {
                $message->embedFromPath(public_path('images/logo.png'), 'logo');
            });
    }
}
