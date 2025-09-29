@extends('layouts.guest')

@section('title', 'Register')

@push('styles')
<style>
    .backdrop-blur-md {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    /* Debug styles for modal */
    #mainRecaptchaModal {
        background-color: rgba(0, 0, 0, 0.7) !important;
    }

    #mainRecaptchaModal.hidden {
        display: none !important;
    }

    #mainRecaptchaModal:not(.hidden) {
        display: block !important;
    }
</style>
@endpush

@section('content')
@php
    // convenience: what role was previously selected (if any)
    $oldRole = old('role');
@endphp

<!-- Success Popup Modal -->
@if(session('registration_success'))
<div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8 transform animate-pulse">
        <!-- Success Header -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-green-600 mb-2">🎉 Success!</h2>
            <p class="text-gray-700">Welcome to PSU Dorm Finder, {{ session('user_name') }}!</p>
        </div>

        <!-- Message -->
        <div class="mb-6">
            @if(session('email_failed'))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <p class="text-yellow-800 text-sm">⚠️ Email sending failed, but your account was created successfully!</p>
            </div>
            @else
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-blue-800 text-sm">📧 We sent a verification email to:</p>
                <p class="text-blue-900 font-semibold">{{ session('user_email') }}</p>
            </div>
            @endif

            <p class="text-gray-700 text-sm">{{ session('success_message') }}</p>
        </div>

        <!-- Instructions -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-green-800 mb-2">What's next:</h3>
            <ol class="text-green-700 text-sm space-y-1">
                <li>1. Check your Gmail inbox (and spam folder)</li>
                <li>2. Click the verification link</li>
                <li>3. You'll be automatically logged in!</li>
            </ol>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <button onclick="closeSuccessModal()" class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors font-semibold">
                Got it!
            </button>
            <button onclick="window.location.href='{{ route('home') }}'" class="w-full bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors">
                Go to Homepage
            </button>
        </div>
    </div>
</div>
@endif

<div class="min-h-screen flex items-center justify-center py-12 px-4" style="background: linear-gradient(to bottom right, #dcfce7, #dbeafe);">

    <!-- Role Selection Modal -->
    <div id="roleModal" class="{{ $oldRole ? 'hidden' : '' }} max-w-md w-full">
        <div class="bg-white rounded-3xl p-8 shadow-2xl">
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold text-green-600 mb-2">PSU Dorm Finder</h2>
                <p class="text-lg text-gray-700">How can we help you today?</p>
            </div>

            <div class="space-y-4">
                <button type="button" onclick="console.log('Button clicked!'); selectRole('tenant');" class="w-full p-4 border-2 border-blue-300 bg-blue-50 rounded-xl hover:bg-blue-100 hover:shadow-lg transition-all">
                    <div class="flex items-center justify-center gap-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="text-lg font-semibold text-blue-700">I'm looking for a place to rent</span>
                    </div>
                </button>

                <button type="button" onclick="console.log('Landlord button clicked!'); selectRole('landlord');" class="w-full p-4 border-2 border-green-300 bg-green-50 rounded-xl hover:bg-green-100 hover:shadow-lg transition-all">
                    <div class="flex items-center justify-center gap-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="text-lg font-semibold text-green-700">I want to post my rental property</span>
                    </div>
                </button>
            </div>

            <div class="text-center mt-6 text-sm text-gray-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-green-600 hover:underline font-semibold">Login here</a>
            </div>
        </div>
    </div>

    <!-- Registration Form -->
    <!-- show the form immediately if old role exists (so validation errors re-show the fields) -->
    <div id="registrationForm" class="{{ $oldRole ? '' : 'hidden' }} max-w-xl w-full">
        <div class="bg-white rounded-2xl shadow-2xl p-10">

            <!-- Show validation errors -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-2 border-red-400 text-red-800 p-6 rounded-xl shadow-lg">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-red-800 mb-2">❌ Registration Failed</h3>
                            <p class="text-sm text-red-700 mb-3">Please fix the following issues:</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-start gap-2 p-3 bg-red-100 border border-red-300 rounded-lg">
                                <span class="text-red-600 text-lg">•</span>
                                <span class="text-sm font-medium text-red-800">{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if ($errors->has('phone') || $errors->has('email'))
                        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-300 rounded-lg">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-yellow-800 text-sm">💡 Helpful Tips:</h4>
                                    <ul class="text-xs text-yellow-700 mt-1 space-y-1">
                                        @if ($errors->has('email'))
                                            <li>• Try a different email address (like: yourname_{{ date('Y') }}@gmail.com)</li>
                                        @endif
                                        @if ($errors->has('phone'))
                                            <li>• Use a different phone number (like: 0917-XXX-XXXX)</li>
                                        @endif
                                        <li>• If you already have an account, <a href="{{ route('login') }}" class="underline font-semibold">click here to login instead</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <h2 class="text-3xl font-bold mb-8 text-center text-green-600">Create Your Account</h2>
            
            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" id="registrationFormElement">
                @csrf
                <input type="hidden" id="roleInput" name="role" value="{{ $oldRole }}">
                <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" value="">

                <div class="space-y-5">
                    <div>
                        <label class="block font-semibold mb-2">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border-2 rounded-xl focus:border-green-500 focus:outline-none">
                        @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 border-2 rounded-xl focus:border-green-500 focus:outline-none @error('email') border-red-500 bg-red-50 @enderror">
                        @error('email')
                            <div class="mt-2 p-2 bg-red-100 border border-red-300 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-red-700 text-sm font-medium">{{ $message }}</span>
                                </div>
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">Phone *</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required
                               class="w-full px-4 py-3 border-2 rounded-xl focus:border-green-500 focus:outline-none @error('phone') border-red-500 bg-red-50 @enderror"
                               placeholder="e.g., 09123456789">
                        <p class="text-xs text-gray-600 mt-1">Enter a unique phone number (format: 09XXXXXXXXX)</p>
                        @error('phone')
                            <div class="mt-2 p-3 bg-red-100 border-2 border-red-400 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-red-700 text-sm font-bold">{{ $message }}</span>
                                </div>
                                <div class="mt-2 text-xs text-yellow-700 bg-yellow-50 border border-yellow-300 rounded p-2">
                                    💡 <strong>Tip:</strong> Try a different number like: 0917{{ rand(1000000, 9999999) }}
                                </div>
                            </div>
                        @enderror
                    </div>

                    <!-- Tenant fields (ALWAYS VISIBLE - controlled by JavaScript) -->
                    <div id="addressSection" style="display: none;">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 text-sm text-blue-800">
                            📍 <strong>Help us show you nearby properties!</strong><br>
                            Please provide your current address so we can show relevant dormitories in your area.
                            <br><strong class="text-red-600">⚠️ Both address and city are required for tenant accounts.</strong>
                        </div>
                        <div>
                            <label class="block font-semibold mb-2">Current Address *</label>
                            <textarea name="address" class="w-full px-4 py-3 border-2 rounded-xl focus:border-green-500 focus:outline-none" rows="2">{{ old('address') }}</textarea>
                            @error('address')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block font-semibold mb-2">City *</label>
                                <select name="city" class="w-full px-4 py-3 border-2 rounded-xl focus:border-green-500 focus:outline-none" required>
                                    <option value="">Select your city *</option>
                                    <option value="Bacolor" {{ old('city')=='Bacolor' ? 'selected' : '' }}>Bacolor</option>
                                    <option value="San Fernando" {{ old('city')=='San Fernando' ? 'selected' : '' }}>San Fernando</option>
                                    <option value="Angeles City" {{ old('city')=='Angeles City' ? 'selected' : '' }}>Angeles City</option>
                                    <option value="Mabalacat" {{ old('city')=='Mabalacat' ? 'selected' : '' }}>Mabalacat</option>
                                    <option value="Other" {{ old('city')=='Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('city')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                            
                            <div>
                                <label class="block font-semibold mb-2">Province</label>
                                <input type="text" name="province" value="{{ old('province', 'Pampanga') }}" readonly class="w-full px-4 py-3 border-2 rounded-xl bg-gray-100 text-gray-600">
                            </div>
                        </div>
                    </div>

                    <!-- Landlord fields (ALWAYS HIDDEN - controlled by JavaScript) -->
                    <div id="validIdSection" style="display: none;">
                        <label class="block font-semibold mb-2">Valid ID (Optional)</label>
                        <input type="file" name="valid_id" accept="image/*,.pdf" class="w-full border-2 rounded-xl p-3">
                        <p class="text-sm text-gray-600 mt-1">You can upload your valid ID later for verification</p>
                        @error('valid_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">Password *</label>
                        <input type="password" name="password" required class="w-full px-4 py-3 border-2 rounded-xl focus:border-green-500 focus:outline-none">
                        @error('password')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required class="w-full px-4 py-3 border-2 rounded-xl focus:border-green-500 focus:outline-none">
                    </div>

                    <!-- Security verification notice -->
                    <div class="flex justify-center">
                        <div class="w-full p-4 bg-blue-50 border border-blue-200 rounded-xl text-center">
                            <div class="flex items-center justify-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-lg font-semibold text-blue-800">Security Verification</span>
                            </div>
                            <p class="text-sm text-blue-700">A security check will be required before completing registration</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="button" onclick="goBack()" class="flex-1 bg-gray-200 py-3 rounded-xl font-semibold hover:bg-gray-300">
                        Back
                    </button>
                    <button type="submit" class="flex-1 bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 shadow-lg" onclick="console.log('🔧 DEBUG: Submit button clicked!');">
                        Sign Up
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- reCAPTCHA Verification Modal --}}
<div id="mainRecaptchaModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-backdrop absolute inset-0" onclick="closeMainRecaptchaModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="modal-slide-up bg-white rounded-2xl shadow-2xl w-full relative" style="max-width: 450px;">
            <button onclick="closeMainRecaptchaModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="text-center pt-8 pb-6">
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.4 5.4L16 20l-4-4m-4 4a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Security Verification</h2>
                <p class="text-sm text-gray-600 mt-2">Please complete the security check to create your account</p>
            </div>

            <div id="mainRecaptchaModalForm" class="px-8 pb-8">
                <div id="mainRecaptchaModalStatus" class="mb-4 hidden"></div>

                <div class="flex justify-center mb-6">
                    <div id="main-popup-recaptcha" class="inline-block"></div>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeMainRecaptchaModal()" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                        Cancel
                    </button>
                    <button type="button" onclick="completeMainRecaptchaVerification()" id="mainRecaptchaSubmitBtn" class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors font-semibold">
                        Verify & Continue
                    </button>
                </div>

                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-green-800">🛡️ Security Notice</h3>
                            <p class="text-sm text-green-700 mt-1">
                                This verification helps us prevent spam and ensure account security. Complete the reCAPTCHA to proceed with registration.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js?onload=initMainRecaptcha&render=explicit" async defer></script>
<script>
    // Test if JavaScript is working
    console.log('🔧 DEBUG: JavaScript is loading...');

    // Test form submission handler immediately
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔧 DEBUG: DOM loaded, checking form...');
        const form = document.getElementById('registrationFormElement');
        console.log('🔧 DEBUG: Form found:', !!form);
        if (form) {
            console.log('🔧 DEBUG: Form action:', form.action);

            // Add event listener to prevent form submission
            form.addEventListener('submit', function(event) {
                console.log('🔧 DEBUG: Form submit event triggered!');
                event.preventDefault(); // ALWAYS prevent submission

                // Call our handler
                handleFormSubmit(event);

                return false;
            });

            console.log('🔧 DEBUG: Event listener attached successfully');
        } else {
            console.error('🔧 DEBUG: Registration form not found!');
        }
    });
</script>
<script>
    let mainRecaptchaWidgetId = null;
    let mainPendingFormData = null;
    let mainPendingFormElement = null;

    // Initialize reCAPTCHA when Google API loads
    function initMainRecaptcha() {
        console.log('Initializing main page reCAPTCHA...');
        try {
            const container = document.getElementById('main-popup-recaptcha');
            if (container && window.grecaptcha) {
                mainRecaptchaWidgetId = grecaptcha.render('main-popup-recaptcha', {
                    'sitekey': '{{ config('captcha.sitekey') }}',
                    'callback': function(response) {
                        console.log('Main reCAPTCHA completed:', response);
                        document.getElementById('mainRecaptchaModalStatus').classList.add('hidden');
                        document.getElementById('mainRecaptchaSubmitBtn').disabled = false;
                    },
                    'expired-callback': function() {
                        console.log('Main reCAPTCHA expired');
                        showMainRecaptchaError('reCAPTCHA expired. Please verify again.');
                        document.getElementById('mainRecaptchaSubmitBtn').disabled = true;
                    }
                });
                console.log('Main reCAPTCHA initialized successfully');
            } else {
                console.error('Main reCAPTCHA container not found or grecaptcha not loaded');
            }
        } catch (error) {
            console.error('Error initializing main reCAPTCHA:', error);
        }
    }

    // Function to get main reCAPTCHA response
    function getMainRecaptchaResponse() {
        if (mainRecaptchaWidgetId !== null && window.grecaptcha) {
            return grecaptcha.getResponse(mainRecaptchaWidgetId);
        }
        return '';
    }

    // Function to reset main reCAPTCHA
    function resetMainRecaptcha() {
        if (mainRecaptchaWidgetId !== null && window.grecaptcha) {
            grecaptcha.reset(mainRecaptchaWidgetId);
            document.getElementById('mainRecaptchaSubmitBtn').disabled = true;
        }
    }

    // Function to show main reCAPTCHA error
    function showMainRecaptchaError(message) {
        const statusDiv = document.getElementById('mainRecaptchaModalStatus');
        statusDiv.className = 'mb-4 bg-red-50 border border-red-400 text-red-800 p-4 rounded-lg';
        statusDiv.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-bold">Verification Error</span>
            </div>
            <p class="mt-1">${message}</p>
        `;
        statusDiv.classList.remove('hidden');
    }

    // Function to open main reCAPTCHA modal
    function openMainRecaptchaModal(formElement) {
        console.log('openMainRecaptchaModal called with:', formElement);

        mainPendingFormElement = formElement;

        // Check if modal element exists
        const modal = document.getElementById('mainRecaptchaModal');
        console.log('Modal element found:', !!modal);

        if (!modal) {
            throw new Error('reCAPTCHA modal element not found');
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Reset reCAPTCHA
        try {
            resetMainRecaptcha();
        } catch (error) {
            console.warn('Error resetting reCAPTCHA:', error);
        }

        const statusElement = document.getElementById('mainRecaptchaModalStatus');
        if (statusElement) {
            statusElement.classList.add('hidden');
        }

        console.log('Main reCAPTCHA modal opened successfully');
    }

    // Function to close main reCAPTCHA modal
    function closeMainRecaptchaModal() {
        document.getElementById('mainRecaptchaModal').classList.add('hidden');
        document.body.style.overflow = 'auto';

        // Clear pending data
        mainPendingFormData = null;
        mainPendingFormElement = null;

        console.log('Main reCAPTCHA modal closed');
    }

    // Function to complete main reCAPTCHA verification and submit form
    function completeMainRecaptchaVerification() {
        const recaptchaResponse = getMainRecaptchaResponse();

        if (!recaptchaResponse) {
            showMainRecaptchaError('Please complete the reCAPTCHA verification.');
            return;
        }

        if (!mainPendingFormElement) {
            showMainRecaptchaError('Form data not found. Please try again.');
            return;
        }

        try {
            console.log('Main reCAPTCHA verification completed, submitting form...');

            // Add reCAPTCHA response to form data
            const hiddenRecaptchaField = mainPendingFormElement.querySelector('[name="g-recaptcha-response"]');
            if (hiddenRecaptchaField) {
                hiddenRecaptchaField.value = recaptchaResponse;
            }

            // Close modal
            closeMainRecaptchaModal();

            // Submit the form
            mainPendingFormElement.submit();

        } catch (error) {
            console.error('Error submitting form:', error);
            showMainRecaptchaError('Error submitting form. Please try again.');
        }
    }
    function selectRole(role) {
        console.log('=== selectRole function called with role:', role, '===');

        try {
            // hide role modal and show registration form
            const roleModal = document.getElementById('roleModal');
            const registrationForm = document.getElementById('registrationForm');

            console.log('Role modal found:', !!roleModal);
            console.log('Registration form found:', !!registrationForm);

            if (roleModal && registrationForm) {
                roleModal.classList.add('hidden');
                registrationForm.classList.remove('hidden');
                console.log('✅ Forms toggled successfully');
            }

            // set hidden input value
            const roleInput = document.getElementById('roleInput');
            if (roleInput) {
                roleInput.value = role;
                console.log('✅ Role input set to:', roleInput.value);
            }

            // Wait a moment for DOM to be ready, then show/hide sections
            setTimeout(function() {
                console.log('=== Applying role-specific visibility ===');

                const addressSection = document.getElementById('addressSection');
                const validIdSection = document.getElementById('validIdSection');

                console.log('Address section element:', addressSection);
                console.log('Valid ID section element:', validIdSection);

                if (role === 'tenant') {
                    console.log('Processing TENANT role...');

                    if (addressSection) {
                        // Force multiple ways to show the element
                        addressSection.style.display = 'block';
                        addressSection.style.visibility = 'visible';
                        addressSection.classList.remove('hidden');
                        addressSection.classList.remove('d-none');

                        console.log('✅ Address section SHOWN for tenant');
                        console.log('Address section styles:', addressSection.style.cssText);

                        // Make fields required
                        const addressInput = addressSection.querySelector('[name="address"]');
                        const cityInput = addressSection.querySelector('[name="city"]');

                        if (addressInput) {
                            addressInput.required = true;
                            console.log('✅ Address input made required');
                        }
                        if (cityInput) {
                            cityInput.required = true;
                            console.log('✅ City input made required');
                        }
                    } else {
                        console.error('❌ Address section NOT FOUND!');
                    }

                    if (validIdSection) {
                        validIdSection.style.display = 'none';
                        console.log('✅ Valid ID section hidden');
                    }

                } else if (role === 'landlord') {
                    console.log('Processing LANDLORD role...');

                    if (addressSection) {
                        addressSection.style.display = 'none';
                        console.log('✅ Address section hidden');
                    }

                    if (validIdSection) {
                        validIdSection.style.display = 'block';
                        validIdSection.style.visibility = 'visible';
                        validIdSection.classList.remove('hidden');
                        console.log('✅ Valid ID section shown for landlord');
                    }
                }

                console.log('=== Role processing complete ===');
            }, 50); // Small delay to ensure DOM is ready

        } catch (error) {
            console.error('❌ Error in selectRole function:', error);
        }
    }

    function goBack() {
        document.getElementById('roleModal').classList.remove('hidden');
        document.getElementById('registrationForm').classList.add('hidden');
        // reset hidden role
        document.getElementById('roleInput').value = '';
    }

    // Enhanced form submission handler with reCAPTCHA popup
    function handleFormSubmit(event) {
        console.log('=== FORM SUBMISSION STARTED ===');
        event.preventDefault(); // Always prevent initial submission

        const form = event.target;
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);

        // Test: Try to open modal immediately for debugging
        console.log('Testing modal opening...');
        try {
            openMainRecaptchaModal(form);
            console.log('✅ Modal opened successfully');
        } catch (error) {
            console.error('❌ Error opening modal:', error);
            alert('Error opening reCAPTCHA modal: ' + error.message);
        }

        return false; // Prevent form submission until reCAPTCHA is completed
    }

    // Form validation before submission (skipRecaptcha for basic validation)
    function validateForm(skipRecaptcha = false) {
        const role = document.getElementById('roleInput').value;
        console.log('Form validation - Role:', role, 'Skip reCAPTCHA:', skipRecaptcha);

        // Basic field validation for all users
        const name = document.querySelector('[name="name"]').value.trim();
        const email = document.querySelector('[name="email"]').value.trim();
        const phone = document.querySelector('[name="phone"]').value.trim();
        const password = document.querySelector('[name="password"]').value;
        const passwordConfirmation = document.querySelector('[name="password_confirmation"]').value;

        if (!name) {
            alert('Please enter your full name.');
            document.querySelector('[name="name"]').focus();
            return false;
        }

        if (!email) {
            alert('Please enter your email address.');
            document.querySelector('[name="email"]').focus();
            return false;
        }

        if (!phone) {
            alert('Please enter your phone number.');
            document.querySelector('[name="phone"]').focus();
            return false;
        }

        if (!password) {
            alert('Please enter a password.');
            document.querySelector('[name="password"]').focus();
            return false;
        }

        if (password !== passwordConfirmation) {
            alert('Passwords do not match.');
            document.querySelector('[name="password_confirmation"]').focus();
            return false;
        }

        // Role-specific validation
        if (role === 'tenant') {
            const address = document.querySelector('[name="address"]').value.trim();
            const city = document.querySelector('[name="city"]').value;
            console.log('Tenant validation - Address:', address, 'City:', city);

            if (!address) {
                alert('Please provide your current address.');
                document.querySelector('[name="address"]').focus();
                return false;
            }

            if (!city) {
                alert('Please select your city from the dropdown.');
                document.querySelector('[name="city"]').focus();
                return false;
            }
        }

        // Skip reCAPTCHA validation if requested (for basic validation before popup)
        if (!skipRecaptcha) {
            const recaptchaResponse = getMainRecaptchaResponse();
            if (!recaptchaResponse) {
                alert('Please complete the reCAPTCHA verification.');
                return false;
            }
        }

        console.log('Form validation passed');
        return true;
    }

    // Initialize the form when page loads
    window.addEventListener('DOMContentLoaded', function() {
        console.log('=== Page loaded - Initializing registration form ===');

        // Check if elements exist
        const roleModal = document.getElementById('roleModal');
        const registrationForm = document.getElementById('registrationForm');
        const roleInput = document.getElementById('roleInput');
        const addressSection = document.getElementById('addressSection');
        const validIdSection = document.getElementById('validIdSection');

        console.log('Elements found:');
        console.log('- Role modal:', !!roleModal);
        console.log('- Registration form:', !!registrationForm);
        console.log('- Role input:', !!roleInput);
        console.log('- Address section:', !!addressSection);
        console.log('- Valid ID section:', !!validIdSection);

        const role = roleInput ? roleInput.value : '';
        console.log('Existing role value:', role);

        if (role) {
            console.log('Found existing role, calling selectRole...');
            selectRole(role);
        } else {
            console.log('No existing role, showing role selection modal');
            if (roleModal) roleModal.classList.remove('hidden');
            if (registrationForm) registrationForm.classList.add('hidden');
        }

        console.log('=== Initialization complete ===');
    });

    // Function to close success modal
    function closeSuccessModal() {
        const modal = document.getElementById('successModal');
        if (modal) {
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    }

    // Auto-close modal after 10 seconds
    @if(session('registration_success'))
    setTimeout(function() {
        closeSuccessModal();
    }, 10000);
    @endif
</script>
@endpush
