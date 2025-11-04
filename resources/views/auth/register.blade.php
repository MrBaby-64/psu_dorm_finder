@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<script>
    // Automatically open the auth modal when the register page is accessed
    document.addEventListener('DOMContentLoaded', function() {
        openAuthModal();

        // If there are errors or old role, show registration form with proper role selected
        @if (old('role'))
            switchToRegistration();
            selectRole('{{ old('role') }}');
        @else
            // Show role selection by default
            switchToRoleSelection();
        @endif
    });
</script>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full">
        <!-- Hero Section while modal loads -->
        <div class="text-center">
            <div class="text-5xl font-bold text-green-600 mb-4">🏠 Dorm Finder</div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Join Our Community</h1>
            <p class="text-xl text-gray-600 mb-8">Create your account and start your journey to finding the perfect accommodation</p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <button onclick="openAuthModal(); switchToRoleSelection();"
                        class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition font-semibold shadow-lg">
                    Create New Account
                </button>
                <button onclick="openAuthModal(); switchToLogin();"
                        class="bg-white text-green-600 border-2 border-green-600 px-8 py-3 rounded-lg hover:bg-green-50 transition font-semibold">
                    Already Have an Account?
                </button>
            </div>

            <!-- Benefits Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-16">
                <!-- For Tenants -->
                <div class="bg-blue-50 p-6 rounded-xl shadow-sm border-2 border-blue-200">
                    <div class="text-4xl mb-3">👤</div>
                    <h3 class="font-bold text-xl text-blue-700 mb-3">For Students & Renters</h3>
                    <ul class="text-left text-gray-700 text-sm space-y-2">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            Browse verified properties near PSU
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            Schedule property visits easily
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            Direct messaging with landlords
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            Save your favorite properties
                        </li>
                    </ul>
                    <button onclick="openAuthModal(); switchToRoleSelection(); setTimeout(() => selectRole('tenant'), 100);"
                            class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                        Sign Up as Tenant
                    </button>
                </div>

                <!-- For Landlords -->
                <div class="bg-green-50 p-6 rounded-xl shadow-sm border-2 border-green-200">
                    <div class="text-4xl mb-3">🏢</div>
                    <h3 class="font-bold text-xl text-green-700 mb-3">For Landlords & Property Owners</h3>
                    <ul class="text-left text-gray-700 text-sm space-y-2">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            List your properties for free
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            Manage inquiries and bookings
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            Reach verified student tenants
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            Track property performance
                        </li>
                    </ul>
                    <button onclick="openAuthModal(); switchToRoleSelection(); setTimeout(() => selectRole('landlord'), 100);"
                            class="mt-4 w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-medium">
                        Sign Up as Landlord
                    </button>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mt-8 max-w-2xl mx-auto bg-red-50 border border-red-400 text-red-800 p-6 rounded-lg">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-bold text-red-800">Registration Failed</span>
                    </div>
                    <div class="space-y-2 text-sm text-left">
                        @foreach ($errors->all() as $error)
                            <div class="text-red-700">• {{ $error }}</div>
                        @endforeach
                    </div>
                    @if ($errors->has('phone') || $errors->has('email'))
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-300 rounded text-yellow-800 text-sm">
                            💡 <strong>Helpful Tips:</strong><br>
                            <ul class="mt-2 space-y-1 text-left">
                                @if ($errors->has('email'))
                                    <li>• Try a different email address</li>
                                @endif
                                @if ($errors->has('phone'))
                                    <li>• Use a different phone number (format: 09XXXXXXXXX)</li>
                                @endif
                                <li>• If you already have an account, <button type="button" onclick="openAuthModal(); switchToLogin();" class="underline text-yellow-900 font-medium">click here to login</button></li>
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Success Message -->
            @if (session('registration_success'))
                <div class="mt-8 max-w-2xl mx-auto bg-green-50 border border-green-400 text-green-800 p-6 rounded-lg">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-bold text-green-800 text-xl">🎉 Registration Successful!</span>
                    </div>
                    <p class="text-green-700 mb-4">Welcome to Dorm Finder, {{ session('user_name') }}!</p>

                    @if (!session('email_failed'))
                        <div class="bg-blue-50 border border-blue-300 rounded p-3 mb-4">
                            <p class="text-blue-800 text-sm">📧 We sent a verification email to:</p>
                            <p class="text-blue-900 font-semibold">{{ session('user_email') }}</p>
                        </div>
                    @endif

                    <div class="bg-white border border-green-300 rounded p-4 text-left">
                        <h3 class="font-semibold text-green-800 mb-2">What's next:</h3>
                        <ol class="text-green-700 text-sm space-y-1">
                            <li>1. Check your email inbox (and spam folder)</li>
                            <li>2. Click the verification link</li>
                            <li>3. Start exploring properties!</li>
                        </ol>
                    </div>

                    <div class="mt-4 flex gap-3">
                        <a href="{{ route('home') }}" class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition text-center font-medium">
                            Go to Homepage
                        </a>
                        <button onclick="openAuthModal(); switchToLogin();" class="flex-1 bg-white text-green-600 border-2 border-green-600 py-2 px-4 rounded-lg hover:bg-green-50 transition font-medium">
                            Login Now
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
