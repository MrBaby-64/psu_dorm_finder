<?php

namespace App\Services;

use SendGrid;
use SendGrid\Mail\Mail;
use Illuminate\Support\Facades\Log;

class SendGridService
{
    protected $sendgrid;

    public function __construct()
    {
        $apiKey = config('services.sendgrid.api_key');
        $this->sendgrid = new SendGrid($apiKey);
    }

    /**
     * Send password reset email using SendGrid API
     */
    public function sendPasswordResetEmail($to, $resetUrl, $userName = null)
    {
        try {
            $email = new Mail();
            $email->setFrom(
                config('mail.from.address'),
                config('mail.from.name')
            );
            $email->setSubject('Reset Your PSU Dorm Finder Password');
            $email->addTo($to, $userName ?? 'User');

            // Simple HTML email
            $htmlContent = $this->getPasswordResetHtml($resetUrl, $userName);
            $email->addContent("text/html", $htmlContent);

            $response = $this->sendgrid->send($email);

            Log::info('SendGrid API Response', [
                'status' => $response->statusCode(),
                'to' => $to
            ]);

            return $response->statusCode() >= 200 && $response->statusCode() < 300;

        } catch (\Exception $e) {
            Log::error('SendGrid API Error', [
                'error' => $e->getMessage(),
                'to' => $to
            ]);
            return false;
        }
    }

    /**
     * Generate password reset email HTML
     */
    private function getPasswordResetHtml($resetUrl, $userName)
    {
        $name = $userName ?? 'there';

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Reset Your Password</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='color: white; margin: 0; font-size: 28px;'>🔐 Password Reset Request</h1>
            </div>

            <div style='background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #ddd;'>
                <p style='font-size: 16px; margin-bottom: 20px;'>Hello {$name},</p>

                <p style='font-size: 14px; color: #666; margin-bottom: 25px;'>
                    We received a request to reset your password for your <strong>PSU Dorm Finder</strong> account.
                    If you didn't make this request, you can safely ignore this email.
                </p>

                <div style='text-align: center; margin: 35px 0;'>
                    <a href='{$resetUrl}'
                       style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                              color: white;
                              padding: 15px 40px;
                              text-decoration: none;
                              border-radius: 5px;
                              font-weight: bold;
                              font-size: 16px;
                              display: inline-block;'>
                        Reset My Password
                    </a>
                </div>

                <p style='font-size: 13px; color: #888; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>
                    ⏱️ <strong>This link will expire in 60 minutes</strong> for security reasons.
                </p>

                <p style='font-size: 12px; color: #999; margin-top: 20px;'>
                    If the button doesn't work, copy and paste this URL into your browser:<br>
                    <span style='color: #667eea; word-break: break-all;'>{$resetUrl}</span>
                </p>

                <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center;'>
                    <p style='font-size: 12px; color: #999; margin: 5px 0;'>
                        PSU Dorm Finder - Your trusted platform for finding accommodation
                    </p>
                    <p style='font-size: 11px; color: #bbb; margin: 5px 0;'>
                        This is an automated email. Please do not reply.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
