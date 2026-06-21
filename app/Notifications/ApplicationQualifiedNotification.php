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
            ->greeting('Congratulations!')
            ->line('Your application to '.config('school.name').' has been approved. You are now a bona fide student.')
            ->line('Use the credentials below to log in:')
            ->line('**School ID:** '.$this->schoolId)
            ->line('**Default Password:** '.$this->defaultPassword)
            ->action('Log In', route('login'))
            ->line('For your security, you will be asked to change your password on first login. You can then proceed to enroll for the semester.')
            ->salutation('— '.config('school.name').' · powered by '.config('school.platform'));
    }
}
