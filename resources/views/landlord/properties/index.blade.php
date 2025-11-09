@extends('layouts.account')

@push('styles')
<style>
    .property-image {
        background: white;
        min-height: 192px;
    }

    .property-image img {
        background: white !important;
        display: block !important;
        opacity: 1 !important;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Properties</h1>
                <p class="text-gray-600">Manage your property listings</p>
            </div>
            <a href="{{ route('landlord.properties.create') }}" 
               class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Property
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </div>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-green-400 hover:text-green-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endif

        @if(session('error') || $errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    @if(session('error'))
                        <div class="text-sm font-medium text-red-800">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                        @foreach($errors->all() as $error)
                            <div class="text-sm font-medium text-red-800">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Properties</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Property name, location, or address..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Approval Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" onchange="this.form.submit()">
                        <option value="" {{ !request('status') ? 'selected' : '' }}>All Statuses</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium">
                        🔍 Search
                    </button>
                </div>

                <div class="flex items-end">
                    @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('landlord.properties.index') }}"
                       class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition text-center font-medium">
                        Clear Filters
                    </a>
                    @endif
                </div>
            </form>

            <!-- Active filter badges -->
            @if(request()->hasAny(['search', 'status']))
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="text-sm text-gray-600 mr-2">Active filters:</span>

                @if(request('search'))
                <span class="inline-flex items-center bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                    Search: "{{ request('search') }}"
                    <a href="{{ request()->url() }}?{{ http_build_query(request()->except('search')) }}" class="ml-2 text-green-600 hover:text-green-800">×</a>
                </span>
                @endif

                @if(request('status'))
                <span class="inline-flex items-center bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                    Status: {{ $statuses[request('status')] }}
                    <a href="{{ request()->url() }}?{{ http_build_query(request()->except('status')) }}" class="ml-2 text-blue-600 hover:text-blue-800">×</a>
                </span>
                @endif
            </div>
            @endif
        </div>

        <!-- Results Summary -->
        <div class="mb-6 flex justify-between items-center">
            <div class="text-sm text-gray-600">
                @if($properties->count() > 0)
                    Showing {{ $properties->count() }} of {{ $properties->total() }} properties
                    @if(request()->hasAny(['search', 'status']))
                        matching your filters
                    @endif
                @else
                    @if(request()->hasAny(['search', 'status']))
                        No properties found matching your filters
                    @else
                        No properties found
                    @endif
                @endif
            </div>

            @if($properties->count() > 0 && request()->hasAny(['search', 'status']))
            <div class="text-sm">
                <a href="{{ route('landlord.properties.index') }}" class="text-green-600 hover:text-green-800">
                    View all properties
                </a>
            </div>
            @endif
        </div>

        <!-- Properties Grid -->
        @if($properties->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($properties as $property)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden border hover:shadow-md transition-shadow">
                        <!-- Property Image -->
                        <div class="relative cursor-pointer group property-image" onclick="openImageGallery('{{ $property->id }}')">
                            @php
                                $coverImage = $property->images->where('is_cover', true)->first() ?? $property->images->first();
                                $imageUrl = $coverImage ? $coverImage->full_url : null;
                            @endphp

                            @if($imageUrl)
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $property->title }}"
                                     style="width: 100%; height: 192px; object-fit: cover; background-color: white; display: block; opacity: 1; visibility: visible; position: relative; z-index: 1;">
                                <!-- Error fallback -->
                                <div style="width: 100%; height: 192px; background-color: #f3f4f6; display: none; align-items: center; justify-content: center; color: #9ca3af;">
                                    <div style="text-align: center;">
                                        <svg style="width: 48px; height: 48px; margin: 0 auto 8px;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <p style="font-size: 14px; font-weight: 500;">Image not available</p>
                                    </div>
                                </div>
                            @else
                                <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                                    <div class="text-center">
                                        <svg class="w-12 h-12 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <p class="text-sm font-medium">No image</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Gallery Overlay -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center">
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <div class="bg-white bg-opacity-90 text-gray-800 px-3 py-2 rounded-full flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">View Gallery</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="absolute top-3 left-3 flex flex-col gap-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $property->approval_status === 'approved' ? 'bg-green-100 text-green-800' :
                                       ($property->approval_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($property->approval_status) }}
                                </span>
                                @if($property->deletionRequest)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Deletion Pending
                                    </span>
                                @endif
                            </div>

                            <!-- Image Count -->
                            @if($property->images->count() > 1)
                                <div class="absolute top-3 right-3">
                                    <span class="bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded-full flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $property->images->count() }}
                                    </span>
                                </div>
                            @else
                                <div class="absolute top-3 right-3">
                                    <span class="bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded-full flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        1
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Hidden Images Data for Gallery -->
                        <script type="application/json" id="property-images-{{ $property->id }}">
                            {!! json_encode($property->images->map(function($image) {
                                return [
                                    'url' => $image->full_url,
                                    'alt' => $image->description ?? 'Property image',
                                    'is_cover' => $image->is_cover
                                ];
                            })) !!}
                        </script>

                        <!-- Property Info -->
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $property->title }}</h3>
                                @if($property->is_featured)
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">Featured</span>
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

                            <!-- Property Stats -->
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    <span>{{ $property->room_count }} rooms</span>
                                </div>
                                
                                @if($property->rating_count > 0)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                        </svg>
                                        <span class="ml-1">{{ number_format($property->rating_avg, 1) }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Created Date -->
                            <div class="text-xs text-gray-500 mb-4">
                                Created {{ $property->created_at->diffForHumans() }}
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="{{ route('properties.show', $property->slug) }}"
                                   class="flex-1 bg-blue-600 text-white text-center px-3 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                    View
                                </a>

                                <a href="{{ route('landlord.properties.edit', $property) }}"
                                   class="flex-1 bg-gray-600 text-white text-center px-3 py-2 rounded-lg hover:bg-gray-700 transition text-sm font-medium">
                                    Edit
                                </a>

                                @if($property->deletionRequest)
                                    <button disabled
                                            class="bg-gray-400 text-white px-3 py-2 rounded-lg cursor-not-allowed text-sm font-medium opacity-60"
                                            title="Deletion request pending admin review">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </button>
                                @else
                                    <button type="button"
                                            onclick="event.preventDefault(); showDeleteModal({{ $property->id }}, '{{ addslashes($property->title) }}')"
                                            class="bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $properties->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>

                @if(request()->hasAny(['search', 'status']))
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No properties match your filters</h3>
                    <div class="text-gray-500 mb-6">
                        <p class="mb-2">We couldn't find any properties matching:</p>
                        <div class="bg-gray-50 rounded-lg p-4 mb-4 text-left max-w-md mx-auto">
                            @if(request('search'))
                                <div class="mb-2"><strong>Search:</strong> "{{ request('search') }}"</div>
                            @endif
                            @if(request('status'))
                                <div class="mb-2"><strong>Status:</strong> {{ $statuses[request('status')] }}</div>
                            @endif
                        </div>
                        <p>Try adjusting your search criteria or viewing all properties.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('landlord.properties.index') }}"
                           class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-medium">
                            View All Properties
                        </a>
                        <a href="{{ route('landlord.properties.create') }}"
                           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                            Add New Property
                        </a>
                    </div>
                @else
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No properties found</h3>
                    <p class="text-gray-500 mb-6">You haven't created any properties yet. Start by adding your first property listing.</p>
                    <a href="{{ route('landlord.properties.create') }}"
                       class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-medium">
                        Add Your First Property
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Professional Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-2 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-gray-900">Request Property Deletion</h3>
                    <p class="text-sm text-gray-600">This action requires admin approval</p>
                </div>
            </div>
        </div>

        <form id="deleteForm" method="POST" action="{{ route('landlord.properties.request-deletion') }}">
            @csrf
            <input type="hidden" id="propertyId" name="property_id">

            <div class="p-6">
                <div class="mb-4">
                    <p class="text-gray-800 mb-2">
                        You are requesting to delete: <span id="propertyTitle" class="font-semibold"></span>
                    </p>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Important:</strong> This request will be sent to admin for approval. Your property will remain active until admin approves the deletion.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="deleteReason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for deletion <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" id="deleteReason" rows="3" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-y"
                              placeholder="Please explain why you want to delete this property...&#10;&#10;Example: Property sold, no longer available, listing error, etc."></textarea>
                    <p class="text-xs text-gray-500 mt-1">This information helps admin process your request faster.</p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">What happens next?</h4>
                            <div class="mt-2 text-sm text-blue-700">
                                <ol class="list-decimal list-inside space-y-1">
                                    <li>Your deletion request is sent to admin</li>
                                    <li>Admin reviews your request and reason</li>
                                    <li>You'll receive notification of the decision</li>
                                    <li>If approved, property will be permanently deleted</li>
                                </ol>
                            </div>
                            <div class="mt-2">
                                <button type="button" onclick="showContactAdmin()" class="text-blue-600 hover:text-blue-800 text-sm font-medium underline">
                                    📩 Need to contact admin directly?
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex gap-3">
                <button type="submit"
                        class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition font-medium">
                    Submit Deletion Request
                </button>
                <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition font-medium">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Contact Admin Modal -->
<div id="contactAdminModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="p-2 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-gray-900">Contact Admin</h3>
                    <p class="text-sm text-gray-600">Send a message to admin</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('landlord.contact-admin') }}">
            @csrf
            <input type="hidden" name="regarding_property_id" id="contactPropertyId">
            <input type="hidden" name="subject" value="Property Deletion Inquiry">

            <div class="p-6">
                <div class="mb-4">
                    <label for="adminMessage" class="block text-sm font-medium text-gray-700 mb-2">
                        Your Message <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message" id="adminMessage" rows="4" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y"
                              placeholder="Hi Admin,&#10;&#10;I need assistance with my property deletion request...&#10;&#10;Please help me with..."></textarea>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Admin will respond within 24-48 hours
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex gap-3">
                <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition font-medium">
                    Send Message
                </button>
                <button type="button" onclick="closeContactAdminModal()"
                        class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition font-medium">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showDeleteModal(propertyId, propertyTitle) {
    document.getElementById('propertyId').value = propertyId;
    document.getElementById('propertyTitle').textContent = propertyTitle;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteReason').value = '';
    document.body.style.overflow = '';
}

function showContactAdmin() {
    const propertyId = document.getElementById('propertyId').value;
    document.getElementById('contactPropertyId').value = propertyId;
    closeDeleteModal();
    document.getElementById('contactAdminModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeContactAdminModal() {
    document.getElementById('contactAdminModal').classList.add('hidden');
    document.getElementById('adminMessage').value = '';
    document.body.style.overflow = '';
}

// Close modals when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

document.getElementById('contactAdminModal').addEventListener('click', function(e) {
    if (e.target === this) closeContactAdminModal();
});

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeContactAdminModal();
    }
});

// Professional Image Gallery System
let currentPropertyImages = [];
let currentImageIndex = 0;

function openImageGallery(propertyId) {

    const imagesScript = document.getElementById(`property-images-${propertyId}`);
    if (!imagesScript) {
        console.error('Images script not found for property:', propertyId);
        return;
    }

    try {
        currentPropertyImages = JSON.parse(imagesScript.textContent);

        if (currentPropertyImages.length === 0) {
            console.error('No images found for property:', propertyId);
            return;
        }

        // Use original URLs without cleaning - let's see what we get
        currentPropertyImages.forEach((img, index) => {
        });

        // Start with cover image if available
        const coverIndex = currentPropertyImages.findIndex(img => img.is_cover);
        currentImageIndex = coverIndex >= 0 ? coverIndex : 0;


        showImageGallery();
    } catch (error) {
        console.error('Failed to load images:', error);
        console.error('Images script content:', imagesScript.textContent);
    }
}

function showImageGallery() {
    const gallery = document.getElementById('imageGallery');
    if (!gallery) {
        console.error('Gallery element not found!');
        return;
    }

    gallery.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Initialize the gallery with proper sequencing
    setTimeout(() => {
        updateImageTitle(); // Update title first
        updateGalleryImage(); // Then load image
    }, 100); // Increased delay to ensure DOM is ready
}

function closeImageGallery() {
    document.getElementById('imageGallery').classList.add('hidden');
    document.body.style.overflow = '';
}

function nextImage() {
    // No navigation needed for single cover image
}

function previousImage() {
    // No navigation needed for single cover image
}

function updateGalleryImage() {
    if (!currentPropertyImages || currentPropertyImages.length === 0) {
        console.error('No images available');
        return;
    }

    const currentImage = currentPropertyImages[currentImageIndex];
    const imgElement = document.getElementById('galleryMainImage');
    const loadingIndicator = document.getElementById('imageLoading');

    if (!currentImage) {
        console.error('Current image is undefined', { currentImageIndex, totalImages: currentPropertyImages.length });
        return;
    }


    // Show loading indicator
    if (loadingIndicator) {
        loadingIndicator.style.display = 'flex';
    }


    // Test the URL directly first
    fetch(currentImage.url)
        .then(response => {
            if (response.ok) {
            } else {
                console.error('URL not accessible via fetch');
            }
        })
        .catch(error => {
            console.error('Fetch failed:', error);
        });

    // Simple direct loading like in public galleries
    imgElement.src = currentImage.url;
    imgElement.alt = currentImage.alt || 'Property image';

    // Add loading indicator
    imgElement.onload = function() {
        this.style.opacity = '1';
        if (loadingIndicator) {
            loadingIndicator.style.display = 'none';
        }
    };

    imgElement.onerror = function() {
        console.error('❌ FAILED: Could not load gallery image');
        console.error('Failed URL:', currentImage.url);
        console.error('Image element src:', this.src);
        this.style.background = '#f3f4f6';
        this.style.color = '#374151';
        this.style.display = 'flex';
        this.style.alignItems = 'center';
        this.style.justifyContent = 'center';
        this.style.opacity = '1';
        this.innerHTML = '<div style="text-align: center; padding: 20px;"><div style="font-size: 24px; margin-bottom: 10px;">📷</div><div>Image not found</div><div style="font-size: 12px; margin-top: 10px;">Check console for URL</div></div>';
        if (loadingIndicator) {
            loadingIndicator.style.display = 'none';
        }
    };
}

function updateImageTitle() {
    // Set the title to "Cover Image" and ensure it's centered
    const titleElement = document.getElementById('imageTitle');
    if (titleElement) {
        titleElement.textContent = 'Cover Image';
            left: titleElement.style.left,
            transform: titleElement.style.transform,
            position: getComputedStyle(titleElement).position
        });

        // Force center positioning
        titleElement.style.left = '50%';
        titleElement.style.transform = 'translateX(-50%)';
    } else {
        console.error('Image title element not found!');
        // Check if there are any elements that might be conflicting
        const allElements = document.querySelectorAll('[id*="image"], [id*="counter"], [id*="title"]');
    }
}

function goToImage(index) {
    // No navigation needed for single cover image
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (!document.getElementById('imageGallery').classList.contains('hidden')) {
        switch(e.key) {
            case 'Escape':
                closeImageGallery();
                break;
            case 'ArrowLeft':
                previousImage();
                break;
            case 'ArrowRight':
                nextImage();
                break;
        }
    }
});
</script>

<!-- Professional Image Gallery Modal -->
<div id="imageGallery" class="fixed inset-0 z-50 hidden" style="background: rgba(0, 0, 0, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
    <!-- Close Button - Fixed to viewport -->
    <button onclick="closeImageGallery()"
            class="fixed top-4 right-4 z-50 bg-black bg-opacity-70 hover:bg-opacity-90 text-white p-3 rounded-full transition-all border-2 border-white border-opacity-50 shadow-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <!-- Image Title - Fixed to center top -->
    <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-black bg-opacity-70 text-white px-6 py-2 rounded-full text-lg font-medium border-2 border-white border-opacity-50 shadow-lg">
        <span id="imageTitle">Cover Image</span>
    </div>


    <!-- Main Image Container -->
    <div class="flex items-center justify-center w-full h-full p-16">
        <img id="galleryMainImage"
             src=""
             alt=""
             class="max-w-full max-h-full object-contain rounded-lg shadow-2xl"
             style="background: transparent; opacity: 1;">
    </div>


    <!-- Loading Indicator -->
    <div id="imageLoading" class="fixed inset-0 flex items-center justify-center z-40" style="display: none;">
        <div class="bg-black bg-opacity-70 text-white p-6 rounded-lg flex items-center border-2 border-white border-opacity-50 shadow-lg">
            <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-lg">Loading image...</span>
        </div>
    </div>
</div>

<script>
// Auto-dismiss success messages after 8 seconds with smooth fade out
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.querySelector('.bg-green-50');
    if (successAlert) {
        // Add smooth fade-in animation
        successAlert.style.opacity = '0';
        successAlert.style.transition = 'opacity 0.3s ease-in-out';
        setTimeout(() => {
            successAlert.style.opacity = '1';
        }, 100);

        // Auto-dismiss after 8 seconds
        setTimeout(() => {
            successAlert.style.opacity = '0';
            setTimeout(() => {
                if (successAlert && successAlert.parentNode) {
                    successAlert.parentNode.removeChild(successAlert);
                }
            }, 300);
        }, 8000);
    }

    const errorAlert = document.querySelector('.bg-red-50');
    if (errorAlert) {
        // Add smooth fade-in animation for errors
        errorAlert.style.opacity = '0';
        errorAlert.style.transition = 'opacity 0.3s ease-in-out';
        setTimeout(() => {
            errorAlert.style.opacity = '1';
        }, 100);

        // Auto-dismiss errors after 10 seconds
        setTimeout(() => {
            errorAlert.style.opacity = '0';
            setTimeout(() => {
                if (errorAlert && errorAlert.parentNode) {
                    errorAlert.parentNode.removeChild(errorAlert);
                }
            }, 300);
        }, 10000);
    }
});
</script>

@endsection