@extends('layouts.account')

@section('content')

{{-- Success Message --}}
@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="ml-3 flex-1">
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
<div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="ml-3 flex-1">
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

<!-- Page Header -->
<div class="mb-8 flex items-start justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Account Settings</h1>
        <p class="text-gray-600 mt-2">Manage your account information and preferences.</p>
    </div>

    @if(auth()->user()->role === 'landlord')
    <button onclick="openContactAdminModal()"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors shadow-sm">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>
        Contact Admin
    </button>
    @endif
</div>

<!-- Settings Content -->
<div class="space-y-8">
    <!-- Profile Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Profile Information</h2>
            <p class="text-sm text-gray-600 mt-1">Update your account's profile information and email address.</p>
        </div>
        <div class="p-6">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <!-- Update Password -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Update Password</h2>
            <p class="text-sm text-gray-600 mt-1">Ensure your account is using a long, random password to stay secure.</p>
        </div>
        <div class="p-6">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <!-- Delete Account -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Delete Account</h2>
            <p class="text-sm text-gray-600 mt-1">Permanently delete your account and all of its data.</p>
        </div>
        <div class="p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>

@if(auth()->user()->role === 'landlord')
<!-- Contact Admin Modal -->
<div id="contactAdminModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b bg-green-50">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Contact Admin</h2>
                        <p class="text-sm text-gray-600">Send a message to the administrator</p>
                    </div>
                </div>
                <button onclick="closeContactAdminModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <form action="{{ route('landlord.contact-admin') }}" method="POST" id="contactAdminForm" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-4">
                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="subject" name="subject" required maxlength="200"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Brief subject of your message">
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" name="message" rows="6" required maxlength="1000"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Describe your issue, question, or concern in detail..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">Maximum 1000 characters</p>
                    </div>

                    <!-- Photo Upload -->
                    <div>
                        <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">
                            Attach Photo (Optional)
                        </label>
                        <div class="flex items-center space-x-4">
                            <label for="attachment" class="flex-1 cursor-pointer">
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-green-400 hover:bg-green-50 transition-colors">
                                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm text-gray-600">
                                        <span class="text-green-600 font-medium">Click to upload</span> or drag and drop
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG up to 5MB</p>
                                </div>
                                <input type="file" id="attachment" name="attachment" accept="image/png,image/jpg,image/jpeg" class="hidden" onchange="handleFileSelect(event)">
                            </label>
                        </div>
                        <!-- Preview -->
                        <div id="imagePreview" class="hidden mt-3">
                            <div class="relative inline-block">
                                <img id="previewImg" src="" alt="Preview" class="max-h-32 rounded-lg border border-gray-300">
                                <button type="button" onclick="removeImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <p id="fileName" class="text-sm text-gray-600 mt-2"></p>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">Response Time</p>
                                <p>Admin typically responds within 24-48 hours. You'll receive a notification when they reply.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end space-x-3">
                    <button type="button" onclick="closeContactAdminModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-green-600 border border-transparent rounded-lg text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors font-semibold">
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openContactAdminModal() {
    document.getElementById('contactAdminModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeContactAdminModal() {
    document.getElementById('contactAdminModal').classList.add('hidden');
    document.body.style.overflow = '';
    // Reset form
    document.getElementById('contactAdminForm').reset();
    // Reset image preview
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('previewImg').src = '';
    document.getElementById('fileName').textContent = '';
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Check file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB');
        event.target.value = '';
        return;
    }

    // Check file type
    if (!file.type.match('image/(png|jpg|jpeg)')) {
        alert('Only PNG, JPG, and JPEG files are allowed');
        event.target.value = '';
        return;
    }

    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('imagePreview').classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    document.getElementById('attachment').value = '';
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('previewImg').src = '';
    document.getElementById('fileName').textContent = '';
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeContactAdminModal();
    }
});

// Close modal when clicking outside
document.getElementById('contactAdminModal')?.addEventListener('click', function(event) {
    if (event.target === this) {
        closeContactAdminModal();
    }
});
</script>
@endif

@endsection