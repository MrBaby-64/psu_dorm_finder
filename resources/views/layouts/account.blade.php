<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Account</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Enhanced scrollbar styling */
        body {
            scrollbar-width: thin;
            scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
        }

        body::-webkit-scrollbar {
            width: 6px;
        }

        body::-webkit-scrollbar-track {
            background: transparent;
        }

        body::-webkit-scrollbar-thumb {
            background-color: rgba(155, 155, 155, 0.5);
            border-radius: 3px;
        }

        body::-webkit-scrollbar-thumb:hover {
            background-color: rgba(155, 155, 155, 0.7);
        }

        /* Better focus states */
        button:focus-visible {
            outline: 2px solid #10b981;
            outline-offset: 2px;
            border-radius: 0.375rem;
        }

        /* Smooth transitions */
        #main-content {
            transition: all 0.3s ease-in-out;
        }

        /* Better dropdown animations */
        .dropdown-menu {
            transform-origin: top right;
        }

        /* Smooth responsive transitions */
        .nav-link, button, .mobile-nav-link {
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
                font-size: 0.8rem;
                padding: 0.5rem 0.75rem;
            }
        }

        @media (min-width: 1024px) {
            .nav-link {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }
        }

        /* Mobile menu panel responsiveness */
        @media (max-width: 640px) {
            #mobileMenuPanel, #guestMobileMenuPanel {
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
            #desktopNavigation {
                display: none !important;
            }
            #mobileNavigation {
                display: flex !important;
            }
        }

        @media (min-width: 768px) {
            #mobileNavigation {
                display: none !important;
            }
            #desktopNavigation {
                display: flex !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">

    @php
        $showSidebar = request()->routeIs('tenant.account', 'admin.account', 'landlord.account', 'profile.edit', 'tenant.notifications', 'tenant.reviews', 'tenant.scheduled-visits', 'landlord.notifications', 'landlord.properties.*', 'landlord.inquiries.*', 'favorites.index');
    @endphp

    {{-- Navbar - Sticky --}}
    @include('layouts.partials.account-navbar')

    <div class="min-h-screen">
        {{-- Main Content --}}
        <main class="pt-16 min-h-screen w-full">
            <div class="p-4 lg:p-8 w-full">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>