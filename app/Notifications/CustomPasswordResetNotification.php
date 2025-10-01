<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomPasswordResetNotification extends Notification
{

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        try {
            $actionUrl = url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            \Log::info('CustomPasswordResetNotification: Preparing email', [
                'email' => $notifiable->getEmailForPasswordReset(),
                'action_url' => $actionUrl,
                'token_length' => strlen($this->token),
                'mail_config' => [
                    'mailer' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                    'from' => config('mail.from.address'),
                    'username' => config('mail.mailers.smtp.username'),
                ]
            ]);

            $mailMessage = (new MailMessage)
                ->subject('Reset Your PSU Dorm Finder Password')
                ->view('emails.password-reset', [
                    'actionUrl' => $actionUrl,
                    'user' => $notifiable,
                ]);

            \Log::info('CustomPasswordResetNotification: Mail message created successfully');

            return $mailMessage;
        } catch (\Exception $e) {
            \Log::error('CustomPasswordResetNotification: Error creating mail message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}