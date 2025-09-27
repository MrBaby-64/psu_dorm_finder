{{-- Simple User Details for Testing --}}
<div class="p-6">
    <h2 class="text-xl font-bold mb-4">User Information</h2>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-medium text-gray-500">Name:</label>
            <div>{{ $user->name }}</div>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-500">Email:</label>
            <div>{{ $user->email }}</div>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-500">Role:</label>
            <div>{{ ucfirst($user->role) }}</div>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-500">Status:</label>
            <div>{{ $user->is_verified ? 'Verified' : 'Unverified' }}</div>
        </div>

        @if($user->phone)
        <div>
            <label class="text-sm font-medium text-gray-500">Phone:</label>
            <div>{{ $user->phone }}</div>
        </div>
        @endif

        @if($user->address)
        <div>
            <label class="text-sm font-medium text-gray-500">Address:</label>
            <div>{{ $user->address }}</div>
        </div>
        @endif

        @if($user->city)
        <div>
            <label class="text-sm font-medium text-gray-500">City:</label>
            <div>{{ $user->city }}</div>
        </div>
        @endif

        @if($user->province)
        <div>
            <label class="text-sm font-medium text-gray-500">Province:</label>
            <div>{{ $user->province }}</div>
        </div>
        @endif
    </div>

    {{-- Stats --}}
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-3">Statistics</h3>
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold">{{ $stats['total_properties'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Properties</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold">{{ $stats['total_bookings'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Bookings</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold">{{ $stats['total_reviews'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Reviews</div>
            </div>
        </div>
    </div>

    {{-- Valid ID for landlords --}}
    @if($user->role === 'landlord' && $user->valid_id_path)
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-3">Valid ID Document</h3>
        <img src="{{ asset('storage/' . $user->valid_id_path) }}"
             alt="Valid ID"
             class="max-w-md rounded border shadow-sm cursor-pointer"
             onclick="viewIDDocument('{{ asset('storage/' . $user->valid_id_path) }}')">
    </div>
    @endif
</div>