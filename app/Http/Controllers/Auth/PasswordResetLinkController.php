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
        try {
            $validated = $request->validate([
                'email' => ['required', 'email'],
            ]);

            // Send password reset link with timeout handling
            try {
                $status = Password::sendResetLink(
                    $request->only('email')
                );
            } catch (\Exception $emailError) {
                \Log::error('Email sending error during password reset: ' . $emailError->getMessage(), [
                    'email' => $request->email,
                    'error_type' => get_class($emailError)
                ]);
                // For presentation: Don't block users if email fails
                return back()->with('status', '✅ For demo purposes: Password reset is temporarily disabled. Please create a new account or contact admin for assistance.');
            }

            if ($request->wantsJson() || $request->expectsJson()) {
                if ($status == Password::RESET_LINK_SENT) {
                    return response()->json([
                        'success' => true,
                        'message' => 'We have sent a password reset link to your email address. Please check your inbox and click the link to reset your password.'
                    ], 200);
                } else if ($status == Password::RESET_THROTTLED) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please wait before requesting another password reset link. You can try again in a few minutes.'
                    ], 429);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => __($status)
                    ], 422);
                }
            }

            if ($status == Password::RESET_LINK_SENT) {
                return back()->with('status', '✅ Password reset link sent! Please check your email inbox (and spam folder) for the reset link. The link will expire in 60 minutes.');
            } else if ($status == Password::RESET_THROTTLED) {
                return back()->withErrors(['email' => '⏱️ Please wait before requesting another reset link. A link was recently sent to this email. Please check your inbox or try again in a few minutes.']);
            } else {
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide a valid email address.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage(), [
                'email' => $request->email ?? 'unknown',
                'ip' => $request->ip(),
                'exception' => get_class($e)
            ]);

            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to send reset link at this time. Please try again in a few moments. If the problem persists, contact support.'
                ], 500);
            }
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => '⚠️ Unable to send reset link at this time. Please try again in a few moments. If the problem persists, please contact support.']);
        }
    }
}
