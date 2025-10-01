<nav class="sticky top-0 z-40 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- LEFT SIDE: Back Button + Logo --}}
            <div class="flex items-center space-x-3">
                {{-- Back Button - Show for non-home pages --}}
                @if(!request()->is('/') && !request()->is('dashboard'))
                <button onclick="goBack()" class="flex items-center text-gray-600 hover:text-green-600 hover:bg-gray-100 px-3 py-2 rounded-lg transition-all duration-200 border border-gray-200" title="Go back">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                @endif

                <a href="{{ route('home') }}" class="text-lg sm:text-xl lg:text-2xl font-bold text-green-600 flex items-center">
                    🎓 PSU Dorm Finder
                </a>
            </div>

            {{-- DESKTOP MENU: Menu Items + User Controls - ONLY VISIBLE ON DESKTOP --}}
            <div class="hidden md:flex items-center space-x-1 lg:space-x-3" id="desktopNavigation">
                {{-- Menu Items - Show for authenticated users --}}
                @auth
                    @if(auth()->user()->role === 'tenant')
                        {{-- Tenant Menu --}}
                        <a href="{{ route('properties.browse') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-gray-100 transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium">Browse</a>
                        <a href="{{ route('tenant.account') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg {{ request()->routeIs('tenant.account') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:text-green-600 hover:bg-gray-100' }} transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium">Dashboard</a>
                        <a href="{{ route('favorites.index') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-gray-100 transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium">Favorites</a>
                        <a href="{{ route('tenant.notifications') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg {{ request()->routeIs('tenant.notifications') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:text-green-600 hover:bg-gray-100' }} transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium">Notifications</a>
                        <a href="{{ route('tenant.scheduled-visits') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-gray-100 transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium">Visits</a>
                        <a href="{{ route('tenant.reviews') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-gray-100 transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium">Reviews</a>
                        <a href="{{ route('bookings.index') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-gray-100 transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium relative">
                            Bookings
                            @php
                                $bookingCount = \App\Models\Booking::where('user_id', auth()->id())->where('status', 'pending')->count();
                            @endphp
                            @if($bookingCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $bookingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('messages.index') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-gray-100 transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium relative">
                            Messages
                            @php
                                $messageCount = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
                            @endphp
                            @if($messageCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $messageCount }}</span>
                            @endif
                        </a>

                    @elseif(auth()->user()->role === 'admin')
                        {{-- Admin Menu --}}
                        <a href="{{ route('admin.dashboard') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-gray-100 transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium">Dashboard</a>
                        <a href="{{ route('admin.account') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg {{ request()->routeIs('admin.account') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:text-green-600 hover:bg-gray-100' }} transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium">Profile</a>

                    @else
                        {{-- Landlord Menu --}}
                        <a href="{{ route('properties.browse') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-gray-100 transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium">Browse</a>
                        <a href="{{ route('landlord.account') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg {{ request()->routeIs('landlord.account') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:text-green-600 hover:bg-gray-100' }} transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium">Dashboard</a>
                        <a href="{{ route('landlord.properties.index') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-gray-100 transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium">Properties</a>
                        <a href="{{ route('landlord.inquiries.index') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-gray-100 transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium relative">
                            Inquiries
                            @php
                                $landlordInquiries = \App\Models\Inquiry::whereHas('property', function($q) {
                                    $q->where('user_id', auth()->id());
                                })->where('status', 'pending')->count();
                            @endphp
                            @if($landlordInquiries > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $landlordInquiries }}</span>
                            @endif
                        </a>
                        <a href="{{ route('messages.index') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-gray-100 transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium relative">
                            Messages
                            @php
                                $landlordMessages = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
                            @endphp
                            @if($landlordMessages > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $landlordMessages }}</span>
                            @endif
                        </a>
                        <a href="{{ route('landlord.notifications') }}" class="nav-link px-2 lg:px-3 py-2 rounded-lg {{ request()->routeIs('landlord.notifications') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:text-green-600 hover:bg-gray-100' }} transition-all duration-200 whitespace-nowrap text-xs lg:text-sm font-medium">Notifications</a>
                    @endif

                    {{-- Separator before user dropdown --}}
                    <div class="border-l border-gray-300 h-6 mx-2 lg:mx-4"></div>

                    {{-- User dropdown with logout - ONLY VISIBLE ON DESKTOP --}}
                    <div class="relative" id="userDropdown">
                        <button onclick="toggleUserDropdown(event)" class="flex items-center text-gray-700 hover:text-green-600 font-medium px-2 lg:px-3 py-2 rounded-lg hover:bg-gray-100 transition-all duration-200 text-xs lg:text-sm" id="userDropdownButton">
                            {{ auth()->user()->name }}
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200" id="userDropdownArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 transform scale-95 transition-all duration-200 z-50 hidden" id="userDropdownMenu">
                            <div class="py-2">
                                <a href="{{ route('profile.edit') }}" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Settings
                                </a>
                                <div class="border-t border-gray-200 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}" id="desktop-logout-form" onsubmit="event.stopPropagation(); return confirm('LOGOUT: Are you sure you want to logout?');">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center" onclick="event.stopPropagation();">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Guest User Navigation --}}
                    <a href="{{ route('login') }}" class="nav-link px-2 lg:px-4 py-2 rounded-lg text-gray-700 hover:text-green-600 hover:bg-gray-100 transition-all duration-200 font-medium text-xs lg:text-sm">Login</a>
                    <a href="{{ route('register') }}" class="nav-link px-2 lg:px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors duration-200 font-medium shadow-sm text-xs lg:text-sm">Sign Up</a>
                @endauth
            </div>

            {{-- MOBILE MENU SECTION - ONLY VISIBLE ON MOBILE --}}
            <div class="md:hidden flex items-center space-x-3" id="mobileNavigation">
                @auth
                    {{-- Logout button for mobile --}}
                    <form method="POST" action="{{ route('logout') }}" class="inline" id="mobile-logout-form" onsubmit="event.stopPropagation(); return confirm('LOGOUT: Are you sure you want to logout?');">
                        @csrf
                        <button type="submit" class="flex items-center text-gray-700 hover:text-red-600 px-3 py-2 rounded-lg hover:bg-gray-100 transition-all duration-200" title="Logout" onclick="event.stopPropagation();">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>

                    {{-- Menu toggle for authenticated users --}}
                    <button onclick="toggleMobileMenu()" class="flex items-center text-gray-600 hover:text-green-600 hover:bg-gray-100 px-3 py-2 rounded-lg transition-all duration-200" id="mobileMenuButton" aria-label="Toggle menu">
                        <svg class="w-6 h-6" id="hamburgerIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg class="w-6 h-6 hidden" id="closeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @else
                    {{-- Menu toggle for guest users --}}
                    <button onclick="toggleMobileMenu()" class="flex items-center text-gray-600 hover:text-green-600 hover:bg-gray-100 px-3 py-2 rounded-lg transition-all duration-200" id="mobileMenuButton" aria-label="Toggle menu">
                        <svg class="w-6 h-6" id="hamburgerIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg class="w-6 h-6 hidden" id="closeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endauth
            </div>
        </div>

        {{-- MOBILE MENU OVERLAY --}}
        <div class="md:hidden fixed inset-0 z-50 hidden" id="mobileMenuOverlay">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeMobileMenu()"></div>
            <div class="fixed top-0 right-0 h-full w-72 sm:w-80 max-w-sm bg-white shadow-xl transform translate-x-full transition-transform duration-300 ease-in-out" id="mobileMenuPanel">
                {{-- Mobile Menu Header --}}
                <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                    <span class="text-lg font-semibold text-gray-900">Menu</span>
                    <button onclick="closeMobileMenu()" class="text-gray-500 hover:text-gray-700">
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
                                {{-- Tenant Mobile Menu --}}
                                <a href="{{ route('properties.browse') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        <span>Browse Properties</span>
                                    </div>
                                </a>
                                <a href="{{ route('tenant.account') }}" class="mobile-nav-link block px-4 py-3 rounded-lg {{ request()->routeIs('tenant.account') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:text-green-600 hover:bg-green-50' }} transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0H8v0z"></path></svg>
                                        <span>Dashboard</span>
                                    </div>
                                </a>
                                <a href="{{ route('favorites.index') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                        <span>Favorites</span>
                                    </div>
                                </a>
                                <a href="{{ route('tenant.notifications') }}" class="mobile-nav-link block px-4 py-3 rounded-lg {{ request()->routeIs('tenant.notifications') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:text-green-600 hover:bg-green-50' }} transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5-5 5h5zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Notifications</span>
                                    </div>
                                </a>
                                <a href="{{ route('tenant.scheduled-visits') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v16a2 2 0 002 2z"></path></svg>
                                        <span>Scheduled Visits</span>
                                    </div>
                                </a>
                                <a href="{{ route('tenant.reviews') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                        <span>Reviews</span>
                                    </div>
                                </a>
                                <a href="{{ route('bookings.index') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium relative">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        <span>Bookings</span>
                                        @php
                                            $bookingCount = \App\Models\Booking::where('user_id', auth()->id())->where('status', 'pending')->count();
                                        @endphp
                                        @if($bookingCount > 0)
                                            <span class="bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $bookingCount }}</span>
                                        @endif
                                    </div>
                                </a>
                                <a href="{{ route('messages.index') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium relative">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        <span>Messages</span>
                                        @php
                                            $messageCount = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
                                        @endphp
                                        @if($messageCount > 0)
                                            <span class="bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $messageCount }}</span>
                                        @endif
                                    </div>
                                </a>

                            @elseif(auth()->user()->role === 'admin')
                                {{-- Admin Mobile Menu --}}
                                <a href="{{ route('admin.dashboard') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                        <span>Dashboard</span>
                                    </div>
                                </a>
                                <a href="{{ route('admin.account') }}" class="mobile-nav-link block px-4 py-3 rounded-lg {{ request()->routeIs('admin.account') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:text-green-600 hover:bg-green-50' }} transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span>Profile</span>
                                    </div>
                                </a>

                            @else
                                {{-- Landlord Mobile Menu --}}
                                <a href="{{ route('properties.browse') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        <span>Browse Properties</span>
                                    </div>
                                </a>
                                <a href="{{ route('landlord.account') }}" class="mobile-nav-link block px-4 py-3 rounded-lg {{ request()->routeIs('landlord.account') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:text-green-600 hover:bg-green-50' }} transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0H8v0z"></path></svg>
                                        <span>Dashboard</span>
                                    </div>
                                </a>
                                <a href="{{ route('landlord.properties.index') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <span>My Properties</span>
                                    </div>
                                </a>
                                <a href="{{ route('landlord.inquiries.index') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium relative">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Inquiries</span>
                                        @php
                                            $landlordInquiries = \App\Models\Inquiry::whereHas('property', function($q) {
                                                $q->where('user_id', auth()->id());
                                            })->where('status', 'pending')->count();
                                        @endphp
                                        @if($landlordInquiries > 0)
                                            <span class="bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $landlordInquiries }}</span>
                                        @endif
                                    </div>
                                </a>
                                <a href="{{ route('messages.index') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium relative">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        <span>Messages</span>
                                        @php
                                            $landlordMessages = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
                                        @endphp
                                        @if($landlordMessages > 0)
                                            <span class="bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $landlordMessages }}</span>
                                        @endif
                                    </div>
                                </a>
                                <a href="{{ route('landlord.notifications') }}" class="mobile-nav-link block px-4 py-3 rounded-lg {{ request()->routeIs('landlord.notifications') ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:text-green-600 hover:bg-green-50' }} transition-all duration-200 font-medium">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5-5 5h5zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Notifications</span>
                                    </div>
                                </a>
                            @endif

                            {{-- Settings Link --}}
                            <div class="border-t border-gray-200 my-4"></div>
                            <a href="{{ route('profile.edit') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>Settings</span>
                                </div>
                            </a>
                        </div>
                    @else
                        {{-- Guest Mobile Menu --}}
                        <div class="space-y-2">
                            <a href="{{ route('properties.browse') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <span>Find Rentals</span>
                                </div>
                            </a>
                            <a href="#about" onclick="closeMobileMenu(); document.getElementById('about').scrollIntoView({behavior: 'smooth'});" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>About Us</span>
                                </div>
                            </a>
                            <a href="#how-it-works" onclick="closeMobileMenu(); document.getElementById('how-it-works').scrollIntoView({behavior: 'smooth'});" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>How It Works</span>
                                </div>
                            </a>

                            <div class="border-t border-gray-200 my-4"></div>

                            <a href="{{ route('login') }}" class="mobile-nav-link block px-4 py-3 rounded-lg text-gray-700 hover:text-green-600 hover:bg-green-50 transition-all duration-200 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    <span>Login</span>
                                </div>
                            </a>
                            <a href="{{ route('register') }}" class="mobile-nav-link block px-4 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-all duration-200 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                    <span>Sign Up</span>
                                </div>
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
// Smart back navigation function
function goBack() {
    // Check if there's previous history in the same domain
    if (document.referrer && document.referrer.indexOf(window.location.origin) === 0) {
        window.history.back();
    } else {
        // Fallback to dashboard or home based on user role
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

// User dropdown functionality - COMPLETELY ISOLATED
let userDropdownOpen = false;

// Initialize dropdown state on page load
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('userDropdownMenu');
    if (dropdown) {
        // Ensure dropdown is properly closed and state is synchronized
        userDropdownOpen = false;
    }
});

function toggleUserDropdown(event) {
    // Stop any event bubbling to prevent interference
    if (event) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
    }

    const dropdown = document.getElementById('userDropdownMenu');
    const arrow = document.getElementById('userDropdownArrow');

    if (!dropdown || !arrow) return;

    // Check current state using the hidden class instead of our variable
    const isCurrentlyHidden = dropdown.classList.contains('hidden');

    if (isCurrentlyHidden) {
        // Show dropdown
        dropdown.classList.remove('hidden', 'scale-95');
        dropdown.classList.add('scale-100');
        arrow.classList.add('rotate-180');
        userDropdownOpen = true;
    } else {
        // Hide dropdown
        dropdown.classList.remove('scale-100');
        dropdown.classList.add('hidden', 'scale-95');
        arrow.classList.remove('rotate-180');
        userDropdownOpen = false;
    }
}

function closeUserDropdown() {
    const dropdown = document.getElementById('userDropdownMenu');
    const arrow = document.getElementById('userDropdownArrow');

    if (dropdown && arrow && userDropdownOpen) {
        dropdown.classList.remove('scale-100');
        dropdown.classList.add('hidden', 'scale-95');
        arrow.classList.remove('rotate-180');
        userDropdownOpen = false;
    }
}

// Only close dropdown on very specific outside clicks
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const dropdownMenu = document.getElementById('userDropdownMenu');

    // Only process if dropdown elements exist and dropdown is actually open AND visible
    if (!dropdown || !dropdownMenu ||
        !userDropdownOpen ||
        dropdownMenu.classList.contains('hidden') ||
        dropdownMenu.classList.contains('invisible') ||
        dropdownMenu.classList.contains('opacity-0')) {
        return;
    }

    // ONLY close if clicking completely outside the dropdown area
    // AND not on any form elements anywhere
    if (!dropdown.contains(event.target) &&
        !event.target.closest('form') &&
        !event.target.closest('button') &&
        event.target.tagName !== 'BUTTON' &&
        event.target.type !== 'submit') {
        closeUserDropdown();
    }
});

// Mobile menu functionality
function toggleMobileMenu() {
    const overlay = document.getElementById('mobileMenuOverlay');
    const panel = document.getElementById('mobileMenuPanel');
    const hamburgerIcon = document.getElementById('hamburgerIcon');
    const closeIcon = document.getElementById('closeIcon');

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
        closeMobileMenu();
    }
}

function closeMobileMenu() {
    const overlay = document.getElementById('mobileMenuOverlay');
    const panel = document.getElementById('mobileMenuPanel');
    const hamburgerIcon = document.getElementById('hamburgerIcon');
    const closeIcon = document.getElementById('closeIcon');

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

// Close mobile menu on navigation
document.addEventListener('click', function(event) {
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
    mobileNavLinks.forEach(link => {
        if (link.contains(event.target)) {
            closeMobileMenu();
        }
    });
});

// Ensure dropdown is closed on page load
document.addEventListener('DOMContentLoaded', function() {
    const dropdownMenu = document.getElementById('userDropdownMenu');
    const arrow = document.getElementById('userDropdownArrow');
    if (dropdownMenu) {
        dropdownMenu.classList.add('hidden', 'scale-95');
        dropdownMenu.classList.remove('scale-100', 'opacity-0', 'invisible');
        arrow.classList.remove('rotate-180');
    }

    // Ensure mobile menu is closed on page load
    const mobileOverlay = document.getElementById('mobileMenuOverlay');
    const mobilePanel = document.getElementById('mobileMenuPanel');
    if (mobileOverlay && mobilePanel) {
        mobileOverlay.classList.add('hidden');
        mobilePanel.classList.add('translate-x-full');
        mobilePanel.classList.remove('translate-x-0');
        document.body.style.overflow = 'auto';
    }
});
</script>