<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSentMessage
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        $message = $event->message;

        \Log::info('Email was sent successfully!', [
            'subject' => $message->getSubject(),
            'to' => collect($message->getTo())->keys()->toArray(),
            'from' => collect($message->getFrom())->keys()->toArray(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
