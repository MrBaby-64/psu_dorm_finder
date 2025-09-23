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
        <h1 class="text-3xl font-bold text-gray-800">Browse Dormitories</h1>
        <p class="text-gray-600 mt-2">Find your perfect student accommodation near PSU</p>
    </div>

    <!-- Search and Filters Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form action="{{ route('properties.browse') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

                <!-- Price Filter -->
                <div>
                    <select 
                        name="price_range" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                        <option value="">All Prices</option>
                        <option value="0-5000" {{ request('price_range') == '0-5000' ? 'selected' : '' }}>Below ₱5,000</option>
                        <option value="5000-10000" {{ request('price_range') == '5000-10000' ? 'selected' : '' }}>₱5,000 - ₱10,000</option>
                        <option value="10000-15000" {{ request('price_range') == '10000-15000' ? 'selected' : '' }}>₱10,000 - ₱15,000</option>
                        <option value="15000+" {{ request('price_range') == '15000+' ? 'selected' : '' }}>Above ₱15,000</option>
                    </select>
                </div>

                <!-- Location Filter -->
                <div>
                    <select 
                        name="city" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                        <option value="">All Locations</option>
                        <option value="Bacolor" {{ request('city') == 'Bacolor' ? 'selected' : '' }}>Bacolor</option>
                        <option value="San Fernando" {{ request('city') == 'San Fernando' ? 'selected' : '' }}>San Fernando</option>
                    </select>
                </div>

                <!-- Search Button -->
                <div>
                    <button 
                        type="submit" 
                        class="w-full bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-200"
                    >
                        🔍 Search
                    </button>
                </div>
            </div>
        </form>
    </div>

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
        $imageUrl = $coverImage ? asset('storage/' . $coverImage->image_path) : null;
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
                            <div class="flex items-center text-gray-600 text-sm mb-3">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $property->address_line }}, {{ $property->city }}</span>
                            </div>

                            <!-- Price -->
                            <div class="mb-3">
                                <span class="text-2xl font-bold text-green-600">
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
                <div class="mt-6">
                    {{ $properties->links() }}
                </div>
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
        <!-- No Properties -->
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No properties found</h3>
            <p class="text-gray-500">Try adjusting your search filters</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
<script>
    let map, markers = {};
    
    function initMap() {
        // Initialize map centered on PSU area
        map = L.map('browseMap').setView([15.1388, 120.5897], 13);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add PSU Campus marker
        L.marker([15.1388, 120.5897], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            })
        }).addTo(map).bindPopup('<b>PSU Campus</b>');
        
        // Add property markers
        @foreach($properties as $property)
            @if($property->latitude && $property->longitude)
                markers[{{ $property->id }}] = L.marker([{{ $property->latitude }}, {{ $property->longitude }}], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41]
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
    document.addEventListener('DOMContentLoaded', initMap);
</script>
@endpush