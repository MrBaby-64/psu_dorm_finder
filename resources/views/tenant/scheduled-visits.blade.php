@extends('layouts.account')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Scheduled Visits</h1>
            <p class="text-gray-600">Manage your property viewing appointments</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input 
                        type="date" 
                        name="from_date" 
                        value="{{ request('from_date') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input 
                        type="date" 
                        name="to_date" 
                        value="{{ request('to_date') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    >
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Visits List -->
        @if($visits->count() > 0)
            <div class="bg-white rounded-lg shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Property
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Landlord
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Preferred Date & Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Confirmed Date & Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($visits as $visit)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @php
                                            $mainImage = $visit->property->images->where('is_cover', true)->first() ?? $visit->property->images->first();
                                            $imageUrl = $mainImage ? asset('storage/' . $mainImage->image_path) : 'https://via.placeholder.com/60x60?text=No+Image';
                                        @endphp
                                        
                                        <img src="{{ $imageUrl }}" 
                                             alt="{{ $visit->property->title }}" 
                                             class="w-12 h-12 rounded-lg object-cover">
                                        
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('properties.show', $visit->property->slug) }}" 
                                                   class="hover:text-green-600">
                                                    {{ $visit->property->title }}
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $visit->property->location_text }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $visit->property->landlord->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $visit->property->landlord->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $visit->preferred_date->format('M j, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $visit->formatted_preferred_time }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($visit->confirmed_date && $visit->confirmed_time)
                                        <div class="text-sm font-medium text-green-600">
                                            {{ $visit->confirmed_date->format('M j, Y') }}
                                        </div>
                                        <div class="text-sm text-green-500">
                                            {{ $visit->formatted_confirmed_time }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">Not confirmed yet</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $visit->status_color }}">
                                        {{ $visit->status_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if($visit->canBeCancelled())
                                            <button onclick="openCancelModal({{ $visit->id }})" 
                                                    class="text-red-600 hover:text-red-900">
                                                Cancel
                                            </button>
                                        @endif
                                        
                                        <button onclick="openDetailsModal({{ $visit->id }})" 
                                                class="text-blue-600 hover:text-blue-900">
                                            Details
                                        </button>
                                        
                                        <a href="{{ route('properties.show', $visit->property->slug) }}" 
                                           class="text-green-600 hover:text-green-900">
                                            View Property
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $visits->links() }}
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No scheduled visits</h3>
                <p class="text-gray-500 mb-6">You haven't scheduled any property visits yet. Browse properties and schedule a viewing!</p>
                <a href="{{ route('properties.browse') }}" 
                   class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-medium">
                    Browse Properties
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Cancel Visit Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md m-4 overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Cancel Visit</h3>
                <button onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="cancelForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Reason for cancellation (optional)</label>
                    <textarea name="reason" 
                              rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" 
                              placeholder="Please provide a reason for cancelling..."></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button"
                            onclick="closeCancelModal()"
                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                        Keep Visit
                    </button>
                    <button type="submit"
                            class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                        Cancel Visit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Visit Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg m-4 overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Visit Details</h3>
                <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div id="visitDetailsContent">
                <!-- Content will be populated by JavaScript -->
            </div>

            <div class="flex justify-end mt-6">
                <button onclick="closeDetailsModal()"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const visits = @json($visits);

document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                if (e.ctrlKey || e.metaKey) {
                    return;
                } else {
                    e.preventDefault();
                    const form = this.closest('form');
                    if (form) {
                        const submitBtn = form.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            submitBtn.click();
                        } else {
                            form.requestSubmit();
                        }
                    }
                }
            }
        });
    });
});

function openCancelModal(visitId) {
    document.getElementById('cancelForm').action = `/tenant/scheduled-visits/${visitId}/cancel`;
    document.getElementById('cancelModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openDetailsModal(visitId) {
    const visit = visits.data.find(v => v.id === visitId);
    if (!visit) return;

    const content = `
        <div class="space-y-4">
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">Property</h4>
                <p class="text-gray-600">${visit.property.title}</p>
                <p class="text-sm text-gray-500">${visit.property.location_text}</p>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">Landlord</h4>
                <p class="text-gray-600">${visit.property.landlord.name}</p>
                <p class="text-sm text-gray-500">${visit.property.landlord.email}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Preferred Date & Time</h4>
                    <p class="text-gray-600">${new Date(visit.preferred_date).toLocaleDateString()}</p>
                    <p class="text-sm text-gray-500">${visit.formatted_preferred_time}</p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Status</h4>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${visit.status === 'confirmed' ? 'bg-green-100 text-green-800' : (visit.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')}">
                        ${visit.status_name}
                    </span>
                </div>
            </div>
            
            ${visit.confirmed_date && visit.confirmed_time ? `
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Confirmed Date & Time</h4>
                    <p class="text-green-600 font-medium">${new Date(visit.confirmed_date).toLocaleDateString()}</p>
                    <p class="text-sm text-green-500">${visit.formatted_confirmed_time}</p>
                </div>
            ` : ''}
            
            ${visit.notes ? `
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Your Notes</h4>
                    <p class="text-gray-600">${visit.notes}</p>
                </div>
            ` : ''}
            
            ${visit.landlord_response ? `
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Landlord Response</h4>
                    <p class="text-gray-600">${visit.landlord_response}</p>
                </div>
            ` : ''}
            
            ${visit.status === 'cancelled' && visit.cancellation_reason ? `
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Cancellation Reason</h4>
                    <p class="text-gray-600">${visit.cancellation_reason}</p>
                    <p class="text-sm text-gray-500">Cancelled by: ${visit.cancelled_by}</p>
                </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('visitDetailsContent').innerHTML = content;
    document.getElementById('detailsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modals when clicking outside
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});

document.getElementById('detailsModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetailsModal();
});

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCancelModal();
        closeDetailsModal();
    }
});
</script>
@endpush
@endsection