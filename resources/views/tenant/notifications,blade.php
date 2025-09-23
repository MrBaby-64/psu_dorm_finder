@extends('layouts.account')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                <p class="text-gray-600">Stay updated on your property activities</p>
            </div>
            
            @if($notifications->where('is_read', false)->count() > 0)
                <form method="GET" action="{{ route('tenant.notifications') }}">
                    <input type="hidden" name="mark_all_read" value="1">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                        Mark All Read
                    </button>
                </form>
            @endif
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6">
                    <a href="{{ route('tenant.notifications') }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ !request('filter') ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        All Notifications
                    </a>
                    <a href="{{ route('tenant.notifications', ['filter' => 'unread']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ request('filter') === 'unread' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Unread 
                        @php $unreadCount = $notifications->where('is_read', false)->count(); @endphp
                        @if($unreadCount > 0)
                            <span class="ml-1 bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('tenant.notifications', ['filter' => 'read']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ request('filter') === 'read' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Read
                    </a>
                </nav>
            </div>

            <!-- Type Filter -->
            <div class="p-6">
                <form method="GET" class="flex items-end space-x-4">
                    @if(request('filter'))
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                    @endif
                    
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Type</label>
                        <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">All Types</option>
                            @foreach($notificationTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- Notifications List -->
        @if($notifications->count() > 0)
            <div class="bg-white rounded-lg shadow-sm">
                <div class="divide-y divide-gray-200">
                    @foreach($notifications as $notification)
                        <div class="p-6 hover:bg-gray-50 {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <!-- Notification Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-lg {{ $notification->color }} bg-opacity-10 flex items-center justify-center">
                                            <span class="text-lg">{{ $notification->icon }}</span>
                                        </div>
                                    </div>

                                    <!-- Notification Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-semibold text-gray-900 {{ !$notification->is_read ? 'font-bold' : '' }}">
                                                {{ $notification->title }}
                                            </h3>
                                            @if(!$notification->is_read)
                                                <div class="flex-shrink-0 ml-4">
                                                    <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <p class="text-gray-600 mt-1 {{ !$notification->is_read ? 'font-medium' : '' }}">
                                            {{ $notification->message }}
                                        </p>
                                        
                                        <div class="flex items-center justify-between mt-3">
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span>{{ $notification->created_at->format('M j, Y g:i A') }}</span>
                                                <span>•</span>
                                                <span class="capitalize">{{ $notification->type_name }}</span>
                                            </div>
                                            
                                            <div class="flex items-center space-x-2">
                                                @if($notification->action_url)
                                                    <a href="{{ $notification->action_url }}" 
                                                       class="text-green-600 hover:text-green-700 text-sm font-medium">
                                                        View Details
                                                    </a>
                                                @endif
                                                
                                                @if(!$notification->is_read)
                                                    <form action="{{ route('tenant.notifications.read', $notification) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="text-gray-500 hover:text-gray-700 text-sm">
                                                            Mark Read
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Additional Data -->
                                        @if($notification->data && is_array($notification->data))
                                            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                                <div class="text-sm text-gray-600">
                                                    @foreach($notification->data as $key => $value)
                                                        <div class="flex justify-between">
                                                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                            <span>{{ $value }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $notifications->links() }}
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications found</h3>
                <p class="text-gray-500 mb-6">
                    @if(request('filter') === 'unread')
                        You're all caught up! No unread notifications.
                    @elseif(request('filter') === 'read')  
                        No read notifications yet.
                    @elseif(request('type'))
                        No notifications of this type.
                    @else
                        You'll see notifications here when landlords respond to your inquiries or when there are updates on your bookings.
                    @endif
                </p>
                <a href="{{ route('properties.browse') }}" 
                   class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-medium">
                    Browse Properties
                </a>
            </div>
        @endif
    </div>
</div>
@endsection