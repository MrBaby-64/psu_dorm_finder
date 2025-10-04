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
                                                            <span class="text-gray-900">{{ $visit->tenant->email }}</span>
                                                        </p>
                                                        @if($visit->tenant->phone)
                                                            <p class="text-sm"><span class="font-medium text-gray-700">📱 Phone:</span>
                                                                <span class="text-gray-900">{{ $visit->tenant->phone }}</span>
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

    <!-- Calendar View -->
    <div id="calendarView" class="hidden">
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <!-- Calendar Header -->
            <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 px-6 py-5">
                <div class="flex items-center justify-between">
                    <button onclick="changeMonth(-1)" class="group p-2 hover:bg-white/20 rounded-lg transition-all duration-200 text-white" title="Previous Month">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div class="text-center">
                        <h3 id="calendarMonthYear" class="text-3xl font-bold text-white mb-1"></h3>
                        <button onclick="goToToday()" class="text-xs text-white/80 hover:text-white hover:underline transition">
                            Go to Today
                        </button>
                    </div>
                    <button onclick="changeMonth(1)" class="group p-2 hover:bg-white/20 rounded-lg transition-all duration-200 text-white" title="Next Month">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div id="calendarGrid" class="bg-white"></div>

            <!-- Quick Stats Bar -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-2 animate-pulse"></div>
                            <span class="text-gray-700"><span id="scheduledCount" class="font-bold">0</span> Scheduled</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-gray-700"><span id="completedCount" class="font-bold">0</span> Completed</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-400 rounded-full mr-2"></div>
                            <span class="text-gray-700"><span id="cancelledCount" class="font-bold">0</span> Cancelled</span>
                        </div>
                    </div>
                    <span id="totalVisitsCount" class="text-gray-500 font-medium"></span>
                </div>
            </div>

            <!-- Visit Details Panel (Enhanced) -->
            <div id="visitDetailsPanel" class="hidden border-t-4 border-blue-500 bg-gradient-to-br from-blue-50 to-indigo-50">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h4 id="visitDetailsTitle" class="text-xl font-bold text-gray-900"></h4>
                            <p id="visitDetailsSubtitle" class="text-sm text-gray-600 mt-1"></p>
                        </div>
                        <button onclick="closeVisitDetails()" class="group p-2 hover:bg-white rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:rotate-90 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="visitDetailsList" class="space-y-3"></div>
                </div>
            </div>
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

            // Initialize calendar when switching to calendar view
            if (view === 'calendar') {
                renderCalendar();
            }
        }

        // Calendar State
        let calendarMonth = new Date().getMonth();
        let calendarYear = new Date().getFullYear();
        let calendarVisits = [];

        // Calendar Rendering
        function renderCalendar() {
            calendarVisits = @json($visits->items());
            const calendarGrid = document.getElementById('calendarGrid');
            const monthYearDisplay = document.getElementById('calendarMonthYear');

            if (!calendarGrid || !monthYearDisplay) return;

            const today = new Date();
            const firstDay = new Date(calendarYear, calendarMonth, 1);
            const lastDay = new Date(calendarYear, calendarMonth + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = firstDay.getDay();
            const prevMonthLastDay = new Date(calendarYear, calendarMonth, 0).getDate();

            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];
            const dayNamesShort = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];

            // Update month/year display
            monthYearDisplay.textContent = `${monthNames[calendarMonth]} ${calendarYear}`;

            let calendarHTML = '<div class="grid grid-cols-7 border-l border-t border-gray-300">';

            // Day headers (enhanced professional styling)
            dayNamesShort.forEach((day, index) => {
                const isWeekend = index === 0 || index === 6;
                calendarHTML += `
                    <div class="bg-gradient-to-b from-slate-100 to-slate-50 border-r border-b border-gray-300 py-3 px-2 text-center">
                        <span class="font-extrabold ${isWeekend ? 'text-blue-600' : 'text-gray-700'} text-xs tracking-wider">
                            ${day}
                        </span>
                    </div>
                `;
            });

            // Track stats
            let totalScheduled = 0;
            let totalCompleted = 0;
            let totalCancelled = 0;

            // Previous month's trailing days
            for (let i = startingDayOfWeek - 1; i >= 0; i--) {
                const day = prevMonthLastDay - i;
                calendarHTML += `
                    <div class="bg-gray-50 border-r border-b border-gray-300 p-3 min-h-[120px] text-gray-400 relative">
                        <div class="text-xs font-semibold opacity-30">${day}</div>
                    </div>
                `;
            }

            // Current month's days
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${calendarYear}-${String(calendarMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const isToday = day === today.getDate() && calendarMonth === today.getMonth() && calendarYear === today.getFullYear();
                const isPast = new Date(dateStr) < new Date(today.setHours(0,0,0,0));
                const isWeekend = new Date(dateStr).getDay() === 0 || new Date(dateStr).getDay() === 6;

                // Find visits for this day
                const dayVisits = calendarVisits.filter(visit => {
                    let visitDate = visit.confirmed_date || visit.preferred_date;
                    if (typeof visitDate === 'object' && visitDate !== null) {
                        visitDate = visitDate.date || visitDate;
                    }
                    if (typeof visitDate === 'string') {
                        return visitDate.startsWith(dateStr);
                    }
                    return false;
                });

                // Determine highlight color based on visit status
                let highlightClass = 'bg-white hover:bg-gray-50';
                let borderClass = 'border border-gray-200';
                let shadowClass = '';
                let ringClass = '';

                if (dayVisits.length > 0) {
                    const hasCompleted = dayVisits.some(v => v.status === 'completed');
                    const hasScheduled = dayVisits.some(v => v.status === 'pending' || v.status === 'confirmed');
                    const hasCancelled = dayVisits.some(v => v.status === 'cancelled' || v.status === 'no_show');

                    // Update stats
                    dayVisits.forEach(v => {
                        if (v.status === 'completed') totalCompleted++;
                        else if (v.status === 'pending' || v.status === 'confirmed') totalScheduled++;
                        else if (v.status === 'cancelled' || v.status === 'no_show') totalCancelled++;
                    });

                    if (hasCompleted && !hasScheduled) {
                        // All completed - GREEN
                        highlightClass = 'bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200';
                        borderClass = 'border-2 border-green-400';
                        shadowClass = 'shadow-md shadow-green-200';
                    } else if (hasScheduled) {
                        // Has scheduled visits - RED
                        highlightClass = 'bg-gradient-to-br from-red-50 to-red-100 hover:from-red-100 hover:to-red-200';
                        borderClass = 'border-2 border-red-400';
                        shadowClass = 'shadow-md shadow-red-200';
                        ringClass = 'ring-1 ring-red-300';
                    } else if (hasCancelled) {
                        // Only cancelled - GRAY
                        highlightClass = 'bg-gradient-to-br from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200';
                        borderClass = 'border-2 border-gray-300';
                    }
                }

                if (isToday) {
                    borderClass = 'border-4 border-yellow-400';
                    ringClass = 'ring-2 ring-yellow-300';
                    if (!dayVisits.length) {
                        highlightClass = 'bg-gradient-to-br from-yellow-50 to-amber-50 hover:from-yellow-100 hover:to-amber-100';
                    }
                }

                if (isWeekend && !dayVisits.length && !isToday) {
                    highlightClass = 'bg-gray-50/50 hover:bg-gray-100/50';
                }

                calendarHTML += `
                    <div class="${highlightClass} border-r border-b border-gray-300 ${shadowClass} p-3 min-h-[120px] transition-all duration-200 cursor-pointer group relative"
                         onclick="showDayVisits('${dateStr}', ${day}, '${monthNames[calendarMonth]}')"
                         ${isToday ? 'data-today="true"' : ''}>

                        ${isToday ? '<div class="absolute inset-0 border-2 border-yellow-400 pointer-events-none rounded-sm"></div>' : ''}
                        ${dayVisits.length > 0 && hasScheduled ? '<div class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>' : ''}

                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-1">
                                    <span class="${isWeekend ? 'text-blue-600' : isToday ? 'text-yellow-700' : isPast ? 'text-gray-500' : 'text-gray-800'} ${isToday ? 'text-xl font-black' : 'text-base font-bold'}">
                                        ${day}
                                    </span>
                                    ${isToday ? '<span class="text-[10px] font-bold text-yellow-600 bg-yellow-100 px-1.5 py-0.5 rounded-full">TODAY</span>' : ''}
                                </div>
                            </div>

                            <div class="space-y-1">
                `;

                // Show visit count and status indicators with better styling
                if (dayVisits.length > 0) {
                    const completed = dayVisits.filter(v => v.status === 'completed').length;
                    const scheduled = dayVisits.filter(v => v.status === 'pending' || v.status === 'confirmed').length;
                    const cancelled = dayVisits.filter(v => v.status === 'cancelled' || v.status === 'no_show').length;

                    if (scheduled > 0) {
                        calendarHTML += `
                            <div class="flex items-center text-xs font-bold text-red-800 bg-red-500 text-white px-2.5 py-1.5 rounded-lg shadow-sm group-hover:shadow-md transition-shadow">
                                <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                                ${scheduled} Scheduled
                            </div>
                        `;
                    }
                    if (completed > 0) {
                        calendarHTML += `
                            <div class="flex items-center text-xs font-bold text-green-800 bg-green-500 text-white px-2.5 py-1.5 rounded-lg shadow-sm">
                                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                ${completed} Done
                            </div>
                        `;
                    }
                    if (cancelled > 0) {
                        calendarHTML += `
                            <div class="flex items-center text-xs font-semibold text-gray-700 bg-gray-300 px-2.5 py-1.5 rounded-lg">
                                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                ${cancelled} Cancelled
                            </div>
                        `;
                    }
                } else if (!isPast) {
                    calendarHTML += `
                        <div class="text-xs text-gray-400 italic opacity-0 group-hover:opacity-100 transition-opacity">
                            No visits
                        </div>
                    `;
                }

                calendarHTML += `
                            </div>
                        </div>
                    </div>
                `;
            }

            // Next month's leading days
            const totalCells = startingDayOfWeek + daysInMonth;
            const remainingCells = 7 - (totalCells % 7);
            if (remainingCells < 7) {
                for (let day = 1; day <= remainingCells; day++) {
                    calendarHTML += `
                        <div class="bg-gray-50 border-r border-b border-gray-300 p-3 min-h-[120px] text-gray-400 relative">
                            <div class="text-xs font-semibold opacity-30">${day}</div>
                        </div>
                    `;
                }
            }

            calendarHTML += '</div>';
            calendarGrid.innerHTML = calendarHTML;

            // Update stats bar
            updateCalendarStats(totalScheduled, totalCompleted, totalCancelled);
        }

        function updateCalendarStats(scheduled, completed, cancelled) {
            const scheduledEl = document.getElementById('scheduledCount');
            const completedEl = document.getElementById('completedCount');
            const cancelledEl = document.getElementById('cancelledCount');
            const totalEl = document.getElementById('totalVisitsCount');

            if (scheduledEl) scheduledEl.textContent = scheduled;
            if (completedEl) completedEl.textContent = completed;
            if (cancelledEl) cancelledEl.textContent = cancelled;
            if (totalEl) totalEl.textContent = `${scheduled + completed + cancelled} total visits this month`;
        }

        function goToToday() {
            calendarMonth = new Date().getMonth();
            calendarYear = new Date().getFullYear();
            renderCalendar();

            // Scroll to today's date
            setTimeout(() => {
                const todayCell = document.querySelector('[data-today="true"]');
                if (todayCell) {
                    todayCell.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    todayCell.classList.add('ring-4', 'ring-blue-400');
                    setTimeout(() => {
                        todayCell.classList.remove('ring-4', 'ring-blue-400');
                    }, 2000);
                }
            }, 100);
        }

        function changeMonth(direction) {
            calendarMonth += direction;

            if (calendarMonth > 11) {
                calendarMonth = 0;
                calendarYear++;
            } else if (calendarMonth < 0) {
                calendarMonth = 11;
                calendarYear--;
            }

            renderCalendar();
        }

        function showDayVisits(dateStr, day, monthName) {
            const dayVisits = calendarVisits.filter(visit => {
                let visitDate = visit.confirmed_date || visit.preferred_date;
                if (typeof visitDate === 'object' && visitDate !== null) {
                    visitDate = visitDate.date || visitDate;
                }
                if (typeof visitDate === 'string') {
                    return visitDate.startsWith(dateStr);
                }
                return false;
            });

            if (dayVisits.length === 0) {
                showNotification('No visits scheduled for this day', 'info');
                return;
            }

            const panel = document.getElementById('visitDetailsPanel');
            const detailsList = document.getElementById('visitDetailsList');
            const detailsTitle = document.getElementById('visitDetailsTitle');
            const detailsSubtitle = document.getElementById('visitDetailsSubtitle');

            if (!panel || !detailsList || !detailsTitle || !detailsSubtitle) return;

            // Update title
            detailsTitle.innerHTML = `📅 Visits on ${monthName} ${day}, ${calendarYear}`;
            detailsSubtitle.innerHTML = `${dayVisits.length} visit${dayVisits.length > 1 ? 's' : ''} • Click on a visit to view full details`;

            let detailsHTML = '';

            dayVisits.forEach((visit, index) => {
                const statusConfig = {
                    'pending': {
                        bg: 'bg-gradient-to-r from-yellow-50 to-yellow-100',
                        border: 'border-yellow-400',
                        text: 'text-yellow-800',
                        badge: 'bg-yellow-500 text-white',
                        icon: '⏳'
                    },
                    'confirmed': {
                        bg: 'bg-gradient-to-r from-blue-50 to-blue-100',
                        border: 'border-blue-400',
                        text: 'text-blue-800',
                        badge: 'bg-blue-500 text-white',
                        icon: '✓'
                    },
                    'completed': {
                        bg: 'bg-gradient-to-r from-green-50 to-green-100',
                        border: 'border-green-400',
                        text: 'text-green-800',
                        badge: 'bg-green-500 text-white',
                        icon: '✓'
                    },
                    'cancelled': {
                        bg: 'bg-gradient-to-r from-red-50 to-red-100',
                        border: 'border-red-400',
                        text: 'text-red-800',
                        badge: 'bg-red-500 text-white',
                        icon: '✕'
                    },
                    'no_show': {
                        bg: 'bg-gradient-to-r from-gray-50 to-gray-100',
                        border: 'border-gray-400',
                        text: 'text-gray-800',
                        badge: 'bg-gray-500 text-white',
                        icon: '👻'
                    }
                };

                const config = statusConfig[visit.status] || statusConfig['pending'];
                const time = visit.confirmed_time || visit.preferred_time;

                detailsHTML += `
                    <div class="group ${config.bg} border-2 ${config.border} rounded-xl p-4 cursor-pointer hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1"
                         onclick="goToVisit(${visit.id})">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-start space-x-3 flex-1">
                                <div class="mt-1">
                                    <span class="text-2xl">${config.icon}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="font-bold ${config.text} text-lg mb-1 truncate">${visit.property.title}</h5>
                                    <div class="space-y-1">
                                        <p class="text-sm ${config.text} flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            ${visit.tenant.name}
                                        </p>
                                        <p class="text-sm ${config.text} flex items-center font-semibold">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            ${time}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold ${config.badge} shadow-sm whitespace-nowrap ml-3">
                                ${visit.status.toUpperCase()}
                            </span>
                        </div>

                        ${visit.notes ? `
                            <div class="mt-3 pt-3 border-t ${config.border} border-opacity-30">
                                <p class="text-xs ${config.text} opacity-80 italic">
                                    <span class="font-semibold not-italic">Note:</span> ${visit.notes.substring(0, 100)}${visit.notes.length > 100 ? '...' : ''}
                                </p>
                            </div>
                        ` : ''}

                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-xs ${config.text} opacity-70">Click to view full details →</span>
                            <svg class="w-5 h-5 ${config.text} opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>
                    </div>
                `;
            });

            detailsList.innerHTML = detailsHTML;
            panel.classList.remove('hidden');

            // Smooth scroll to panel
            setTimeout(() => {
                panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        }

        function closeVisitDetails() {
            const panel = document.getElementById('visitDetailsPanel');
            if (panel) {
                panel.classList.add('hidden');
            }
        }

        function goToVisit(visitId) {
            closeVisitDetails();
            const visitElement = document.getElementById(`visit-${visitId}`);
            if (visitElement) {
                toggleView('list');
                setTimeout(() => {
                    visitElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    visitElement.classList.add('bg-blue-100');
                    setTimeout(() => visitElement.classList.remove('bg-blue-100'), 3000);
                }, 300);
            }
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