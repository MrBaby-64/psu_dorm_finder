<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * New Password Controller
 * Handles password reset completion after email link
 */
class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    // Handle password reset form submission
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            // Find the token in database
            $passwordReset = \DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            // Check if token exists
            if (!$passwordReset) {
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'This password reset link is invalid or has expired.']);
            }

            // Verify token matches (token is hashed in database)
            if (hash('sha256', $request->token) !== $passwordReset->token) {
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'This password reset link is invalid.']);
            }

            // Check if token has expired (60 minutes)
            $tokenAge = now()->diffInMinutes($passwordReset->created_at);
            if ($tokenAge > 60) {
                // Delete expired token
                \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'This password reset link has expired. Please request a new one.']);
            }

            // Find user
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'We could not find a user with that email address.']);
            }

            // Update password
            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            // Delete the used token
            \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Trigger password reset event
            event(new PasswordReset($user));

            return redirect()->route('login')->with('status', 'Your password has been reset successfully. You can now log in with your new password.');

        } catch (\Exception $e) {
            \Log::error('Password reset error', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'An error occurred while resetting your password. Please try again.']);
        }
    }
}
