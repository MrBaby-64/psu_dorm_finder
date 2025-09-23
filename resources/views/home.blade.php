@extends('layouts.guest')

@section('title', 'Find Your Perfect Student Housing')

@section('content')
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-green-500 to-green-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    Find Rooms for Rent with PSU Dorm Finder
                </h1>
                <p class="text-xl mb-8 text-green-100">
                    Discover verified dormitories and apartments near PSU Bacolor & San Fernando
                </p>
                
                {{-- Search Form --}}
                <form action="{{ route('properties.browse') }}" method="GET" class="max-w-2xl mx-auto">
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            name="q" 
                            placeholder="Search by location, property name..."
                            class="flex-1 px-6 py-4 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-400 text-lg"
                        >
                        <button type="submit" class="bg-white text-green-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition">
                            Search
                        </button>
                    </div>
                </form>

                {{-- Popular Amenities --}}
                <div class="mt-8">
                    <p class="text-sm text-green-100 mb-3">Popular Amenities:</p>
                    <div class="flex gap-3 overflow-x-auto pb-2 justify-center">
                        <a href="{{ route('properties.browse', ['amenity' => 'wifi']) }}" class="flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full hover:bg-white/30 transition whitespace-nowrap">
                            <span>📶</span> WiFi
                        </a>
                        <a href="{{ route('properties.browse', ['amenity' => 'kitchen']) }}" class="flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full hover:bg-white/30 transition whitespace-nowrap">
                            <span>🍳</span> Kitchen
                        </a>
                        <a href="{{ route('properties.browse', ['amenity' => 'parking']) }}" class="flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full hover:bg-white/30 transition whitespace-nowrap">
                            <span>🅿️</span> Parking
                        </a>
                        <a href="{{ route('properties.browse', ['amenity' => 'laundry']) }}" class="flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full hover:bg-white/30 transition whitespace-nowrap">
                            <span>🧺</span> Laundry
                        </a>
                        <a href="{{ route('properties.browse', ['amenity' => 'ac']) }}" class="flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full hover:bg-white/30 transition whitespace-nowrap">
                            <span>❄️</span> Air Con
                        </a>
                        <a href="{{ route('properties.browse', ['amenity' => 'security']) }}" class="flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full hover:bg-white/30 transition whitespace-nowrap">
                            <span>🔒</span> Security
                        </a>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="mt-6 flex justify-center gap-4 text-sm flex-wrap">
                    <a href="{{ route('properties.browse', ['city' => 'Bacolor']) }}" class="hover:underline">
                        📍 Near Bacolor Campus
                    </a>
                    <a href="{{ route('properties.browse', ['city' => 'San Fernando']) }}" class="hover:underline">
                        📍 Near San Fernando Campus
                    </a>
                    <a href="{{ route('properties.browse', ['is_verified' => 1]) }}" class="hover:underline">
                        ✓ PSU Verified Properties
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Rest of homepage content from Phase 2... --}}
    {{-- (Include Featured Properties, Info Section, CTA Section from the Phase 2 code I gave you earlier) --}}
@endsection