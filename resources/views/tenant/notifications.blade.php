@extends('layouts.account')

@section('title', 'Notifications')

@section('content')
<div class="bg-white shadow-sm rounded-lg">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900">🔔 Notifications</h2>
            @if($notifications->where('is_read', false)->count() > 0)
                <form method="POST" action="{{ route('tenant.notifications.mark-all-read') }}" class="inline" id="tenant-mark-all-read-form" onsubmit="event.stopPropagation(); return confirm('NOTIFICATIONS: Are you sure you want to mark all {{ $notifications->where('is_read', false)->count() }} notification(s) as read?');">
                    @csrf
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm" id="tenant-mark-all-read-btn" onclick="event.stopPropagation();">
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <form method="GET" action="{{ route('tenant.notifications') }}" class="flex flex-wrap gap-4">
            <div class="flex gap-2">
                <select name="filter" class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                    <option value="">All Notifications</option>
                    <option value="unread" {{ request('filter') === 'unread' ? 'selected' : '' }}>Unread</option>
                    <option value="read" {{ request('filter') === 'read' ? 'selected' : '' }}>Read</option>
                </select>

                <select name="type" class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                    <option value="">All Types</option>
                    @foreach($notificationTypes as $key => $name)
                        <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded-lg hover:bg-gray-700 text-sm">
                    Filter
                </button>

                @if(request()->hasAny(['filter', 'type']))
                    <a href="{{ route('tenant.notifications') }}" class="bg-gray-300 text-gray-700 px-4 py-1 rounded-lg hover:bg-gray-400 text-sm">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Notifications List -->
    <div class="divide-y divide-gray-200">
        @forelse($notifications as $notification)
            <div class="px-6 py-4 {{ !$notification->is_read ? 'bg-blue-50 border-l-4 border-l-blue-500' : 'hover:bg-gray-50' }} transition duration-150">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3 flex-1">
                        <div class="text-2xl">{{ $notification->icon }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-sm font-semibold {{ $notification->color }}">
                                    {{ $notification->title }}
                                </h3>
                                @if(!$notification->is_read)
                                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">New</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700 mb-2">{{ $notification->message }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                                <div class="flex items-center gap-2">
                                    @if($notification->action_url)
                                        <form method="POST" action="{{ route('tenant.notifications.read', $notification) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 underline">
                                                View Details
                                            </button>
                                        </form>
                                    @endif
                                    @if(!$notification->is_read)
                                        <form method="POST" action="{{ route('tenant.notifications.read', $notification) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs text-gray-600 hover:text-gray-800 underline">
                                                Mark as Read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center">
                <div class="text-4xl mb-4">🔔</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications yet</h3>
                <p class="text-gray-600">You'll see notifications here when landlords respond to your inquiries or when there are updates about your bookings and visits.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $notifications->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@endsection