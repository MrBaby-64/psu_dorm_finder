<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Account</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Enhanced sidebar styling */
        #sidebar {
            scrollbar-width: thin;
            scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
        }

        #sidebar::-webkit-scrollbar {
            width: 4px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(155, 155, 155, 0.5);
            border-radius: 2px;
        }

        #sidebar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(155, 155, 155, 0.7);
        }

        /* Smooth transitions for content */
        #main-content {
            transition: margin-left 0.3s ease-in-out, padding 0.3s ease-in-out;
        }

        /* Backdrop blur for overlay */
        #sidebar-overlay {
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }

        /* Focus styles for accessibility */
        #sidebar a:focus,
        #sidebar-toggle:focus {
            outline: 2px solid #10b981;
            outline-offset: 2px;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">

    @php
        $showSidebar = request()->routeIs('tenant.account', 'admin.account', 'landlord.account', 'profile.edit');
    @endphp

    {{-- Navbar - Sticky --}}
    @include('layouts.partials.account-navbar')

    {{-- Back Button and Toggle - Sticky --}}
    <div class="sticky top-16 z-30 bg-white border-b px-4 py-2 flex items-center justify-between">
        @if(!request()->is('/') && !request()->is('account') && !request()->is('landlord/account') && !request()->is('admin/account'))
        <button onclick="window.history.back()" class="flex items-center text-gray-600 hover:text-green-600">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back
        </button>
        @else
        <div></div>
        @endif

        @if($showSidebar)
        <button id="sidebar-toggle" class="flex items-center text-gray-600 hover:text-green-600 hover:bg-gray-100 p-2 rounded-lg transition-all duration-200" aria-label="Toggle sidebar menu">
            <svg id="menu-icon" class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <svg id="close-icon" class="w-6 h-6 hidden transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        @endif
    </div>

    {{-- Overlay for mobile --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

    <div class="flex min-h-screen">
        @if($showSidebar)
        {{-- Sidebar --}}
        <aside id="sidebar" class="fixed lg:relative left-0 top-16 lg:top-0 h-[calc(100vh-4rem)] lg:h-auto w-64 bg-white border-r transform transition-transform duration-300 ease-in-out z-40 lg:z-10 lg:translate-x-0" aria-hidden="false">
            <div class="p-4 h-full overflow-y-auto">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Menu</h2>
        
        @if(auth()->user()->role === 'tenant')
            {{-- Tenant Menu --}}
            <nav class="space-y-2">
                <a href="{{ route('tenant.account') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg {{ request()->routeIs('tenant.account') ? 'bg-gray-100' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Account
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Transactions
                </a>
                <a href="{{ route('favorites.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    Favorites
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    Notifications
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Scheduled Visits
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    To Review
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Security & Privacy
                </a>
            </nav>
        @elseif(auth()->user()->role === 'admin')
            {{-- Admin Menu --}}
            <nav class="space-y-2">
                <a href="{{ route('admin.account') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg {{ request()->routeIs('admin.account') ? 'bg-gray-100' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Account
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    Notifications
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Security & Privacy
                </a>
            </nav>
        @else
            {{-- Landlord Menu --}}
            <nav class="space-y-2">
                <a href="{{ route('landlord.account') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg {{ request()->routeIs('landlord.account') ? 'bg-gray-100' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Account
                </a>
                <a href="{{ route('landlord.properties.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Properties
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Transactions
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    Notifications
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Security & Privacy
                </a>
            </nav>
        @endif
            </div>
        </aside>
        @endif

        {{-- Main Content --}}
        <main id="main-content" class="flex-1 transition-all duration-300 ease-in-out min-h-screen {{ $showSidebar ? 'lg:pl-4' : 'p-4' }} lg:p-8">
            @yield('content')
        </main>
    </div>

    <script>
        // Enhanced Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleButton = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');

            // Check if we're on mobile
            function isMobile() {
                return window.innerWidth < 1024;
            }

            // Show/hide overlay for mobile
            function toggleOverlay(show) {
                if (overlay) {
                    if (show && isMobile()) {
                        overlay.classList.remove('hidden');
                    } else {
                        overlay.classList.add('hidden');
                    }
                }
            }

            // Update toggle button icons
            function updateToggleIcons(isOpen) {
                if (menuIcon && closeIcon) {
                    if (isOpen) {
                        menuIcon.classList.add('hidden');
                        closeIcon.classList.remove('hidden');
                    } else {
                        menuIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                    }
                }
            }

            // Update sidebar state
            function updateSidebarState(isOpen) {
                if (!sidebar) return;

                if (isOpen) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.setAttribute('aria-hidden', 'false');
                    toggleOverlay(true);
                } else {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.setAttribute('aria-hidden', 'true');
                    toggleOverlay(false);
                }

                // Update toggle button icons
                updateToggleIcons(isOpen);

                // Save state only for desktop
                if (!isMobile()) {
                    sessionStorage.setItem('sidebarCollapsed', !isOpen);
                }
            }

            if (sidebar && toggleButton) {
                // Get initial state
                let isOpen = true;

                // On mobile, start collapsed
                if (isMobile()) {
                    isOpen = false;
                } else {
                    // On desktop, use saved preference
                    const savedState = sessionStorage.getItem('sidebarCollapsed');
                    isOpen = savedState !== 'true';
                }

                // Set initial state
                updateSidebarState(isOpen);

                // Toggle button click
                toggleButton.addEventListener('click', function() {
                    const currentlyOpen = !sidebar.classList.contains('-translate-x-full');
                    updateSidebarState(!currentlyOpen);
                });

                // Close sidebar when clicking overlay (mobile only)
                if (overlay) {
                    overlay.addEventListener('click', function() {
                        updateSidebarState(false);
                    });
                }

                // Handle window resize
                window.addEventListener('resize', function() {
                    if (isMobile()) {
                        // On mobile, always start collapsed
                        updateSidebarState(false);
                    } else {
                        // On desktop, restore saved preference
                        const savedState = sessionStorage.getItem('sidebarCollapsed');
                        const shouldBeOpen = savedState !== 'true';
                        updateSidebarState(shouldBeOpen);
                        toggleOverlay(false); // Hide overlay on desktop
                    }
                });

                // Close sidebar with Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
                        updateSidebarState(false);
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>