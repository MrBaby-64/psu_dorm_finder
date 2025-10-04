@extends('layouts.account')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">My Favorites</h1>
            <p class="text-gray-600">Properties you've saved for later</p>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Property name or location..." 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Price</label>
                    <input 
                        type="number" 
                        name="min_price" 
                        value="{{ request('min_price') }}"
                        placeholder="0" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Price</label>
                    <input 
                        type="number" 
                        name="max_price" 
                        value="{{ request('max_price') }}"
                        placeholder="50000" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    >
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Favorites Grid -->
        @if($favorites->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($favorites as $favorite)
                    @php $property = $favorite->property @endphp
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                        <!-- Property Image -->
                        <div class="relative">
                            @php
                                // First try to get cover image, then any image, with better fallback
                                $coverImage = $property->images->where('is_cover', true)->first();
                                $firstImage = $property->images->first();
                                $selectedImage = $coverImage ?: $firstImage;

                                // Use the model's accessor for proper URL generation
                                if ($selectedImage && $selectedImage->full_url) {
                                    $imageUrl = $selectedImage->full_url;
                                } else {
                                    $imageUrl = 'https://via.placeholder.com/400x300/f3f4f6/9ca3af?text=' . urlencode($property->title ?: 'Property');
                                }
                            @endphp

                            <img src="{{ $imageUrl }}"
                                 alt="{{ $property->title }}"
                                 class="w-full h-48 object-cover"
                                 onerror="this.src='https://via.placeholder.com/400x300/f3f4f6/9ca3af?text={{ urlencode($property->title ?: 'Property') }}'"
                                 loading="lazy">
                            
                            <!-- Remove from favorites button -->
                            <form action="{{ route('favorites.destroy', $property) }}" method="POST" class="absolute top-3 right-3">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-2 bg-white rounded-full shadow-md hover:bg-red-50 transition"
                                        onclick="return confirm('Remove from favorites?')">
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                            </form>

                            <!-- Property badges -->
                            <div class="absolute top-3 left-3">
                                @if($property->is_featured)
                                    <span class="bg-yellow-500 text-white px-2 py-1 rounded text-xs font-medium">Featured</span>
                                @endif
                                @if($property->is_verified)
                                    <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-medium ml-1">Verified</span>
                                @endif
                            </div>
                        </div>

                        <!-- Property Info -->
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $property->title }}</h3>
                                    @if($property->is_verified)
                                        <x-psu-verified-badge size="sm" />
                                    @endif
                                </div>
                                @if($property->rating_count > 0)
                                    <div class="flex items-center ml-2">
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-600 ml-1">{{ number_format($property->rating_avg, 1) }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center text-gray-600 mb-3">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"></path>
                                </svg>
                                <span class="text-sm truncate">{{ $property->location_text }}</span>
                            </div>

                            <!-- Price -->
                            <div class="mb-4">
                                <span class="text-2xl font-bold text-green-600">₱{{ number_format($property->price) }}</span>
                                <span class="text-gray-500 text-sm">/month</span>
                            </div>

                            <!-- Room count -->
                            <div class="flex items-center text-gray-600 mb-4">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span class="text-sm">{{ $property->room_count }} {{ Str::plural('room', $property->room_count) }}</span>
                            </div>

                            <!-- Amenities (first 3) -->
                            @if($property->amenities && $property->amenities->count() > 0)
                                <div class="mb-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($property->amenities->take(3) as $amenity)
                                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">{{ $amenity->name }}</span>
                                        @endforeach
                                        @if($property->amenities->count() > 3)
                                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">+{{ $property->amenities->count() - 3 }} more</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="{{ route('properties.show', $property->slug) }}"
                                   class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-center text-sm font-medium">
                                    View Details
                                </a>
                            </div>

                            <!-- Saved date -->
                            <div class="mt-3 text-xs text-gray-500">
                                Saved {{ $favorite->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $favorites->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No favorites yet</h3>
                <p class="text-gray-500 mb-6">Start browsing properties and save the ones you like!</p>
                <a href="{{ route('properties.browse') }}" 
                   class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-medium">
                    Browse Properties
                </a>
            </div>
        @endif
    </div>
</div>
@endsection