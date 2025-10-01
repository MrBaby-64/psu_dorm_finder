@extends('layouts.account')

@section('content')
<div class="py-8">
    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Pending Approval -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm mb-2">Pending Approval</h3>
            <p class="text-4xl font-bold text-orange-500">{{ $stats['pending_properties'] }}</p>
            <a href="{{ route('admin.properties.pending') }}" class="text-blue-600 text-sm mt-2 inline-block">Review now →</a>
        </div>

        <!-- Deletion Requests -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm mb-2">Deletion Requests</h3>
            <p class="text-4xl font-bold text-red-500">{{ $stats['pending_deletion_requests'] }}</p>
            <a href="{{ route('admin.properties.deletion-requests') }}" class="text-blue-600 text-sm mt-2 inline-block">
                @if($stats['pending_deletion_requests'] > 0)
                    Review now →
                @else
                    View all →
                @endif
            </a>
        </div>

        <!-- Approved Properties -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm mb-2">Approved Properties</h3>
            <p class="text-4xl font-bold text-green-500">{{ $stats['approved_properties'] }}</p>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm mb-2">Total Users</h3>
            <p class="text-4xl font-bold text-blue-500">{{ $stats['total_users'] }}</p>
            <p class="text-sm text-gray-500 mt-2">{{ $stats['landlords'] }} landlords, {{ $stats['tenants'] }} tenants</p>
        </div>

        <!-- Total Bookings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm mb-2">Total Bookings</h3>
            <p class="text-4xl font-bold text-purple-500">{{ $stats['total_bookings'] }}</p>
            <p class="text-sm text-gray-500 mt-2">{{ $stats['pending_bookings'] }} pending</p>
        </div>
    </div>

    <!-- Quick Actions Menu -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold">Admin Menu</h2>
            <p class="text-gray-600 text-sm">Quick access to admin functions</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 p-6">
            <a href="{{ route('admin.properties.pending') }}"
               class="flex items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 transition-colors group">
                <div class="flex-shrink-0 w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-orange-700">Property Approvals</h3>
                    <p class="text-sm text-gray-600">Review pending properties</p>
                </div>
                @if($stats['pending_properties'] > 0)
                    <div class="ml-auto">
                        <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $stats['pending_properties'] }}</span>
                    </div>
                @endif
            </a>

            <a href="{{ route('admin.properties.deletion-requests') }}"
               class="flex items-center p-4 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors group">
                <div class="flex-shrink-0 w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-red-700">Deletion Requests</h3>
                    <p class="text-sm text-gray-600">Manage property deletions</p>
                </div>
                @if($stats['pending_deletion_requests'] > 0)
                    <div class="ml-auto">
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $stats['pending_deletion_requests'] }}</span>
                    </div>
                @endif
            </a>

            <a href="{{ route('admin.messages.index') }}"
               class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition-colors group">
                <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-green-700">Admin Messages</h3>
                    <p class="text-sm text-gray-600">View landlord messages</p>
                </div>
                @php
                    $unreadMessages = \App\Models\AdminMessage::where('status', 'unread')->count();
                @endphp
                @if($unreadMessages > 0)
                    <div class="ml-auto">
                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">{{ $unreadMessages }}</span>
                    </div>
                @endif
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors group">
                <div class="flex-shrink-0 w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-blue-700">User Management</h3>
                    <p class="text-sm text-gray-600">Manage users and roles</p>
                </div>
            </a>

            <a href="{{ route('admin.reports.index') }}"
               class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition-colors group">
                <div class="flex-shrink-0 w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-purple-700">Reports & Analytics</h3>
                    <p class="text-sm text-gray-600">View system reports</p>
                </div>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Properties -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">Recent Properties</h2>
            </div>
            <div class="divide-y">
                @if($recentProperties->count() > 0)
                    @foreach($recentProperties as $property)
                    <div class="p-6 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold">{{ $property->title }}</h3>
                            <p class="text-sm text-gray-600">by {{ $property->landlord_name }}</p>
                        </div>
                        <span class="px-3 py-1
                            {{ $property->approval_status === 'approved' ? 'bg-green-100 text-green-800' :
                               ($property->approval_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}
                            rounded-full text-sm">
                            {{ ucfirst($property->approval_status) }}
                        </span>
                    </div>
                    @endforeach
                @else
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-sm">No recent properties</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Deletion Requests -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold">Deletion Requests</h2>
                @if($stats['pending_deletion_requests'] > 0)
                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">
                        {{ $stats['pending_deletion_requests'] }} pending
                    </span>
                @endif
            </div>
            <div class="divide-y">
                @if($recentDeletionRequests->count() > 0)
                    @foreach($recentDeletionRequests as $request)
                    <a href="{{ route('admin.properties.deletion-requests.view', $request->id) }}" class="block p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold truncate">
                                    @if($request->property_title)
                                        {{ $request->property_title }}
                                    @else
                                        <span class="text-gray-400">Property Deleted</span>
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-600 truncate">by {{ $request->landlord_name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}</p>
                            </div>
                            <div class="ml-2">
                                <span class="px-2 py-1 text-xs rounded font-medium {{ $request->status_color }}">
                                    {{ $request->status_name }}
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                    <div class="p-4 bg-gray-50">
                        <a href="{{ route('admin.properties.deletion-requests') }}"
                           class="text-sm text-blue-600 hover:text-blue-800 font-medium block text-center">
                            View All Deletion Requests →
                        </a>
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">
                        <div class="w-12 h-12 mx-auto mb-3 text-gray-300">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <p class="text-sm">No deletion requests</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">Recent Users</h2>
            </div>
            <div class="divide-y">
                @if($recentUsers->count() > 0)
                    @foreach($recentUsers as $user)
                    <div class="p-6 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                        </div>
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    @endforeach
                @else
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-sm">No recent users</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection