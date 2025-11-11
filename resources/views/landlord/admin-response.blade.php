@extends('layouts.account')

@section('title', 'Admin Response')

@section('content')
<div class="py-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('landlord.notifications') }}"
           class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Notifications
        </a>

        <div class="flex-1">
            <h1 class="text-3xl font-bold">Admin Response</h1>
            <div class="flex items-center gap-4 mt-2">
                <span class="px-3 py-1 text-sm font-medium rounded bg-indigo-100 text-indigo-800">
                    🛡️ Response from Admin
                </span>
                <span class="text-sm text-gray-600">
                    Received {{ $notification->created_at->format('M j, Y g:i A') }}
                </span>
            </div>
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

    <div class="grid lg:grid-cols-2 gap-8">
        {{-- Your Original Message --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Your Original Message</h2>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Subject</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $adminMessage->subject }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Your Message</label>
                        <div class="mt-1 p-4 bg-gray-50 rounded-md border">
                            <p class="text-gray-800 whitespace-pre-wrap">{{ $adminMessage->message }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Sent On</label>
                        <div class="mt-1 text-sm text-gray-600">
                            {{ $adminMessage->created_at->format('M j, Y g:i A') }}
                            <span class="text-gray-400">({{ $adminMessage->created_at->diffForHumans() }})</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Property Information (if applicable) --}}
            @if($adminMessage->property)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Related Property</h3>

                <div class="space-y-3">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $adminMessage->property->title }}</h4>
                        <p class="text-sm text-gray-600">{{ $adminMessage->property->location_text }}</p>
                        <p class="text-sm text-gray-500">{{ $adminMessage->property->city }}, {{ $adminMessage->property->barangay }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-600">Monthly Rate:</span>
                            <p class="font-medium text-green-600">₱{{ number_format($adminMessage->property->price, 0) }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Rooms:</span>
                            <p class="font-medium">{{ $adminMessage->property->room_count }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('properties.show', $adminMessage->property) }}"
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
                                <strong>Response from:</strong> {{ $adminMessage->responder->name ?? 'PSU Dorm Finder Admin' }}
                                <br>
                                <strong>Sent:</strong> {{ $adminMessage->responded_at->format('M j, Y g:i A') }}
                                <span class="text-indigo-600">({{ $adminMessage->responded_at->diffForHumans() }})</span>
                            </div>
                            <div class="text-gray-800">
                                <p class="whitespace-pre-wrap leading-relaxed">{{ $adminMessage->admin_response }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-blue-700">
                            <p class="font-medium">Need more help?</p>
                            <p class="mt-1">If you need further assistance or have follow-up questions, you can submit a new message to the admin through your property management page.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">What's Next?</h3>

                <div class="space-y-3">
                    <a href="{{ route('landlord.properties.index') }}"
                       class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition font-medium text-center block">
                        Go to My Properties
                    </a>

                    <a href="{{ route('landlord.notifications') }}"
                       class="w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-200 transition font-medium text-center block">
                        Back to All Notifications
                    </a>

                    @if($adminMessage->property)
                    <a href="{{ route('properties.show', $adminMessage->property) }}"
                       target="_blank"
                       class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition font-medium text-center block">
                        View Related Property
                    </a>
                    @endif
                </div>
            </div>
        </div>
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