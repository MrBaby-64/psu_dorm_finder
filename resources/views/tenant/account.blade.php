@extends('layouts.account')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ $user->name }}!</h1>
                    <p class="text-gray-600">{{ $user->role_name }} Account</p>
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
            <a href="{{ route('favorites.index') }}" class="bg-white rounded-lg shadow-sm p-6 block hover:shadow-md hover:bg-gray-50 transition-all duration-200 group">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg group-hover:bg-red-200 transition-colors">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 group-hover:text-gray-700">Favorites</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['favorites_count'] }}</p>
                        <p class="text-xs text-red-600 group-hover:text-red-700 mt-1">Click to view →</p>
                    </div>
                </div>
            </a>


            <a href="{{ route('tenant.scheduled-visits') }}" class="bg-white rounded-lg shadow-sm p-6 block hover:shadow-md hover:bg-gray-50 transition-all duration-200 group">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 group-hover:text-gray-700">Scheduled Visits</p>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['scheduled_visits_count'] }}</p>
                            @if($stats['scheduled_visits_count'] > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    active
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-blue-600 group-hover:text-blue-700 mt-1">Click to manage →</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('tenant.reviews') }}" class="bg-white rounded-lg shadow-sm p-6 block hover:shadow-md hover:bg-gray-50 transition-all duration-200 group">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg group-hover:bg-yellow-200 transition-colors">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 group-hover:text-gray-700">Reviews</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['reviews_count'] }}</p>
                        <p class="text-xs text-yellow-600 group-hover:text-yellow-700 mt-1">Click to view →</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('bookings.index') }}" class="bg-white rounded-lg shadow-sm p-6 block hover:shadow-md hover:bg-gray-50 transition-all duration-200 group">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 group-hover:text-gray-700">Bookings</p>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['bookings_count'] ?? 0 }}</p>
                            @if(($stats['bookings_count'] ?? 0) > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    active
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-green-600 group-hover:text-green-700 mt-1">Click to view →</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('tenant.notifications') }}" class="bg-white rounded-lg shadow-sm p-6 block hover:shadow-md hover:bg-gray-50 transition-all duration-200 group">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 group-hover:text-gray-700">Notifications</p>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['unread_notifications'] }}</p>
                            @if($stats['unread_notifications'] > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    unread
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-purple-600 group-hover:text-purple-700 mt-1">Click to view →</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2">

                <!-- Upcoming Visits -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Upcoming Visits</h2>
                        <a href="{{ route('tenant.scheduled-visits') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">View All</a>
                    </div>
                    
                    @if($upcomingVisits->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingVisits as $visit)
                            <a href="{{ route('tenant.scheduled-visits') }}" class="flex items-center justify-between py-3 border-b border-gray-200 last:border-0 hover:bg-gray-50 transition-colors duration-200 rounded-lg px-2 cursor-pointer group">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg mr-3 group-hover:bg-blue-200">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 group-hover:text-blue-800">{{ $visit->property->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $visit->display_date_time }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $visit->status_color }}">
                                    {{ $visit->status_name }}
                                </span>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-500">No scheduled visits</p>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <!-- Recent Notifications -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Notifications</h2>
                        <a href="{{ route('tenant.notifications') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">View All</a>
                    </div>
                    
                    @if($recentNotifications->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentNotifications as $notification)
                            <a href="{{ route('tenant.notifications') }}" class="flex items-start space-x-3 hover:bg-gray-50 transition-colors duration-200 rounded-lg p-2 cursor-pointer group">
                                <div class="flex-shrink-0">
                                    <span class="text-xl">{{ $notification->icon }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 {{ !$notification->is_read ? 'font-semibold' : '' }} group-hover:text-blue-800">
                                        {{ $notification->title }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                @if(!$notification->is_read)
                                    <div class="flex-shrink-0">
                                        <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                                    </div>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <p class="text-gray-500">No notifications yet</p>
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
    const dashboardUrl = '{{ route("tenant.account") }}';

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