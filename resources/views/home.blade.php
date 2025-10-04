@extends('layouts.guest')

@section('title', 'Find Your Perfect Student Housing')

@section('content')
    {{-- Hero Section --}}
    <div class="min-h-screen gradient-bg flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl w-full">
            <!-- Hero Section -->
            <div class="text-center fade-in-up">
                <!-- Logo with floating animation -->
                <div class="inline-block mb-6 float-animation">
                    <div class="text-6xl sm:text-7xl font-bold text-green-600 mb-2 drop-shadow-lg">
                        🎓 PSU Dorm Finder
                    </div>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 mb-4 leading-tight">
                    Find Your Perfect <span class="text-green-600">Student Accommodation</span>
                </h1>

                <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-2xl mx-auto">
                    Connect with verified landlords and discover quality dormitories near PSU campus
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                    @auth
                        <a href="{{ route('properties.browse') }}"
                           class="btn-primary bg-green-600 text-white px-8 py-4 rounded-xl hover:bg-green-700 transition font-bold shadow-lg text-lg w-full sm:w-auto">
                            🏠 Browse Properties
                        </a>
                        <a href="{{ route(auth()->user()->role . '.account') }}"
                           class="btn-secondary bg-white text-green-600 border-2 border-green-600 px-8 py-4 rounded-xl transition font-bold text-lg w-full sm:w-auto hover:bg-green-50">
                            📊 My Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="btn-primary bg-green-600 text-white px-8 py-4 rounded-xl hover:bg-green-700 transition font-bold shadow-lg text-lg w-full sm:w-auto">
                            🔐 Sign In to Your Account
                        </a>
                        <a href="{{ route('register') }}"
                           class="btn-secondary bg-white text-green-600 border-2 border-green-600 px-8 py-4 rounded-xl transition font-bold text-lg w-full sm:w-auto hover:bg-green-50">
                            ✨ Create New Account
                        </a>
                    @endauth
                </div>

                <!-- Quick Features Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 mt-16">
                    <!-- Feature Card 1 -->
                    <div class="feature-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <span class="text-3xl">🏠</span>
                        </div>
                        <h3 class="font-bold text-xl text-gray-900 mb-3">Browse Properties</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Explore verified dormitories and student housing options near PSU campus with detailed information
                        </p>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <span class="text-xs font-semibold text-green-600 uppercase tracking-wide">100+ Properties</span>
                        </div>
                    </div>

                    <!-- Feature Card 2 -->
                    <div class="feature-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <span class="text-3xl">📅</span>
                        </div>
                        <h3 class="font-bold text-xl text-gray-900 mb-3">Schedule Visits</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Book property viewings directly with landlords at your convenience and find your ideal home
                        </p>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Easy Booking</span>
                        </div>
                    </div>

                    <!-- Feature Card 3 -->
                    <div class="feature-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <span class="text-3xl">💬</span>
                        </div>
                        <h3 class="font-bold text-xl text-gray-900 mb-3">Direct Messaging</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Communicate with property owners in real-time and get your questions answered instantly
                        </p>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <span class="text-xs font-semibold text-purple-600 uppercase tracking-wide">24/7 Support</span>
                        </div>
                    </div>
                </div>

                <!-- Additional Benefits -->
                <div class="mt-16 bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg border border-green-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Why Choose PSU Dorm Finder?</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-3xl mb-2">✓</div>
                            <p class="text-sm font-semibold text-gray-900">Verified Listings</p>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl mb-2">🔒</div>
                            <p class="text-sm font-semibold text-gray-900">Secure Platform</p>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl mb-2">⚡</div>
                            <p class="text-sm font-semibold text-gray-900">Quick Response</p>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl mb-2">🎯</div>
                            <p class="text-sm font-semibold text-gray-900">Best Matches</p>
                        </div>
                    </div>
                </div>

                <!-- Trust Indicators -->
                <div class="mt-16 text-center text-gray-500 text-sm">
                    <p class="mb-2">🔐 Secured with SSL encryption</p>
                    <p>Trusted by <span class="font-bold text-green-600">500+</span> PSU students and property owners</p>
                </div>
            </div>
        </div>
    </div>

    {{-- About Us Section (Only for non-logged in users) --}}
    @guest
    <section id="about-us" class="bg-white py-20 scroll-mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Inspirational Quote --}}
            <div class="max-w-5xl mx-auto mb-16">
                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-2xl p-8 md:p-12 shadow-lg">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-green-600 mx-auto mb-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                        </svg>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">About Us</h2>
                        <blockquote class="text-2xl md:text-3xl font-serif text-gray-800 italic mb-6 leading-relaxed">
                            "From a simple idea to a bridge connecting students with their home away from home."
                        </blockquote>
                        <p class="text-lg text-gray-700 leading-relaxed">
                            PSU Dorm Finder began as a Laravel learning project to solve a real problem: helping Pampanga State University students
                            find safe, affordable, and convenient housing near campus. Today, we're proud to connect hundreds of students with
                            their perfect living space while empowering landlords to reach ideal tenants.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Quick Info Cards --}}
            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="bg-white border-2 border-green-100 rounded-xl p-6 hover:shadow-lg transition text-center">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <span class="text-2xl">🛡️</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">PSU-Verified</h3>
                    <p class="text-gray-600">
                        All listings verified for quality, safety, and proximity to campus
                    </p>
                </div>

                <div class="bg-white border-2 border-green-100 rounded-xl p-6 hover:shadow-lg transition text-center">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <span class="text-2xl">💬</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Direct Communication</h3>
                    <p class="text-gray-600">
                        Connect directly with landlords through secure messaging
                    </p>
                </div>

                <div class="bg-white border-2 border-green-100 rounded-xl p-6 hover:shadow-lg transition text-center">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <span class="text-2xl">⭐</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Transparent Reviews</h3>
                    <p class="text-gray-600">
                        Real reviews from PSU students to help you decide
                    </p>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('about') }}" class="inline-flex items-center text-green-600 hover:text-green-700 font-semibold">
                    Learn more about our story
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    @endguest

    {{-- How It Works Section (Only for non-logged in users) --}}
    @guest
    <section id="how-it-works" class="bg-gradient-to-b from-gray-50 to-white py-20 scroll-mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                <div class="w-20 h-1 bg-green-600 mx-auto mb-6"></div>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Finding your perfect student accommodation is easy with PSU Dorm Finder
                </p>
            </div>

            {{-- For Students --}}
            <div class="mb-16">
                <div class="text-center mb-12">
                    <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-6 py-3 rounded-full font-semibold">
                        <span class="text-2xl">👤</span>
                        <span>For Students</span>
                    </div>
                </div>

                <div class="grid md:grid-cols-4 gap-6 max-w-6xl mx-auto">
                    {{-- Step 1 --}}
                    <div class="relative">
                        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition h-full border-t-4 border-blue-600">
                            <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 mx-auto">
                                1
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-3 text-center">Create Account</h3>
                            <p class="text-gray-600 text-center text-sm">
                                Sign up for free as a student to access verified listings
                            </p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="relative">
                        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition h-full border-t-4 border-blue-600">
                            <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 mx-auto">
                                2
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-3 text-center">Search Properties</h3>
                            <p class="text-gray-600 text-center text-sm">
                                Browse verified dorms near PSU. Filter by price and amenities
                            </p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="relative">
                        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition h-full border-t-4 border-blue-600">
                            <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 mx-auto">
                                3
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-3 text-center">Schedule Visit</h3>
                            <p class="text-gray-600 text-center text-sm">
                                Contact landlords and schedule property visits
                            </p>
                        </div>
                    </div>

                    {{-- Step 4 --}}
                    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition h-full border-t-4 border-blue-600">
                        <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 mx-auto">
                            4
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3 text-center">Move In</h3>
                        <p class="text-gray-600 text-center text-sm">
                            Complete booking and move into your new home
                        </p>
                    </div>
                </div>
            </div>

            {{-- For Landlords --}}
            <div class="mt-8">
                <div class="text-center mb-12">
                    <div class="inline-flex items-center gap-2 bg-green-100 text-green-800 px-6 py-3 rounded-full font-semibold">
                        <span class="text-2xl">🏢</span>
                        <span>For Landlords</span>
                    </div>
                </div>

                <div class="grid md:grid-cols-4 gap-6 max-w-6xl mx-auto">
                    {{-- Step 1 --}}
                    <div class="relative">
                        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition h-full border-t-4 border-green-600">
                            <div class="w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 mx-auto">
                                1
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-3 text-center">Register</h3>
                            <p class="text-gray-600 text-center text-sm">
                                Create landlord account with valid ID for verification
                            </p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="relative">
                        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition h-full border-t-4 border-green-600">
                            <div class="w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 mx-auto">
                                2
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-3 text-center">List Property</h3>
                            <p class="text-gray-600 text-center text-sm">
                                Add property with photos, details, and pricing
                            </p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="relative">
                        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition h-full border-t-4 border-green-600">
                            <div class="w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 mx-auto">
                                3
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-3 text-center">Receive Inquiries</h3>
                            <p class="text-gray-600 text-center text-sm">
                                Get messages from students and manage visits
                            </p>
                        </div>
                    </div>

                    {{-- Step 4 --}}
                    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition h-full border-t-4 border-green-600">
                        <div class="w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 mx-auto">
                            4
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3 text-center">Welcome Tenants</h3>
                        <p class="text-gray-600 text-center text-sm">
                            Accept bookings and build your reputation
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('how-it-works') }}" class="inline-flex items-center text-green-600 hover:text-green-700 font-semibold">
                    View detailed guide
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    @endguest
@endsection