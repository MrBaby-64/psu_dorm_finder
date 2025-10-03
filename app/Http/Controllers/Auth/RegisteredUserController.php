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
use Illuminate\Support\Facades\Http;

/**
 * User Registration Controller
 * Handles new user account creation with reCAPTCHA verification
 */
class RegisteredUserController extends Controller
{
    // Show registration page
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Determine if reCAPTCHA should be enforced - real-world approach
     */
    private function shouldEnforceRecaptcha($request): bool
    {
        // Skip in local/development environments
        if (app()->environment(['local', 'development', 'testing'])) {
            return false;
        }

        // Skip if keys aren't properly configured
        if (empty(config('captcha.sitekey')) || empty(config('captcha.secret'))) {
            Log::warning('reCAPTCHA keys not configured');
            return false;
        }

        // Test the configuration by making a quick API call
        try {
            $testResponse = Http::timeout(5)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('captcha.secret'),
                'response' => 'test', // Invalid response to test connectivity
                'remoteip' => $request->ip(),
            ]);

            if ($testResponse->failed()) {
                Log::warning('reCAPTCHA service unreachable');
                return false;
            }

            $result = $testResponse->json();

            // If we get specific domain errors, disable enforcement temporarily
            $errorCodes = $result['error-codes'] ?? [];
            if (in_array('hostname-mismatch', $errorCodes) ||
                in_array('invalid-input-secret', $errorCodes)) {
                Log::warning('reCAPTCHA configuration issue detected', [
                    'errors' => $errorCodes,
                    'domain' => $request->getHost()
                ]);
                return false;
            }

            return true; // Configuration looks good
        } catch (\Exception $e) {
            Log::error('reCAPTCHA configuration test failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
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
        ];

        // Real-world reCAPTCHA approach: Only enforce in production with proper config
        $shouldEnforceRecaptcha = $this->shouldEnforceRecaptcha($request);

        if ($shouldEnforceRecaptcha) {
            $rules['g-recaptcha-response'] = ['required', 'captcha'];
        } else {
            // In development or when not properly configured, just require the field exists
            $rules['g-recaptcha-response'] = ['required'];
            Log::info('reCAPTCHA enforcement disabled', [
                'environment' => app()->environment(),
                'reason' => 'Development mode or configuration issue'
            ]);
        }

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
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification by checking the "I\'m not a robot" box.',
        ];

        // Increment rate limit attempt
        RateLimiter::hit($key, 3600); // 1 hour expiry

        // Debug reCAPTCHA configuration in non-production environments
        if (app()->environment('local', 'development')) {
            Log::info('reCAPTCHA Debug Info', [
                'site_key' => config('captcha.sitekey'),
                'secret_key_exists' => !empty(config('captcha.secret')),
                'recaptcha_response_exists' => !empty($request->input('g-recaptcha-response')),
                'recaptcha_response_length' => strlen($request->input('g-recaptcha-response', '')),
                'request_ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        }

        try {
            $validated = $request->validate($rules, $messages);
        } catch (ValidationException $e) {
            // Log reCAPTCHA validation failures for debugging
            if ($e->validator->errors()->has('g-recaptcha-response')) {
                Log::warning('reCAPTCHA validation failed', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'recaptcha_response' => $request->input('g-recaptcha-response') ? 'present' : 'missing',
                    'error' => $e->validator->errors()->get('g-recaptcha-response'),
                ]);
            }
            throw $e;
        }

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

            // Fire the registered event - wrapped in try-catch to prevent email errors from blocking registration
            try {
                event(new Registered($user));
            } catch (\Exception $e) {
                Log::error('Email notification failed during registration', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
                // Don't block registration if email fails
            }

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
