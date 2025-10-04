@extends('layouts.account')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto">


        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Property Inquiries</h1>
                <p class="text-gray-600">Manage inquiries from potential tenants</p>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-400 text-green-800 p-4 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Inquiries List -->
        @if($inquiries->count() > 0)
            <div class="space-y-6">
                @foreach($inquiries as $inquiry)
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <!-- Inquiry Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $inquiry->user->name }}
                                    </h3>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $inquiry->status_color }}">
                                        {{ $inquiry->status_name }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    Property: <span class="font-medium">{{ $inquiry->property->title }}</span>
                                </p>
                                @if($inquiry->room)
                                    <p class="text-sm text-gray-600">
                                        Room: <span class="font-medium">{{ $inquiry->room->room_number }}</span>
                                        <span class="text-xs text-gray-500">
                                            (Capacity: {{ $inquiry->room->capacity }},
                                            Occupied: {{ $inquiry->room->occupied_count ?? 0 }}/{{ $inquiry->room->capacity }})
                                        </span>
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500 italic">
                                        No specific room selected
                                    </p>
                                @endif
                                <p class="text-xs text-gray-500">
                                    Submitted: {{ $inquiry->created_at->format('M j, Y \a\t g:i A') }}
                                </p>
                            </div>
                        </div>

                        <!-- Inquiry Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Move-in Date</p>
                                <p class="text-sm text-gray-600">{{ $inquiry->move_in_date ? $inquiry->move_in_date->format('M j, Y') : 'Not specified' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Move-out Date</p>
                                <p class="text-sm text-gray-600">{{ $inquiry->move_out_date ? $inquiry->move_out_date->format('M j, Y') : 'Not specified' }}</p>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Message</p>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $inquiry->message }}</p>
                            </div>
                        </div>

                        <!-- Landlord Reply -->
                        @if($inquiry->landlord_reply)
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Your Reply</p>
                                <div class="bg-blue-50 p-3 rounded-lg border-l-4 border-blue-400">
                                    <p class="text-sm text-blue-800 whitespace-pre-line">{{ $inquiry->landlord_reply }}</p>
                                    <p class="text-xs text-blue-600 mt-1">
                                        Replied: {{ $inquiry->replied_at ? $inquiry->replied_at->format('M j, Y \a\t g:i A') : '' }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3">
                            @if($inquiry->canBeApproved())
                                <!-- Approve Button -->
                                <form action="{{ route('landlord.inquiries.approve', $inquiry) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to approve this inquiry?')"
                                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium text-sm">
                                        ✓ Approve
                                    </button>
                                </form>

                                <!-- Reject Button -->
                                <button onclick="showRejectModal({{ $inquiry->id }})"
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition font-medium text-sm">
                                    ✗ Reject
                                </button>
                            @endif

                            @if($inquiry->canBeReplied())
                                <!-- Reply Button -->
                                <button onclick="showReplyModal({{ $inquiry->id }})"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                                    💬 Reply
                                </button>
                            @endif

                            <!-- View Inquiry Details -->
                            <button onclick="showInquiryDetails({{ $inquiry->id }})"
                                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                                👁️ View Details
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $inquiries->links() }}
            </div>
        @else
            <!-- No Inquiries -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900">No inquiries yet</h3>
                <p class="mt-2 text-gray-500">When tenants send inquiries about your properties, they'll appear here.</p>

                <!-- Quick Actions for Empty State -->
                <div style="margin-top: 32px; display: flex; justify-content: center;">
                    <a href="{{ route('landlord.properties.index') }}"
                       style="display: inline-flex; align-items: center; padding: 8px 16px; background-color: #4b5563; color: #ffffff; font-weight: 500; border-radius: 8px; text-decoration: none; transition: background-color 0.2s;"
                       onmouseover="this.style.backgroundColor='#374151'"
                       onmouseout="this.style.backgroundColor='#4b5563'">
                        <svg style="width: 16px; height: 16px; margin-right: 8px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span style="color: #ffffff; font-weight: 500;">Manage Properties</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Inquiry</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for rejection (optional)
                    </label>
                    <textarea name="reason" id="reason" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-y"
                              placeholder="Let the tenant know why their inquiry was rejected...&#10;&#10;Be professional and specific about the reason."
                              onkeydown="handleTextareaKeydown(event)"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition font-medium">
                        Reject Inquiry
                    </button>
                    <button type="button" onclick="closeRejectModal()"
                            class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reply to Inquiry</h3>
            <form id="replyForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reply" class="block text-sm font-medium text-gray-700 mb-2">
                        Your Reply <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reply" id="reply" rows="4" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y"
                              placeholder="Write your reply to the tenant...&#10;&#10;Address their questions and provide next steps."
                              onkeydown="handleTextareaKeydown(event)"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition font-medium">
                        Send Reply
                    </button>
                    <button type="button" onclick="closeReplyModal()"
                            class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inquiry Details Modal -->
<div id="detailsModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Inquiry Details</h3>
                    <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="detailsContent" class="space-y-4">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const inquiriesData = {
        @foreach($inquiries as $inquiry)
        {{ $inquiry->id }}: {
            tenant: '{{ $inquiry->user->name }}',
            email: '{{ $inquiry->user->email }}',
            property: '{{ $inquiry->property->title }}',
            room: '{{ $inquiry->room ? $inquiry->room->room_number : "No specific room" }}',
            moveInDate: '{{ $inquiry->move_in_date ? $inquiry->move_in_date->format("M j, Y") : "Not specified" }}',
            moveOutDate: '{{ $inquiry->move_out_date ? $inquiry->move_out_date->format("M j, Y") : "Not specified" }}',
            message: `{{ str_replace(["\r\n", "\n", "\r"], "\\n", addslashes($inquiry->message)) }}`,
            status: '{{ $inquiry->status_name }}',
            submitted: '{{ $inquiry->created_at->format("M j, Y \\a\\t g:i A") }}'
        },
        @endforeach
    };

    document.addEventListener('DOMContentLoaded', function() {
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    if (e.shiftKey) {
                        // Allow Shift+Enter for new line
                        return;
                    } else {
                        // Enter alone submits the form
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

    function showInquiryDetails(inquiryId) {
        const inquiry = inquiriesData[inquiryId];
        const content = `
            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Tenant Name</p>
                        <p class="text-sm text-gray-900">${inquiry.tenant}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Email</p>
                        <p class="text-sm text-gray-900">${inquiry.email}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Property</p>
                    <p class="text-sm text-gray-900">${inquiry.property}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Room</p>
                    <p class="text-sm text-gray-900">${inquiry.room}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Move-in Date</p>
                        <p class="text-sm text-gray-900">${inquiry.moveInDate}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Move-out Date</p>
                        <p class="text-sm text-gray-900">${inquiry.moveOutDate}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Status</p>
                    <p class="text-sm text-gray-900">${inquiry.status}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Submitted</p>
                    <p class="text-sm text-gray-900">${inquiry.submitted}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Tenant's Message</p>
                    <div class="bg-white p-3 rounded border border-gray-200">
                        <p class="text-sm text-gray-700 whitespace-pre-line">${inquiry.message}</p>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('detailsContent').innerHTML = content;
        document.getElementById('detailsModal').classList.remove('hidden');
    }

    function closeDetailsModal() {
        document.getElementById('detailsModal').classList.add('hidden');
    }

    function showRejectModal(inquiryId) {
        document.getElementById('rejectForm').action = `/landlord/inquiries/${inquiryId}/reject`;
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('reason').value = '';
    }

    function showReplyModal(inquiryId) {
        document.getElementById('replyForm').action = `/landlord/inquiries/${inquiryId}/reply`;
        document.getElementById('replyModal').classList.remove('hidden');
    }

    function closeReplyModal() {
        document.getElementById('replyModal').classList.add('hidden');
        document.getElementById('reply').value = '';
    }

    // Close modals when clicking outside
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });

    document.getElementById('replyModal').addEventListener('click', function(e) {
        if (e.target === this) closeReplyModal();
    });

    document.getElementById('detailsModal').addEventListener('click', function(e) {
        if (e.target === this) closeDetailsModal();
    });

    // Handle textarea keyboard events
    function handleTextareaKeydown(event) {
        if (event.key === 'Enter') {
            if (event.shiftKey) {
                // Allow Shift+Enter for new line
                return;
            } else {
                // Enter alone submits the form
                event.preventDefault();
                event.target.closest('form').submit();
            }
        }
    }
</script>
@endsection