@extends('layouts.account')

@section('content')
<div class="py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Messages</h1>

        @if(auth()->user()->role === 'landlord')
        <button onclick="openContactSupportModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            Contact Support Team
        </button>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <div class="text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-green-400 hover:text-green-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    @if(session('error') || $errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                @if(session('error'))
                    <div class="text-sm font-medium text-red-800">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    @foreach($errors->all() as $error)
                        <div class="text-sm font-medium text-red-800">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($messages->count() > 0)
    <div class="bg-white rounded-lg shadow">
        @foreach($messages as $otherUserId => $conversation)
            @php
                $lastMessage = $conversation->first();
                $otherUser = $lastMessage->sender_id === auth()->id() ? $lastMessage->receiver : $lastMessage->sender;
            @endphp
            
            <div class="p-6 border-b last:border-0 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('messages.conversation', ['userId' => $otherUser->id, 'propertyId' => $lastMessage->property_id]) }}'">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-semibold text-lg">{{ $otherUser->name }}</h3>
                            <span class="text-xs px-2 py-1 rounded-full {{ auth()->user()->role === 'tenant' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $otherUser->role === 'landlord' ? 'Landlord' : 'Tenant' }}
                            </span>
                            @if($lastMessage->inquiry_id)
                                <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-800">
                                    Inquiry
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-1">📍 {{ $lastMessage->property->title }}</p>
                        @if($lastMessage->inquiry_id && $lastMessage->inquiry)
                            <p class="text-xs text-purple-600 mb-2">
                                Status: {{ $lastMessage->inquiry->status_name }} •
                                Submitted: {{ $lastMessage->inquiry->created_at->format('M j, Y') }}
                            </p>
                        @endif
                        <p class="text-gray-700 mt-2">{{ Str::limit($lastMessage->body, 120) }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $lastMessage->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        @if($lastMessage->receiver_id === auth()->id() && !$lastMessage->read_at)
                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">New</span>
                        @endif
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <h3 class="text-lg font-medium text-gray-900">No messages yet</h3>
        <p class="mt-1 text-sm text-gray-500">Your conversations will appear here</p>
        <div class="mt-6">
            <a href="{{ route('properties.browse') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 inline-block">
                Browse Properties
            </a>
        </div>
    </div>
    @endif
</div>

@if(auth()->user()->role === 'landlord')
<!-- Contact Support Modal -->
<div id="contactSupportModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-8 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-4 mb-4 border-b">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Contact Support Team</h3>
                <p class="text-sm text-gray-600 mt-1">Send a message to the admin team. We'll respond as soon as possible.</p>
            </div>
            <button onclick="closeContactSupportModal()" class="text-gray-400 hover:text-gray-600 text-3xl font-bold">&times;</button>
        </div>

        <!-- Contact Form -->
        <form action="{{ route('landlord.contact-admin') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateContactForm()">
            @csrf

            <!-- Subject Field -->
            <div class="mb-6">
                <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">
                    Subject <span class="text-gray-500 font-normal">(Optional)</span>
                </label>
                <input
                    type="text"
                    id="subject"
                    name="subject"
                    maxlength="200"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Brief description of your concern..."
                >
                <p class="text-xs text-gray-500 mt-1">E.g., "Property approval issue", "Account question", etc.</p>
            </div>

            <!-- Message Field -->
            <div class="mb-6">
                <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="message"
                    name="message"
                    rows="6"
                    required
                    maxlength="2000"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Describe your issue or question in detail..."
                ></textarea>
                <div class="flex justify-between items-center mt-1">
                    <p class="text-xs text-gray-500">Please provide as much detail as possible so we can help you better.</p>
                    <p class="text-xs text-gray-500" id="charCount">0 / 2000</p>
                </div>
            </div>

            <!-- Image Attachment (Optional) -->
            <div class="mb-6">
                <label for="attachment" class="block text-sm font-semibold text-gray-700 mb-2">
                    Attachment <span class="text-gray-500 font-normal">(Optional)</span>
                </label>
                <div class="flex items-center space-x-4">
                    <label class="flex-1 flex flex-col items-center px-4 py-6 bg-white border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <span class="mt-2 text-sm text-gray-500" id="fileLabel">Upload screenshot or image (PNG, JPG - Max 5MB)</span>
                        <input
                            type="file"
                            id="attachment"
                            name="attachment"
                            accept="image/png,image/jpeg,image/jpg"
                            class="hidden"
                            onchange="updateFileName(this)"
                        >
                    </label>
                </div>
                <div id="imagePreview" class="mt-3 hidden">
                    <div class="relative inline-block">
                        <img id="previewImage" src="" alt="Preview" class="max-h-40 rounded-lg border shadow-sm">
                        <button type="button" onclick="removeImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Note:</strong> You can view all your messages with the admin team in the
                            <a href="{{ route('landlord.admin-messages') }}" class="underline hover:text-blue-800">Admin Messages</a> section.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button
                    type="button"
                    onclick="closeContactSupportModal()"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition inline-flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Send Message
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal functions
function openContactSupportModal() {
    document.getElementById('contactSupportModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeContactSupportModal() {
    document.getElementById('contactSupportModal').classList.add('hidden');
    document.body.style.overflow = '';

    // Reset form
    document.getElementById('subject').value = '';
    document.getElementById('message').value = '';
    document.getElementById('attachment').value = '';
    document.getElementById('fileLabel').textContent = 'Upload screenshot or image (PNG, JPG - Max 5MB)';
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('charCount').textContent = '0 / 2000';
}

// Character counter for message
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('message');
    if (messageTextarea) {
        messageTextarea.addEventListener('input', function() {
            const count = this.value.length;
            document.getElementById('charCount').textContent = count + ' / 2000';
        });
    }
});

// File upload preview
function updateFileName(input) {
    const file = input.files[0];
    if (file) {
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            input.value = '';
            return;
        }

        // Validate file type
        if (!['image/png', 'image/jpeg', 'image/jpg'].includes(file.type)) {
            alert('Only PNG and JPG files are allowed');
            input.value = '';
            return;
        }

        // Update label
        document.getElementById('fileLabel').textContent = file.name;

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    document.getElementById('attachment').value = '';
    document.getElementById('fileLabel').textContent = 'Upload screenshot or image (PNG, JPG - Max 5MB)';
    document.getElementById('imagePreview').classList.add('hidden');
}

function validateContactForm() {
    const message = document.getElementById('message').value.trim();

    if (!message) {
        alert('Please enter a message');
        return false;
    }

    return confirm('Are you sure you want to send this message to the support team?');
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeContactSupportModal();
    }
});

// Close modal when clicking outside
document.getElementById('contactSupportModal')?.addEventListener('click', function(event) {
    if (event.target === this) {
        closeContactSupportModal();
    }
});

// Auto-dismiss success and error messages
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.querySelector('.bg-green-50');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'opacity 0.3s ease-in-out';
            successAlert.style.opacity = '0';
            setTimeout(() => {
                if (successAlert && successAlert.parentNode) {
                    successAlert.parentNode.removeChild(successAlert);
                }
            }, 300);
        }, 5000);
    }

    const errorAlert = document.querySelector('.bg-red-50');
    if (errorAlert) {
        setTimeout(() => {
            errorAlert.style.transition = 'opacity 0.3s ease-in-out';
            errorAlert.style.opacity = '0';
            setTimeout(() => {
                if (errorAlert && errorAlert.parentNode) {
                    errorAlert.parentNode.removeChild(errorAlert);
                }
            }, 300);
        }, 8000);
    }
});
</script>
@endif
@endsection