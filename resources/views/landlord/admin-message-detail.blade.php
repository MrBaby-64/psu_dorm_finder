@extends('layouts.account')

@section('title', 'Admin Message Details')

@section('content')
<div class="py-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('landlord.admin-messages') }}"
           class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Admin Messages
        </a>

        <div class="flex-1">
            <h1 class="text-3xl font-bold">Message Details</h1>
            <div class="flex items-center gap-4 mt-2">
                @if($message->status === 'resolved')
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Resolved
                    </span>
                @elseif($message->status === 'read')
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded bg-blue-100 text-blue-800">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Read by Admin
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded bg-gray-100 text-gray-800">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pending Review
                    </span>
                @endif

                <span class="text-sm text-gray-600">
                    Sent {{ $message->created_at->format('M j, Y g:i A') }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
        {{-- Your Message --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Your Message to Admin</h2>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Subject</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $message->subject }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Your Message</label>
                        <div class="mt-1 p-4 bg-gray-50 rounded-md border">
                            <p class="text-gray-800 whitespace-pre-wrap">{{ $message->message }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Timeline</label>
                        <div class="mt-2 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Sent:</span>
                                <span>{{ $message->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                            @if($message->responded_at)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Admin Responded:</span>
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
            {{-- Admin Response --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Admin Response</h3>

                <div class="bg-indigo-50 border border-indigo-200 p-6 rounded-lg">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                🛡️
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-indigo-700 mb-3">
                                <strong>Response from:</strong> {{ $message->responder->name ?? 'PSU Dorm Finder Admin' }}
                                <br>
                                <strong>Sent:</strong> {{ $message->responded_at->format('M j, Y g:i A') }}
                                <span class="text-indigo-600">({{ $message->responded_at->diffForHumans() }})</span>
                            </div>
                            <div class="text-gray-800">
                                <p class="whitespace-pre-wrap leading-relaxed">{{ $message->admin_response }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-green-700">
                            <p class="font-medium">Issue Resolved</p>
                            <p class="mt-1">The admin has responded to your message. If you need further assistance, you can submit a new message.</p>
                        </div>
                    </div>
                </div>
            </div>
            @else
            {{-- Waiting for Response --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Admin Response</h3>

                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Waiting for Admin Response</h4>
                    <p class="text-sm text-gray-600 mb-4">
                        Your message has been sent to the admin team. They will review it and respond as soon as possible.
                    </p>

                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium">You'll be notified when admin responds</p>
                                <p class="mt-1">Check your notifications or visit this page again to see the admin's response.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Actions</h3>

                <div class="space-y-3">
                    <a href="{{ route('landlord.admin-messages') }}"
                       class="w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-200 transition font-medium text-center block">
                        Back to All Admin Messages
                    </a>

                    <a href="{{ route('landlord.properties.index') }}"
                       class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition font-medium text-center block">
                        Go to My Properties
                    </a>

                    @if($message->property)
                    <a href="{{ route('properties.show', $message->property) }}"
                       target="_blank"
                       class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition font-medium text-center block">
                        View Related Property
                    </a>
                    @endif

                    @if(!$message->admin_response)
                    <div class="pt-3 border-t border-gray-200">
                        <p class="text-sm text-gray-600 text-center mb-3">
                            Need to follow up or add more details?
                        </p>
                        @if($message->property)
                            <a href="{{ route('landlord.properties.show', $message->property) }}#contact-admin"
                               class="w-full bg-orange-600 text-white py-2 px-4 rounded-lg hover:bg-orange-700 transition font-medium text-center block text-sm">
                                Send Another Message
                            </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection