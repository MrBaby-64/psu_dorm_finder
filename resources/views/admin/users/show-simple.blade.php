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

    {{-- Tenant ID for tenants --}}
    @if($user->role === 'tenant')
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-3">Valid ID / School ID</h3>
        @if($user->tenant_id_path)
            <div class="mb-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($user->tenant_id_verification_status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($user->tenant_id_verification_status === 'approved') bg-green-100 text-green-800
                    @elseif($user->tenant_id_verification_status === 'rejected') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($user->tenant_id_verification_status ?? 'Pending') }}
                </span>
            </div>
            <img src="{{ asset('storage/' . $user->tenant_id_path) }}"
                 alt="Tenant ID"
                 class="max-w-md rounded border shadow-sm cursor-pointer"
                 onclick="window.open('{{ asset('storage/' . $user->tenant_id_path) }}', '_blank')">
        @else
            <p class="text-sm text-gray-500">No ID uploaded</p>
        @endif
    </div>
    @endif

    {{-- Property Documents for landlords --}}
    @if($user->role === 'landlord')
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-3">Property Ownership Documents</h3>
        @if($user->property_documents_path)
            <div class="mb-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($user->document_verification_status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($user->document_verification_status === 'approved') bg-green-100 text-green-800
                    @elseif($user->document_verification_status === 'rejected') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($user->document_verification_status ?? 'Pending') }}
                </span>
            </div>
            <img src="{{ asset('storage/' . $user->property_documents_path) }}"
                 alt="Property Documents"
                 class="max-w-md rounded border shadow-sm cursor-pointer"
                 onclick="window.open('{{ asset('storage/' . $user->property_documents_path) }}', '_blank')">
        @else
            <p class="text-sm text-gray-500">No property documents uploaded</p>
        @endif
    </div>
    @endif
</div>