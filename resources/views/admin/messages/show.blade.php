@extends('layouts.account')

@section('content')
<div class="py-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.messages.index') }}"
           class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Messages
        </a>

        <div class="flex-1">
            <h1 class="text-3xl font-bold">Message #{{ $message->id }}</h1>
            <div class="flex items-center gap-4 mt-2">
                <span class="px-3 py-1 text-sm font-medium rounded {{ $message->status_color }}">
                    {{ $message->status_name }}
                </span>
                <span class="text-sm text-gray-600">
                    Received {{ $message->created_at->format('M j, Y g:i A') }}
                </span>
            </div>
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

    <div class="grid lg:grid-cols-2 gap-8">
        {{-- Message Details --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Message Details</h2>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Subject</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $message->subject }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">From</label>
                        <div class="mt-1">
                            <p class="font-medium text-gray-900">{{ $message->sender->name ?? 'Unknown User' }}</p>
                            <p class="text-sm text-gray-600">{{ $message->sender->email ?? 'No email available' }}</p>
                            @if($message->sender && $message->sender->role)
                                <p class="text-xs text-gray-500 capitalize">{{ $message->sender->role }}</p>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Message</label>
                        <div class="mt-1 p-4 bg-gray-50 rounded-md border">
                            <p class="text-gray-800 whitespace-pre-wrap">{{ $message->message }}</p>
                        </div>
                    </div>

                    @if($message->attachment_path)
                    <div>
                        <label class="text-sm font-medium text-gray-700">Attachment</label>
                        <div class="mt-2 p-4 bg-gray-50 rounded-md border">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs text-gray-600">Landlord uploaded photo:</span>
                                <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank"
                                   class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    Open in new tab
                                </a>
                            </div>
                            <img src="{{ asset('storage/' . $message->attachment_path) }}"
                                 alt="Message Attachment"
                                 class="w-full max-w-md rounded-lg border border-gray-300 shadow-sm cursor-pointer hover:shadow-md transition-shadow"
                                 onclick="window.open('{{ asset('storage/' . $message->attachment_path) }}', '_blank')">
                            <p class="text-xs text-gray-500 mt-2 italic">Click image to view full size</p>
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="text-sm font-medium text-gray-700">Timeline</label>
                        <div class="mt-2 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Received:</span>
                                <span>{{ $message->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                            @if($message->responded_at)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Responded:</span>
                                <span>{{ $message->responded_at->format('M j, Y g:i A') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Property Information (if applicable) --}}
            @if($message->property)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Related Property</h3>

                <div class="space-y-3">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $message->property->title }}</h4>
                        <p class="text-sm text-gray-600">{{ $message->property->location_text }}</p>
                        <p class="text-sm text-gray-500">{{ $message->property->city }}, {{ $message->property->barangay }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-600">Monthly Rate:</span>
                            <p class="font-medium text-green-600">₱{{ number_format($message->property->price, 0) }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Rooms:</span>
                            <p class="font-medium">{{ $message->property->room_count }}</p>
                        </div>
                    </div>

                    <div>
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="ml-2 px-2 py-1 text-xs font-medium rounded
                            {{ $message->property->approval_status === 'approved' ? 'bg-green-100 text-green-800' :
                               ($message->property->approval_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($message->property->approval_status) }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('properties.show', $message->property) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            View Property Page
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Admin Response Section --}}
        <div class="space-y-6">
            @if($message->admin_response)
            {{-- Existing Response --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Admin Response</h3>

                <div class="bg-blue-50 border border-blue-200 p-4 rounded-md">
                    <div class="text-sm text-blue-700 mb-2">
                        <strong>Response from:</strong> {{ $message->responder->name ?? 'Unknown Admin' }}
                        <br>
                        <strong>Sent:</strong> {{ $message->responded_at->format('M j, Y g:i A') }}
                    </div>
                    <div class="text-gray-800">
                        <p class="whitespace-pre-wrap">{{ $message->admin_response }}</p>
                    </div>
                </div>

                <div class="mt-4 text-sm text-gray-500">
                    <p><strong>Status:</strong> {{ $message->status_name }}</p>
                </div>
            </div>
            @else
            {{-- Response Form --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Send Response</h3>

                <form action="{{ route('admin.messages.respond', $message) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="admin_response" class="block text-sm font-medium text-gray-700 mb-2">
                            Your Response <span class="text-red-500">*</span>
                        </label>
                        <textarea name="admin_response" id="admin_response" rows="8" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y"
                                  placeholder="Type your response to {{ $message->sender->name ?? 'the user' }}...">{{ old('admin_response') }}</textarea>
                        @error('admin_response')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium">Note:</p>
                                <p class="mt-1">Sending this response will mark the message as "Resolved" and notify the user. This action cannot be undone.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition font-medium">
                            Send Response & Mark as Resolved
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Quick Actions --}}
            @if($message->status !== 'resolved')
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>

                <div class="space-y-3">
                    @if($message->status === 'unread')
                    <form action="{{ route('admin.messages.read', $message) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="w-full bg-yellow-500 text-white py-2 px-4 rounded-lg hover:bg-yellow-600 transition">
                            Mark as Read
                        </button>
                    </form>
                    @endif

                    @if($message->status !== 'resolved' && !$message->admin_response)
                    <form action="{{ route('admin.messages.resolve', $message) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition"
                                onclick="return confirm('Mark this message as resolved without sending a response?')">
                            Mark as Resolved (No Response)
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection