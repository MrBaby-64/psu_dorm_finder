{{-- resources/views/landlord/properties/index.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Properties - Landlord Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    
    @include('layouts.navigation')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">My Properties</h1>
            <a href="{{ route('landlord.properties.create') }}" 
               class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                + Add New Property
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if($properties->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stats</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($properties as $property)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $property->title }}</div>
                            <div class="text-sm text-gray-500">{{ $property->room_count }} rooms</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $property->city }}</div>
                            <div class="text-xs text-gray-500">{{ $property->barangay }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-green-600">₱{{ number_format($property->price, 0) }}</div>
                            <div class="text-xs text-gray-500">/month</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($property->approval_status === 'approved')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                            @elseif($property->approval_status === 'pending')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                            @endif
                            
                            @if($property->is_verified)
                                <div class="text-xs text-blue-600 mt-1">✓ PSU Verified</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div>{{ $property->bookings_count }} bookings</div>
                            <div>{{ $property->reviews_count }} reviews</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <a href="{{ route('properties.show', $property) }}" 
                                   class="text-blue-600 hover:text-blue-900">View</a>
                                <a href="{{ route('landlord.properties.edit', $property) }}" 
                                   class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('landlord.properties.destroy', $property) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure?')" 
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $properties->links() }}
        </div>

        @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No properties yet</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating your first property listing.</p>
            <div class="mt-6">
                <a href="{{ route('landlord.properties.create') }}" 
                   class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 inline-block">
                    + Add New Property
                </a>
            </div>
        </div>
        @endif

    </div>

</body>
</html>