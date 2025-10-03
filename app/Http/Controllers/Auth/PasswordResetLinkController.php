<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SendGridService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            // Check if user exists
            $user = \App\Models\User::where('email', $request->email)->first();

            if (!$user) {
                // For security, don't reveal if email doesn't exist
                return response()->json([
                    'success' => true,
                    'message' => '✅ If that email exists in our system, you will receive a password reset link shortly.'
                ]);
            }

            // Create password reset token
            $token = Str::random(64);

            // Store token in database
            \DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => hash('sha256', $token),
                    'created_at' => now()
                ]
            );

            // Generate reset URL
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $request->email,
            ], false));

            // Try SendGrid API first (bypasses SMTP port blocking)
            $sendGridService = new SendGridService();
            $sent = $sendGridService->sendPasswordResetEmail(
                $user->email,
                $resetUrl,
                $user->name
            );

            if ($sent) {
                \Log::info('Password reset email sent via SendGrid API', [
                    'email' => $user->email
                ]);

                return response()->json([
                    'success' => true,
                    'message' => '✅ Password reset link sent! Please check your email (including spam folder).'
                ]);
            }

            // If SendGrid API fails, log and return graceful error
            \Log::warning('SendGrid API failed, email not sent', [
                'email' => $user->email
            ]);

            return response()->json([
                'success' => false,
                'message' => '⚠️ Unable to send email at this time. Please try again later or contact support.'
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Password reset failed', [
                'error' => $e->getMessage(),
                'type' => get_class($e),
                'email' => $request->email
            ]);

            return response()->json([
                'success' => false,
                'message' => '⚠️ Unable to send email. For demo purposes, please create a new account or contact admin for assistance.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
