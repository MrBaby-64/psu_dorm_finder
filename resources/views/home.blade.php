{{-- resources/views/home.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSU Dorm Finder - Find Your Perfect Student Housing</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    
    {{-- Navigation --}}
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-green-600">
                        PSU Dorm Finder
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('properties.browse') }}" class="text-gray-700 hover:text-green-600">Browse</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-green-600">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-green-600">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-green-600">Login</a>
                        <a href="{{ route('register') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-green-500 to-green-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    Find Your Perfect Student Housing
                </h1>
                <p class="text-xl mb-8">
                    Discover verified dormitories and apartments near PSU Bacolor & San Fernando
                </p>
                
                {{-- Search Form --}}
                <form action="{{ route('properties.browse') }}" method="GET" class="max-w-2xl mx-auto">
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            name="q" 
                            placeholder="Search by location, property name..."
                            class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-400"
                        >
                        <button type="submit" class="bg-white text-green-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100">
                            Search
                        </button>
                    </div>
                </form>

                {{-- Quick Links --}}
                <div class="mt-6 flex justify-center gap-4 text-sm">
                    <a href="{{ route('properties.browse', ['city' => 'Bacolor']) }}" class="hover:underline">
                        Near Bacolor Campus
                    </a>
                    <a href="{{ route('properties.browse', ['city' => 'San Fernando']) }}" class="hover:underline">
                        Near San Fernando Campus
                    </a>
                    <a href="{{ route('properties.browse', ['is_verified' => 1]) }}" class="hover:underline">
                        PSU Verified
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Featured Properties --}}
    @if($featuredProperties->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-3xl font-bold mb-8">Featured Properties</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredProperties as $property)
            <a href="{{ route('properties.show', $property) }}" class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <div class="h-48 bg-gray-300 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                
                <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="font-semibold text-lg">{{ $property->title }}</h3>
                        @if($property->is_verified)
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Verified</span>
                        @endif
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-3">{{ $property->location_text }}</p>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-2xl font-bold text-green-600">₱{{ number_format($property->price, 0) }}</span>
                        <span class="text-sm text-gray-500">/month</span>
                    </div>
                    
                    @if($property->rating_count > 0)
                    <div class="mt-2 flex items-center text-sm">
                        <span class="text-yellow-500">★</span>
                        <span class="ml-1">{{ number_format($property->rating_avg, 1) }}</span>
                        <span class="text-gray-500 ml-1">({{ $property->rating_count }} reviews)</span>
                    </div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Info Section --}}
    <section class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Why Choose PSU Dorm Finder?</h2>
                <p class="text-gray-600">Your trusted platform for student housing near Pampanga State University</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl">✓</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">PSU Verified</h3>
                    <p class="text-gray-600">Properties verified by university administration for your safety</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl">📍</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Near Campus</h3>
                    <p class="text-gray-600">Find properties within walking distance of PSU campuses</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl">₱</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Affordable</h3>
                    <p class="text-gray-600">Budget-friendly options starting from ₱2,200/month</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; {{ date('Y') }} PSU Dorm Finder. All rights reserved.</p>
            <p class="text-gray-400 text-sm mt-2">Pampanga State University - Bacolor & San Fernando Campuses</p>
        </div>
    </footer>

</body>
</html>