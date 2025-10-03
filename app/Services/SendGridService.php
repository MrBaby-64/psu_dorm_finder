<?php

namespace App\Services;

use SendGrid;
use SendGrid\Mail\Mail;
use Illuminate\Support\Facades\Log;

/**
 * SendGrid Email Service
 *
 * This service handles sending emails via SendGrid HTTP API.
 * We use HTTP API (port 443) instead of SMTP (port 587) because
 * many free hosting providers block outbound SMTP connections.
 *
 * Benefits of using SendGrid API:
 * - Works on all hosting platforms (uses HTTPS port 443)
 * - More reliable than SMTP
 * - Faster email delivery
 * - Better error handling and logging
 */
class SendGridService
{
    /**
     * @var SendGrid SendGrid client instance
     */
    protected $sendgrid;

    /**
     * Initialize SendGrid client with API key
     *
     * The API key is stored in config/services.php and
     * retrieved from SENDGRID_API_KEY environment variable
     */
    public function __construct()
    {
        $apiKey = config('services.sendgrid.api_key');
        $this->sendgrid = new SendGrid($apiKey);
    }

    /**
     * Send password reset email via SendGrid API
     *
     * This method constructs and sends a password reset email with a
     * professionally designed HTML template containing the reset link.
     *
     * @param string $to Recipient email address
     * @param string $resetUrl The password reset URL with token
     * @param string|null $userName Optional user's name for personalization
     * @return bool True if email sent successfully, false otherwise
     */
    public function sendPasswordResetEmail($to, $resetUrl, $userName = null)
    {
        try {
            // Create new email message
            $email = new Mail();

            // Set sender information from config
            $email->setFrom(
                config('mail.from.address'),
                config('mail.from.name')
            );

            // Set email subject
            $email->setSubject('Reset Your PSU Dorm Finder Password');

            // Set recipient
            $email->addTo($to, $userName ?? 'User');

            // Generate HTML content for the email
            $htmlContent = $this->getPasswordResetHtml($resetUrl, $userName);
            $email->addContent("text/html", $htmlContent);

            // Send email via SendGrid API
            $response = $this->sendgrid->send($email);

            // Log the response for monitoring
            Log::info('SendGrid email sent', [
                'status' => $response->statusCode(),
                'recipient' => $to
            ]);

            // Check if response code is in success range (200-299)
            return $response->statusCode() >= 200 && $response->statusCode() < 300;

        } catch (\Exception $e) {
            // Log any errors that occur during sending
            Log::error('SendGrid sending failed', [
                'error' => $e->getMessage(),
                'recipient' => $to
            ]);
            return false;
        }
    }

    /**
     * Generate HTML template for password reset email
     *
     * Creates a responsive, professional-looking HTML email with:
     * - Branded header with gradient
     * - Clear call-to-action button
     * - Security information (60 minute expiry)
     * - Fallback plain URL if button doesn't work
     *
     * @param string $resetUrl The password reset URL
     * @param string|null $userName User's name for personalization
     * @return string Complete HTML email template
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
