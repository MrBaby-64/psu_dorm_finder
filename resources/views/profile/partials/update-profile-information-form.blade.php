<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
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
</section>