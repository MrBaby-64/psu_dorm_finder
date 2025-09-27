@extends('layouts.account')

@section('title', 'Scheduled Visits')

@push('styles')
<style>
    .visit-status-badge {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
    }
    .status-pending { @apply bg-yellow-100 text-yellow-800; }
    .status-confirmed { @apply bg-green-100 text-green-800; }
    .status-completed { @apply bg-blue-100 text-blue-800; }
    .status-cancelled { @apply bg-red-100 text-red-800; }
    .status-no_show { @apply bg-gray-100 text-gray-800; }

    .priority-urgent { @apply border-l-4 border-red-500; }
    .priority-today { @apply border-l-4 border-orange-500; }
    .priority-upcoming { @apply border-l-4 border-blue-500; }
</style>
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Header with Action Buttons -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Visit Scheduling Center</h1>
                    <p class="text-gray-600 mt-1">Manage property visits and tenant appointments</p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- View Toggle -->
                    <div class="bg-gray-100 p-1 rounded-lg">
                        <button onclick="toggleView('list')" id="listViewBtn" class="px-3 py-1 text-sm font-medium rounded-md bg-white text-gray-700 shadow-sm">
                            📋 List
                        </button>
                        <button onclick="toggleView('calendar')" id="calendarViewBtn" class="px-3 py-1 text-sm font-medium rounded-md text-gray-700">
                            📅 Calendar
                        </button>
                    </div>

                    <!-- Quick Actions -->
                    <button onclick="exportVisits()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">
                        📊 Export
                    </button>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div class="mt-6 border-t pt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Property Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Property</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" onchange="applyFilter('property_id', this.value)">
                            <option value="">All Properties</option>
                            @foreach($userProperties as $property)
                                <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                    {{ Str::limit($property->title, 30) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" onchange="applyFilter('status', this.value)">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" value="{{ request('from_date') }}"
                               onchange="applyFilter('from_date', this.value)"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date" value="{{ request('to_date') }}"
                               onchange="applyFilter('to_date', this.value)"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    </div>

                    <!-- Quick Filters -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quick Filter</label>
                        <select class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" onchange="applyQuickFilter(this.value)">
                            <option value="">All Visits</option>
                            <option value="today">Today's Visits</option>
                            <option value="tomorrow">Tomorrow's Visits</option>
                            <option value="this_week">This Week</option>
                            <option value="pending">Pending Confirmation</option>
                            <option value="confirmed">Confirmed Only</option>
                            <option value="overdue">Overdue Response</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Pending Visits (Urgent) -->
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg shadow p-6 border border-yellow-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-200">
                        <svg class="w-6 h-6 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-yellow-800">Awaiting Response</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ $visits->where('status', 'pending')->count() }}</p>
                        <p class="text-xs text-yellow-700 mt-1">Needs Action</p>
                    </div>
                </div>
            </div>

            <!-- Today's Visits -->
            @php
                $todayVisits = $visits->filter(function($visit) {
                    return $visit->confirmed_date && $visit->confirmed_date->isToday();
                });
            @endphp
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg shadow p-6 border border-orange-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-200">
                        <svg class="w-6 h-6 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v1a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-orange-800">Today's Visits</p>
                        <p class="text-2xl font-bold text-orange-900">{{ $todayVisits->count() }}</p>
                        <p class="text-xs text-orange-700 mt-1">{{ now()->format('M j, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Confirmed Visits -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow p-6 border border-green-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-200">
                        <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-800">Confirmed</p>
                        <p class="text-2xl font-bold text-green-900">{{ $visits->where('status', 'confirmed')->count() }}</p>
                        <p class="text-xs text-green-700 mt-1">Scheduled</p>
                    </div>
                </div>
            </div>

            <!-- Completed This Month -->
            @php
                $completedThisMonth = $visits->filter(function($visit) {
                    return $visit->status === 'completed' && $visit->visited_at && $visit->visited_at->isCurrentMonth();
                });
            @endphp
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow p-6 border border-blue-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-200">
                        <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-800">This Month</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $completedThisMonth->count() }}</p>
                        <p class="text-xs text-blue-700 mt-1">Completed</p>
                    </div>
                </div>
            </div>

            <!-- Conversion Rate -->
            @php
                $totalVisits = $visits->count();
                $convertedVisits = $visits->where('status', 'completed')->count();
                $conversionRate = $totalVisits > 0 ? round(($convertedVisits / $totalVisits) * 100) : 0;
            @endphp
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow p-6 border border-purple-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-200">
                        <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-purple-800">Success Rate</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $conversionRate }}%</p>
                        <p class="text-xs text-purple-700 mt-1">Visit Conversion</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Views -->
        <div id="listView" class="space-y-4">
            <!-- Priority Visits Alert -->
            @php
                $urgentVisits = $visits->filter(function($visit) {
                    return $visit->status === 'pending' && $visit->created_at->lt(now()->subHours(24));
                })->count();
            @endphp

            @if($urgentVisits > 0)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Urgent Action Required</h3>
                            <p class="text-sm text-red-700 mt-1">
                                {{ $urgentVisits }} visit request{{ $urgentVisits > 1 ? 's' : '' }} pending for over 24 hours. Prompt responses improve tenant satisfaction.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Visits List with Enhanced UI -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                @if($visits->count() > 0)
                    <!-- Table Header -->
                    <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <h3 class="text-sm font-medium text-gray-900">
                                    Showing {{ $visits->count() }} visit{{ $visits->count() > 1 ? 's' : '' }}
                                </div>
                            <div class="flex items-center space-x-2">
                                <button onclick="selectAll()" class="text-xs text-blue-600 hover:text-blue-800">
                                    Select All
                                </button>
                                <span class="text-gray-300">|</span>
                                <button onclick="bulkAction('confirm')" class="text-xs text-green-600 hover:text-green-800">
                                    Bulk Confirm
                                </button>
                                <button onclick="bulkAction('cancel')" class="text-xs text-red-600 hover:text-red-800">
                                    Bulk Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Visit Cards -->
                    <div class="divide-y divide-gray-200">
                        @foreach($visits as $visit)
                            @php
                                $priority = 'upcoming';
                                if ($visit->status === 'pending' && $visit->created_at->lt(now()->subHours(24))) {
                                    $priority = 'urgent';
                                } elseif ($visit->confirmed_date && $visit->confirmed_date->isToday()) {
                                    $priority = 'today';
                                }
                            @endphp

                            <div class="p-6 hover:bg-gray-50 transition-colors priority-{{ $priority }}" id="visit-{{ $visit->id }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4">
                                        <!-- Selection Checkbox -->
                                        <input type="checkbox" class="mt-1 visit-checkbox" value="{{ $visit->id }}"
                                               onchange="updateBulkActions()">

                                        <!-- Visit Details -->
                                        <div class="flex-1 min-w-0">
                                            <!-- Header -->
                                            <div class="flex items-center space-x-3 mb-3">
                                                <h3 class="text-lg font-semibold text-gray-900 truncate">
                                                    {{ $visit->property->title }}
                                                </h3>
                                                <span class="visit-status-badge status-{{ $visit->status }}">
                                                    {{ ucfirst($visit->status) }}
                                                </span>
                                                @if($priority === 'urgent')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        🚨 Urgent
                                                    </span>
                                                @elseif($priority === 'today')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                        📅 Today
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Tenant Info Card -->
                                            <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    <div>
                                                        <p class="text-sm"><span class="font-medium text-gray-700">👤 Tenant:</span>
                                                            <span class="text-gray-900">{{ $visit->tenant->name }}</span>
                                                        </p>
                                                        <p class="text-sm"><span class="font-medium text-gray-700">📧 Email:</span>
                                                            <a href="mailto:{{ $visit->tenant->email }}" class="text-blue-600 hover:text-blue-800">
                                                                {{ $visit->tenant->email }}
                                                            </a>
                                                        </p>
                                                        @if($visit->tenant->phone)
                                                            <p class="text-sm"><span class="font-medium text-gray-700">📱 Phone:</span>
                                                                <a href="tel:{{ $visit->tenant->phone }}" class="text-blue-600 hover:text-blue-800">
                                                                    {{ $visit->tenant->phone }}
                                                                </a>
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-sm"><span class="font-medium text-gray-700">🗓️ Requested:</span>
                                                            <span class="text-gray-900">{{ $visit->preferred_date->format('M j, Y') }}</span>
                                                        </p>
                                                        <p class="text-sm"><span class="font-medium text-gray-700">⏰ Preferred Time:</span>
                                                            <span class="text-gray-900">{{ $visit->preferred_time }}</span>
                                                        </p>
                                                        @if($visit->confirmed_date)
                                                            <p class="text-sm"><span class="font-medium text-green-700">✅ Confirmed:</span>
                                                                <span class="text-green-800 font-medium">
                                                                    {{ $visit->confirmed_date->format('M j, Y') }} at {{ $visit->confirmed_time }}
                                                                </span>
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Additional Info -->
                                            @if($visit->notes || $visit->landlord_response || $visit->cancellation_reason)
                                                <div class="space-y-2">
                                                    @if($visit->notes)
                                                        <div class="bg-blue-50 border-l-4 border-blue-200 p-3 rounded-r">
                                                            <p class="text-sm"><span class="font-medium text-blue-800">💭 Tenant Notes:</span></p>
                                                            <p class="text-sm text-blue-700 mt-1">{{ $visit->notes }}</p>
                                                        </div>
                                                    @endif

                                                    @if($visit->landlord_response)
                                                        <div class="bg-green-50 border-l-4 border-green-200 p-3 rounded-r">
                                                            <p class="text-sm"><span class="font-medium text-green-800">💬 Your Response:</span></p>
                                                            <p class="text-sm text-green-700 mt-1">{{ $visit->landlord_response }}</p>
                                                        </div>
                                                    @endif

                                                    @if($visit->cancellation_reason)
                                                        <div class="bg-red-50 border-l-4 border-red-200 p-3 rounded-r">
                                                            <p class="text-sm"><span class="font-medium text-red-800">❌ Cancellation:</span></p>
                                                            <p class="text-sm text-red-700 mt-1">{{ $visit->cancellation_reason }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- Timestamps -->
                                            <div class="flex items-center justify-between text-xs text-gray-400 mt-3 pt-3 border-t border-gray-100">
                                                <span>📝 Requested {{ $visit->created_at->diffForHumans() }}</span>
                                                @if($visit->visited_at)
                                                    <span>✅ Completed {{ $visit->visited_at->diffForHumans() }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions Panel -->
                                    <div class="flex flex-col items-end space-y-2 ml-6">
                                        @if($visit->status === 'pending')
                                            <div class="flex flex-col space-y-2">
                                                <button onclick="quickConfirm({{ $visit->id }})"
                                                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                                                    ✅ Quick Confirm
                                                </button>
                                                <button onclick="confirmVisit({{ $visit->id }})"
                                                       class="inline-flex items-center px-3 py-2 border border-green-600 text-sm font-medium rounded-md text-green-600 bg-white hover:bg-green-50 transition-colors">
                                                    📅 Schedule
                                                </button>
                                                <button onclick="cancelVisit({{ $visit->id }})"
                                                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                                    ❌ Decline
                                                </button>
                                            </div>
                                        @elseif($visit->status === 'confirmed')
                                            <div class="flex flex-col space-y-2">
                                                <button onclick="markCompleted({{ $visit->id }})"
                                                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                                    ✅ Complete
                                                </button>
                                                <button onclick="markNoShow({{ $visit->id }})"
                                                       class="inline-flex items-center px-3 py-2 border border-orange-600 text-sm font-medium rounded-md text-orange-600 bg-white hover:bg-orange-50 transition-colors">
                                                    👻 No Show
                                                </button>
                                                <button onclick="rescheduleVisit({{ $visit->id }})"
                                                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                                    📅 Reschedule
                                                </button>
                                            </div>
                                        @elseif($visit->status === 'completed')
                                            <div class="text-center">
                                                <div class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                    ✅ Completed
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $visit->visited_at ? $visit->visited_at->format('M j, Y') : 'Date not recorded' }}
                                                </p>
                                            </div>
                                        @endif

                                        <!-- Quick Contact -->
                                        <div class="flex space-x-2 mt-2">
                                            <a href="mailto:{{ $visit->tenant->email }}"
                                               class="text-gray-400 hover:text-blue-500 transition-colors"
                                               title="Send Email">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                                </svg>
                                            </a>
                                            @if($visit->tenant->phone)
                                                <a href="tel:{{ $visit->tenant->phone }}"
                                                   class="text-gray-400 hover:text-green-500 transition-colors"
                                                   title="Call Tenant">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-6 py-3 border-t border-gray-200">
                        {{ $visits->links() }}
                    </div>
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v1a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 17h2a2 2 0 002-2v-5a2 2 0 00-2-2H6a2 2 0 00-2 2v5a2 2 0 002 2h2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No scheduled visits</h3>
                    <p class="mt-1 text-sm text-gray-500">No tenants have scheduled visits to your properties yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Confirm Visit Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Visit</h3>
                    <form id="confirmForm" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Confirmed Date</label>
                                <input type="date" name="confirmed_date" required
                                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Confirmed Time</label>
                                <input type="time" name="confirmed_time" required
                                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Response Message (Optional)</label>
                                <textarea name="landlord_response" rows="3"
                                         class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                         placeholder="Any additional information for the tenant..."></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closeModal('confirmModal')"
                                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                   class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                Confirm Visit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Visit Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cancel Visit</h3>
                    <form id="cancelForm" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cancellation Reason</label>
                                <textarea name="reason" rows="3" required
                                         class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                         placeholder="Please provide a reason for cancellation..."></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closeModal('cancelModal')"
                                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Keep Visit
                            </button>
                            <button type="submit"
                                   class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                Cancel Visit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentView = 'list';
        let selectedVisits = [];

        // View Management
        function toggleView(view) {
            currentView = view;
            document.getElementById('listViewBtn').classList.toggle('bg-white', view === 'list');
            document.getElementById('listViewBtn').classList.toggle('shadow-sm', view === 'list');
            document.getElementById('calendarViewBtn').classList.toggle('bg-white', view === 'calendar');
            document.getElementById('calendarViewBtn').classList.toggle('shadow-sm', view === 'calendar');

            document.getElementById('listView').style.display = view === 'list' ? 'block' : 'none';
            document.getElementById('calendarView').style.display = view === 'calendar' ? 'block' : 'none';
        }

        // Enhanced Filtering
        function applyFilter(paramName, value) {
            const url = new URL(window.location);
            if (value) {
                url.searchParams.set(paramName, value);
            } else {
                url.searchParams.delete(paramName);
            }
            window.location.href = url.toString();
        }

        function applyQuickFilter(filter) {
            const url = new URL(window.location);

            // Clear existing filters
            url.searchParams.delete('status');
            url.searchParams.delete('from_date');
            url.searchParams.delete('to_date');

            const today = new Date().toISOString().split('T')[0];
            const tomorrow = new Date(Date.now() + 24*60*60*1000).toISOString().split('T')[0];
            const weekStart = new Date();
            weekStart.setDate(weekStart.getDate() - weekStart.getDay());
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekStart.getDate() + 6);

            switch(filter) {
                case 'today':
                    url.searchParams.set('from_date', today);
                    url.searchParams.set('to_date', today);
                    break;
                case 'tomorrow':
                    url.searchParams.set('from_date', tomorrow);
                    url.searchParams.set('to_date', tomorrow);
                    break;
                case 'this_week':
                    url.searchParams.set('from_date', weekStart.toISOString().split('T')[0]);
                    url.searchParams.set('to_date', weekEnd.toISOString().split('T')[0]);
                    break;
                case 'pending':
                    url.searchParams.set('status', 'pending');
                    break;
                case 'confirmed':
                    url.searchParams.set('status', 'confirmed');
                    break;
                case 'overdue':
                    url.searchParams.set('status', 'pending');
                    url.searchParams.set('overdue', '1');
                    break;
            }

            if (filter) {
                window.location.href = url.toString();
            }
        }

        // Bulk Actions
        function selectAll() {
            const checkboxes = document.querySelectorAll('.visit-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            updateBulkActions();
        }

        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.visit-checkbox:checked');
            selectedVisits = Array.from(checkboxes).map(cb => cb.value);

            // Update UI to show selected count
            const selectedCount = selectedVisits.length;
            if (selectedCount > 0) {
                showNotification(`${selectedCount} visit${selectedCount > 1 ? 's' : ''} selected`, 'info');
            }
        }

        function bulkAction(action) {
            if (selectedVisits.length === 0) {
                showNotification('Please select visits first', 'warning');
                return;
            }

            const actionText = action === 'confirm' ? 'confirm' : 'cancel';
            if (confirm(`Are you sure you want to ${actionText} ${selectedVisits.length} selected visit(s)?`)) {
                // Process bulk action
                processBulkAction(action, selectedVisits);
            }
        }

        function processBulkAction(action, visitIds) {
            const promises = visitIds.map(visitId => {
                const url = action === 'confirm'
                    ? `/landlord/scheduled-visits/${visitId}/confirm`
                    : `/landlord/scheduled-visits/${visitId}/cancel-by-landlord`;

                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        confirmed_date: new Date().toISOString().split('T')[0],
                        confirmed_time: '14:00',
                        reason: action === 'cancel' ? 'Bulk cancellation' : null
                    })
                });
            });

            Promise.allSettled(promises).then(results => {
                const successful = results.filter(r => r.status === 'fulfilled').length;
                const failed = results.length - successful;

                showNotification(
                    `${successful} visits ${action}ed successfully${failed > 0 ? `, ${failed} failed` : ''}`,
                    successful > 0 ? 'success' : 'error'
                );

                if (successful > 0) {
                    setTimeout(() => location.reload(), 1500);
                }
            });
        }

        // Enhanced Visit Actions
        function quickConfirm(visitId) {
            const tomorrow = new Date(Date.now() + 24*60*60*1000);
            const confirmData = {
                confirmed_date: tomorrow.toISOString().split('T')[0],
                confirmed_time: '14:00',
                landlord_response: 'Visit confirmed for tomorrow at 2:00 PM'
            };

            fetch(`/landlord/scheduled-visits/${visitId}/confirm`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(confirmData)
            }).then(response => {
                if (response.ok) {
                    showNotification('Visit confirmed successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Failed to confirm visit', 'error');
                }
            });
        }

        function markNoShow(visitId) {
            if (confirm('Mark this visit as no-show? This action cannot be undone.')) {
                fetch(`/landlord/scheduled-visits/${visitId}/no-show`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        showNotification('Visit marked as no-show', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification('Failed to update visit status', 'error');
                    }
                });
            }
        }

        function rescheduleVisit(visitId) {
            // Open reschedule modal with current visit data
            confirmVisit(visitId); // Reuse confirm modal for rescheduling
        }

        // Original functions (enhanced)
        function confirmVisit(visitId) {
            const form = document.getElementById('confirmForm');
            form.action = `/landlord/scheduled-visits/${visitId}/confirm`;

            // Pre-fill with tomorrow's date
            const tomorrow = new Date(Date.now() + 24*60*60*1000);
            document.querySelector('input[name="confirmed_date"]').value = tomorrow.toISOString().split('T')[0];
            document.querySelector('input[name="confirmed_time"]').value = '14:00';

            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function cancelVisit(visitId) {
            const form = document.getElementById('cancelForm');
            form.action = `/landlord/scheduled-visits/${visitId}/cancel-by-landlord`;
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function markCompleted(visitId) {
            if (confirm('Mark this visit as completed?')) {
                fetch(`/landlord/scheduled-visits/${visitId}/complete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        showNotification('Visit marked as completed!', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification('Failed to mark visit as completed', 'error');
                    }
                });
            }
        }

        // Export functionality
        function exportVisits() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'csv');

            const exportUrl = '/landlord/scheduled-visits/export?' + params.toString();
            window.open(exportUrl, '_blank');

            showNotification('Export started! Check your downloads.', 'info');
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const colors = {
                'success': 'bg-green-100 text-green-800 border-green-200',
                'error': 'bg-red-100 text-red-800 border-red-200',
                'warning': 'bg-yellow-100 text-yellow-800 border-yellow-200',
                'info': 'bg-blue-100 text-blue-800 border-blue-200'
            };

            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg border ${colors[type]} shadow-lg transition-all transform translate-x-full`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span class="mr-2">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-lg leading-none">&times;</button>
                </div>
            `;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => notification.classList.remove('translate-x-full'), 100);

            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // Modal functions
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Close modals when clicking outside
            document.getElementById('confirmModal').addEventListener('click', function(e) {
                if (e.target === this) closeModal('confirmModal');
            });

            document.getElementById('cancelModal').addEventListener('click', function(e) {
                if (e.target === this) closeModal('cancelModal');
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal('confirmModal');
                    closeModal('cancelModal');
                }
            });
        });
    </script>
@endsection