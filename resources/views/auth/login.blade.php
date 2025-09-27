<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Enhanced Error Display -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-400 text-red-800 p-4 rounded-lg">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-bold text-red-800">Login Failed</span>
            </div>
            <div class="space-y-2">
                @foreach ($errors->all() as $error)
                    <div class="text-red-700">• {{ $error }}</div>
                @endforeach
            </div>
            <div class="mt-4 p-3 bg-blue-50 border border-blue-300 rounded text-blue-800 text-sm">
                💡 <strong>Having trouble logging in?</strong><br>
                • Check your email and password are correct<br>
                • Make sure caps lock is off<br>
                • <a href="{{ route('register') }}" class="underline text-blue-600">Create a new account</a> if you don't have one<br>
                • <a href="{{ route('password.request') }}" class="underline text-blue-600">Reset your password</a> if you forgot it
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full @error('email') border-red-500 bg-red-50 @enderror" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full @error('password') border-red-500 bg-red-50 @enderror"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
