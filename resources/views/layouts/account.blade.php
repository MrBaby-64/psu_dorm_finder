<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Account</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Global Animations & Enhancements -->
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">

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

    {{-- Main Content Container --}}
    <div class="min-h-screen pt-16">
        <main class="w-full">
            <div class="p-4 lg:p-8 w-full max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')

    <!-- Session Keep-Alive & Auto-Refresh System -->
    <script>
        (function() {
            // Configuration
            const CONFIG = {
                pingInterval: 5 * 60 * 1000,        // Ping every 5 minutes
                csrfRefreshInterval: 10 * 60 * 1000, // Refresh CSRF token every 10 minutes
                inactivityTimeout: 30 * 60 * 1000,   // Show warning after 30 minutes of inactivity
                autoRefreshOnReturn: true,           // Auto-refresh when user returns after long absence
                absenceThreshold: 15 * 60 * 1000     // Consider user absent after 15 minutes
            };

            let lastActivity = Date.now();
            let lastPing = Date.now();
            let pingInterval = null;
            let csrfInterval = null;
            let isPageVisible = true;
            let wasAbsent = false;
            let isLoggingOut = false;

            // Update CSRF token in all forms and meta tag
            function updateCSRFToken(newToken) {
                // Update meta tag
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', newToken);
                }

                // Update all forms
                document.querySelectorAll('input[name="_token"]').forEach(input => {
                    input.value = newToken;
                });

                // Update axios/fetch default headers if they exist
                if (window.axios && window.axios.defaults && window.axios.defaults.headers) {
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
                }

                console.log('✓ CSRF token refreshed');
            }

            // Ping server to keep session alive
            async function pingServer() {
                // Don't ping if logging out
                if (isLoggingOut) {
                    console.log('Skipping ping - logout in progress');
                    return true;
                }

                try {
                    const response = await fetch('/keep-alive', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });

                    if (response.ok) {
                        const data = await response.json();

                        // Update CSRF token if provided
                        if (data.csrf_token) {
                            updateCSRFToken(data.csrf_token);
                        }

                        lastPing = Date.now();
                        console.log('✓ Session keep-alive ping successful');
                        return true;
                    } else if (response.status === 419) {
                        // CSRF token mismatch - page needs refresh
                        if (!isLoggingOut) {
                            console.warn('⚠ Session expired (419). Refreshing page...');
                            showRefreshNotification();
                            setTimeout(() => window.location.reload(), 2000);
                        }
                        return false;
                    } else if (response.status === 401) {
                        // Unauthorized - redirect to login
                        if (!isLoggingOut) {
                            console.warn('⚠ Unauthorized (401). Redirecting to login...');
                            window.location.href = '/login';
                        }
                        return false;
                    }
                } catch (error) {
                    if (!isLoggingOut) {
                        console.error('✗ Keep-alive ping failed:', error);
                    }
                    return false;
                }
            }

            // Refresh CSRF token
            async function refreshCSRFToken() {
                // Don't refresh if logging out
                if (isLoggingOut) {
                    return true;
                }

                try {
                    const response = await fetch('/refresh-csrf', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data.csrf_token) {
                            updateCSRFToken(data.csrf_token);
                            return true;
                        }
                    }
                } catch (error) {
                    if (!isLoggingOut) {
                        console.error('✗ CSRF refresh failed:', error);
                    }
                }
                return false;
            }

            // Show notification to user
            function showRefreshNotification() {
                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #3b82f6;
                    color: white;
                    padding: 16px 24px;
                    border-radius: 8px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    z-index: 10000;
                    font-family: system-ui, -apple-system, sans-serif;
                    font-size: 14px;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                `;
                notification.innerHTML = `
                    <svg class="animate-spin" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Refreshing page to restore session...</span>
                `;
                document.body.appendChild(notification);
            }

            // Track user activity
            function updateActivity() {
                lastActivity = Date.now();
            }

            // Handle page visibility changes
            function handleVisibilityChange() {
                const nowVisible = !document.hidden;

                if (!isPageVisible && nowVisible) {
                    // Page became visible again
                    const timeAway = Date.now() - lastActivity;

                    console.log(`Page visible again. Time away: ${Math.round(timeAway / 1000)}s`);

                    // If user was away for more than threshold
                    if (timeAway > CONFIG.absenceThreshold) {
                        wasAbsent = true;
                        console.log('User was absent for a while. Pinging server...');

                        // Immediately ping server to check session
                        pingServer().then(success => {
                            if (!success && CONFIG.autoRefreshOnReturn) {
                                // Session expired, will auto-refresh
                                showRefreshNotification();
                                setTimeout(() => window.location.reload(), 2000);
                            }
                        });

                        // Also refresh CSRF token
                        refreshCSRFToken();
                    }
                }

                isPageVisible = nowVisible;
                updateActivity();
            }

            // Start keep-alive intervals
            function startKeepAlive() {
                // Clear any existing intervals
                if (pingInterval) clearInterval(pingInterval);
                if (csrfInterval) clearInterval(csrfInterval);

                // Ping server periodically
                pingInterval = setInterval(() => {
                    if (isPageVisible) {
                        pingServer();
                    }
                }, CONFIG.pingInterval);

                // Refresh CSRF token periodically
                csrfInterval = setInterval(() => {
                    if (isPageVisible) {
                        refreshCSRFToken();
                    }
                }, CONFIG.csrfRefreshInterval);

                console.log('✓ Session keep-alive system started');
                console.log(`  - Ping interval: ${CONFIG.pingInterval / 1000}s`);
                console.log(`  - CSRF refresh: ${CONFIG.csrfRefreshInterval / 1000}s`);
            }

            // Detect logout forms
            document.addEventListener('DOMContentLoaded', function() {
                const logoutForms = document.querySelectorAll('form[action*="logout"]');
                logoutForms.forEach(form => {
                    form.addEventListener('submit', function() {
                        isLoggingOut = true;
                        console.log('Logout initiated - disabling auto-refresh');
                    });
                });
            });

            // Global error handler for 419 errors
            window.addEventListener('error', function(event) {
                if (event.message && event.message.includes('419') && !isLoggingOut) {
                    event.preventDefault();
                    showRefreshNotification();
                    setTimeout(() => window.location.reload(), 2000);
                }
            }, true);

            // Handle fetch/axios 419 errors
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args).then(response => {
                    if (response.status === 419 && !isLoggingOut) {
                        console.warn('⚠ 419 error detected. Refreshing page...');
                        showRefreshNotification();
                        setTimeout(() => window.location.reload(), 2000);
                    }
                    return response;
                });
            };

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Track user activity
                ['mousedown', 'keydown', 'scroll', 'touchstart', 'click'].forEach(event => {
                    document.addEventListener(event, updateActivity, { passive: true });
                });

                // Monitor page visibility
                document.addEventListener('visibilitychange', handleVisibilityChange);

                // Start keep-alive system
                startKeepAlive();

                // Initial ping
                setTimeout(() => pingServer(), 1000);

                console.log('✓ Auto-refresh system initialized');
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (pingInterval) clearInterval(pingInterval);
                if (csrfInterval) clearInterval(csrfInterval);
            });
        })();
    </script>
</body>
</html>