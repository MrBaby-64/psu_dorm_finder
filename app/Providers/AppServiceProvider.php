<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSent;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Set default timezone for the application
        date_default_timezone_set('Asia/Manila');

        if (app()->environment('production')) {
            // Make all generated URLs use https
            URL::forceScheme('https');

            // Also respect APP_URL if you set a custom domain
            if ($appUrl = config('app.url')) {
                URL::forceRootUrl($appUrl);
            }
        }

        // Log when emails are sent
        Event::listen(MessageSent::class, \App\Listeners\LogSentMessage::class);
    }
}
