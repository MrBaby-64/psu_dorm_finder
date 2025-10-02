@extends('layouts.account')

@section('content')
<div class="py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('messages.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold">{{ $otherUser->name }}</h1>
                <p class="text-gray-600">{{ $property->title }}</p>
            </div>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="bg-white rounded-lg shadow min-h-[600px] flex flex-col">

        <!-- Messages List -->
        <div class="flex-1 p-6 space-y-4 overflow-y-auto max-h-[500px]">
            @if($messages->count() > 0)
                @foreach($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[70%] {{ $message->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800' }} rounded-lg px-4 py-3">
                            <!-- Inquiry Context -->
                            @if($message->inquiry_id && $message->inquiry)
                                <div class="text-xs {{ $message->sender_id === auth()->id() ? 'text-blue-200' : 'text-purple-600' }} mb-2 font-medium">
                                    📋 Inquiry: {{ $message->inquiry->status_name }}
                                </div>
                            @endif

                            <!-- Message Content -->
                            <div class="whitespace-pre-line break-words">{{ $message->body }}</div>

                            <!-- Timestamp -->
                            <div class="text-xs {{ $message->sender_id === auth()->id() ? 'text-blue-200' : 'text-gray-500' }} mt-2">
                                {{ $message->created_at->format('M j, Y \\a\\t g:i A') }}
                                @if($message->sender_id === auth()->id() && $message->read_at)
                                    • Read
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">No messages in this conversation yet.</p>
                </div>
            @endif
        </div>

        <!-- Message Input -->
        <div class="border-t p-4">
            <form action="{{ route('messages.store') }}" method="POST" class="flex gap-3">
                @csrf
                <input type="hidden" name="property_id" value="{{ $property->id }}">
                <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">

                <div class="flex-1">
                    <textarea name="body"
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y"
                              placeholder="Type your message... (Press Enter to send, Ctrl+Enter for new line)"
                              required
                              onkeydown="handleTextareaKeydown(event)"></textarea>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium self-end">
                    Send
                </button>
            </form>
        </div>
    </div>

    <!-- Property Info Card -->
    <div class="mt-6 bg-gray-50 rounded-lg p-4">
        <h3 class="font-semibold mb-2">Property Information</h3>
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <p class="font-medium">{{ $property->title }}</p>
                <p class="text-sm text-gray-600">{{ $property->address_line }}, {{ $property->city }}</p>
                <p class="text-sm text-green-600 font-semibold">₱{{ number_format($property->price) }}/month</p>
            </div>
            <a href="{{ route('properties.show', $property->slug) }}"
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm">
                View Property
            </a>
        </div>
    </div>
</div>

<script>
// Auto-scroll to bottom of messages
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.querySelector('.overflow-y-auto');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});

function handleTextareaKeydown(event) {
    if (event.key === 'Enter') {
        if (event.ctrlKey || event.metaKey) {
            return;
        } else {
            event.preventDefault();
            event.target.closest('form').submit();
        }
    }
}
</script>
@endsection