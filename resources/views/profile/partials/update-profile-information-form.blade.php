<section>
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Picture Section -->
        <div class="border-b pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Profile Picture</h3>
            <div class="flex items-center space-x-6">
                <!-- Current Profile Picture -->
                <div class="flex-shrink-0">
                    @if($user->profile_picture_url)
                        <img src="{{ $user->profile_picture_url }}"
                             alt="Profile Picture"
                             class="w-24 h-24 rounded-full object-cover border-4 border-gray-200"
                             id="current-avatar">
                    @else
                        <div class="w-24 h-24 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-2xl border-4 border-gray-200" id="current-avatar">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <!-- Upload Controls -->
                <div class="flex-1">
                    <div class="mb-4">
                        <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-2">
                            Choose New Picture
                        </label>

                        <!-- Custom Upload Area -->
                        <div class="relative">
                            <input type="file"
                                   id="profile_picture"
                                   name="profile_picture"
                                   accept="image/*"
                                   class="hidden"
                                   onchange="previewImage(this)">

                            <div onclick="document.getElementById('profile_picture').click()"
                                 class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-500 hover:bg-green-50 transition-colors cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium text-green-600 hover:text-green-500">Click to upload</span>
                                        or drag and drop
                                    </p>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Alternative Simple Upload Button -->
                        <div class="mt-3">
                            <button type="button" onclick="document.getElementById('profile_picture').click()"
                                    class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition font-medium">
                                Choose Profile Picture
                            </button>
                        </div>

                        <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                    </div>

                    @if($user->profile_picture)
                        <label class="flex items-center cursor-pointer hover:bg-red-50 p-2 rounded">
                            <input type="checkbox" name="remove_profile_picture" value="1" class="mr-2">
                            <span class="text-sm text-red-600">Remove current picture</span>
                        </label>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full break-all" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

        </div>

        @if(auth()->user()->role === 'tenant')
        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
            <p class="text-sm text-gray-600 mb-4">Update your address to get better location-based property recommendations.</p>
            
            <div class="space-y-4">
                <div>
                    <x-input-label for="address" value="Address" />
                    <textarea id="address" name="address" rows="2" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                              placeholder="Your current address">{{ old('address', $user->address) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="city" value="City" />
                        <select id="city" name="city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select City</option>
                            <option value="Bacolor" {{ old('city', $user->city) === 'Bacolor' ? 'selected' : '' }}>Bacolor</option>
                            <option value="San Fernando" {{ old('city', $user->city) === 'San Fernando' ? 'selected' : '' }}>San Fernando</option>
                            <option value="Angeles City" {{ old('city', $user->city) === 'Angeles City' ? 'selected' : '' }}>Angeles City</option>
                            <option value="Mabalacat" {{ old('city', $user->city) === 'Mabalacat' ? 'selected' : '' }}>Mabalacat</option>
                            <option value="Other" {{ old('city', $user->city) === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('city')" />
                    </div>
                    
                    <div>
                        <x-input-label for="province" value="Province" />
                        <x-text-input id="province" name="province" type="text" class="mt-1 block w-full" :value="old('province', $user->province ?? 'Pampanga')" readonly />
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-medium"
                >{{ __('Profile updated successfully!') }}</p>
            @endif
        </div>
    </form>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                const currentAvatar = document.getElementById('current-avatar');

                reader.onload = function(e) {
                    // Create new image element or update existing one
                    if (currentAvatar.tagName === 'IMG') {
                        currentAvatar.src = e.target.result;
                    } else {
                        // Replace div with img
                        const newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.alt = 'Profile Picture Preview';
                        newImg.className = 'w-24 h-24 rounded-full object-cover border-4 border-gray-200';
                        newImg.id = 'current-avatar';
                        currentAvatar.parentNode.replaceChild(newImg, currentAvatar);
                    }
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</section>