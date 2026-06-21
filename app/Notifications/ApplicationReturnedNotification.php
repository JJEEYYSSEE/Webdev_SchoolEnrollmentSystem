<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationReturnedNotification extends Notification
{
    public string $remarks;

    public function __construct(string $remarks)
    {
        $this->remarks = $remarks;
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
            ->subject('Action needed on your application — '.config('school.short'))
            ->greeting('Hello!')
            ->line('Your application to '.config('school.name').' needs correction before it can be approved:')
            ->line('"'.$this->remarks.'"')
            ->action('Fix My Application', route('application.show'))
            ->line('Please log in, re-upload the correct document(s), and resubmit. Your password is unchanged.')
            ->salutation('— '.config('school.name').' · powered by '.config('school.platform'));
    }
}
