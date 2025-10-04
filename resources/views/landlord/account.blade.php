@extends('layouts.account')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ $user->name }}!</h1>
                    <p class="text-gray-600">{{ $user->role_name }} Dashboard</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $user->is_verified_badge }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <a href="{{ route('landlord.properties.index') }}" class="bg-white rounded-lg shadow-sm p-6 block hover:shadow-md hover:bg-gray-50 transition-all duration-200 group">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 group-hover:text-gray-700">Properties</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_properties'] }}</p>
                        <p class="text-xs text-blue-600 group-hover:text-blue-700 mt-1">Click to manage →</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('landlord.inquiries.index') }}" class="bg-white rounded-lg shadow-sm p-6 block hover:shadow-md hover:bg-gray-50 transition-all duration-200 group">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg group-hover:bg-yellow-200 transition-colors">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 group-hover:text-gray-700">Inquiries</p>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_inquiries'] }}</p>
                            @if($stats['pending_inquiries'] > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    pending
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-yellow-600 group-hover:text-yellow-700 mt-1">Click to view →</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('landlord.scheduled-visits') }}" class="bg-white rounded-lg shadow-sm p-6 block hover:shadow-md hover:bg-gray-50 transition-all duration-200 group">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 group-hover:text-gray-700">Visit Requests</p>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['scheduled_visits'] }}</p>
                            @if($stats['scheduled_visits'] > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    needs attention
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-purple-600 group-hover:text-purple-700 mt-1">Click to manage →</p>
                    </div>
                </div>
            </a>


            <a href="{{ route('landlord.notifications') }}" class="bg-white rounded-lg shadow-sm p-6 block hover:shadow-md hover:bg-gray-50 transition-all duration-200 group">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg group-hover:bg-red-200 transition-colors">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 group-hover:text-gray-700">Notifications</p>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['unread_notifications'] }}</p>
                            @if($stats['unread_notifications'] > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    unread
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-red-600 group-hover:text-red-700 mt-1">Click to view →</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('landlord.admin-messages') }}" class="bg-white rounded-lg shadow-sm p-6 block hover:shadow-md hover:bg-gray-50 transition-all duration-200 group">
                <div class="flex items-center">
                    <div class="p-3 bg-indigo-100 rounded-lg group-hover:bg-indigo-200 transition-colors">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 group-hover:text-gray-700">Admin Messages</p>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['admin_messages'] }}</p>
                            @if($stats['admin_responses'] > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    🛡️ {{ $stats['admin_responses'] }} new
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-indigo-600 group-hover:text-indigo-700 mt-1">Click to view →</p>
                    </div>
                </div>
            </a>
        </div>


        <!-- Detailed Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2">
                <!-- Recent Inquiries -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Inquiries</h2>
                        <a href="{{ route('landlord.inquiries.index') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">View All</a>
                    </div>

                    @if($recentInquiries->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentInquiries as $inquiry)
                            <a href="{{ route('landlord.inquiries.index') }}" class="flex items-center justify-between py-3 border-b border-gray-200 last:border-0 hover:bg-gray-50 transition-colors duration-200 rounded-lg px-2 cursor-pointer group">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg mr-3 group-hover:bg-blue-200">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 group-hover:text-blue-800">{{ $inquiry->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $inquiry->property->title }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $inquiry->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $inquiry->status_name }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">{{ $inquiry->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-500">No inquiries yet</p>
                        </div>
                    @endif
                </div>

                <!-- Visit Scheduling Center -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Visit Scheduling</h2>
                                <p class="text-sm text-gray-600">Manage property viewing requests</p>
                            </div>
                        </div>
                        <a href="{{ route('landlord.scheduled-visits') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Manage Visits
                        </a>
                    </div>

                    <!-- Urgent Alert for Today's Visits -->
                    @if($stats['today_visits'] > 0)
                        <div class="bg-red-50 border border-red-300 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-bold text-red-900">
                                        🚨 TODAY: You have {{ $stats['today_visits'] }} property visit{{ $stats['today_visits'] > 1 ? 's' : '' }} scheduled for today!
                                    </p>
                                    <p class="text-sm text-red-800 mt-1">
                                        Make sure you're prepared and arrive on time. Check your scheduled visits below.
                                    </p>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <a href="{{ route('landlord.scheduled-visits') }}" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-md hover:bg-red-700 animate-pulse">
                                        View Today's Visits
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Alert for Next 2-3 Days Visits -->
                    @if($stats['next_3_days_visits'] > 0)
                        <div class="bg-amber-50 border border-amber-300 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-amber-900">
                                        ⏰ UPCOMING: You have {{ $stats['next_3_days_visits'] }} visit{{ $stats['next_3_days_visits'] > 1 ? 's' : '' }} in the next 2-3 days
                                    </p>
                                    <p class="text-sm text-amber-800 mt-1">
                                        Plan ahead and prepare for these upcoming property visits.
                                    </p>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <a href="{{ route('landlord.scheduled-visits') }}" class="inline-flex items-center px-3 py-1.5 bg-amber-600 text-white text-xs font-medium rounded-md hover:bg-amber-700">
                                        Plan Ahead
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Alert for pending visit requests needing response -->
                    @if($stats['pending_visit_requests'] > 0)
                        <div class="bg-blue-50 border border-blue-300 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-blue-900">
                                        📋 PENDING: You have {{ $stats['pending_visit_requests'] }} visit request{{ $stats['pending_visit_requests'] > 1 ? 's' : '' }} awaiting your response
                                    </p>
                                    <p class="text-sm text-blue-800 mt-1">
                                        Tenants are waiting to schedule property visits. Quick responses improve your property ratings.
                                    </p>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <a href="{{ route('landlord.scheduled-visits') }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700">
                                        Review Requests
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Pending Visit Requests Section -->
                    @if($pendingVisitRequests->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-md font-semibold text-blue-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Pending Requests ({{ $pendingVisitRequests->count() }})
                            </h3>
                            <div class="space-y-3">
                                @foreach($pendingVisitRequests->take(3) as $request)
                                <a href="{{ route('landlord.scheduled-visits') }}" class="flex items-center justify-between py-3 px-3 border border-blue-200 bg-blue-50 hover:bg-blue-100 transition-colors duration-200 rounded-lg cursor-pointer group">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-blue-100 rounded-lg mr-3 group-hover:bg-blue-200">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <p class="text-sm font-medium text-blue-900 group-hover:text-blue-700">{{ $request->tenant->name }}</p>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-200 text-blue-800">
                                                    📋 NEEDS RESPONSE
                                                </span>
                                            </div>
                                            <p class="text-xs text-blue-700">{{ $request->property->title }}</p>
                                            <p class="text-xs text-blue-600 mt-1">Requested: {{ $request->preferred_date->format('M j, Y') }} at {{ $request->formatted_preferred_time }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-blue-600">{{ $request->created_at->diffForHumans() }}</p>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-600 text-white">
                                            Action Required
                                        </span>
                                    </div>
                                </a>
                                @endforeach

                                @if($pendingVisitRequests->count() > 3)
                                    <div class="text-center pt-2">
                                        <a href="{{ route('landlord.scheduled-visits') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                            View {{ $pendingVisitRequests->count() - 3 }} more pending requests →
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Confirmed Upcoming Visits Section -->
                    @if($imminentVisits->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-md font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Confirmed Visits ({{ $imminentVisits->count() }})
                            </h3>
                            <div class="space-y-3">
                                @foreach($imminentVisits as $visit)
                                <a href="{{ route('landlord.scheduled-visits') }}" class="flex items-center justify-between py-3 px-3 border hover:bg-gray-50 transition-colors duration-200 rounded-lg cursor-pointer group {{ $visit->isToday() ? 'border-red-300 bg-red-50' : ($visit->isUrgent() ? 'border-amber-300 bg-amber-50' : 'border-gray-200 bg-white') }}">
                                    <div class="flex items-center">
                                        @if($visit->isToday())
                                            <div class="p-2 bg-red-100 rounded-lg mr-3 group-hover:bg-red-200">
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                            </div>
                                        @elseif($visit->isUrgent())
                                            <div class="p-2 bg-amber-100 rounded-lg mr-3 group-hover:bg-amber-200">
                                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="p-2 bg-green-100 rounded-lg mr-3 group-hover:bg-green-200">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <p class="text-sm font-medium text-gray-900">{{ $visit->tenant->name }}</p>
                                                @if($visit->isToday())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 animate-pulse">
                                                        🚨 TODAY
                                                    </span>
                                                @elseif($visit->isUrgent())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                        ⏰ SOON
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500">{{ $visit->property->title }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium {{ $visit->isToday() ? 'text-red-900' : ($visit->isUrgent() ? 'text-amber-900' : 'text-gray-900') }}">{{ $visit->display_date_time }}</p>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ Confirmed
                                        </span>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    @elseif($upcomingVisits->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-md font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Upcoming Visits ({{ $upcomingVisits->count() }})
                            </h3>
                            <div class="space-y-3">
                                @foreach($upcomingVisits->take(3) as $visit)
                                <a href="{{ route('landlord.scheduled-visits') }}" class="flex items-center justify-between py-3 px-3 border border-gray-200 bg-white hover:bg-gray-50 transition-colors duration-200 rounded-lg cursor-pointer group">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-green-100 rounded-lg mr-3 group-hover:bg-green-200">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $visit->tenant->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $visit->property->title }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">{{ $visit->display_date_time }}</p>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ Confirmed
                                        </span>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Empty state -->
                    @if($pendingVisitRequests->count() == 0 && $imminentVisits->count() == 0 && $upcomingVisits->count() == 0)
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-500 text-sm">No visit requests or scheduled visits</p>
                            <p class="text-gray-400 text-xs mt-1">Visit requests will appear here when tenants request to view your properties</p>
                        </div>
                    @endif
                </div>

            </div>

            <div>
                <!-- Properties Overview -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">My Properties</h2>
                        <a href="{{ route('landlord.properties.index') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">Manage</a>
                    </div>
                    
                    @if($properties->count() > 0)
                        <div class="space-y-3">
                            @foreach($properties->take(3) as $property)
                            <a href="{{ route('properties.show', $property->slug) }}" class="flex items-center space-x-3 hover:bg-gray-50 transition-colors duration-200 rounded-lg p-2 cursor-pointer group">
                                @php
                                    $mainImage = $property->images->where('is_cover', true)->first() ?? $property->images->first();
                                    $imageUrl = $mainImage ? $mainImage->full_url : 'https://via.placeholder.com/50x50?text=No+Image';
                                @endphp

                                <img src="{{ $imageUrl }}"
                                     alt="{{ $property->title }}"
                                     class="w-12 h-12 rounded-lg object-cover">

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate group-hover:text-blue-800">{{ $property->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $property->city }}</p>
                                </div>

                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $property->approval_status === 'approved' ? 'bg-green-100 text-green-800' :
                                       ($property->approval_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($property->approval_status) }}
                                </span>
                            </a>
                            @endforeach
                            
                            @if($properties->count() > 3)
                                <div class="text-center pt-2">
                                    <a href="{{ route('landlord.properties.index') }}" class="text-sm text-green-600 hover:text-green-700">
                                        View {{ $properties->count() - 3 }} more properties
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <p class="text-gray-500 text-sm mb-3">No properties yet</p>
                            <a href="{{ route('landlord.properties.create') }}" 
                               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                                Add Property
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Prevent going back to login/register after successful login
(function() {
    const dashboardUrl = '{{ route("landlord.account") }}';

    // Check if this is a fresh login
    const urlParams = new URLSearchParams(window.location.search);
    const isFreshLogin = urlParams.get('fresh_login') === '1';

    if (isFreshLogin) {
        // Remove the query parameter and replace history
        window.history.replaceState(null, '', dashboardUrl);

        // Set flag in sessionStorage
        sessionStorage.setItem('preventBackToLogin', 'true');
    }

    // Prevent back navigation completely when flag is set
    if (sessionStorage.getItem('preventBackToLogin') === 'true') {
        // Override browser back button
        history.pushState(null, document.title, location.href);

        window.addEventListener('popstate', function () {
            history.pushState(null, document.title, location.href);
        });

        // Clear flag after user navigates away from dashboard
        window.addEventListener('beforeunload', function() {
            sessionStorage.removeItem('preventBackToLogin');
        });
    }
})();
</script>
@endpush