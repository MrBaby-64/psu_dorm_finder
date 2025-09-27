@extends('layouts.account')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Booking & Inquiry History</h1>
            <p class="text-gray-600">Track all your property interactions, bookings, and communications in one place</p>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        <!-- Statistics Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Inquiries</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_inquiries'] }}</p>
                        @if($stats['pending_inquiries'] > 0)
                            <p class="text-xs text-blue-600">{{ $stats['pending_inquiries'] }} pending</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_bookings'] }}</p>
                        @if($stats['active_bookings'] > 0)
                            <p class="text-xs text-green-600">{{ $stats['active_bookings'] }} active</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Scheduled Visits</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['scheduled_visits'] }}</p>
                        @if($stats['pending_visits'] > 0)
                            <p class="text-xs text-purple-600">{{ $stats['pending_visits'] }} pending</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Success Rate</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            @if($stats['total_inquiries'] > 0)
                                {{ round(($stats['approved_inquiries'] / $stats['total_inquiries']) * 100) }}%
                            @else
                                0%
                            @endif
                        </p>
                        <p class="text-xs text-yellow-600">Inquiry approval rate</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button onclick="showTab('all')" id="tab-all" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-200">
                        All Activity
                    </button>
                    <button onclick="showTab('bookings')" id="tab-bookings" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-200">
                        Bookings ({{ $stats['total_bookings'] }})
                    </button>
                    <button onclick="showTab('inquiries')" id="tab-inquiries" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-200">
                        Inquiries ({{ $stats['total_inquiries'] }})
                    </button>
                    <button onclick="showTab('visits')" id="tab-visits" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-200">
                        Visits ({{ $stats['scheduled_visits'] }})
                    </button>
                </nav>
            </div>
        </div>

        <!-- Content Sections -->

        <!-- All Activity Timeline -->
        <div id="content-all" class="tab-content">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Activity Timeline</h2>

                @php
                    $allActivities = collect();

                    // Add bookings to timeline
                    foreach($bookings as $booking) {
                        $allActivities->push([
                            'type' => 'booking',
                            'date' => $booking->created_at,
                            'data' => $booking,
                            'title' => 'Booking Request',
                            'subtitle' => $booking->property->title,
                            'status' => $booking->status,
                            'icon' => 'booking'
                        ]);
                    }

                    // Add inquiries to timeline
                    foreach($inquiries as $inquiry) {
                        $allActivities->push([
                            'type' => 'inquiry',
                            'date' => $inquiry->created_at,
                            'data' => $inquiry,
                            'title' => 'Property Inquiry',
                            'subtitle' => $inquiry->property->title,
                            'status' => $inquiry->status,
                            'icon' => 'inquiry'
                        ]);
                    }

                    // Add visits to timeline
                    foreach($scheduledVisits as $visit) {
                        $allActivities->push([
                            'type' => 'visit',
                            'date' => $visit->created_at,
                            'data' => $visit,
                            'title' => 'Scheduled Visit',
                            'subtitle' => $visit->property->title,
                            'status' => $visit->status,
                            'icon' => 'visit'
                        ]);
                    }

                    // Sort by date (newest first)
                    $allActivities = $allActivities->sortByDesc('date');
                @endphp

                @if($allActivities->count() > 0)
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($allActivities as $index => $activity)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div class="flex-shrink-0">
                                            @if($activity['icon'] === 'booking')
                                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                            @elseif($activity['icon'] === 'inquiry')
                                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-900 font-medium">{{ $activity['title'] }}</p>
                                                <p class="text-sm text-gray-500">{{ $activity['subtitle'] }}</p>
                                                @if($activity['type'] === 'inquiry' && $activity['data']->room)
                                                    <p class="text-xs text-gray-400">Room: {{ $activity['data']->room->room_number }}</p>
                                                @elseif($activity['type'] === 'booking' && $activity['data']->room)
                                                    <p class="text-xs text-gray-400">Room: {{ $activity['data']->room->room_number }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap">
                                                <div class="mb-1">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $activity['data']->status_color }}">
                                                        {{ $activity['data']->status_name }}
                                                    </span>
                                                </div>
                                                <time class="text-gray-500 text-xs">{{ $activity['date']->format('M d, Y') }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No activity yet</h3>
                        <p class="text-gray-500 mb-6">Start browsing properties to create your first inquiry or booking!</p>
                        <a href="{{ route('properties.browse') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 inline-block transition">
                            Browse Properties
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bookings Section -->
        <div id="content-bookings" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">My Bookings</h2>
                    <div class="text-sm text-gray-500">
                        {{ $bookings->count() }} total bookings
                    </div>
                </div>

                @if($bookings->count() > 0)
                    <div class="space-y-6">
                        @foreach($bookings as $booking)
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $booking->property->title }}</h3>
                                            <p class="text-gray-600 mt-1">{{ $booking->property->location_text }}</p>
                                            <p class="text-sm text-gray-500">Landlord: {{ $booking->property->user->name }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $booking->status_color }}">
                                            {{ $booking->status_name }}
                                        </span>
                                    </div>

                                    @if($booking->room)
                                        <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                            <p class="text-sm font-medium text-gray-700">Room Details:</p>
                                            <p class="text-sm text-gray-600">{{ $booking->room->room_number }} - Capacity: {{ $booking->room->capacity }} person(s)</p>
                                        </div>
                                    @endif

                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="font-medium">Check-in:</span>
                                            <span class="ml-1">{{ $booking->check_in_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="font-medium">Check-out:</span>
                                            <span class="ml-1">{{ $booking->check_out_date->format('M d, Y') }}</span>
                                        </div>
                                    </div>

                                    @if($booking->total_amount)
                                        <div class="mt-3 p-3 bg-green-50 rounded-lg">
                                            <p class="text-sm font-medium text-green-800">Total Amount: {{ $booking->formatted_total_amount }}</p>
                                            @if($booking->payment_status)
                                                <p class="text-xs text-green-600">Payment Status: {{ $booking->payment_status_name }}</p>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="mt-4 flex items-center justify-between">
                                        <p class="text-xs text-gray-500">
                                            Submitted on {{ $booking->created_at->format('M d, Y \a\t g:i A') }}
                                        </p>

                                        <div class="flex space-x-2">
                                            @if($booking->canBeCancelled())
                                                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')"
                                                            class="text-red-600 hover:text-red-800 text-sm font-medium transition">
                                                        Cancel Booking
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('properties.show', $booking->property) }}" class="text-green-600 hover:text-green-800 text-sm font-medium transition">
                                                View Property
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings yet</h3>
                        <p class="text-gray-500 mb-6">Browse properties and create your first booking!</p>
                        <a href="{{ route('properties.browse') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 inline-block transition">
                            Browse Properties
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Inquiries Section -->
        <div id="content-inquiries" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">My Inquiries</h2>
                    <div class="text-sm text-gray-500">
                        {{ $inquiries->count() }} total inquiries
                    </div>
                </div>

                @if($inquiries->count() > 0)
                    <div class="space-y-6">
                        @foreach($inquiries as $inquiry)
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $inquiry->property->title }}</h3>
                                    <p class="text-gray-600 mt-1">{{ $inquiry->property->location_text }}</p>
                                    <p class="text-sm text-gray-500">Landlord: {{ $inquiry->property->user->name }}</p>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $inquiry->status_color }}">
                                    {{ $inquiry->status_name }}
                                </span>
                            </div>

                            @if($inquiry->room)
                                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm font-medium text-gray-700">Room Details:</p>
                                    <p class="text-sm text-gray-600">{{ $inquiry->room->room_number }} - Capacity: {{ $inquiry->room->capacity }} person(s)</p>
                                </div>
                            @endif

                            @if($inquiry->move_in_date || $inquiry->move_out_date)
                                <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @if($inquiry->move_in_date)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="font-medium">Move-in:</span>
                                            <span class="ml-1">{{ $inquiry->move_in_date->format('M d, Y') }}</span>
                                        </div>
                                    @endif
                                    @if($inquiry->move_out_date)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="font-medium">Move-out:</span>
                                            <span class="ml-1">{{ $inquiry->move_out_date->format('M d, Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if($inquiry->message)
                                <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                                    <p class="text-sm font-medium text-blue-800 mb-1">Your Message:</p>
                                    <p class="text-sm text-blue-700">{{ $inquiry->message }}</p>
                                </div>
                            @endif

                            @if($inquiry->landlord_reply)
                                <div class="mb-4 p-3 bg-green-50 rounded-lg">
                                    <p class="text-sm font-medium text-green-800 mb-1">Landlord Reply:</p>
                                    <p class="text-sm text-green-700">{{ $inquiry->landlord_reply }}</p>
                                    <p class="text-xs text-green-600 mt-1">Replied on {{ $inquiry->replied_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            @endif

                            @if($inquiry->messages && $inquiry->messages->count() > 0)
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Messages ({{ $inquiry->messages->count() }}):</p>
                                    <div class="space-y-2 max-h-32 overflow-y-auto">
                                        @foreach($inquiry->messages->take(3) as $message)
                                            <div class="text-xs p-2 {{ $message->user_id === auth()->id() ? 'bg-blue-50 text-blue-700' : 'bg-gray-50 text-gray-700' }} rounded">
                                                <span class="font-medium">{{ $message->user_id === auth()->id() ? 'You' : $inquiry->property->user->name }}:</span>
                                                {{ $message->content }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center justify-between">
                                <p class="text-xs text-gray-500">
                                    Submitted on {{ $inquiry->created_at->format('M d, Y \a\t g:i A') }}
                                </p>

                                <div class="flex space-x-2">
                                    <a href="{{ route('properties.show', $inquiry->property) }}" class="text-green-600 hover:text-green-800 text-sm font-medium transition">
                                        View Property
                                    </a>
                                    @if($inquiry->messages && $inquiry->messages->count() > 0)
                                        <a href="{{ route('messages.index', ['inquiry_id' => $inquiry->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition">
                                            View Messages
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No inquiries yet</h3>
                        <p class="text-gray-500 mb-6">Start browsing properties and send your first inquiry!</p>
                        <a href="{{ route('properties.browse') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 inline-block transition">
                            Browse Properties
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Visits Section -->
        <div id="content-visits" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Scheduled Visits</h2>
                    <div class="text-sm text-gray-500">
                        {{ $scheduledVisits->count() }} total visits
                    </div>
                </div>

                @if($scheduledVisits->count() > 0)
                    <div class="space-y-6">
                        @foreach($scheduledVisits as $visit)
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $visit->property->title }}</h3>
                                    <p class="text-gray-600 mt-1">{{ $visit->property->location_text }}</p>
                                    <p class="text-sm text-gray-500">Landlord: {{ $visit->property->user->name }}</p>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $visit->status_color }}">
                                    {{ $visit->status_name }}
                                </span>
                            </div>

                            <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="font-medium">Visit Date:</span>
                                    <span class="ml-1">{{ $visit->visit_date->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">Time:</span>
                                    <span class="ml-1">{{ $visit->visit_time }}</span>
                                </div>
                            </div>

                            @if($visit->tenant_notes)
                                <div class="mb-4 p-3 bg-purple-50 rounded-lg">
                                    <p class="text-sm font-medium text-purple-800 mb-1">Your Notes:</p>
                                    <p class="text-sm text-purple-700">{{ $visit->tenant_notes }}</p>
                                </div>
                            @endif

                            <div class="flex items-center justify-between">
                                <p class="text-xs text-gray-500">
                                    Scheduled on {{ $visit->created_at->format('M d, Y \a\t g:i A') }}
                                </p>

                                <div class="flex space-x-2">
                                    <a href="{{ route('properties.show', $visit->property) }}" class="text-green-600 hover:text-green-800 text-sm font-medium transition">
                                        View Property
                                    </a>
                                    <a href="{{ route('tenant.scheduled-visits') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium transition">
                                        Manage Visits
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No scheduled visits</h3>
                        <p class="text-gray-500 mb-6">Browse properties and schedule your first visit!</p>
                        <a href="{{ route('properties.browse') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 inline-block transition">
                            Browse Properties
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active styles from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-green-500', 'text-green-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');

    // Add active styles to selected tab
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-green-500', 'text-green-600');
}

// Initialize with 'all' tab active
document.addEventListener('DOMContentLoaded', function() {
    showTab('all');
});
</script>
@endsection