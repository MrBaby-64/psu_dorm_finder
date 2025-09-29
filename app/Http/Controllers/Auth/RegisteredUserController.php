<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Rate limiting: Max 5 registration attempts per hour per IP
        $key = 'register_attempts:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);

            return back()->withErrors([
                'rate_limit' => "Too many registration attempts. Please try again in {$minutes} minutes."
            ])->withInput();
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:tenant,landlord'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            // reCAPTCHA is required but will be provided by popup modal
            'g-recaptcha-response' => ['required', 'captcha'],
        ];

        if ($request->role === 'tenant') {
            $rules['address'] = ['required', 'string', 'max:500'];
            $rules['city'] = ['required', 'string', 'max:100'];
            $rules['province'] = ['nullable', 'string', 'max:100'];
        }

        if ($request->role === 'landlord') {
            $rules['valid_id'] = ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'];
        }

        $messages = [
            'phone.unique' => 'This phone number is already registered. Please use a different phone number or login if you already have an account.',
            'email.unique' => 'This email address is already registered. Please use a different email or login if you already have an account.',
            'address.required' => 'Please provide your current address so we can show you relevant properties nearby.',
            'city.required' => 'Please select your city from the dropdown list.',
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
            'g-recaptcha-response.captcha' => 'reCAPTCHA verification failed. Please try again.',
        ];

        // Increment rate limit attempt
        RateLimiter::hit($key, 3600); // 1 hour expiry

        $validated = $request->validate($rules, $messages);

        try {
            $user = DB::transaction(function () use ($validated, $request) {
                $userData = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => $validated['role'],
                    'phone' => $validated['phone'],
                ];

                if ($validated['role'] === 'tenant') {
                    $userData['address'] = $validated['address'];
                    $userData['city'] = $validated['city'];
                    $userData['province'] = $validated['province'] ?? 'Pampanga';
                }

                $user = User::create($userData);

                if (!$user) {
                    throw new \Exception('User creation failed');
                }

                if ($validated['role'] === 'landlord' && $request->hasFile('valid_id')) {
                    $idPath = $request->file('valid_id')->store('landlord-ids', 'public');
                    $user->update(['valid_id_path' => $idPath]);
                }

                return $user;
            });

            // Automatically log the user in after successful registration
            Auth::login($user);

            // Clear rate limit on successful registration
            RateLimiter::clear($key);

            // Fire the registered event without email verification
            event(new Registered($user));

            // Redirect to dashboard based on user role
            $redirectRoute = match($user->role) {
                'admin' => 'admin.dashboard',
                'landlord' => 'landlord.account',
                'tenant' => 'tenant.account',
                default => 'dashboard'
            };

            return redirect()->route($redirectRoute)
                ->with('success', 'Welcome! Your account has been created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['general' => 'Registration failed. Please try again.']);
        }
    }
}
