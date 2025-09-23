{{-- resources/views/layouts/navigation.blade.php --}}

<nav class="sticky top-0 z-40 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-green-600">
                    PSU Dorm Finder
                </a>
                <div class="hidden md:flex ml-10 space-x-8">
                    <a href="{{ route('properties.browse') }}" class="text-gray-700 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium">
                        Browse Properties
                    </a>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                @auth
                    @if(auth()->user()->role === 'landlord')
                    <a href="{{ route('landlord.properties.index') }}" class="text-gray-700 hover:text-green-600">
                        My Properties
                    </a>
                    @endif

                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-green-600">
                        Admin Panel
                    </a>
                    @endif

                    <a href="{{ route('favorites.index') }}" class="text-gray-700 hover:text-green-600">
                        Favorites
                    </a>
                    <a href="{{ route('messages.index') }}" class="text-gray-700 hover:text-green-600">
                        Messages
                    </a>
                    <a href="{{ route('bookings.index') }}" class="text-gray-700 hover:text-green-600">
                        Bookings
                    </a>
                    <a href="{{ route('about') }}" class="text-gray-700 hover:text-green-600">About Us</a>
                    <a href="{{ route('how-it-works') }}" class="text-gray-700 hover:text-green-600">How It Works</a>
                    
                    {{-- User Menu - Simple Version without Alpine.js --}}
                    <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:text-green-600">
                        {{ auth()->user()->name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-green-600">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-green-600">Login</a>
                    <a href="{{ route('register') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        Sign Up
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>