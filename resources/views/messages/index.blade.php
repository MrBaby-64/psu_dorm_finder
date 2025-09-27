@extends('layouts.account')

@section('content')
<div class="py-8">
    <h1 class="text-3xl font-bold mb-6">Messages</h1>

    @if($messages->count() > 0)
    <div class="bg-white rounded-lg shadow">
        @foreach($messages as $otherUserId => $conversation)
            @php
                $lastMessage = $conversation->first();
                $otherUser = $lastMessage->sender_id === auth()->id() ? $lastMessage->receiver : $lastMessage->sender;
            @endphp
            
            <div class="p-6 border-b last:border-0 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('messages.conversation', ['userId' => $otherUser->id, 'propertyId' => $lastMessage->property_id]) }}'">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-semibold text-lg">{{ $otherUser->name }}</h3>
                            <span class="text-xs px-2 py-1 rounded-full {{ auth()->user()->role === 'tenant' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $otherUser->role === 'landlord' ? 'Landlord' : 'Tenant' }}
                            </span>
                            @if($lastMessage->inquiry_id)
                                <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-800">
                                    Inquiry
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-1">📍 {{ $lastMessage->property->title }}</p>
                        @if($lastMessage->inquiry_id && $lastMessage->inquiry)
                            <p class="text-xs text-purple-600 mb-2">
                                Status: {{ $lastMessage->inquiry->status_name }} •
                                Submitted: {{ $lastMessage->inquiry->created_at->format('M j, Y') }}
                            </p>
                        @endif
                        <p class="text-gray-700 mt-2">{{ Str::limit($lastMessage->body, 120) }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $lastMessage->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        @if($lastMessage->receiver_id === auth()->id() && !$lastMessage->read_at)
                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">New</span>
                        @endif
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <h3 class="text-lg font-medium text-gray-900">No messages yet</h3>
        <p class="mt-1 text-sm text-gray-500">Your conversations will appear here</p>
        <div class="mt-6">
            <a href="{{ route('properties.browse') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 inline-block">
                Browse Properties
            </a>
        </div>
    </div>
    @endif
</div>
@endsection