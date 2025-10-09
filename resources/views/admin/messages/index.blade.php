@extends('layouts.account')

@section('content')
<div class="py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Admin Messages</h1>

        <div class="flex gap-2">
            @php
                $unreadCount = $messages->where('status', 'unread')->count();
                $totalCount = $messages->total();
            @endphp
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                {{ $unreadCount }} Unread
            </span>
            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm">
                {{ $totalCount }} Total
            </span>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error') || $errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        @if(session('error'))
            {{ session('error') }}
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        @endif
    </div>
    @endif

    {{-- Search and Filter Bar --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.messages.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by sender name, subject, or message content..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.messages.index') }}"
                   class="border border-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-50">
                    Clear
                </a>
            @endif
        </form>
    </div>

    @if($messages->count() > 0)
    <div class="space-y-4">
        @foreach($messages as $message)
        <div class="bg-white rounded-lg shadow {{ $message->status === 'unread' ? 'border-l-4 border-blue-500' : '' }}">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $message->subject }}
                            </h3>
                            <span class="px-2 py-1 text-xs font-medium rounded {{ $message->status_color }}">
                                {{ $message->status_name }}
                            </span>
                            @if($message->attachment_path)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-purple-100 text-purple-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                Has Attachment
                            </span>
                            @endif
                        </div>

                        <div class="text-sm text-gray-600 mb-3">
                            <strong>From:</strong> {{ $message->sender->name ?? 'Unknown User' }}
                            ({{ $message->sender->email ?? 'No email' }})
                            <span class="mx-2">•</span>
                            <strong>Sent:</strong> {{ $message->created_at->format('M j, Y g:i A') }}
                            @if($message->property)
                                <span class="mx-2">•</span>
                                <strong>Property:</strong> {{ $message->property->title }}
                            @endif
                        </div>

                        <div class="text-gray-800 bg-gray-50 p-3 rounded-md">
                            <p>{{ Str::limit($message->message, 200) }}</p>
                        </div>

                        @if($message->admin_response)
                            <div class="mt-3 bg-blue-50 border border-blue-200 p-3 rounded-md">
                                <div class="text-sm text-blue-700 mb-1">
                                    <strong>Admin Response</strong> by {{ $message->responder->name ?? 'Unknown' }}
                                    on {{ $message->responded_at->format('M j, Y g:i A') }}:
                                </div>
                                <p class="text-gray-800">{{ $message->admin_response }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <div class="flex gap-3">
                        <a href="{{ route('admin.messages.show', $message) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Details
                        </a>

                        @if($message->property)
                            <a href="{{ route('properties.show', $message->property) }}"
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                View Property
                            </a>
                        @endif
                    </div>

                    <div class="text-sm text-gray-500">
                        Message #{{ $message->id }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $messages->links() }}
    </div>

    @else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No messages found</h3>
        <p class="text-sm text-gray-500">
            @if(request()->hasAny(['search', 'status']))
                No messages match your current filters.
            @else
                There are no admin messages at this time.
            @endif
        </p>
    </div>
    @endif
</div>
@endsection