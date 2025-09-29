<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PSU Dorm Finder') }} - @yield('title', 'Find Your Perfect Student Housing')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Ensure navbar takes full width */
        nav {
            width: 100vw !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">

    {{-- Navigation --}}
    <nav class="fixed top-0 left-0 right-0 w-full z-50 bg-white shadow-sm">
        <div class="w-full px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="flex justify-between h-16 max-w-7xl mx-auto">
                <div class="flex items-center space-x-4">
                    {{-- Back Button - Show for non-home pages --}}
                    @if(!request()->is('/') && !request()->is('dashboard'))
                    <button onclick="goBack()" class="flex items-center text-gray-600 hover:text-green-600 hover:bg-gray-100 px-3 py-2 rounded-lg transition-all duration-200 border border-gray-200" title="Go back">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    @endif

                    <a href="{{ route('home') }}" class="text-2xl font-bold text-green-600">
                        🎓 PSU Dorm Finder
                    </a>
                </div>

                {{-- DESKTOP MENU --}}
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        @if(auth()->user()->role === 'tenant')
                            <a href="{{ route('properties.browse') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Find Rentals</a>
                            <a href="{{ route('tenant.account') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Dashboard</a>
                        @elseif(auth()->user()->role === 'admin')
                            <a href="{{ route('properties.browse') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Find Rentals</a>
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Admin Panel</a>
                        @else
                            <a href="{{ route('properties.browse') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Find Rentals</a>
                            <a href="{{ route('landlord.account') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Dashboard</a>
                        @endif

                        <div class="border-l border-gray-300 h-6 mx-4"></div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-red-600 transition-all duration-200 font-medium">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('properties.browse') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Find Rentals</a>
                        <a href="{{ route('about') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">About Us</a>
                        <a href="{{ route('how-it-works') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">How It Works</a>
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Login</a>
                        <a href="{{ route('register') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all duration-200 font-medium shadow-sm">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="pt-16">
        {{ $slot }}
    </main>

    <script>
        // Smart back navigation function
        function goBack() {
            if (document.referrer && document.referrer.indexOf(window.location.origin) === 0) {
                window.history.back();
            } else {
                @auth
                    @if(auth()->user()->role === 'tenant')
                        window.location.href = "{{ route('tenant.account') }}";
                    @elseif(auth()->user()->role === 'admin')
                        window.location.href = "{{ route('admin.account') }}";
                    @else
                        window.location.href = "{{ route('landlord.account') }}";
                    @endif
                @else
                    window.location.href = "{{ route('home') }}";
                @endauth
            }
        }
    </script>

    @stack('scripts')
</body>
</html>