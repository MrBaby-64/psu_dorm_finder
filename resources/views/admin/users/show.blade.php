{{-- User Details Modal Content --}}
<div class="max-h-[80vh] overflow-y-auto">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover">
                    @else
                        <span class="text-lg font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                    <p class="text-blue-100">{{ $user->email }}</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="px-2 py-1 bg-white bg-opacity-20 rounded-full text-xs font-medium">
                            {{ ucfirst($user->role) }}
                        </span>
                        @if($user->is_verified)
                            <span class="px-2 py-1 bg-green-500 rounded-full text-xs font-medium">✓ Verified</span>
                        @else
                            <span class="px-2 py-1 bg-yellow-500 rounded-full text-xs font-medium">⚠ Unverified</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-blue-100">Member since</div>
                <div class="font-medium">{{ $user->created_at->format('M d, Y') }}</div>
                <div class="text-xs text-blue-200">{{ $stats['account_age_days'] }} days ago</div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="px-6 py-4 bg-gray-50 border-b">
        <div class="grid grid-cols-3 gap-4">
            @if($user->role === 'landlord')
                <div class="bg-white p-3 rounded-lg border text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['total_properties'] }}</div>
                    <div class="text-sm text-gray-600">Properties</div>
                </div>
                <div class="bg-white p-3 rounded-lg border text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['active_properties'] }}</div>
                    <div class="text-sm text-gray-600">Active</div>
                </div>
            @elseif($user->role === 'tenant')
                <div class="bg-white p-3 rounded-lg border text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['total_bookings'] }}</div>
                    <div class="text-sm text-gray-600">Bookings</div>
                </div>
                <div class="bg-white p-3 rounded-lg border text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total_inquiries'] }}</div>
                    <div class="text-sm text-gray-600">Inquiries</div>
                </div>
            @endif
            <div class="bg-white p-3 rounded-lg border text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['total_reviews'] }}</div>
                <div class="text-sm text-gray-600">Reviews</div>
            </div>
        </div>
    </div>

    {{-- User Information --}}
    <div class="px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Personal Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Full Name</label>
                    <div class="text-gray-900">{{ $user->name }}</div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Email Address</label>
                    <div class="text-gray-900">{{ $user->email }}</div>
                    @if($user->email_verified_at)
                        <span class="inline-flex items-center text-xs text-green-600">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Email Verified
                        </span>
                    @else
                        <span class="inline-flex items-center text-xs text-red-600">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Email Not Verified
                        </span>
                    @endif
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Phone Number</label>
                    <div class="text-gray-900">{{ $user->phone ?: 'Not provided' }}</div>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Address</label>
                    <div class="text-gray-900">{{ $user->address ?: 'Not provided' }}</div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">City</label>
                    <div class="text-gray-900">{{ $user->city ?: 'Not provided' }}</div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Province</label>
                    <div class="text-gray-900">{{ $user->province ?: 'Not provided' }}</div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Account Status</label>
                    <div class="flex items-center space-x-2">
                        @if($user->is_verified)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ Verified
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                ⚠ Unverified
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tenant ID Section (for tenants) --}}
    @if($user->role === 'tenant')
    <div class="px-6 py-4 border-t">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🆔 Valid ID / School ID</h3>

        @if($user->tenant_id_path)
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Uploaded ID Document</span>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($user->tenant_id_verification_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($user->tenant_id_verification_status === 'approved') bg-green-100 text-green-800
                                @elseif($user->tenant_id_verification_status === 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($user->tenant_id_verification_status ?? 'Pending') }}
                            </span>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500">Click to view full size</span>
                </div>
                <div class="relative">
                    <img src="{{ asset('storage/' . $user->tenant_id_path) }}"
                         alt="Tenant ID"
                         class="w-full max-w-md mx-auto rounded-lg border shadow-sm cursor-pointer hover:shadow-md transition-shadow"
                         onclick="window.open('{{ asset('storage/' . $user->tenant_id_path) }}', '_blank')">
                </div>

                @if($user->tenant_id_verification_status === 'pending')
                    <div class="mt-4 flex items-center justify-center space-x-3">
                        <form action="{{ route('admin.tenants.approve-id', $user) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to approve {{ $user->name }}\'s ID and verify their account?')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Approve ID & Verify Account
                            </button>
                        </form>

                        <form action="{{ route('admin.tenants.reject-id', $user) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to reject {{ $user->name }}\'s ID?')"
                                    class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reject ID
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">No ID Uploaded</h3>
                        <div class="mt-1 text-sm text-yellow-700">
                            This tenant has not uploaded any ID document yet.
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @endif

    {{-- Suspension Section (for tenants) --}}
    @if($user->role === 'tenant')
    <div class="px-6 py-4 border-t">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">⚠️ Account Suspension Management</h3>

        @if($user->is_suspended)
            {{-- Currently Suspended --}}
            <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4 mb-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h4 class="text-sm font-bold text-red-900">⛔ ACCOUNT CURRENTLY SUSPENDED</h4>
                        <div class="mt-2 text-sm text-red-800">
                            @php
                                $activeSuspension = $user->activeSuspension;
                            @endphp
                            @if($activeSuspension)
                                <p><strong>Warning Level:</strong> {{ $activeSuspension->warning_level }}</p>
                                <p><strong>Duration:</strong> {{ $activeSuspension->duration_text }}</p>
                                <p><strong>Suspended On:</strong> {{ $activeSuspension->suspended_at->format('M d, Y g:i A') }}</p>
                                @if($activeSuspension->expires_at)
                                    <p><strong>Expires On:</strong> {{ $activeSuspension->expires_at->format('M d, Y g:i A') }}</p>
                                    <p><strong>Time Remaining:</strong> {{ $activeSuspension->expires_at->diffForHumans() }}</p>
                                @else
                                    <p class="font-bold text-red-900">⛔ PERMANENT BAN</p>
                                @endif
                                <p class="mt-2"><strong>Reason:</strong> {{ $activeSuspension->reason }}</p>
                                @if($activeSuspension->admin_notes)
                                    <p class="mt-1"><strong>Admin Notes:</strong> {{ $activeSuspension->admin_notes }}</p>
                                @endif
                                <p class="mt-2 text-xs"><strong>Suspended By:</strong> {{ $activeSuspension->suspendedBy->name ?? 'Unknown Admin' }}</p>
                            @endif
                        </div>
                        <div class="mt-3">
                            <form action="{{ route('admin.users.lift-suspension', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to lift the suspension for {{ $user->name }}? They will be able to login again immediately.')"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors text-sm font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                    </svg>
                                    Lift Suspension
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Not Suspended - Show Suspend Button --}}
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Account Status: <span class="text-green-600 font-bold">✓ Active</span></p>
                        <p class="text-xs text-gray-500 mt-1">Total Warnings: {{ $user->suspension_count }}</p>
                    </div>
                    <button onclick="openSuspendModal()"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                        Suspend Account
                    </button>
                </div>
            </div>
        @endif

        {{-- Suspension History --}}
        @if($user->suspensions && $user->suspensions->count() > 0)
        <div class="mt-4">
            <h4 class="text-sm font-semibold text-gray-900 mb-3">📜 Suspension History ({{ $user->suspensions->count() }} total)</h4>
            <div class="space-y-2">
                @foreach($user->suspensions()->latest()->take(5)->get() as $suspension)
                <div class="bg-white border rounded-lg p-3 text-sm">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="font-medium {{ $suspension->is_active ? 'text-red-600' : 'text-gray-600' }}">
                                    {{ $suspension->warning_level }} - {{ $suspension->duration_text }}
                                </span>
                                @if($suspension->is_active)
                                    <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-xs font-medium">Active</span>
                                @else
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Ended</span>
                                @endif
                            </div>
                            <p class="text-gray-700"><strong>Reason:</strong> {{ $suspension->reason }}</p>
                            @if($suspension->admin_notes)
                                <p class="text-gray-600 text-xs mt-1"><strong>Notes:</strong> {{ $suspension->admin_notes }}</p>
                            @endif
                            <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                <span>Suspended: {{ $suspension->suspended_at->format('M d, Y') }}</span>
                                @if($suspension->expires_at)
                                    <span>Expires: {{ $suspension->expires_at->format('M d, Y') }}</span>
                                @endif
                                @if($suspension->lifted_at)
                                    <span class="text-green-600">Lifted: {{ $suspension->lifted_at->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Property Documents Section (for landlords) --}}
    @if($user->role === 'landlord')
    <div class="px-6 py-4 border-t">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📄 Property Ownership Documents</h3>

        @if($user->property_documents_path)
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Uploaded Property Documents</span>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($user->document_verification_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($user->document_verification_status === 'approved') bg-green-100 text-green-800
                                @elseif($user->document_verification_status === 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($user->document_verification_status ?? 'Pending') }}
                            </span>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500">Click to view full size</span>
                </div>
                <div class="relative">
                    <img src="{{ asset('storage/' . $user->property_documents_path) }}"
                         alt="Property Documents"
                         class="w-full max-w-md mx-auto rounded-lg border shadow-sm cursor-pointer hover:shadow-md transition-shadow"
                         onclick="window.open('{{ asset('storage/' . $user->property_documents_path) }}', '_blank')">
                </div>

                @if($user->document_verification_status === 'pending')
                    <div class="mt-4 flex items-center justify-center space-x-3">
                        <form action="{{ route('admin.landlords.approve-id', $user) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to approve {{ $user->name }}\'s property documents?')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Approve Documents
                            </button>
                        </form>

                        <form action="{{ route('admin.landlords.reject-id', $user) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to reject {{ $user->name }}\'s property documents?')"
                                    class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reject Documents
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">No Property Documents Uploaded</h3>
                        <div class="mt-1 text-sm text-yellow-700">
                            This landlord has not uploaded any property ownership documents yet. They won't be able to post properties until documents are verified.
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @endif

    {{-- Recent Activity --}}
    <div class="px-6 py-4 border-t">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Account Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Account Details --}}
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Account Details</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Registration Date:</span>
                        <span class="text-gray-900">{{ $user->created_at->format('M d, Y g:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Last Updated:</span>
                        <span class="text-gray-900">{{ $user->updated_at->format('M d, Y g:i A') }}</span>
                    </div>
                    @if($user->email_verified_at)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email Verified:</span>
                        <span class="text-gray-900">{{ $user->email_verified_at->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Activity Summary --}}
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Activity Summary</h4>
                <div class="space-y-2 text-sm">
                    @if($user->role === 'landlord')
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Properties:</span>
                            <span class="text-gray-900">{{ $stats['total_properties'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Active Properties:</span>
                            <span class="text-gray-900">{{ $stats['active_properties'] }}</span>
                        </div>
                    @elseif($user->role === 'tenant')
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Bookings:</span>
                            <span class="text-gray-900">{{ $stats['total_bookings'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Inquiries:</span>
                            <span class="text-gray-900">{{ $stats['total_inquiries'] }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Reviews Written:</span>
                        <span class="text-gray-900">{{ $stats['total_reviews'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Unread Notifications:</span>
                        <span class="text-gray-900">{{ $stats['unread_notifications'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tenant Activity Tabs --}}
    @if($user->role === 'tenant')
    <div class="px-6 py-4 border-t">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Tenant Activity</h3>

        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 mb-4">
            <nav class="-mb-px flex space-x-8">
                <button onclick="switchTab('bookings')" id="bookings-tab" class="tenant-tab py-2 px-1 border-b-2 border-purple-500 text-purple-600 font-medium text-sm whitespace-nowrap">
                    📋 Bookings ({{ $stats['total_bookings'] }})
                </button>
                <button onclick="switchTab('inquiries')" id="inquiries-tab" class="tenant-tab py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm whitespace-nowrap">
                    ❓ Inquiries ({{ $stats['total_inquiries'] }})
                </button>
                <button onclick="switchTab('reviews')" id="reviews-tab" class="tenant-tab py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm whitespace-nowrap">
                    ⭐ Reviews ({{ $stats['total_reviews'] }})
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div id="tab-content">
            <!-- Bookings Tab Content -->
            <div id="bookings-content" class="tab-content">
                @if($user->bookings && $user->bookings->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->bookings->take(5) as $booking)
                            <div class="bg-gray-50 rounded-lg p-4 border">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $booking->property->title ?? 'Property Deleted' }}</h4>
                                        <div class="text-sm text-gray-600 mt-1">
                                            <span>Check-in: {{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</span>
                                            <span class="mx-2">•</span>
                                            <span>Check-out: {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Submitted: {{ $booking->created_at->format('M d, Y g:i A') }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($booking->status === 'approved') bg-green-100 text-green-800
                                            @elseif($booking->status === 'rejected') bg-red-100 text-red-800
                                            @elseif($booking->status === 'active') bg-blue-100 text-blue-800
                                            @elseif($booking->status === 'completed') bg-gray-100 text-gray-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if($user->bookings->count() > 5)
                            <div class="text-center text-sm text-gray-500 mt-3">
                                Showing 5 of {{ $user->bookings->count() }} bookings
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p>No bookings found</p>
                    </div>
                @endif
            </div>

            <!-- Inquiries Tab Content -->
            <div id="inquiries-content" class="tab-content hidden">
                @if($user->inquiries && $user->inquiries->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->inquiries->take(5) as $inquiry)
                            <div class="bg-gray-50 rounded-lg p-4 border">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $inquiry->property->title ?? 'Property Deleted' }}</h4>
                                        <div class="text-sm text-gray-600 mt-1">
                                            @if($inquiry->move_in_date)
                                                <span>Move-in: {{ \Carbon\Carbon::parse($inquiry->move_in_date)->format('M d, Y') }}</span>
                                            @endif
                                            @if($inquiry->move_out_date)
                                                <span class="mx-2">•</span>
                                                <span>Move-out: {{ \Carbon\Carbon::parse($inquiry->move_out_date)->format('M d, Y') }}</span>
                                            @endif
                                        </div>
                                        @if($inquiry->message)
                                            <div class="text-sm text-gray-600 mt-1">
                                                Message: "{{ Str::limit($inquiry->message, 100) }}"
                                            </div>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">
                                            Submitted: {{ $inquiry->created_at->format('M d, Y g:i A') }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($inquiry->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($inquiry->status === 'approved') bg-green-100 text-green-800
                                            @elseif($inquiry->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($inquiry->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if($user->inquiries->count() > 5)
                            <div class="text-center text-sm text-gray-500 mt-3">
                                Showing 5 of {{ $user->inquiries->count() }} inquiries
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p>No inquiries found</p>
                    </div>
                @endif
            </div>

            <!-- Reviews Tab Content -->
            <div id="reviews-content" class="tab-content hidden">
                @if($user->reviews && $user->reviews->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->reviews->take(5) as $review)
                            <div class="bg-gray-50 rounded-lg p-4 border">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $review->property->title ?? 'Property Deleted' }}</h4>
                                        <div class="flex items-center mt-1">
                                            <div class="flex text-yellow-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                        </svg>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="ml-2 text-sm text-gray-600">({{ $review->rating }}/5)</span>
                                        </div>
                                        @if($review->comment)
                                            <div class="text-sm text-gray-600 mt-2">
                                                "{{ Str::limit($review->comment, 150) }}"
                                            </div>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-2">
                                            Posted: {{ $review->created_at->format('M d, Y g:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if($user->reviews->count() > 5)
                            <div class="text-center text-sm text-gray-500 mt-3">
                                Showing 5 of {{ $user->reviews->count() }} reviews
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        <p>No reviews found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Admin Actions --}}
    <div class="px-6 py-4 bg-gray-50 border-t">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">⚡ Admin Actions</h3>
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Use these actions to manage the user's account status and role.
            </div>
            <div class="flex space-x-3">
                <form action="{{ route('admin.users.verify', $user) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Are you sure you want to {{ $user->is_verified ? 'unverify' : 'verify' }} this user?')"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg
                            {{ $user->is_verified ? 'text-red-700 bg-red-100 hover:bg-red-200 focus:ring-red-500' : 'text-green-700 bg-green-100 hover:bg-green-200 focus:ring-green-500' }}
                            focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                        {{ $user->is_verified ? '✗ Unverify User' : '✓ Verify User' }}
                    </button>
                </form>

                <button onclick="closeUserDetailsModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@if($user->role === 'tenant')
<script>
function switchTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => content.classList.add('hidden'));

    // Remove active styles from all tabs
    const tabs = document.querySelectorAll('.tenant-tab');
    tabs.forEach(tab => {
        tab.classList.remove('border-purple-500', 'text-purple-600', 'border-blue-500', 'text-blue-600', 'border-orange-500', 'text-orange-600');
        tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });

    // Show selected tab content
    const selectedContent = document.getElementById(tabName + '-content');
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }

    // Add active styles to selected tab
    const selectedTab = document.getElementById(tabName + '-tab');
    if (selectedTab) {
        selectedTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');

        // Set appropriate color based on tab
        if (tabName === 'bookings') {
            selectedTab.classList.add('border-purple-500', 'text-purple-600');
        } else if (tabName === 'inquiries') {
            selectedTab.classList.add('border-blue-500', 'text-blue-600');
        } else if (tabName === 'reviews') {
            selectedTab.classList.add('border-orange-500', 'text-orange-600');
        }
    }
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    // Default to bookings tab
    switchTab('bookings');
});

// Suspension modal functions
function openSuspendModal() {
    document.getElementById('suspendModal').classList.remove('hidden');
}

function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
}

// Update recommended duration based on warning count
function updateRecommendedDuration() {
    const warningCount = {{ $user->suspension_count ?? 0 }};
    const recommendations = document.querySelectorAll('.duration-recommendation');

    recommendations.forEach(rec => rec.classList.add('hidden'));

    if (warningCount === 0) {
        document.getElementById('rec-1-day').classList.remove('hidden');
    } else if (warningCount === 1) {
        document.getElementById('rec-3-days').classList.remove('hidden');
    } else {
        document.getElementById('rec-permanent').classList.remove('hidden');
    }
}

// Initialize recommendations on page load
document.addEventListener('DOMContentLoaded', function() {
    updateRecommendedDuration();
});
</script>

<!-- Suspension Modal -->
<div id="suspendModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-8 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-4 mb-4 border-b">
            <h3 class="text-2xl font-bold text-gray-900">Suspend Tenant Account</h3>
            <button onclick="closeSuspendModal()" class="text-gray-400 hover:text-gray-600 text-3xl font-bold">&times;</button>
        </div>

        <!-- Warning Info -->
        <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Warning {{ $user->suspension_count + 1 }}:</strong>
                        @if($user->suspension_count == 0)
                            This is the first warning for {{ $user->name }}.
                        @elseif($user->suspension_count == 1)
                            This is the second warning for {{ $user->name }}.
                        @elseif($user->suspension_count >= 2)
                            This is the final warning for {{ $user->name }}. Consider permanent ban.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Suspension Form -->
        <form action="{{ route('admin.users.suspend', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to suspend this tenant? They will not be able to login until the suspension is lifted.');">
            @csrf

            <!-- Duration Selection -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Suspension Duration <span class="text-red-500">*</span></label>

                <div class="space-y-3">
                    <!-- 1 Day Option -->
                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="duration_type" value="1_day" class="mt-1 h-4 w-4 text-blue-600" required>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-gray-900">1 Day Suspension</span>
                                <span id="rec-1-day" class="duration-recommendation hidden text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Recommended</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Suitable for first-time minor violations</p>
                        </div>
                    </label>

                    <!-- 3 Days Option -->
                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="duration_type" value="3_days" class="mt-1 h-4 w-4 text-blue-600" required>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-gray-900">3 Days Suspension</span>
                                <span id="rec-3-days" class="duration-recommendation hidden text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Recommended</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">For second warnings or moderate violations</p>
                        </div>
                    </label>

                    <!-- Permanent Option -->
                    <label class="flex items-start p-4 border-2 border-red-300 rounded-lg cursor-pointer hover:bg-red-50 transition">
                        <input type="radio" name="duration_type" value="permanent" class="mt-1 h-4 w-4 text-red-600" required>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-red-900">Permanent Ban</span>
                                <span id="rec-permanent" class="duration-recommendation hidden text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Recommended</span>
                            </div>
                            <p class="text-sm text-red-600 mt-1">Account will be permanently banned. Use for severe violations or third warnings.</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Reason (Required) -->
            <div class="mb-6">
                <label for="reason" class="block text-sm font-semibold text-gray-700 mb-2">
                    Reason for Suspension <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="reason"
                    name="reason"
                    rows="4"
                    required
                    maxlength="1000"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter the reason for this suspension (will be visible to the tenant)..."
                ></textarea>
                <p class="text-xs text-gray-500 mt-1">This reason will be shown to the tenant. Be clear and professional.</p>
            </div>

            <!-- Admin Notes (Optional) -->
            <div class="mb-6">
                <label for="admin_notes" class="block text-sm font-semibold text-gray-700 mb-2">
                    Admin Notes (Internal Only)
                </label>
                <textarea
                    id="admin_notes"
                    name="admin_notes"
                    rows="3"
                    maxlength="2000"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Internal notes for admin records only (optional)..."
                ></textarea>
                <p class="text-xs text-gray-500 mt-1">These notes are for admin use only and will not be shown to the tenant.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button
                    type="button"
                    onclick="closeSuspendModal()"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition"
                >
                    Suspend Account
                </button>
            </div>
        </form>
    </div>
</div>

@endif