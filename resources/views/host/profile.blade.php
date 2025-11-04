@extends('layouts.guest')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Host Profile Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Profile Picture -->
                <div class="flex-shrink-0">
                    @if($host->profile_picture)
                        @php
                            // Check if it's a Cloudinary URL or local path
                            $profileImageUrl = str_starts_with($host->profile_picture, 'http')
                                ? $host->profile_picture
                                : asset('storage/' . $host->profile_picture);
                        @endphp
                        <img src="{{ $profileImageUrl }}" alt="{{ $host->name }}" class="w-24 h-24 md:w-32 md:h-32 rounded-full object-cover border-4 border-green-500">
                    @else
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-green-600 flex items-center justify-center text-white text-4xl md:text-5xl font-bold border-4 border-green-500">
                            {{ strtoupper(substr($host->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <!-- Host Info -->
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $host->name }}</h1>
                            <div class="flex items-center gap-2 mb-3">
                                @if($isActive)
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                        Active now
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 text-sm font-medium px-3 py-1 rounded-full">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        Last active {{ $host->last_active_at ? $host->last_active_at->diffForHumans() : 'N/A' }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-600 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                                </svg>
                                Joined {{ $host->created_at->format('F Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Host Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center justify-center mb-2">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $verifiedListings }}</div>
                    <div class="text-xs text-gray-600">Verified Listings</div>
                </div>

                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center justify-center mb-2">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalInquiries }}</div>
                    <div class="text-xs text-gray-600">Total Inquiries</div>
                </div>

                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="flex items-center justify-center mb-2">
                        <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalFavorites }}</div>
                    <div class="text-xs text-gray-600">User Favorites</div>
                </div>

                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="flex items-center justify-center mb-2">
                        <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $approvedVisits }}</div>
                    <div class="text-xs text-gray-600">Approved Visits</div>
                </div>
            </div>
        </div>

        <!-- Properties Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                </svg>
                {{ $host->name }}'s Properties ({{ $properties->count() }})
            </h2>

            @if($properties->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($properties as $property)
                        <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300 cursor-pointer" onclick="window.location.href='{{ route('properties.show', $property) }}'">
                            <!-- Property Image -->
                            <div class="relative h-48 bg-gray-200">
                                @php
                                    $coverImage = $property->images->where('is_cover', true)->first() ?? $property->images->first();
                                    $imageUrl = $coverImage ? $coverImage->full_url : null;
                                @endphp

                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Verification Badge -->
                                @if($property->approval_status === 'approved')
                                    <div class="absolute top-2 left-2 bg-green-600 text-white px-3 py-1 rounded-lg text-xs font-bold shadow-lg flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Verified
                                    </div>
                                @endif
                            </div>

                            <!-- Property Details -->
                            <div class="p-4">
                                <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-1">{{ $property->title }}</h3>

                                <!-- Location -->
                                <div class="flex items-center text-gray-600 text-sm mb-2">
                                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="line-clamp-1">{{ $property->address_line }}, {{ $property->city }}</span>
                                </div>

                                <!-- Price -->
                                <div class="flex items-center justify-between">
                                    <div class="text-green-600 font-bold text-xl">
                                        ₱{{ number_format($property->price, 2) }}
                                        <span class="text-sm text-gray-500">/month</span>
                                    </div>

                                    <span class="text-xs px-2 py-1 rounded-full {{ $property->approval_status === 'approved' ? 'bg-green-100 text-green-800' : ($property->approval_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($property->approval_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">No properties listed yet</p>
                </div>
            @endif
        </div>

        <!-- Back Button -->
        <div class="mt-6 text-center">
            <button onclick="window.history.back()" class="inline-flex items-center text-green-600 hover:text-green-700 font-semibold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </button>
        </div>
    </div>
</div>
@endsection
