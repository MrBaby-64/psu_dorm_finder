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
                        🏠 Dorm Finder
                    </div>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900 mb-4 leading-tight px-4">
                    Find Rooms for Rent with <span class="text-green-600">Dorm Finder</span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-3xl mx-auto px-4">
                    Discover verified dormitories and apartments near Bacolor & San Fernando
                </p>

                {{-- Search Form --}}
                <form action="{{ route('properties.browse') }}" method="GET" class="max-w-3xl mx-auto mb-10 px-4 fade-in-up">
                    <div class="bg-white rounded-2xl shadow-xl p-3 flex flex-col sm:flex-row gap-3 border-2 border-green-100 hover:border-green-300 transition-all duration-300">
                        <div class="flex-1 relative">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input
                                type="text"
                                name="q"
                                placeholder="Search by location, property name..."
                                class="w-full pl-12 pr-4 py-4 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-400 text-base sm:text-lg border-0 bg-gray-50 hover:bg-white transition-colors"
                            >
                        </div>
                        <button type="submit" class="bg-green-600 text-white px-6 sm:px-8 py-4 rounded-xl font-bold hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 whitespace-nowrap">
                            🔍 Search Now
                        </button>
                    </div>
                </form>

                {{-- Popular Amenities --}}
                <div class="max-w-4xl mx-auto mb-10 px-4 fade-in-up">
                    <div class="bg-white/60 backdrop-blur-md rounded-2xl p-6 shadow-lg border border-white/50">
                        <p class="text-sm font-semibold text-gray-700 mb-4 text-center flex items-center justify-center gap-2">
                            <span class="text-lg">✨</span> Popular Amenities
                        </p>
                        <div class="flex flex-wrap gap-2 sm:gap-3 justify-center">
                            <a href="{{ route('properties.browse', ['amenity' => 'wifi']) }}"
                               class="amenity-badge flex items-center gap-2 bg-gradient-to-r from-green-400 to-green-500 text-white px-4 py-2.5 rounded-full hover:from-green-500 hover:to-green-600 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1 text-sm font-medium">
                                <span class="text-base">📶</span> WiFi
                            </a>
                            <a href="{{ route('properties.browse', ['amenity' => 'kitchen']) }}"
                               class="amenity-badge flex items-center gap-2 bg-gradient-to-r from-green-400 to-green-500 text-white px-4 py-2.5 rounded-full hover:from-green-500 hover:to-green-600 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1 text-sm font-medium">
                                <span class="text-base">🍳</span> Kitchen
                            </a>
                            <a href="{{ route('properties.browse', ['amenity' => 'parking']) }}"
                               class="amenity-badge flex items-center gap-2 bg-gradient-to-r from-green-400 to-green-500 text-white px-4 py-2.5 rounded-full hover:from-green-500 hover:to-green-600 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1 text-sm font-medium">
                                <span class="text-base">🅿️</span> Parking
                            </a>
                            <a href="{{ route('properties.browse', ['amenity' => 'laundry']) }}"
                               class="amenity-badge flex items-center gap-2 bg-gradient-to-r from-green-400 to-green-500 text-white px-4 py-2.5 rounded-full hover:from-green-500 hover:to-green-600 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1 text-sm font-medium">
                                <span class="text-base">🧺</span> Laundry
                            </a>
                            <a href="{{ route('properties.browse', ['amenity' => 'ac']) }}"
                               class="amenity-badge flex items-center gap-2 bg-gradient-to-r from-green-400 to-green-500 text-white px-4 py-2.5 rounded-full hover:from-green-500 hover:to-green-600 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1 text-sm font-medium">
                                <span class="text-base">❄️</span> Air Con
                            </a>
                            <a href="{{ route('properties.browse', ['amenity' => 'security']) }}"
                               class="amenity-badge flex items-center gap-2 bg-gradient-to-r from-green-400 to-green-500 text-white px-4 py-2.5 rounded-full hover:from-green-500 hover:to-green-600 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1 text-sm font-medium">
                                <span class="text-base">🔒</span> Security
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="flex justify-center gap-3 sm:gap-4 text-sm flex-wrap mb-16 px-4 fade-in-up">
                    <a href="{{ route('properties.browse', ['city' => 'Bacolor']) }}"
                       class="quick-link flex items-center gap-2 bg-white/80 backdrop-blur-sm text-gray-700 px-4 sm:px-5 py-2.5 rounded-full hover:bg-white hover:text-green-600 transition-all duration-300 shadow-sm hover:shadow-md font-medium border border-gray-200 hover:border-green-300 transform hover:-translate-y-0.5">
                        <span>📍</span> <span class="hidden sm:inline">Near</span> Bacolor Campus
                    </a>
                    <a href="{{ route('properties.browse', ['city' => 'San Fernando']) }}"
                       class="quick-link flex items-center gap-2 bg-white/80 backdrop-blur-sm text-gray-700 px-4 sm:px-5 py-2.5 rounded-full hover:bg-white hover:text-green-600 transition-all duration-300 shadow-sm hover:shadow-md font-medium border border-gray-200 hover:border-green-300 transform hover:-translate-y-0.5">
                        <span>📍</span> <span class="hidden sm:inline">Near</span> San Fernando
                    </a>
                    <a href="{{ route('properties.browse', ['is_verified' => 1]) }}"
                       class="quick-link flex items-center gap-2 bg-green-600 text-white px-4 sm:px-5 py-2.5 rounded-full hover:bg-green-700 transition-all duration-300 shadow-md hover:shadow-lg font-medium transform hover:-translate-y-0.5">
                        <span>✓</span> Verified Properties
                    </a>
                    <a href="{{ route('safety-guidelines') }}"
                       class="quick-link flex items-center gap-2 bg-green-600 text-white px-4 sm:px-5 py-2.5 rounded-full hover:bg-green-700 transition-all duration-300 shadow-md hover:shadow-lg font-medium transform hover:-translate-y-0.5">
                        <span>🛡️</span> Safety Guidelines
                    </a>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
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