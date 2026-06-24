<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify your email — '.config('school.short'))
                ->markdown('emails.verify', ['url' => $url])
                ->withSymfonyMessage(function (\Symfony\Component\Mime\Email $message) {
                    $message->embedFromPath(public_path('images/logo.png'), 'logo');
                });
        });
    }
}
