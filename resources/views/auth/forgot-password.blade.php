{{--
    Forgot Password Page

    This page allows users to request a password reset link via email.
    The system uses SendGrid API to send emails reliably on cloud hosting.

    Features:
    - Email validation
    - AJAX form submission (prevents page reload)
    - Loading state with disabled button during submission
    - Success/error message display
    - Security information for users
--}}

@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg">

        {{-- Page Header --}}
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3.586l6.879-6.88a6 6 0 018.242 8.242z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Reset Your Password</h2>
            <p class="mt-2 text-sm text-gray-600">Enter your email address and we'll send you a reset link</p>
        </div>

        {{-- Session Status Message (shown after form submission if not using AJAX) --}}
        <x-auth-session-status class="mb-4" :status="session('status')" />

        {{-- Security Information Box --}}
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Security Information</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Password reset links are <strong>valid for 60 minutes</strong></li>
                            <li>Check your <strong>spam/junk folder</strong> if you don't see the email</li>
                            <li>For security, we won't reveal if an email exists in our system</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Password Reset Request Form --}}
        <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-6" id="forgot-password-form">
            @csrf

            {{-- Email Input Field --}}
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Status Message (dynamically updated via JavaScript) --}}
            <div id="status-message" class="hidden rounded-md p-4"></div>

            {{-- Submit Button with Loading State --}}
            <div class="mt-6">
                <x-primary-button type="submit" class="w-full justify-center" id="submit-btn">
                    <span id="btn-text">{{ __('Email Password Reset Link') }}</span>
                    <span id="btn-loading" class="hidden">Sending...</span>
                </x-primary-button>
            </div>
        </form>

        {{--
            JavaScript for AJAX Form Submission

            This script handles the password reset form submission without page reload.
            It provides better user experience with loading states and inline error messages.

            Flow:
            1. Prevent default form submission
            2. Show loading state (disable button, change text)
            3. Send AJAX request to server with email
            4. Display success or error message
            5. Re-enable button for retry if needed
        --}}
        <script>
        // Listen for form submission
        document.getElementById('forgot-password-form').addEventListener('submit', async function(e) {
            // Prevent default form submission (page reload)
            e.preventDefault();

            // Get form elements
            const form = this;
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');
            const statusMessage = document.getElementById('status-message');
            const email = document.getElementById('email').value;

            // Step 1: Show loading state
            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
            statusMessage.classList.add('hidden');

            try {
                // Step 2: Send AJAX request to server
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email: email })
                });

                // Step 3: Parse JSON response from server
                const data = await response.json();

                // Step 4: Display appropriate message based on response
                statusMessage.classList.remove('hidden');

                if (data.success) {
                    // Success: Show green message with checkmark
                    statusMessage.className = 'rounded-md p-4 bg-green-50 border border-green-200';
                    statusMessage.innerHTML = `
                        <div class="flex">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="ml-3 text-sm text-green-800">${data.message}</p>
                        </div>
                    `;
                } else {
                    // Error: Show red message with X icon
                    statusMessage.className = 'rounded-md p-4 bg-red-50 border border-red-200';
                    statusMessage.innerHTML = `
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="ml-3 text-sm text-red-800">${data.message}</p>
                        </div>
                    `;
                }
            } catch (error) {
                // Step 5: Handle network/connection errors
                console.error('Network error:', error);
                statusMessage.classList.remove('hidden');
                statusMessage.className = 'rounded-md p-4 bg-red-50 border border-red-200';
                statusMessage.innerHTML = `
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="ml-3 text-sm text-red-800">Connection error. Please check your internet and try again.</p>
                    </div>
                `;
            } finally {
                // Step 6: Always re-enable button after request completes
                submitBtn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            }
        });
        </script>

        {{-- Additional Information Box --}}
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-blue-800">🔐 How It Works</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Enter your email and we'll send you a secure link to reset your password.
                        The link expires in 60 minutes for your security.
                    </p>
                </div>
            </div>
        </div>

        {{-- Back to Login Link --}}
        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800 underline">
                ← Back to Login
            </a>
        </div>
    </div>
</div>
@endsection
