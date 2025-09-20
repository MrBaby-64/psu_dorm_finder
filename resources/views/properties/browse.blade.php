@extends('layouts.app')

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
                        name="search" 
                        placeholder="Search properties..." 
                        value="{{ request('search') }}"
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
                        <option value="0-5000">Below ₱5,000</option>
                        <option value="5000-10000">₱5,000 - ₱10,000</option>
                        <option value="10000-15000">₱10,000 - ₱15,000</option>
                        <option value="15000+">Above ₱15,000</option>
                    </select>
                </div>

                <!-- Location Filter -->
                <div>
                    <select 
                        name="city" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                        <option value="">All Locations</option>
                        <option value="Lingayen">Lingayen</option>
                        <option value="Dagupan">Dagupan</option>
                        <option value="Urdaneta">Urdaneta</option>
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

    <!-- Properties Grid -->
    @if(isset($properties) && $properties->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($properties as $property)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                <!-- Property Image -->
                <div class="relative h-48 overflow-hidden">
                    @php
                        $coverImage = $property->images->where('is_cover', true)->first();
                    @endphp
                    
                    @if($coverImage)
                        <img 
                            src="{{ asset('storage/' . $coverImage->image_path) }}" 
                            alt="{{ $property->title }}"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">No Image</span>
                        </div>
                    @endif
                    
                    <!-- Status Badge -->
                    @if($property->approval_status === 'approved')
                        <span class="absolute top-2 right-2 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                            Verified
                        </span>
                    @endif
                </div>

                <!-- Property Details -->
                <div class="p-4">
                    <!-- Title -->
                    <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-1">
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
                    <div class="flex flex-wrap gap-2 mb-4">
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

                    <!-- View Details Button - FIXED THIS LINE -->
                    <a 
                        href="{{ route('properties.show', $property) }}" 
                        class="block w-full text-center bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition duration-200"
                    >
                        View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $properties->links() }}
        </div>
    @else
        <!-- No Properties -->
        <div class="text-center py-12">
            <h3 class="text-gray-600">No properties found</h3>
        </div>
    @endif
</div>
@endsection