@extends('layouts.account')

@section('content')
<div class="py-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.properties.deletion-requests') }}"
           class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Deletion Requests
        </a>

        <div class="flex-1">
            <h1 class="text-3xl font-bold">Deletion Request #{{ $deletionRequest->id }}</h1>
            <div class="flex items-center gap-4 mt-2">
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $deletionRequest->status_color }}">
                    {{ $deletionRequest->status_name }}
                </span>
                <span class="text-sm text-gray-600">
                    Submitted {{ $deletionRequest->created_at->format('M j, Y g:i A') }}
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
        {{-- Property Information --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Property Information</h2>

                @if($deletionRequest->property)
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $deletionRequest->property->title }}</h3>
                            <p class="text-gray-600">{{ $deletionRequest->property->location_text }}</p>
                            <p class="text-sm text-gray-500">{{ $deletionRequest->property->city }}, {{ $deletionRequest->property->barangay }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Monthly Rate</label>
                                <p class="text-lg font-bold text-green-600">₱{{ number_format($deletionRequest->property->price, 0) }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Rooms</label>
                                <p class="text-lg font-semibold">{{ $deletionRequest->property->room_count }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Description</label>
                            <p class="mt-1 text-gray-800">{{ $deletionRequest->property->description }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Address</label>
                            <p class="mt-1 text-gray-800">{{ $deletionRequest->property->address_line }}</p>
                            <p class="text-sm text-gray-600">
                                Coordinates: {{ $deletionRequest->property->latitude }}, {{ $deletionRequest->property->longitude }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-1 flex gap-4">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                                    {{ ucfirst($deletionRequest->property->approval_status) }}
                                </span>
                                @if($deletionRequest->property->is_verified)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">
                                        PSU Verified
                                    </span>
                                @endif
                                @if($deletionRequest->property->is_featured)
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-sm">
                                        Featured
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('properties.show', $deletionRequest->property) }}"
                               target="_blank"
                               class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                View Property Page
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Property Not Found</h3>
                        <p class="text-sm text-gray-500">The property associated with this deletion request no longer exists.</p>
                    </div>
                @endif
            </div>

            {{-- Property Images --}}
            @if($deletionRequest->property && $deletionRequest->property->images->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Property Images</h3>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($deletionRequest->property->images->take(4) as $image)
                    <div class="aspect-square">
                        <img src="{{ asset('storage/' . $image->image_path) }}"
                             alt="{{ $image->alt_text }}"
                             class="w-full h-full object-cover rounded-lg">
                    </div>
                    @endforeach
                </div>
                @if($deletionRequest->property->images->count() > 4)
                <p class="text-sm text-gray-500 mt-2">
                    And {{ $deletionRequest->property->images->count() - 4 }} more images...
                </p>
                @endif
            </div>
            @endif

            {{-- Room Details --}}
            @if($deletionRequest->property && $deletionRequest->property->rooms->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Room Details</h3>
                <div class="space-y-3">
                    @foreach($deletionRequest->property->rooms as $room)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-md">
                        <div>
                            <p class="font-medium">{{ $room->room_number }}</p>
                            @if($room->description)
                                <p class="text-sm text-gray-600">{{ $room->description }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-medium">{{ $room->capacity }} people</p>
                            <p class="text-sm text-gray-600">{{ ucfirst($room->status) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Request Information --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Request Details</h2>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Landlord Information</label>
                        @if($deletionRequest->landlord)
                            <div class="mt-1">
                                <p class="font-medium">{{ $deletionRequest->landlord->name }}</p>
                                <p class="text-sm text-gray-600">{{ $deletionRequest->landlord->email }}</p>
                                @if($deletionRequest->landlord->phone)
                                    <p class="text-sm text-gray-600">{{ $deletionRequest->landlord->phone }}</p>
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-red-600">Landlord information not available</p>
                        @endif
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Reason for Deletion</label>
                        <div class="mt-1 p-4 bg-gray-50 rounded-md">
                            <p class="text-gray-800">{{ $deletionRequest->reason }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Request Timeline</label>
                        <div class="mt-2 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Submitted:</span>
                                <span>{{ $deletionRequest->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                            @if($deletionRequest->reviewed_at)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Reviewed:</span>
                                <span>{{ $deletionRequest->reviewed_at->format('M j, Y g:i A') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- View Messages Link --}}
                    @php
                        $messageCount = \App\Models\AdminMessage::where('sender_id', $deletionRequest->landlord_id)
                            ->where('property_id', $deletionRequest->property_id)
                            ->count();
                    @endphp
                    @if($messageCount > 0)
                    <div>
                        <label class="text-sm font-medium text-gray-700">Related Messages</label>
                        <div class="mt-2">
                            <a href="{{ route('admin.messages.index', ['search' => $deletionRequest->landlord->name ?? '', 'property_id' => $deletionRequest->property_id]) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg text-blue-700 hover:text-blue-800 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                View Messages ({{ $messageCount }})
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($deletionRequest->admin_notes)
                    <div>
                        <label class="text-sm font-medium text-gray-700">Admin Notes</label>
                        <div class="mt-1 p-4 bg-blue-50 rounded-md">
                            <p class="text-gray-800">{{ $deletionRequest->admin_notes }}</p>
                        </div>
                        @if($deletionRequest->reviewer)
                            <p class="text-xs text-gray-500 mt-1">
                                By {{ $deletionRequest->reviewer->name }} on {{ $deletionRequest->reviewed_at->format('M j, Y g:i A') }}
                            </p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- Action Buttons (only for pending requests) --}}
            @if($deletionRequest->status === 'pending' && $deletionRequest->property)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Admin Actions</h3>

                <div class="flex flex-col gap-3">
                    <button onclick="document.getElementById('approveModal').classList.remove('hidden')"
                            class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approve & Delete Property
                    </button>

                    <button onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                            class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reject Request
                    </button>
                </div>

                {{-- Warning Box --}}
                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Important:</p>
                            <p class="text-sm text-yellow-700 mt-1">
                                Approving this request will permanently delete the property and all associated data. This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Approve Modal --}}
    @if($deletionRequest->status === 'pending' && $deletionRequest->property)
    <div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Confirm Property Deletion</h3>
                    <p class="text-sm text-gray-600">This action cannot be undone</p>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                <p class="text-sm text-yellow-800">
                    <strong>Warning:</strong> You are about to permanently delete
                    "{{ $deletionRequest->property->title }}" and all its associated data.
                </p>
            </div>

            <form action="{{ route('admin.properties.deletion-requests.approve', $deletionRequest) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
                    <textarea name="admin_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Add any notes about this approval..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 font-medium">
                        Yes, Delete Property
                    </button>
                    <button type="button"
                            onclick="document.getElementById('approveModal').classList.add('hidden')"
                            class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Reject Deletion Request</h3>
            <p class="text-sm text-gray-600 mb-4">
                Please provide a reason for rejecting this deletion request. The landlord will receive your feedback.
            </p>

            <form action="{{ route('admin.properties.deletion-requests.reject', $deletionRequest) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection *</label>
                    <textarea name="admin_notes" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Explain why this deletion request is being rejected..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 font-medium">
                        Reject Request
                    </button>
                    <button type="button"
                            onclick="document.getElementById('rejectModal').classList.add('hidden')"
                            class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection