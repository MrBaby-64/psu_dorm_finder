<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // Log request for debugging
        \Log::info('Password reset requested', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'mail_driver' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host')
        ]);

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            // Check if user exists first
            $user = \App\Models\User::where('email', $request->email)->first();

            if (!$user) {
                // For security, don't reveal if email doesn't exist
                return response()->json([
                    'success' => true,
                    'message' => '✅ If that email exists in our system, you will receive a password reset link shortly.'
                ]);
            }

            // Attempt to send the password reset link with timeout protection
            $status = Password::sendResetLink(
                $request->only('email')
            );

            \Log::info('Password reset status: ' . $status);

            // If successful, return success message
            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => '✅ Password reset link sent! Please check your email (including spam folder).'
                ]);
            }

            // Handle throttling
            if ($status === Password::RESET_THROTTLED) {
                return response()->json([
                    'success' => false,
                    'message' => '⏱️ Please wait before requesting another reset link.'
                ], 429);
            }

            // Generic success for security
            return response()->json([
                'success' => true,
                'message' => '✅ If that email exists in our system, you will receive a password reset link shortly.'
            ]);

        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            // Symfony Mailer transport error (Laravel 9+)
            \Log::error('Mail Transport Error', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);

            return response()->json([
                'success' => false,
                'message' => '📧 Email service temporarily unavailable. For your presentation, please use the demo account or contact admin.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);

        } catch (\Swift_TransportException $e) {
            // Swift Mailer transport error (Laravel 8 and older)
            \Log::error('SMTP Error in password reset', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);

            return response()->json([
                'success' => false,
                'message' => '📧 Email service temporarily unavailable. For your presentation, please use the demo account or contact admin.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Password reset failed', [
                'error' => $e->getMessage(),
                'type' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            // Return graceful error message
            return response()->json([
                'success' => false,
                'message' => '⚠️ Unable to send email. For demo purposes, please create a new account or contact admin for assistance.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
