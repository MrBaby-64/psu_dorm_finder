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
            // Attempt to send the password reset link
            $status = Password::sendResetLink(
                $request->only('email')
            );

            \Log::info('Password reset status: ' . $status);

            // If successful, return success message
            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => '✅ Password reset link sent! Please check your email.'
                ]);
            }

            // If user not found, still return success for security (don't reveal if email exists)
            return response()->json([
                'success' => true,
                'message' => '✅ If that email exists in our system, you will receive a password reset link shortly.'
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Password reset email failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return graceful error message
            return response()->json([
                'success' => false,
                'message' => '⚠️ Unable to send email at this time. Please try again later or contact support.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
