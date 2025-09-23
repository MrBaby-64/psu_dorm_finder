<nav class="sticky top-0 z-40 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-green-600">
                    🎓 PSU Dorm Finder
                </a>
            </div>
            
            <div class="flex items-center space-x-4">
                @auth
                    @if(auth()->user()->role === 'tenant')
                        @php
                            $inquiryCount = \App\Models\Inquiry::where('user_id', auth()->id())->where('status', 'pending')->count();
                            $bookingCount = \App\Models\Booking::where('user_id', auth()->id())->where('status', 'pending')->count();
                            $messageCount = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
                        @endphp
                        
                        <a href="{{ route('properties.browse') }}" class="text-gray-700 hover:text-green-600">Find Rentals</a>
                        
                        <a href="{{ route('bookings.index') }}" class="text-gray-700 hover:text-green-600 relative">
                            Bookings
                            @if($bookingCount > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $bookingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('messages.index') }}" class="text-gray-700 hover:text-green-600 relative">
                            Messages
                            @if($messageCount > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $messageCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('favorites.index') }}" class="text-gray-700 hover:text-green-600">Favorites</a>
                        
                        <a href="{{ route('tenant.account') }}" class="text-gray-700 hover:text-green-600 font-medium">
                            {{ auth()->user()->name }}
                        </a>
                        
                    @elseif(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-green-600">Admin Panel</a>
                        
                        <a href="{{ route('admin.account') }}" class="text-gray-700 hover:text-green-600 font-medium">
                            {{ auth()->user()->name }}
                        </a>
                        
                    @else
                        @php
                            $landlordInquiries = \App\Models\Inquiry::whereHas('property', function($q) {
                                $q->where('user_id', auth()->id());
                            })->where('status', 'pending')->count();
                            
                            $landlordMessages = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
                        @endphp
                        
                        <a href="{{ route('properties.browse') }}" class="text-gray-700 hover:text-green-600">Find Rentals</a>
                        <a href="{{ route('landlord.properties.index') }}" class="text-gray-700 hover:text-green-600">My Properties</a>
                        <a href="{{ route('landlord.inquiries.index') }}" class="text-gray-700 hover:text-green-600 relative">
                            Inquiries
                            @if($landlordInquiries > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $landlordInquiries }}</span>
                            @endif
                        </a>
                        <a href="{{ route('messages.index') }}" class="text-gray-700 hover:text-green-600 relative">
                            Messages
                            @if($landlordMessages > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $landlordMessages }}</span>
                            @endif
                        </a>
                        
                        <a href="{{ route('landlord.account') }}" class="text-gray-700 hover:text-green-600 font-medium">
                            {{ auth()->user()->name }}
                        </a>
                    @endif
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-green-600">Logout</button>
                    </form>
                @else
                    {{-- Guest User Navigation --}}
                    <a href="{{ route('properties.browse') }}" class="text-gray-700 hover:text-green-600">Find Rentals</a>
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-green-600">Login</a>
                    <a href="{{ route('register') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Sign Up</a>
                @endauth
            </div>
        </div>
    </div>
</nav>