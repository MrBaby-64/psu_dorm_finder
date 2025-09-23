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
        Log::info('Registration started. Active DB connection: ' . config('database.default'), [
            'email' => $request->email,
            'role' => $request->role,
            'request_method' => $request->method(),
            'all_request_data' => $request->all()
        ]);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:tenant,landlord'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
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
        ];

        try {
            $validated = $request->validate($rules, $messages);
        } catch (ValidationException $e) {
            Log::warning('Validation failed during registration', [
                'email' => $request->email,
                'role' => $request->role,
                'errors' => $e->errors(),
                'request_data' => [
                    'has_address' => !empty($request->address),
                    'has_city' => !empty($request->city),
                    'has_phone' => !empty($request->phone),
                ]
            ]);
            throw $e;
        }

        Log::info('Registration validation passed', ['validated_keys' => array_keys($validated), 'user_id' => null]);

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

            Log::info('Registration successful', ['user_id' => $user->id, 'role' => $user->role, 'email' => $user->email]);

            event(new Registered($user));
            Auth::login($user);

            Log::info('User logged in successfully', ['user_id' => $user->id]);

            return $user->isLandlord()
                ? redirect()->route('landlord.account')->with('success', 'Registration successful! Welcome to PSU Dorm Finder.')
                : redirect()->route('tenant.account')->with('success', 'Registration successful! Welcome to PSU Dorm Finder.');

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'first_error' => $e->getMessage(),
                'validated_keys' => array_keys($validated),
                'request_keys' => array_keys($request->all())
            ]);

            return back()->withInput()->withErrors(['general' => 'Registration failed. Please try again.']);
        }
    }
}
