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

            // Enhanced logging before sending
            \Log::info('Password reset initiated', [
                'email' => $request->email,
                'environment' => config('app.env'),
                'mail_driver' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_encryption' => config('mail.mailers.smtp.encryption'),
                'mail_from' => config('mail.from.address'),
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            // Enhanced logging after attempt
            \Log::info('Password reset link attempt completed', [
                'email' => $request->email,
                'status' => $status,
                'status_message' => __($status),
            ]);

            if ($request->wantsJson() || $request->expectsJson()) {
                if ($status == Password::RESET_LINK_SENT) {
                    return response()->json([
                        'success' => true,
                        'message' => 'We have sent a password reset link to your email address. Please check your inbox and click the link to reset your password.'
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => __($status)
                    ], 422);
                }
            }

            if ($status == Password::RESET_LINK_SENT) {
                return back()->with('status', 'We have sent a password reset link to your email address. Please check your inbox and click the link to reset your password.');
            } else {
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Password reset validation failed', [
                'email' => $request->email ?? 'N/A',
                'errors' => $e->errors()
            ]);

            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide a valid email address.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Password reset failed with exception', [
                'email' => $request->email ?? 'N/A',
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to send reset link. Please try again later.'
                ], 500);
            }
            return back()->withErrors(['email' => 'Unable to send reset link. Please try again later.']);
        }
    }
}
