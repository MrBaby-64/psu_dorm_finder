@extends('layouts.guest')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
<style>
    #browseMap {
        height: calc(100vh - 200px);
        min-height: 500px;
    }
    .property-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .property-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Browse Dormitories</h1>
        <p class="text-gray-600 mt-2">Find your perfect student accommodation near PSU</p>

        @auth
            @if(auth()->user()->role === 'landlord')
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-blue-800">
                            <strong>Browsing as Landlord</strong>
                            <p class="text-sm mt-1">You're currently browsing properties as a landlord. To send inquiries or book rooms, please sign in with a tenant account.</p>
                            <div class="mt-2">
                                <a href="{{ route('landlord.properties.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Manage Your Properties →</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
    </div>

    <!-- Search and Filters Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form action="{{ route('properties.browse') }}" method="GET" id="searchForm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <!-- Search Input -->
                <div>
                    <input
                        type="text"
                        name="q"
                        placeholder="Search properties..."
                        value="{{ request('q') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                </div>

                <!-- Sort By -->
                <div>
                    <select
                        name="sort"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        onchange="this.form.submit()"
                    >
                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="room_asc" {{ request('sort') == 'room_asc' ? 'selected' : '' }}>Rooms: Low to High</option>
                        <option value="room_desc" {{ request('sort') == 'room_desc' ? 'selected' : '' }}>Rooms: High to Low</option>
                        <option value="nearest" {{ request('sort') == 'nearest' ? 'selected' : '' }}>Nearest to Campus</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-2">
                    <button
                        type="submit"
                        class="flex-1 bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-200"
                    >
                        🔍 Search
                    </button>
                    @if(request()->hasAny(['q', 'sort']) && (request('q') || request('sort') != 'newest'))
                    <a
                        href="{{ route('properties.browse') }}"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center"
                        title="Clear all filters"
                    >
                        ✕
                    </a>
                    @endif
                </div>
            </div>

            <!-- Active Filters Display -->
            @if(request()->hasAny(['q', 'sort']) && (request('q') || request('sort') != 'newest'))
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="text-sm text-gray-600 mr-2">Active filters:</span>

                @if(request('q'))
                <span class="inline-flex items-center bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                    Search: "{{ request('q') }}"
                    <a href="{{ request()->url() }}?{{ http_build_query(request()->except('q')) }}" class="ml-2 text-green-600 hover:text-green-800">×</a>
                </span>
                @endif

                @if(request('sort') && request('sort') != 'newest')
                <span class="inline-flex items-center bg-orange-100 text-orange-800 text-sm px-3 py-1 rounded-full">
                    Sort:
                    @switch(request('sort'))
                        @case('price_asc') Price: Low to High @break
                        @case('price_desc') Price: High to Low @break
                        @case('room_asc') Rooms: Low to High @break
                        @case('room_desc') Rooms: High to Low @break
                        @case('nearest') Nearest to Campus @break
                        @default {{ request('sort') }} @break
                    @endswitch
                    <a href="{{ request()->url() }}?{{ http_build_query(request()->except('sort')) }}" class="ml-2 text-orange-600 hover:text-orange-800">×</a>
                </span>
                @endif

                @if(request()->hasAny(['q', 'sort']))
                <a href="{{ request()->url() }}" class="text-red-600 hover:text-red-800 text-sm ml-2">Clear all filters</a>
                @endif
            </div>
            @endif
        </form>
    </div>

    <!-- Results Summary -->
    @if(isset($properties))
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    @if($properties->count() > 0)
                        Showing {{ $properties->count() }} of {{ $properties->total() }} properties
                        @if(request('q'))
                            matching your search criteria
                        @endif
                    @else
                        No properties found
                        @if(request('q'))
                            matching your search criteria
                        @endif
                    @endif
                </div>

                @if($properties->count() > 0 && request('q'))
                <div class="text-sm">
                    <a href="{{ route('properties.browse') }}" class="text-green-600 hover:text-green-800">
                        View all properties
                    </a>
                </div>
                @endif
            </div>

        </div>
    @endif

    <!-- SPLIT LAYOUT: Properties + Map -->
    @if(isset($properties) && $properties->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            
            <!-- LEFT SIDE: Property Listings (3/5 width) -->
            <div class="lg:col-span-3 space-y-4">
                @foreach($properties as $property)
                <div class="property-card bg-white rounded-lg shadow-md overflow-hidden cursor-pointer" 
                     onmouseover="highlightMarker({{ $property->id }})" 
                     onmouseout="unhighlightMarker({{ $property->id }})"
                     onclick="window.location.href='{{ route('properties.show', $property) }}'">
                    
                    <div class="flex flex-col md:flex-row">
                        <!-- Property Image -->
<div class="relative md:w-1/3 h-48 md:h-auto overflow-hidden">
    @php
        // Get cover image or first image
        $coverImage = $property->images->where('is_cover', true)->first() ?? $property->images->first();
        $imageUrl = $coverImage ? $coverImage->full_url : null;
    @endphp
    
    @if($imageUrl)
        <img 
            src="{{ $imageUrl }}" 
            alt="{{ $property->title }}"
            class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
            onload="this.style.opacity='1'" 
            onerror="this.onerror=null; this.src='https://via.placeholder.com/400x250/e5e7eb/9ca3af?text=No+Image'; this.alt='No image available';"
            style="opacity:0; transition: opacity 0.3s ease-in-out;"
        >
        
        <!-- Image count badge -->
        @if($property->images->count() > 1)
            <div class="absolute top-2 right-2">
                <span class="bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded-full flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ $property->images->count() }}
                </span>
            </div>
        @endif
    @else
        <!-- No Image Fallback -->
        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
            <div class="text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-sm">No Image</p>
            </div>
        </div>
    @endif
    
    <!-- Status Badges -->
    @if($property->is_featured ?? false)
        <span class="absolute top-2 left-2 bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-bold">
            ⭐ Featured
        </span>
    @endif
    
    @if($property->approval_status === 'approved')
        <span class="absolute top-2 {{ ($property->is_featured ?? false) ? 'left-20' : 'left-2' }} bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
            ✓ Verified
        </span>
    @endif
</div>

                        <!-- Property Details -->
                        <div class="md:w-2/3 p-4">
                            <!-- Title -->
                            <h3 class="text-xl font-bold text-gray-800 mb-2">
                                {{ $property->title }}
                            </h3>

                            <!-- Location -->
                            <div class="flex items-center text-gray-600 text-sm mb-2">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $property->address_line }}, {{ $property->city }}</span>
                            </div>

                            <!-- Property Details -->
                            @if($property->room_count || $property->bathroom_count)
                            <div class="flex items-center text-gray-600 text-sm mb-3 space-x-4">
                                @if($property->room_count)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21l0-12"></path>
                                    </svg>
                                    <span>{{ $property->room_count }} {{ $property->room_count == 1 ? 'Room' : 'Rooms' }}</span>
                                </div>
                                @endif

                                @if($property->bathroom_count)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11"></path>
                                    </svg>
                                    <span>{{ $property->bathroom_count }} {{ $property->bathroom_count == 1 ? 'Bath' : 'Baths' }}</span>
                                </div>
                                @endif
                            </div>
                            @endif

                            <!-- Price -->
                            <div class="mb-3">
                                <span class="text-xl sm:text-2xl font-bold text-green-600 break-words">
                                    ₱{{ number_format($property->price) }}
                                </span>
                                <span class="text-gray-500 text-sm">/month</span>
                            </div>

                            <!-- Amenities -->
                            @if($property->amenities && $property->amenities->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($property->amenities->take(3) as $amenity)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                    {{ $amenity->name }}
                                </span>
                                @endforeach
                                @if($property->amenities->count() > 3)
                                <span class="text-gray-500 text-xs self-center">
                                    +{{ $property->amenities->count() - 3 }} more
                                </span>
                                @endif
                            </div>
                            @endif

                            <!-- View Details Button -->
                            <a 
                                href="{{ route('properties.show', $property) }}" 
                                class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-200"
                                onclick="event.stopPropagation()"
                            >
                                View Details →
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                @if($properties->hasPages())
                <div class="mt-8 flex justify-center">
                    <div class="bg-white rounded-lg shadow-md p-4">
                        {{ $properties->links() }}
                    </div>
                </div>
                @endif
            </div>

            <!-- RIGHT SIDE: Map (2/5 width) -->
            <div class="lg:col-span-2">
                <div class="sticky top-4">
                    <div id="browseMap" class="rounded-lg shadow-lg"></div>
                    
                    <!-- Map Legend -->
                    <div class="bg-white rounded-lg shadow-md p-4 mt-4">
                        <h4 class="font-semibold mb-2">Map Legend</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                                <span>Available Properties</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                <span>PSU Campus</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Properties Found -->
        <div class="text-center py-16 bg-white rounded-lg shadow-md">
            <div class="max-w-md mx-auto">
                <svg class="w-20 h-20 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16zM9 10h6M9 14h6"></path>
                </svg>

                <h3 class="text-2xl font-bold text-gray-700 mb-4">
                    @if(request('q'))
                        No properties match your search criteria
                    @else
                        No properties available
                    @endif
                </h3>

                <div class="text-gray-600 mb-8 space-y-2">
                    @if(request('q'))
                        <p>We couldn't find any properties matching your filters:</p>
                        <div class="bg-gray-50 rounded-lg p-4 mt-4 text-left">
                            @if(request('q'))
                                <div class="mb-2"><strong>Search:</strong> "{{ request('q') }}"</div>
                            @endif
                            @if(request('price_range'))
                                <div class="mb-2"><strong>Price Range:</strong>
                                    @switch(request('price_range'))
                                        @case('0-5000') Below ₱5,000 @break
                                        @case('5000-10000') ₱5,000 - ₱10,000 @break
                                        @case('10000-15000') ₱10,000 - ₱15,000 @break
                                        @case('15000+') Above ₱15,000 @break
                                    @endswitch
                                </div>
                            @endif
                            @if(request('city'))
                                <div class="mb-2"><strong>Location:</strong> {{ request('city') }}</div>
                            @endif
                            @if(request('room_count'))
                                <div class="mb-2"><strong>Minimum Rooms:</strong> {{ request('room_count') }}+</div>
                            @endif
                        </div>
                        <p class="mt-4">Try adjusting your search criteria or browse all available properties.</p>
                    @else
                        <p>There are currently no properties available in our system.</p>
                        <p>Please check back later or contact us for more information.</p>
                    @endif
                </div>

                <div class="space-y-4">
                    @if(request('q'))
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('properties.browse') }}"
                               class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200">
                                View All Properties
                            </a>
                            <button onclick="clearSearchFilters()"
                                    class="inline-block bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition duration-200">
                                Adjust Search Filters
                            </button>
                        </div>
                    @else
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('home') }}"
                               class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200">
                                Back to Home
                            </a>
                            <a href="#"
                               class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                                Contact Us
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Search Suggestions -->
                @if(request()->hasAny(['q', 'price_range', 'city']))
                <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                    <h4 class="font-semibold text-blue-900 mb-3">Search suggestions:</h4>
                    <div class="text-blue-800 text-sm space-y-1">
                        <div>• Try a broader price range</div>
                        <div>• Check both Bacolor and San Fernando locations</div>
                        <div>• Search for specific amenities or features</div>
                        <div>• Remove some filters to see more results</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <script>
        function clearSearchFilters() {
            document.querySelector('input[name="q"]').value = '';
            document.querySelector('select[name="sort"]').value = 'newest';

            // Submit the form to clear filters
            document.getElementById('searchForm').submit();
        }
        </script>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
<script>
    let map, markers = {};
    
    function initMap() {
        try {
            // Check if Leaflet is loaded
            if (typeof L === 'undefined') {
                console.error('Leaflet library not loaded');
                const mapElement = document.getElementById('browseMap');
                if (mapElement) {
                    mapElement.innerHTML = '<div class="p-4 text-center text-red-600">Map library failed to load. Please refresh the page.</div>';
                }
                return;
            }

            // Initialize map centered on PSU Main Campus
            map = L.map('browseMap', {
                scrollWheelZoom: true,
                tap: true
            }).setView([14.997609479592196, 120.65313160859495], 13);

            // Add tile layer with error handling
            const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            });

            tileLayer.on('tileerror', function(error) {
                console.warn('Tile loading error:', error);
            });

            tileLayer.addTo(map);

            // Add PSU Main Campus marker (RED to distinguish from properties)
            L.marker([14.997609479592196, 120.65313160859495], {
                icon: L.icon({
                    iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(map).bindPopup('<strong style="color: #dc2626;">🏫 PSU Main Campus</strong><br><small style="color: #6b7280;">Universidad Pangasinan State</small>');

            // Add property markers (BLUE to distinguish from PSU Campus)
            @foreach($properties as $property)
                @if($property->latitude && $property->longitude)
                    markers[{{ $property->id }}] = L.marker([{{ $property->latitude }}, {{ $property->longitude }}], {
                        icon: L.icon({
                            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    }).addTo(map)
                    .bindPopup(`
                        <div class="p-2">
                            <b>{{ $property->title }}</b><br>
                            <span class="text-green-600 font-bold">₱{{ number_format($property->price) }}</span>/month<br>
                            <a href="{{ route('properties.show', $property) }}" class="text-blue-600 text-sm">View Details →</a>
                        </div>
                    `);
                @endif
            @endforeach

            // Invalidate size to ensure proper rendering
            setTimeout(() => {
                map.invalidateSize();
            }, 100);

            console.log('Browse map initialized successfully');

        } catch (error) {
            console.error('Map initialization error:', error);
            const mapElement = document.getElementById('browseMap');
            if (mapElement) {
                mapElement.innerHTML = '<div class="p-4 text-center text-red-600"><p class="font-semibold mb-2">Map failed to load</p><p class="text-sm">Error: ' + error.message + '</p></div>';
            }
        }
    }

    function highlightMarker(propertyId) {
        if (markers[propertyId]) {
            markers[propertyId].openPopup();
        }
    }

    function unhighlightMarker(propertyId) {
        if (markers[propertyId]) {
            markers[propertyId].closePopup();
        }
    }

    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Wait for Leaflet to load
        let attempts = 0;
        const maxAttempts = 10;

        const tryInitMap = setInterval(() => {
            attempts++;
            if (typeof L !== 'undefined') {
                clearInterval(tryInitMap);
                initMap();
            } else if (attempts >= maxAttempts) {
                clearInterval(tryInitMap);
                console.error('Leaflet failed to load after ' + maxAttempts + ' attempts');
                const mapElement = document.getElementById('browseMap');
                if (mapElement) {
                    mapElement.innerHTML = '<div class="p-4 text-center text-red-600"><p class="font-semibold mb-2">Map library failed to load</p><p class="text-sm">Please check your internet connection and refresh the page.</p></div>';
                }
            }
        }, 300);
    });
</script>
@endpush