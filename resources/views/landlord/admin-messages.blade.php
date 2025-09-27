@extends('layouts.account')

@section('title', 'Admin Messages')

@section('content')
<div class="py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Admin Messages</h1>

        <div class="flex gap-2">
            @php
                $totalMessages = $messages->total();
                $respondedMessages = $messages->where('status', 'resolved')->count();
            @endphp
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                {{ $totalMessages }} Total Messages
            </span>
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                {{ $respondedMessages }} Responded
            </span>
        </div>
    </div>

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
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Messages with Admin</h2>
            <p class="text-sm text-gray-600">Your conversations with the admin team about properties and system issues</p>
        </div>

        <!-- Filters -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('landlord.admin-messages') }}" class="flex flex-wrap gap-4">
                <div class="flex gap-2">
                    <select name="status" class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $key => $name)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded-lg hover:bg-gray-700 text-sm">
                        Filter
                    </button>

                    @if(request()->has('status'))
                        <a href="{{ route('landlord.admin-messages') }}" class="bg-gray-300 text-gray-700 px-4 py-1 rounded-lg hover:bg-gray-400 text-sm">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Messages List -->
        @if($messages->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($messages as $message)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $message->subject }}</h3>

                                    @if($message->status === 'resolved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Resolved
                                        </span>
                                    @elseif($message->status === 'read')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Read
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Unread
                                        </span>
                                    @endif

                                    @if($message->admin_response)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            🛡️ Admin Responded
                                        </span>
                                    @endif
                                </div>

                                @if($message->property)
                                    <p class="text-sm text-gray-600 mb-2">
                                        📍 Related to: <strong>{{ $message->property->title }}</strong>
                                    </p>
                                @endif

                                <p class="text-gray-700 mb-2">{{ Str::limit($message->message, 120) }}</p>

                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span>Sent: {{ $message->created_at->format('M j, Y g:i A') }}</span>
                                    @if($message->responded_at)
                                        <span>• Responded: {{ $message->responded_at->format('M j, Y g:i A') }}</span>
                                    @endif
                                </div>

                                @if($message->admin_response)
                                    <div class="mt-3 p-3 bg-indigo-50 border border-indigo-200 rounded-md">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-medium text-indigo-800">🛡️ Admin Response:</span>
                                            @if($message->responder)
                                                <span class="text-xs text-indigo-600">by {{ $message->responder->name }}</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-indigo-900">{{ Str::limit($message->admin_response, 150) }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col gap-2 ml-4">
                                <a href="{{ route('landlord.admin-messages.show', $message) }}"
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Details
                                </a>

                                @if($message->admin_response)
                                    <span class="text-xs text-center text-green-600 font-medium">
                                        ✓ Response Available
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No admin messages found</h3>
                <p class="text-sm text-gray-500 max-w-sm mx-auto">
                    You haven't sent any messages to the admin yet. Messages about property deletions, issues, or questions will appear here.
                </p>
                <div class="mt-6">
                    <a href="{{ route('landlord.properties.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Go to My Properties
                    </a>
                </div>
            </div>
        @endif

        @if($messages->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $messages->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<script>
// Auto-dismiss success messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.querySelector('.bg-green-50');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'opacity 0.3s ease-in-out';
            successAlert.style.opacity = '0';
            setTimeout(() => {
                if (successAlert && successAlert.parentNode) {
                    successAlert.parentNode.removeChild(successAlert);
                }
            }, 300);
        }, 5000);
    }
});
</script>
@endsection