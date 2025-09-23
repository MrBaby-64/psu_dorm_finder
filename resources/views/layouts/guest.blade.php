<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PSU Dorm Finder') }} - @yield('title', 'Find Your Perfect Student Housing')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .modal-backdrop {
            backdrop-filter: blur(8px);
            background-color: rgba(0, 0, 0, 0.6);
        }
        
        .modal-slide-up {
            animation: slideUp 0.3s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    
    {{-- Navigation --}}
    <nav class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-green-600">
                        🎓 PSU Dorm Finder
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('properties.browse') }}" class="text-gray-700 hover:text-green-600 font-medium">Find Rentals</a>
                    <a href="{{ route('about') }}" class="text-gray-700 hover:text-green-600">About Us</a>
                    <a href="{{ route('how-it-works') }}" class="text-gray-700 hover:text-green-600">How It Works</a>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-green-600">Dashboard</a>
                        @if(auth()->user()->role === 'landlord')
                            <a href="{{ route('landlord.properties.create') }}" class="text-gray-700 hover:text-green-600">Post Property</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-green-600 font-medium">Logout</button>
                        </form>
                    @else
                        <button onclick="openAuthModal('login')" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Account
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Back Button --}}
    @if(!request()->is('/') && !request()->is('register*') && !request()->is('login*') && !request()->is('properties/show*'))
    <div class="sticky top-16 z-30 bg-white border-b px-4 py-2">
        <button onclick="window.history.back()" class="flex items-center text-gray-600 hover:text-green-600">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back
        </button>
    </div>
    @endif

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('layouts.partials.footer')

    {{-- Auth Modal --}}
    @guest
    <div id="authModal" class="fixed inset-0 z-50 hidden">
        <div class="modal-backdrop absolute inset-0" onclick="closeAuthModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="modal-slide-up bg-white rounded-2xl shadow-2xl w-full relative" style="max-width: 400px;">
                <button onclick="closeAuthModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <div class="text-center pt-8 pb-6">
                    <div class="text-3xl font-bold text-green-600">🎓 PSU Dorm Finder</div>
                </div>

                <div id="loginForm" class="px-8 pb-8">
                    <h2 class="text-2xl font-bold mb-6 text-center">Welcome Back</h2>
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                   placeholder="your@email.com">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                   placeholder="Enter your password">
                        </div>
                        
                        <div class="flex items-center justify-between mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" class="mr-2">
                                <span class="text-sm text-gray-600">Remember me</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:underline">Forgot Password?</a>
                        </div>
                        
                        <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-semibold">
                            Log In
                        </button>
                    </form>
                    
                    <p class="text-center text-sm text-gray-600 mt-6">
                        Not registered yet? 
                        <button type="button" onclick="switchToRoleSelection()" class="text-green-600 font-semibold hover:underline">
                            Create an account here
                        </button>
                    </p>
                </div>

                <div id="roleSelection" class="px-8 pb-8 hidden">
                    <h2 class="text-2xl font-bold mb-6 text-center">Create an Account</h2>
                    <p class="text-center text-gray-600 mb-6">Select what best describes you</p>
                    
                    <div class="space-y-4 mb-6">
                        <button type="button" onclick="console.log('Tenant button clicked in modal'); selectRole('tenant')"
                           class="w-full flex items-start gap-4 p-4 border-2 border-blue-300 bg-blue-50 rounded-lg hover:border-blue-500 hover:bg-blue-100 transition">
                            <div class="text-3xl">👤</div>
                            <div class="text-left">
                                <div class="font-bold text-lg text-blue-700">I am looking for a place to stay</div>
                                <div class="text-sm text-blue-600">for renters</div>
                            </div>
                        </button>

                        <button type="button" onclick="console.log('Landlord button clicked in modal'); selectRole('landlord')"
                           class="w-full flex items-start gap-4 p-4 border-2 border-green-300 bg-green-50 rounded-lg hover:border-green-500 hover:bg-green-100 transition">
                            <div class="text-3xl">🏢</div>
                            <div class="text-left">
                                <div class="font-bold text-lg text-green-700">I want to post my property</div>
                                <div class="text-sm text-green-600">for hosts, landlords, agents</div>
                            </div>
                        </button>
                    </div>
                    
                    <p class="text-center text-sm text-gray-600">
                        Already have an account? 
                        <button type="button" onclick="switchToLogin()" class="text-green-600 font-semibold hover:underline">
                            Log in here
                        </button>
                    </p>
                </div>

                <div id="registrationForm" class="px-8 pb-8 hidden max-h-[80vh] overflow-y-auto">
                    <h2 class="text-2xl font-bold mb-6 text-center">Create Your Account</h2>

                    <!-- Show validation errors in modal -->
                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-400 text-red-800 p-4 rounded-lg text-sm">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-bold text-red-800">Registration Error</span>
                            </div>
                            <div class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <div class="text-red-700">• {{ $error }}</div>
                                @endforeach
                            </div>
                            @if ($errors->has('phone') || $errors->has('email'))
                                <div class="mt-2 p-2 bg-yellow-50 border border-yellow-300 rounded text-yellow-800 text-xs">
                                    💡 <strong>Tip:</strong> Try different credentials or <a href="{{ route('login') }}" class="underline">login if you have an account</a>
                                </div>
                            @endif
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" onsubmit="return validateGuestRegistrationForm()">
                        @csrf
                        <input type="hidden" id="roleInput" name="role" value="">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="name" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                                <input type="email" name="email" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
                                <input type="tel" name="phone" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="09XXXXXXXXX">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Password *</label>
                                <input type="password" name="password" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password *</label>
                                <input type="password" name="password_confirmation" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>

                            <!-- Tenant Address Fields -->
                            <div id="tenantAddressSection" class="hidden">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm text-blue-800">
                                    📍 <strong>Help us show you nearby properties!</strong><br>
                                    Please provide your address so we can show relevant dormitories.
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Current Address *</label>
                                    <textarea name="address" rows="2"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                              placeholder="Enter your complete address"></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">City *</label>
                                        <select name="city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                            <option value="">Select your city</option>
                                            <option value="Bacolor">Bacolor</option>
                                            <option value="San Fernando">San Fernando</option>
                                            <option value="Angeles City">Angeles City</option>
                                            <option value="Mabalacat">Mabalacat</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Province</label>
                                        <input type="text" name="province" value="Pampanga" readonly
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                                    </div>
                                </div>
                            </div>

                            <!-- Landlord Valid ID Field -->
                            <div id="landlordIdSection" class="hidden">
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Valid ID (Optional)</label>
                                    <input type="file" name="valid_id" accept="image/*,.pdf"
                                           class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <p class="text-xs text-gray-600 mt-1">You can upload your valid ID later for verification</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-6">
                            <button type="button" onclick="switchToRoleSelection()" class="flex-1 bg-gray-200 py-2 rounded-lg font-semibold hover:bg-gray-300">
                                Back
                            </button>
                            <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700">
                                Sign Up
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endguest

    <script>
        function openAuthModal(mode = 'login') {
            document.getElementById('authModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            if (mode === 'login') {
                switchToLogin();
            } else {
                switchToRoleSelection();
            }
        }

        function closeAuthModal() {
            document.getElementById('authModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function switchToLogin() {
            document.getElementById('loginForm').classList.remove('hidden');
            document.getElementById('roleSelection').classList.add('hidden');
            document.getElementById('registrationForm').classList.add('hidden');
        }

        function switchToRoleSelection() {
            document.getElementById('loginForm').classList.add('hidden');
            document.getElementById('roleSelection').classList.remove('hidden');
            document.getElementById('registrationForm').classList.add('hidden');
        }

        function selectRole(role) {
            console.log('Guest layout selectRole called with:', role);

            // Set the role value
            document.getElementById('roleInput').value = role;

            // Show registration form
            document.getElementById('roleSelection').classList.add('hidden');
            document.getElementById('registrationForm').classList.remove('hidden');

            // Show/hide role-specific sections
            const tenantSection = document.getElementById('tenantAddressSection');
            const landlordSection = document.getElementById('landlordIdSection');

            if (role === 'tenant') {
                if (tenantSection) {
                    tenantSection.classList.remove('hidden');
                    console.log('✅ Tenant address section shown');

                    // Make address and city required
                    const addressField = tenantSection.querySelector('[name="address"]');
                    const cityField = tenantSection.querySelector('[name="city"]');
                    if (addressField) addressField.required = true;
                    if (cityField) cityField.required = true;
                }
                if (landlordSection) {
                    landlordSection.classList.add('hidden');
                }
            } else if (role === 'landlord') {
                if (tenantSection) {
                    tenantSection.classList.add('hidden');

                    // Remove required from address fields
                    const addressField = tenantSection.querySelector('[name="address"]');
                    const cityField = tenantSection.querySelector('[name="city"]');
                    if (addressField) addressField.required = false;
                    if (cityField) cityField.required = false;
                }
                if (landlordSection) {
                    landlordSection.classList.remove('hidden');
                    console.log('✅ Landlord ID section shown');
                }
            }
        }

        function validateGuestRegistrationForm() {
            const role = document.getElementById('roleInput').value;
            console.log('Validating guest form for role:', role);

            if (role === 'tenant') {
                const addressField = document.querySelector('#tenantAddressSection [name="address"]');
                const cityField = document.querySelector('#tenantAddressSection [name="city"]');

                if (!addressField || !addressField.value.trim()) {
                    alert('Please provide your current address.');
                    if (addressField) addressField.focus();
                    return false;
                }

                if (!cityField || !cityField.value) {
                    alert('Please select your city from the dropdown.');
                    if (cityField) cityField.focus();
                    return false;
                }
            }

            console.log('Form validation passed');
            return true;
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAuthModal();
            }
        });

        // Auto-show registration form if there are validation errors
        @if ($errors->any())
            window.addEventListener('DOMContentLoaded', function() {
                console.log('Validation errors detected, opening registration modal...');
                openAuthModal('register');
                switchToRoleSelection();

                // If there's an old role, show the registration form directly
                @if (old('role'))
                    setTimeout(function() {
                        selectRole('{{ old('role') }}');
                    }, 100);
                @endif
            });
        @endif
    </script>

    @stack('scripts')
</body>
</html>