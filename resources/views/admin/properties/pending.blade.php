{{-- resources/views/admin/properties/pending.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Properties - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    
    @include('layouts.navigation')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <h1 class="text-3xl font-bold mb-6">Property Approval Queue</h1>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if($properties->count() > 0)
        <div class="space-y-4">
            @foreach($properties as $property)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold">{{ $property->title }}</h3>
                        <p class="text-gray-600 mt-1">{{ $property->location_text }}</p>
                        <p class="text-sm text-gray-500">{{ $property->city }}, {{ $property->barangay }}</p>
                        
                        <div class="mt-3">
                            <span class="font-semibold text-green-600">₱{{ number_format($property->price, 0) }}/month</span>
                            <span class="text-gray-500 mx-2">•</span>
                            <span class="text-gray-600">{{ $property->room_count }} rooms</span>
                        </div>

                        <p class="mt-3 text-gray-700">{{ Str::limit($property->description, 200) }}</p>

                        <div class="mt-3 text-sm text-gray-500">
                            <strong>Landlord:</strong> {{ $property->landlord->name }} ({{ $property->landlord->email }})
                        </div>

                        <div class="mt-2 text-sm text-gray-500">
                            <strong>Location:</strong> Lat {{ $property->latitude }}, Lng {{ $property->longitude }}
                        </div>
                    </div>

                    <div class="ml-6">
                        <a href="{{ route('properties.show', $property) }}" 
                           target="_blank"
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            View Full Details →
                        </a>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <form action="{{ route('admin.properties.approve', $property) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                            ✓ Approve
                        </button>
                    </form>

                    <button onclick="document.getElementById('rejectModal{{ $property->id }}').classList.remove('hidden')"
                            class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">
                        ✗ Reject
                    </button>

                    <form action="{{ route('admin.properties.verify', $property) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="border border-blue-600 text-blue-600 px-6 py-2 rounded-lg hover:bg-blue-50">
                            {{ $property->is_verified ? 'Unverify' : 'Mark as PSU Verified' }}
                        </button>
                    </form>
                </div>

                {{-- Reject Modal --}}
                <div id="rejectModal{{ $property->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                        <h3 class="text-xl font-bold mb-4">Reject Property</h3>
                        <form action="{{ route('admin.properties.reject', $property) }}" method="POST">
                            @csrf
                            <textarea name="rejection_reason" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md mb-4"
                                      placeholder="Reason for rejection (optional)"></textarea>
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 bg-red-600 text-white py-2 rounded-md hover:bg-red-700">
                                    Confirm Rejection
                                </button>
                                <button type="button" 
                                        onclick="document.getElementById('rejectModal{{ $property->id }}').classList.add('hidden')"
                                        class="flex-1 border border-gray-300 py-2 rounded-md hover:bg-gray-50">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $properties->links() }}
        </div>

        @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <h3 class="text-lg font-medium text-gray-900">No pending properties</h3>
            <p class="mt-1 text-sm text-gray-500">All properties have been reviewed!</p>
        </div>
        @endif

    </div>

</body>
</html>