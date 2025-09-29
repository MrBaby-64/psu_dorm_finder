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
        ];

        // Custom reCAPTCHA validation that handles domain issues
        $rules['g-recaptcha-response'] = [
            'required',
            function ($attribute, $value, $fail) use ($request) {
                // If no response token, fail immediately
                if (empty($value)) {
                    $fail('Please complete the reCAPTCHA verification.');
                    return;
                }

                // Try Google's verification
                try {
                    $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                        'secret' => config('captcha.secret'),
                        'response' => $value,
                        'remoteip' => $request->ip(),
                    ]);

                    $result = $response->json();

                    if (!$result['success']) {
                        $errorCodes = $result['error-codes'] ?? [];

                        // Handle specific domain issues gracefully
                        if (in_array('invalid-input-secret', $errorCodes) ||
                            in_array('missing-input-secret', $errorCodes)) {
                            Log::error('reCAPTCHA secret key issue', ['errors' => $errorCodes]);
                            $fail('reCAPTCHA configuration error. Please contact support.');
                        } elseif (in_array('hostname-mismatch', $errorCodes)) {
                            Log::warning('reCAPTCHA domain not configured', [
                                'domain' => $request->getHost(),
                                'errors' => $errorCodes
                            ]);
                            // For domain mismatch in production, log but allow registration
                            // This is temporary until domain is properly configured
                            Log::info('Allowing registration despite domain mismatch - temporary measure');
                            return; // Allow registration
                        } else {
                            Log::warning('reCAPTCHA verification failed', ['errors' => $errorCodes]);
                            $fail('reCAPTCHA verification failed. Please try again.');
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('reCAPTCHA verification exception', [
                        'error' => $e->getMessage(),
                        'domain' => $request->getHost()
                    ]);

                    // In case of network/service issues, log but allow registration
                    Log::info('Allowing registration due to reCAPTCHA service issue');
                    return;
                }
            }
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
