<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SendGridService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Password Reset Link Controller
 *
 * Handles password reset requests for the PSU Dorm Finder application.
 * Uses SendGrid HTTP API for reliable email delivery on cloud hosting.
 */
class PasswordResetLinkController extends Controller
{
    /**
     * Display the forgot password page
     *
     * @return View The forgot password form view
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Process password reset request and send email
     *
     * This method validates the email, generates a secure token,
     * stores it in the database, and sends a reset link via SendGrid API.
     *
     * @param Request $request The HTTP request containing the email
     * @return \Illuminate\Http\JsonResponse JSON response with success/error status
     */
    public function store(Request $request)
    {
        // Validate that email is provided and properly formatted
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            // Step 1: Look up user by email address
            $user = \App\Models\User::where('email', $request->email)->first();

            // Step 2: Security measure - don't reveal if email exists or not
            // This prevents attackers from discovering valid email addresses
            if (!$user) {
                return response()->json([
                    'success' => true,
                    'message' => '✅ If that email exists in our system, you will receive a password reset link shortly.'
                ]);
            }

            // Step 3: Generate a secure random token (64 characters)
            $token = Str::random(64);

            // Step 4: Store the hashed token in password_reset_tokens table
            // The token is hashed using SHA-256 for security
            // If email already has a token, it will be replaced (updateOrInsert)
            \DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => hash('sha256', $token),
                    'created_at' => now()
                ]
            );

            // Step 5: Build the password reset URL with token and email
            // User will click this link to reset their password
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $request->email,
            ], false));

            // Step 6: Send email using SendGrid HTTP API
            // We use HTTP API instead of SMTP to avoid port blocking issues
            // on free hosting platforms like Render
            $sendGridService = new SendGridService();
            $sent = $sendGridService->sendPasswordResetEmail(
                $user->email,
                $resetUrl,
                $user->name
            );

            // Step 7: Check if email was sent successfully
            if ($sent) {
                // Log successful email delivery for monitoring
                \Log::info('Password reset email sent successfully', [
                    'email' => $user->email,
                    'timestamp' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => '✅ Password reset link sent! Please check your email (including spam folder).'
                ]);
            }

            // Step 8: If SendGrid fails, log the issue and notify user
            \Log::warning('Email service unavailable', [
                'email' => $user->email,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => false,
                'message' => '⚠️ Unable to send email at this time. Please try again later or contact support.'
            ], 500);

        } catch (\Exception $e) {
            // Step 9: Catch any unexpected errors and log them
            // This helps with debugging if something goes wrong
            \Log::error('Password reset process failed', [
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
                'timestamp' => now()
            ]);

            // Return user-friendly error message
            return response()->json([
                'success' => false,
                'message' => '⚠️ Unable to process request. Please try again later or contact support.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
