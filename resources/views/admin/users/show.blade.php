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
</script>
@endif