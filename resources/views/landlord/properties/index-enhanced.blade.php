@extends('layouts.account')

@section('title', 'My Properties')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Properties</h1>
        <a href="{{ route('landlord.properties.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
            Add New Property
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            {{ session('warning') }}
        </div>
    @endif

    <!-- Search and Filter -->
    <form method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search Properties</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Search by title, description, or location..."
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status Filter</label>
                <select name="status" id="status"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit"
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md">
                    Search
                </button>
            </div>
        </div>
        @if(request()->anyFilled(['search', 'status']))
            <div class="mt-3">
                <a href="{{ route('landlord.properties.index') }}"
                   class="text-sm text-blue-600 hover:text-blue-800">Clear Filters</a>
            </div>
        @endif
    </form>

    @if($properties->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-2 0v-11a2 2 0 011-1h1m-1 1v11m0 0h2m-2 0h-4"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No properties found</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->anyFilled(['search', 'status']))
                    Try adjusting your search criteria.
                @else
                    Get started by creating a new property.
                @endif
            </p>
            <div class="mt-6">
                <a href="{{ route('landlord.properties.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    New Property
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($properties as $property)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <!-- Property Image -->
                    <div class="h-48 bg-gray-200 overflow-hidden">
                        @if(isset($property->images) && $property->images->isNotEmpty())
                            @php
                                $coverImage = $property->images->firstWhere('is_cover', true) ?: $property->images->first();
                            @endphp
                            <img src="{{ asset('storage/' . $coverImage->image_path) }}"
                                 alt="{{ $coverImage->alt_text }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Property Info -->
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">{{ $property->title }}</h3>
                            <!-- Status Badge -->
                            @if($property->approval_status === 'approved')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Approved
                                </span>
                            @elseif($property->approval_status === 'pending')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Rejected
                                </span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $property->location_text }}</p>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-lg font-bold text-blue-600">₱{{ number_format($property->price, 2) }}/month</span>
                            <span class="text-sm text-gray-500">{{ $property->room_count }} rooms</span>
                        </div>

                        <!-- Visit Scheduling Status -->
                        @if($property->visit_schedule_enabled)
                            <div class="mb-3 text-sm">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Visit Scheduling Enabled
                                </span>
                            </div>
                        @endif

                        <!-- Deletion Request Status -->
                        @if(isset($property->deletionRequest) && $property->deletionRequest)
                            <div class="mb-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-7.938 0h15.875c.621 0 1.125.504 1.125 1.125v12.75c0 .621-.504 1.125-1.125 1.125H4.062C3.441 24 2.937 23.496 2.937 22.875V10.125C2.937 9.504 3.441 9 4.062 9h15.875z"/>
                                    </svg>
                                    Deletion Requested
                                </span>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">
                                Created {{ \Carbon\Carbon::parse($property->created_at)->format('M d, Y') }}
                            </span>
                            <div class="flex space-x-2">
                                <a href="{{ route('properties.show', $property->slug) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View
                                </a>
                                @if($property->approval_status !== 'rejected')
                                    <a href="{{ route('landlord.properties.edit', $property) }}"
                                       class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        Edit
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($properties->hasPages())
            <div class="mt-6">
                {{ $properties->links() }}
            </div>
        @endif
    @endif
</div>
@endsection