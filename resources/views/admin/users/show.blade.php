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

    {{-- Valid ID Section (for landlords) --}}
    @if($user->role === 'landlord' && $user->valid_id_path)
    <div class="px-6 py-4 border-t">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🆔 Valid ID Document</h3>
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600">Uploaded ID Document</span>
                <span class="text-xs text-gray-500">Click to view full size</span>
            </div>
            <div class="relative">
                <img src="{{ asset('storage/' . $user->valid_id_path) }}"
                     alt="Valid ID"
                     class="w-full max-w-md mx-auto rounded-lg border shadow-sm cursor-pointer hover:shadow-md transition-shadow"
                     onclick="viewIDDocument('{{ asset('storage/' . $user->valid_id_path) }}')">
            </div>
        </div>
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