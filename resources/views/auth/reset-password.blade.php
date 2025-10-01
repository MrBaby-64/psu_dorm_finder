@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3.586l6.879-6.88a6 6 0 018.242 8.242z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Set New Password</h2>
            <p class="mt-2 text-sm text-gray-600">Enter your new password below to complete the reset process</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.store') }}" class="mt-8 space-y-6">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('New Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <x-primary-button class="w-full justify-center">
                    {{ __('Reset Password') }}
                </x-primary-button>
            </div>
        </form>

        <!-- Security Information -->
        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-green-800">🔒 Security Tips</h3>
                    <ul class="text-sm text-green-700 mt-1 list-disc list-inside space-y-1">
                        <li>Use a strong password with at least 8 characters</li>
                        <li>Include uppercase, lowercase, numbers, and symbols</li>
                        <li>Don't reuse passwords from other accounts</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Back to Login -->
        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800 underline">
                ← Back to Login
            </a>
        </div>
    </div>
</div>
@endsection
