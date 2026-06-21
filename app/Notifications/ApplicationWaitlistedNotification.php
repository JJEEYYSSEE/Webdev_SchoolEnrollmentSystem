<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationWaitlistedNotification extends Notification
{
    public string $strand;

    public function __construct(string $strand)
    {
        $this->strand = $strand;
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
            ->subject('Your application is waitlisted — '.config('school.short'))
            ->greeting('Hello!')
            ->line('Your application to '.config('school.name').' is qualified, but slots for Grade 11 — '.$this->strand.' are currently full.')
            ->line('You have been placed on the **waitlist**. We will email your School ID and login details the moment a slot opens.')
            ->line('No action is needed from you for now — thank you for your patience.')
            ->salutation('— '.config('school.name').' · powered by '.config('school.platform'));
    }
}
