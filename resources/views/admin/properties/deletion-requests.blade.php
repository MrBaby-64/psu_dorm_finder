@extends('layouts.account')

@section('content')
<div class="py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Deletion & Reported Requests</h1>

        <div class="flex gap-2">
            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">
                {{ $deletionRequests->where('status', 'pending')->count() }} Deletion Pending
            </span>
            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                {{ $landlordReports->where('status', 'pending')->count() }} Reports Pending
            </span>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button onclick="switchTab('deletions')" id="tab-deletions"
                    class="tab-button border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600">
                Property Deletion Requests
                <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-blue-100 text-blue-800">
                    {{ $deletionRequests->total() }}
                </span>
            </button>
            <button onclick="switchTab('reports')" id="tab-reports"
                    class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Reported Hosts
                <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100 text-gray-800">
                    {{ $landlordReports->total() }}
                </span>
            </button>
        </nav>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error') || $errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        @if(session('error'))
            {{ session('error') }}
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        @endif
    </div>
    @endif

    {{-- Deletion Requests Tab Content --}}
    <div id="content-deletions" class="tab-content">
    {{-- Search and Filter Bar --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.properties.deletion-requests') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by property title, location, or landlord name..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.properties.deletion-requests') }}"
                   class="border border-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-50">
                    Clear
                </a>
            @endif
        </form>
    </div>

    @if($deletionRequests->count() > 0)
    <div class="space-y-6">
        @foreach($deletionRequests as $request)
        <div class="bg-white rounded-lg shadow">
            {{-- Header with status --}}
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Deletion Request #{{ $request->id }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        Submitted {{ $request->created_at->format('M j, Y g:i A') }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $request->status_color }}">
                        {{ $request->status_name }}
                    </span>
                    @if($request->status !== 'pending')
                        <span class="text-xs text-gray-500">
                            by {{ $request->reviewer->name ?? 'Unknown' }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Property Information --}}
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Property Details</h4>
                        @if($request->property)
                            <div class="space-y-2">
                                <p class="font-medium">{{ $request->property->title }}</p>
                                <p class="text-gray-600">{{ $request->property->location_text }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $request->property->city }}, {{ $request->property->barangay }}
                                </p>
                                <p class="text-green-600 font-medium">
                                    ₱{{ number_format($request->property->price, 0) }}/month
                                </p>
                                <p class="text-gray-600">{{ $request->property->room_count }} rooms</p>
                            </div>
                        @else
                            <p class="text-red-600 text-sm">Property no longer exists</p>
                        @endif
                    </div>

                    {{-- Landlord and Request Information --}}
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Request Information</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Landlord:</label>
                                <p class="text-gray-900">{{ $request->landlord->name ?? 'Unknown' }}</p>
                                @if($request->landlord)
                                    <p class="text-sm text-gray-600">{{ $request->landlord->email }}</p>
                                @endif
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700">Reason for Deletion:</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                    <p class="text-gray-800">{{ $request->reason }}</p>
                                </div>
                            </div>

                            @if($request->admin_notes)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Admin Notes:</label>
                                <div class="mt-1 p-3 bg-blue-50 rounded-md">
                                    <p class="text-gray-800">{{ $request->admin_notes }}</p>
                                </div>
                            </div>
                            @endif

                            @if($request->reviewed_at)
                            <div class="text-sm text-gray-500">
                                <strong>Reviewed:</strong> {{ $request->reviewed_at->format('M j, Y g:i A') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Buttons (only for pending requests) --}}
                @if($request->status === 'pending' && $request->property)
                <div class="mt-6 pt-4 border-t border-gray-200 flex gap-3">
                    <button onclick="document.getElementById('approveModal{{ $request->id }}').classList.remove('hidden')"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approve Deletion
                    </button>

                    <button onclick="document.getElementById('rejectModal{{ $request->id }}').classList.remove('hidden')"
                            class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reject Request
                    </button>

                    <a href="{{ route('admin.properties.deletion-requests.view', $request) }}"
                       class="border border-blue-600 text-blue-600 px-6 py-2 rounded-lg hover:bg-blue-50 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Details
                    </a>
                </div>
                @endif
            </div>

            {{-- Approve Modal --}}
            @if($request->status === 'pending' && $request->property)
            <div id="approveModal{{ $request->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Confirm Property Deletion</h3>
                            <p class="text-sm text-gray-600">This action cannot be undone</p>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                        <p class="text-sm text-yellow-800">
                            <strong>Warning:</strong> Approving this request will permanently delete the property
                            "{{ $request->property->title ?? 'Unknown' }}" and all associated data (images, rooms, bookings, etc.).
                        </p>
                    </div>

                    <form action="{{ route('admin.properties.deletion-requests.approve', $request) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
                            <textarea name="admin_notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Add any notes about this approval..."></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 font-medium">
                                Yes, Delete Property
                            </button>
                            <button type="button"
                                    onclick="document.getElementById('approveModal{{ $request->id }}').classList.add('hidden')"
                                    class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Reject Modal --}}
            <div id="rejectModal{{ $request->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Reject Deletion Request</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Please provide a reason for rejecting this deletion request. The landlord will receive your feedback.
                    </p>

                    <form action="{{ route('admin.properties.deletion-requests.reject', $request) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection *</label>
                            <textarea name="admin_notes" rows="4" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Explain why this deletion request is being rejected..."></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 font-medium">
                                Reject Request
                            </button>
                            <button type="button"
                                    onclick="document.getElementById('rejectModal{{ $request->id }}').classList.add('hidden')"
                                    class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $deletionRequests->links() }}
    </div>

    @else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No deletion requests found</h3>
        <p class="text-sm text-gray-500">
            @if(request()->hasAny(['search', 'status']))
                No requests match your current filters.
            @else
                There are no property deletion requests at this time.
            @endif
        </p>
    </div>
    @endif
    </div>
    {{-- End Deletion Requests Tab Content --}}

    {{-- Reported Hosts Tab Content --}}
    <div id="content-reports" class="tab-content hidden">
        @if($landlordReports->count() > 0)
        <div class="space-y-6">
            @foreach($landlordReports as $report)
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Reported: {{ $report->landlord->name }}
                            </h3>
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $report->status_color }}">
                                {{ $report->status_name }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">
                            Reported by: <span class="font-medium">{{ $report->reporter->name }}</span>
                            ({{ $report->reporter->email }})
                        </p>
                        @if($report->property)
                        <p class="text-sm text-gray-600">
                            Property: <span class="font-medium">{{ $report->property->title }}</span>
                        </p>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">
                            Submitted: {{ $report->created_at->format('M j, Y \a\t g:i A') }}
                        </p>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="mb-3">
                        <span class="text-sm font-medium text-gray-700">Reason:</span>
                        <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded">
                            {{ $report->reason_name }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-1">Description:</p>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $report->description }}</p>
                        </div>
                    </div>
                </div>

                @if($report->admin_notes)
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-700 mb-1">Admin Notes:</p>
                    <div class="bg-blue-50 p-3 rounded-lg border-l-4 border-blue-400">
                        <p class="text-sm text-blue-800 whitespace-pre-line">{{ $report->admin_notes }}</p>
                        <p class="text-xs text-blue-600 mt-1">
                            Reviewed by {{ $report->reviewer->name }} on {{ $report->reviewed_at->format('M j, Y') }}
                        </p>
                    </div>
                </div>
                @endif

                @if($report->status === 'pending')
                <div class="flex gap-3 pt-4 border-t">
                    <button onclick="resolveReport({{ $report->id }})"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium text-sm">
                        Mark as Resolved
                    </button>
                    <button onclick="dismissReport({{ $report->id }})"
                            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                        Dismiss Report
                    </button>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $landlordReports->links() }}
        </div>

        @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No reports found</h3>
            <p class="text-sm text-gray-500">There are no reported hosts at this time.</p>
        </div>
        @endif
    </div>
    {{-- End Reported Hosts Tab Content --}}
</div>

<script>
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active styles from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');

    // Add active styles to selected tab
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.add('border-blue-500', 'text-blue-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}

function resolveReport(reportId) {
    if (!confirm('Mark this report as resolved?')) return;

    // Implement AJAX call to resolve report
    fetch(`/admin/reports/${reportId}/resolve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function dismissReport(reportId) {
    if (!confirm('Dismiss this report? This cannot be undone.')) return;

    // Implement AJAX call to dismiss report
    fetch(`/admin/reports/${reportId}/dismiss`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}
</script>

@endsection