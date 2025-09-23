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
            
            <div class="p-6 border-b last:border-0 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg">{{ $otherUser->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $lastMessage->property->title }}</p>
                        <p class="text-gray-700 mt-2">{{ Str::limit($lastMessage->body, 100) }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $lastMessage->created_at->diffForHumans() }}</p>
                    </div>
                    @if($lastMessage->receiver_id === auth()->id() && !$lastMessage->read_at)
                    <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">New</span>
                    @endif
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