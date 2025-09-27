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

        .shake {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: translateX(-5px);
            }
            20%, 40%, 60%, 80% {
                transform: translateX(5px);
            }
        }

        /* Smooth responsive transitions */
        .nav-link, button, .guest-mobile-nav-link {
            transition: all 0.3s ease-in-out;
        }

        /* STRICT RESPONSIVE SEPARATION */
        @media (max-width: 767.98px) {
            /* MOBILE ONLY - Hide all desktop elements */
            .hidden-mobile,
            .hidden\\:md\\:flex,
            .md\\:flex {
                display: none !important;
            }

            /* MOBILE ONLY - Show mobile elements */
            .md\\:hidden {
                display: flex !important;
            }

            .nav-link {
                font-size: 0.75rem;
                padding: 0.375rem 0.5rem;
            }
        }

        @media (min-width: 768px) {
            /* DESKTOP ONLY - Hide all mobile elements */
            .md\\:hidden,
            .hidden-desktop {
                display: none !important;
            }

            /* DESKTOP ONLY - Show desktop elements */
            .hidden {
                display: none;
            }

            .hidden.md\\:flex {
                display: flex !important;
            }

            .nav-link {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
            }
        }

        /* Mobile menu panel responsiveness */
        @media (max-width: 640px) {
            #guestMobileMenuPanel {
                width: 85vw !important;
                max-width: 320px !important;
            }
        }

        /* Improved hover states */
        .nav-link:hover, button:hover {
            transform: translateY(-1px);
        }

        /* Mobile button active states */
        @media (max-width: 768px) {
            button:active {
                transform: scale(0.95);
            }
        }

        /* ABSOLUTE CONTROL - Force hide/show by ID */
        @media (max-width: 767.98px) {
            #guestDesktopNavigation {
                display: none !important;
            }
            #guestMobileNavigation {
                display: flex !important;
            }
        }

        @media (min-width: 768px) {
            #guestMobileNavigation {
                display: none !important;
            }
            #guestDesktopNavigation {
                display: flex !important;
            }
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
                <div class="hidden md:flex items-center space-x-4" id="guestDesktopNavigation">
                    @auth
                        @if(auth()->user()->role === 'tenant')
                            <a href="{{ route('properties.browse') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Find Rentals</a>
                            <a href="{{ route('tenant.account') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Dashboard</a>

                        @elseif(auth()->user()->role === 'admin')
                            <a href="{{ route('properties.browse') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Find Rentals</a>
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Admin Panel</a>
                            <a href="{{ route('admin.account') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Dashboard</a>

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
                        {{-- Guest Desktop Navigation --}}
                        <a href="{{ route('properties.browse') }}" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Find Rentals</a>
                        <a href="#about" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">About Us</a>
                        <a href="#how-it-works" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">How It Works</a>
                        <button onclick="openAuthModal('login')" class="text-gray-700 hover:text-green-600 transition-all duration-200 font-medium">Login</button>
                        <button onclick="openAuthModal('register')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all duration-200 font-medium shadow-sm">Sign Up</button>
                    @endauth
                </div>

                {{-- MOBILE MENU SECTION - ONLY VISIBLE ON MOBILE --}}
                <div class="md:hidden flex items-center space-x-3" id="guestMobileNavigation">
                    @auth
                        {{-- Logout button for mobile --}}
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center text-gray-700 hover:text-red-600 px-3 py-2 rounded-lg hover:bg-gray-100 transition-all duration-200" title="Logout">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>

                        {{-- Menu toggle for authenticated users --}}
                        <button onclick="toggleGuestMobileMenu()" class="flex items-center text-gray-600 hover:text-green-600 hover:bg-gray-100 px-3 py-2 rounded-lg transition-all duration-200" id="guestMobileMenuButton" aria-label="Toggle menu">
                            <svg class="w-6 h-6" id="guestHamburgerIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <svg class="w-6 h-6 hidden" id="guestCloseIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @else
                        {{-- Menu toggle for guest users --}}
                        <button onclick="toggleGuestMobileMenu()" class="flex items-center text-gray-600 hover:text-green-600 hover:bg-gray-100 px-3 py-2 rounded-lg transition-all duration-200" id="guestMobileMenuButton" aria-label="Toggle menu">
                            <svg class="w-6 h-6" id="guestHamburgerIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <svg class="w-6 h-6 hidden" id="guestCloseIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endauth
                </div>
            </div>
        {{-- MOBILE MENU OVERLAY --}}
        <div class="md:hidden fixed inset-0 z-50 hidden" id="guestMobileMenuOverlay">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeGuestMobileMenu()"></div>
            <div class="fixed top-0 right-0 h-full w-72 sm:w-80 max-w-sm bg-white shadow-xl transform translate-x-full transition-transform duration-300 ease-in-out" id="guestMobileMenuPanel">
                {{-- Mobile Menu Header --}}
                <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                    <span class="text-lg font-semibold text-gray-900">Menu</span>
                    <button onclick="closeGuestMobileMenu()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Mobile Menu Content --}}
                <div class="px-6 py-4 h-full overflow-y-auto">
                    @auth
                        {{-- User Info Section --}}
                        <div class="mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-green-600 font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-sm text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Navigation Links --}}
                        <div class="space-y-2">
                            @if(auth()->user()->role === 'tenant')
                                <a href="{{ route('properties.browse') }}" class="guest-mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        <span>Find Rentals</span>
                                    </div>
                                </a>
                                <a href="{{ route('tenant.account') }}" class="guest-mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0H8v0z"></path></svg>
                                        <span>Dashboard</span>
                                    </div>
                                </a>

                            @elseif(auth()->user()->role === 'admin')
                                <a href="{{ route('properties.browse') }}" class="guest-mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        <span>Find Rentals</span>
                                    </div>
                                </a>
                                <a href="{{ route('admin.dashboard') }}" class="guest-mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                        <span>Admin Panel</span>
                                    </div>
                                </a>
                                <a href="{{ route('admin.account') }}" class="guest-mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0H8v0z"></path></svg>
                                        <span>Dashboard</span>
                                    </div>
                                </a>

                            @else
                                <a href="{{ route('properties.browse') }}" class="guest-mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        <span>Find Rentals</span>
                                    </div>
                                </a>
                                <a href="{{ route('landlord.account') }}" class="guest-mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0H8v0z"></path></svg>
                                        <span>Dashboard</span>
                                    </div>
                                </a>
                            @endif

                            {{-- Settings Link --}}
                            <div class="border-t border-gray-200 my-4"></div>
                            <a href="{{ route('profile.edit') }}" class="guest-mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>Settings</span>
                                </div>
                            </a>
                        </div>
                    @else
                        {{-- Guest Mobile Menu --}}
                        <div class="space-y-2">
                            <a href="{{ route('properties.browse') }}" class="guest-mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <span>Find Rentals</span>
                                </div>
                            </a>
                            <a href="#about" onclick="closeGuestMobileMenu(); document.getElementById('about').scrollIntoView({behavior: 'smooth'});" class="guest-mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>About Us</span>
                                </div>
                            </a>
                            <a href="#how-it-works" onclick="closeGuestMobileMenu(); document.getElementById('how-it-works').scrollIntoView({behavior: 'smooth'});" class="guest-mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>How It Works</span>
                                </div>
                            </a>

                            <div class="border-t border-gray-200 my-4"></div>

                            <button onclick="closeGuestMobileMenu(); openAuthModal('login');" class="guest-mobile-nav-link block w-full text-left px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    <span>Login</span>
                                </div>
                            </button>
                            <button onclick="closeGuestMobileMenu(); openAuthModal('register');" class="guest-mobile-nav-link block w-full text-left px-4 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-all duration-200 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                    <span>Sign Up</span>
                                </div>
                            </button>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="pt-16">
        @yield('content')
    </main>

    {{-- About Us Section --}}
    <section id="about" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">About PSU Dorm Finder</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Your trusted platform for finding the perfect student accommodation near Pampanga State University
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-6">🎓 Built for PSU Students</h3>
                    <div class="space-y-4 text-gray-700">
                        <p>
                            PSU Dorm Finder is specifically designed to help Pampanga State University students find safe,
                            affordable, and convenient housing options near the campus.
                        </p>
                        <p>
                            We understand the unique needs of college students - from budget constraints to proximity to campus,
                            from study-friendly environments to reliable internet connectivity.
                        </p>
                        <p>
                            Our platform connects students with verified landlords and property owners who understand
                            the student lifestyle and offer flexible rental terms.
                        </p>
                    </div>
                </div>

                <div class="bg-green-50 p-8 rounded-2xl">
                    <h4 class="text-xl font-semibold text-green-800 mb-6">Why Choose PSU Dorm Finder?</h4>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="text-green-600 text-xl">✅</div>
                            <div>
                                <strong class="text-green-800">Verified Properties</strong>
                                <p class="text-green-700 text-sm">All listings are verified for authenticity and safety</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="text-green-600 text-xl">🏠</div>
                            <div>
                                <strong class="text-green-800">Student-Friendly</strong>
                                <p class="text-green-700 text-sm">Properties tailored for student needs and budgets</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="text-green-600 text-xl">📍</div>
                            <div>
                                <strong class="text-green-800">Near Campus</strong>
                                <p class="text-green-700 text-sm">All properties within reasonable distance to PSU</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="text-green-600 text-xl">💬</div>
                            <div>
                                <strong class="text-green-800">Direct Communication</strong>
                                <p class="text-green-700 text-sm">Chat directly with landlords and property owners</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works Section --}}
    <section id="how-it-works" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Finding your perfect student accommodation has never been easier
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 mb-16">
                {{-- For Students --}}
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-8">
                    <div class="text-center mb-6">
                        <div class="text-4xl mb-3">👤</div>
                        <h3 class="text-2xl font-bold text-blue-800">For Students</h3>
                        <p class="text-blue-600">Looking for a place to stay</p>
                    </div>

                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm">1</div>
                            <div>
                                <h4 class="font-semibold text-blue-800">Create Your Account</h4>
                                <p class="text-blue-700 text-sm">Sign up as a student and provide your basic information</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm">2</div>
                            <div>
                                <h4 class="font-semibold text-blue-800">Browse Properties</h4>
                                <p class="text-blue-700 text-sm">Search and filter dormitories based on your preferences</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm">3</div>
                            <div>
                                <h4 class="font-semibold text-blue-800">Send Inquiries</h4>
                                <p class="text-blue-700 text-sm">Contact landlords directly through our messaging system</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm">4</div>
                            <div>
                                <h4 class="font-semibold text-blue-800">Schedule Visits</h4>
                                <p class="text-blue-700 text-sm">Arrange property visits and make your decision</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- For Landlords --}}
                <div class="bg-green-50 border border-green-200 rounded-2xl p-8">
                    <div class="text-center mb-6">
                        <div class="text-4xl mb-3">🏢</div>
                        <h3 class="text-2xl font-bold text-green-800">For Landlords</h3>
                        <p class="text-green-600">Want to list your property</p>
                    </div>

                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm">1</div>
                            <div>
                                <h4 class="font-semibold text-green-800">Register as Landlord</h4>
                                <p class="text-green-700 text-sm">Create your landlord account with property owner details</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm">2</div>
                            <div>
                                <h4 class="font-semibold text-green-800">List Your Property</h4>
                                <p class="text-green-700 text-sm">Add detailed information, photos, and amenities</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm">3</div>
                            <div>
                                <h4 class="font-semibold text-green-800">Receive Inquiries</h4>
                                <p class="text-green-700 text-sm">Get notifications when students are interested</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm">4</div>
                            <div>
                                <h4 class="font-semibold text-green-800">Manage Bookings</h4>
                                <p class="text-green-700 text-sm">Review applications and confirm tenants</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Key Features --}}
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-8">Key Platform Features</h3>
                <div class="grid md:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border">
                        <div class="text-3xl mb-3">🔍</div>
                        <h4 class="font-semibold text-gray-800 mb-2">Smart Search</h4>
                        <p class="text-gray-600 text-sm">Filter by price, location, amenities, and more</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border">
                        <div class="text-3xl mb-3">❤️</div>
                        <h4 class="font-semibold text-gray-800 mb-2">Favorites</h4>
                        <p class="text-gray-600 text-sm">Save properties you like for easy comparison</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border">
                        <div class="text-3xl mb-3">⭐</div>
                        <h4 class="font-semibold text-gray-800 mb-2">Reviews</h4>
                        <p class="text-gray-600 text-sm">Read honest reviews from previous tenants</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border">
                        <div class="text-3xl mb-3">🔔</div>
                        <h4 class="font-semibold text-gray-800 mb-2">Notifications</h4>
                        <p class="text-gray-600 text-sm">Stay updated on inquiries and bookings</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

                    <!-- Show login errors -->
                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-400 text-red-800 p-4 rounded-lg text-sm">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-bold text-red-800">Login Failed</span>
                            </div>
                            <div class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <div class="text-red-700">• {{ $error }}</div>
                                @endforeach
                            </div>
                            <div class="mt-3 p-3 bg-blue-50 border border-blue-300 rounded text-blue-800 text-xs">
                                💡 <strong>Having trouble logging in?</strong><br>
                                • Check your email and password are correct<br>
                                • Make sure caps lock is off<br>
                                • <button type="button" onclick="switchToRoleSelection()" class="underline text-blue-600">Create a new account</button> if you don't have one
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('email') border-red-500 bg-red-50 @enderror"
                                   placeholder="your@email.com">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('password') border-red-500 bg-red-50 @enderror"
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
            } else if (mode === 'register') {
                switchToRoleSelection();
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

        // Smart back navigation function
        function goBack() {
            // Check if there's previous history in the same domain
            if (document.referrer && document.referrer.indexOf(window.location.origin) === 0) {
                window.history.back();
            } else {
                // Fallback to home for guest users, or appropriate dashboard for authenticated users
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

        // Guest mobile menu functionality
        function toggleGuestMobileMenu() {
            const overlay = document.getElementById('guestMobileMenuOverlay');
            const panel = document.getElementById('guestMobileMenuPanel');
            const hamburgerIcon = document.getElementById('guestHamburgerIcon');
            const closeIcon = document.getElementById('guestCloseIcon');

            if (overlay.classList.contains('hidden')) {
                // Show mobile menu
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                // Animate panel slide in
                setTimeout(() => {
                    panel.classList.remove('translate-x-full');
                    panel.classList.add('translate-x-0');
                }, 10);

                // Switch icons
                hamburgerIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
            } else {
                closeGuestMobileMenu();
            }
        }

        function closeGuestMobileMenu() {
            const overlay = document.getElementById('guestMobileMenuOverlay');
            const panel = document.getElementById('guestMobileMenuPanel');
            const hamburgerIcon = document.getElementById('guestHamburgerIcon');
            const closeIcon = document.getElementById('guestCloseIcon');

            if (overlay && !overlay.classList.contains('hidden')) {
                // Animate panel slide out
                panel.classList.remove('translate-x-0');
                panel.classList.add('translate-x-full');

                // Hide overlay after animation
                setTimeout(() => {
                    overlay.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }, 300);

                // Switch icons
                hamburgerIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }
        }

        // Close guest mobile menu on navigation
        document.addEventListener('click', function(event) {
            const guestMobileNavLinks = document.querySelectorAll('.guest-mobile-nav-link');
            guestMobileNavLinks.forEach(link => {
                if (link.contains(event.target)) {
                    closeGuestMobileMenu();
                }
            });
        });

        // Auto-show modal if there are validation errors
        @if ($errors->any())
            window.addEventListener('DOMContentLoaded', function() {
                console.log('Validation errors detected...');

                // Check if it's a registration error (has role) or login error
                @if (old('role'))
                    // Registration error - show registration form
                    console.log('Registration error detected, opening registration modal...');
                    openAuthModal('register');
                    switchToRoleSelection();
                    setTimeout(function() {
                        selectRole('{{ old('role') }}');
                        // Add shake effect to the registration form
                        const regForm = document.getElementById('registrationForm');
                        if (regForm) {
                            regForm.classList.add('shake');
                            setTimeout(() => regForm.classList.remove('shake'), 500);
                        }
                    }, 100);
                @else
                    // Login error - show login form
                    console.log('Login error detected, opening login modal...');
                    openAuthModal('login');
                    // Add shake effect to the login form
                    setTimeout(function() {
                        const loginForm = document.getElementById('loginForm');
                        if (loginForm) {
                            loginForm.classList.add('shake');
                            setTimeout(() => loginForm.classList.remove('shake'), 500);
                        }

                        // Also shake the input fields with errors
                        const errorInputs = document.querySelectorAll('.border-red-500');
                        errorInputs.forEach(input => {
                            input.classList.add('shake');
                            setTimeout(() => input.classList.remove('shake'), 500);
                        });
                    }, 100);
                @endif
            });
        @endif
    </script>

    @stack('scripts')
</body>
</html>