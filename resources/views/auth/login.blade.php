@extends('layouts.guest')

@section('content')
<script>
    // Automatically open the auth modal when the login page is accessed
    document.addEventListener('DOMContentLoaded', function() {
        openAuthModal();

        // If there are errors, ensure login form is shown
        @if ($errors->any())
            switchToLogin();
        @endif
    });
</script>

<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }

    .float-animation {
        animation: float 3s ease-in-out infinite;
    }

    .gradient-bg {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 50%, #bbf7d0 100%);
    }

    .feature-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
        transition: left 0.5s;
    }

    .feature-card:hover::before {
        left: 100%;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .btn-primary {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-primary:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(22, 163, 74, 0.3);
    }

    .btn-secondary:hover {
        background-color: #f0fdf4;
        border-color: #16a34a;
        color: #16a34a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
    }
</style>

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

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 mb-4 leading-tight">
                Find Your Perfect <span class="text-green-600">Student Accommodation</span>
            </h1>

            <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-2xl mx-auto">
                Connect with verified landlords and discover quality dormitories near PSU campus
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                <button onclick="openAuthModal(); switchToLogin();"
                        class="btn-primary bg-green-600 text-white px-8 py-4 rounded-xl hover:bg-green-700 transition font-bold shadow-lg text-lg w-full sm:w-auto">
                    🔐 Sign In to Your Account
                </button>
                <button onclick="openAuthModal(); switchToRoleSelection();"
                        class="btn-secondary bg-white text-green-600 border-2 border-green-600 px-8 py-4 rounded-xl transition font-bold text-lg w-full sm:w-auto">
                    ✨ Create New Account
                </button>
            </div>

            <!-- Quick Features Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 mt-16">
                <!-- Feature Card 1 -->
                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
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
                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
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
                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
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

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mt-8 max-w-md mx-auto bg-red-50 border-l-4 border-red-500 text-red-800 p-6 rounded-xl shadow-lg fade-in-up">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-lg text-red-900">Login Failed</span>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm mb-4">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-start gap-2">
                                <span class="text-red-500 mt-0.5">•</span>
                                <span class="text-red-700">{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 text-sm">
                        <div class="flex items-start gap-2">
                            <span class="text-xl">💡</span>
                            <div>
                                <strong class="block mb-2">Having trouble logging in?</strong>
                                <ul class="space-y-1 text-left">
                                    <li>• Check your email and password are correct</li>
                                    <li>• Make sure caps lock is off</li>
                                    <li>• <button type="button" onclick="openAuthModal(); switchToRoleSelection();" class="underline text-blue-700 font-semibold hover:text-blue-900">Create a new account</button> if you don't have one</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Success Messages -->
            @if (session('status'))
                <div class="mt-8 max-w-md mx-auto bg-green-50 border-l-4 border-green-500 text-green-800 p-6 rounded-xl shadow-lg fade-in-up">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="font-semibold text-lg">{{ session('status') }}</span>
                    </div>
                </div>
            @endif>

            <!-- Trust Indicators -->
            <div class="mt-16 text-center text-gray-500 text-sm">
                <p class="mb-2">🔐 Secured with SSL encryption</p>
                <p>Trusted by <span class="font-bold text-green-600">500+</span> PSU students and property owners</p>
            </div>
        </div>
    </div>
</div>
@endsection
