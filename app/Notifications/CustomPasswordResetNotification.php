<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Custom Password Reset Notification
 *
 * IMPORTANT: This notification is currently NOT IN USE.
 * The system uses SendGridService directly for password resets
 * (see app/Services/SendGridService.php and PasswordResetLinkController.php)
 *
 * This class is kept as a backup in case you want to use Laravel's
 * built-in Password::sendResetLink() method in the future.
 *
 * To use this notification:
 * 1. Make sure User model has sendPasswordResetNotification() method
 * 2. Use Password::sendResetLink() in PasswordResetLinkController
 * 3. Configure SMTP settings in .env file
 */
class CustomPasswordResetNotification extends Notification
{
    /**
     * @var string The password reset token
     */
    public $token;

    /**
     * Create a new notification instance
     *
     * @param string $token The generated password reset token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification delivery channels
     *
     * @param object $notifiable The user receiving the notification
     * @return array<int, string> Array of delivery channels (currently only 'mail')
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail message for password reset
     *
     * This method generates the email content and reset URL that will
     * be sent to the user. It uses the custom email template located at
     * resources/views/emails/password-reset.blade.php
     *
     * @param object $notifiable The user receiving the notification
     * @return MailMessage The configured mail message
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Build the password reset URL with token and email
        $actionUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Send email using custom view template
        return (new MailMessage)
            ->subject('Reset Your PSU Dorm Finder Password')
            ->view('emails.password-reset', [
                'actionUrl' => $actionUrl,
                'user' => $notifiable,
            ]);
    }

    /**
     * Get array representation of notification (for database storage)
     *
     * @param object $notifiable The user receiving the notification
     * @return array<string, mixed> Array representation of the notification
     */
    public function toArray(object $notifiable): array
    {
        return [
            // Currently not storing notifications in database
        ];
    }
}