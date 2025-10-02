@extends('layouts.account')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="text-center mb-8">
        <!-- Profile Picture Display -->
        <div class="relative inline-block">
            @if(auth()->user()->profile_picture)
                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}"
                     alt="Profile Picture"
                     class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-gray-200"
                     id="profile-preview">
            @else
                <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center" id="profile-preview">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            @endif

            <!-- Upload Button Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity cursor-pointer"
                 onclick="document.getElementById('profile-upload').click()">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Upload Form -->
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form" style="display: none;">
            @csrf
            @method('PATCH')
            <input type="file" id="profile-upload" name="profile_picture" accept="image/*" onchange="uploadProfilePicture(this)">
        </form>

        <button onclick="document.getElementById('profile-upload').click()"
                class="text-green-600 text-sm hover:underline font-medium">
            📷 Change Picture
        </button>

        @if(auth()->user()->profile_picture)
            <form action="{{ route('profile.update') }}" method="POST" class="inline ml-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="remove_profile_picture" value="1">
                <button type="submit" onclick="return confirm('Remove profile picture?')"
                        class="text-red-600 text-sm hover:underline font-medium">
                    🗑️ Remove
                </button>
            </form>
        @endif

        <h1 class="text-2xl font-bold mt-4">Welcome, {{ auth()->user()->name }}!</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Name</label>
                <p class="font-medium">{{ auth()->user()->name }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Email Address</label>
                <p class="font-medium break-all">{{ auth()->user()->email }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Mobile Number</label>
                <p class="font-medium">{{ auth()->user()->phone ?? 'Not Set' }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Account Type</label>
                <p class="font-medium uppercase">{{ auth()->user()->role }}</p>
            </div>
        </div>
    </div>

    @if(!auth()->user()->phone)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="font-bold text-blue-900">Complete Your Profile</h3>
                <p class="text-sm text-blue-700 mt-1">Add your mobile number to receive important notifications.</p>
                <a href="{{ route('profile.edit') }}" class="inline-block mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                    Update Profile
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

<script>
function uploadProfilePicture(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File too large. Please choose an image under 2MB.');
            return;
        }

        // Validate file type
        if (!file.type.match('image.*')) {
            alert('Please choose an image file.');
            return;
        }

        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('profile-preview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Replace div with img
                const newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.alt = 'Profile Picture';
                newImg.className = 'w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-gray-200';
                newImg.id = 'profile-preview';
                preview.parentNode.replaceChild(newImg, preview);
            }
        };
        reader.readAsDataURL(file);

        // Show loading state
        const form = document.getElementById('profile-form');
        const formData = new FormData(form);

        // Auto-submit form
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.status === 'profile-updated') {
                // Show success message
                showToast('Profile picture updated successfully!', 'success');
                // Refresh page after short delay to show new picture
                setTimeout(() => window.location.reload(), 1500);
            } else {
                throw new Error('Upload failed');
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            showToast('Failed to upload profile picture. Please try again.', 'error');
        });
    }
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-100 border border-green-400 text-green-800' :
        'bg-red-100 border border-red-400 text-red-800'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>