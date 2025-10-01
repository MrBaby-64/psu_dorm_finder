@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3.586l6.879-6.88a6 6 0 018.242 8.242z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Reset Your Password</h2>
            <p class="mt-2 text-sm text-gray-600">Enter your email address below and we'll send you a password reset link</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-6">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-primary-button class="w-full justify-center">
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>
            </div>
        </form>

        <!-- Email Information -->
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-blue-800">🔐 Security Notice</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        We'll send the password reset link to your email address. The link will be valid for 60 minutes for security reasons.
                        Make sure to check your inbox and click the link to reset your password securely.
                    </p>
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
