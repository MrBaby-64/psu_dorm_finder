<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

/**
 * Google OAuth Controller
 * Handles Google Sign-In authentication
 */
class GoogleController extends Controller
{
    // Redirect to Google OAuth page
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // Update Google ID if not set
                if (!$existingUser->google_id) {
                    $existingUser->update(['google_id' => $googleUser->getId()]);
                }

                // Log them in directly
                Auth::login($existingUser);
                return redirect($this->getRedirectUrl($existingUser));
            }

            // Create new user and log them in immediately
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(24)), // Generate random password
                'role' => 'tenant', // Default role for Google sign-ups
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(), // Auto-verify Google users
            ]);

            // Fire the registered event and log them in
            event(new Registered($user));
            Auth::login($user);

            return redirect($this->getRedirectUrl($user))
                ->with('success', 'Welcome! Your account has been created via Google.');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['google' => 'Unable to authenticate with Google. Please try again or use email/password login.']);
        }
    }

    /**
     * Get the appropriate redirect URL based on user role
     */
    private function getRedirectUrl($user)
    {
        switch ($user->role) {
            case 'admin':
                return route('admin.dashboard');
            case 'landlord':
                return route('landlord.account');
            case 'tenant':
                return route('tenant.account');
            default:
                return route('home');
        }
    }
}