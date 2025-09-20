<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - PSU Dorm Finder</title>
    <style>
        /* Add blur effect manually */
        .backdrop-blur-md {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        body {
            background: linear-gradient(to bottom right, #dcfce7, #dbeafe);
            min-height: 100vh;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <!-- Role Selection Modal -->
<div id="roleModal" class="fixed inset-0 flex items-center justify-center z-50 p-4">
    <div class="absolute inset-0 bg-gradient-to-br from-green-900/30 to-blue-900/30 backdrop-blur-md"></div>
    
    <!-- Much narrower modal like Dormy -->
    <div class="relative bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl">
        
        <!-- Close Button -->
        <div class="absolute -top-3 -right-3">
            <button onclick="window.location.href='{{ route('home') }}'" 
                    class="bg-white rounded-full p-2 shadow-lg hover:shadow-xl transition-all hover:scale-110 border-2 border-gray-200 hover:border-red-400">
                <svg class="w-6 h-6 text-gray-600 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-green-600 mb-2">PSU Dorm Finder</h2>
            <p class="text-lg text-gray-700">How can we help you today?</p>
        </div>

        <!-- Stacked buttons instead of side-by-side -->
        <div class="space-y-4">
            <!-- Tenant Button -->
            <button onclick="selectRole('tenant')" 
                    class="w-full p-4 border-2 border-blue-300 bg-blue-50 rounded-xl hover:bg-blue-100 hover:shadow-lg transition-all">
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="text-lg font-semibold text-blue-700">I'm looking for a place to rent</span>
                </div>
            </button>

            <!-- Landlord Button -->
            <button onclick="selectRole('landlord')" 
                    class="w-full p-4 border-2 border-green-300 bg-green-50 rounded-xl hover:bg-green-100 hover:shadow-lg transition-all">
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-lg font-semibold text-green-700">I want to post my rental property</span>
                </div>
            </button>
        </div>

        <div class="text-center mt-6 text-sm text-gray-600">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-green-600 hover:underline font-semibold">Login here</a>
        </div>
    </div>
</div>
        <div class="text-center mt-6 text-sm">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-green-600 hover:underline font-semibold">Login here</a>
        </div>
    </div>
</div>

    <!-- Registration Form -->
    <div id="registrationForm" class="hidden min-h-screen flex items-center justify-center py-12 px-4">
        <div class="bg-white rounded-2xl shadow-2xl p-10 max-w-xl w-full">
            <h2 class="text-3xl font-bold mb-8 text-center text-green-600">Create Your Account</h2>
            
            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="roleInput" name="role">

                <div class="space-y-5">
                    <div>
                        <label class="block font-semibold mb-2">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-3 border-2 rounded-xl focus:border-green-500 focus:outline-none">
                        @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 border-2 rounded-xl focus:border-green-500 focus:outline-none">
                        @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                               class="w-full px-4 py-3 border-2 rounded-xl focus:border-green-500 focus:outline-none">
                    </div>

                    <!-- Valid ID (landlords only) -->
                    <div id="validIdSection" class="hidden">
                        <label class="block font-semibold mb-2">Valid ID *</label>
                        <div class="border-2 border-dashed rounded-xl p-6 text-center hover:border-green-500 transition-colors">
                            <input type="file" name="valid_id" accept="image/*,.pdf" class="hidden" id="idUpload" onchange="displayFileName(this)">
                            <label for="idUpload" class="cursor-pointer">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <span id="fileName" class="text-green-600 font-semibold">Click to upload ID</span>
                                <p class="text-xs text-gray-500 mt-2">Government-issued ID required</p>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">Password *</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 border-2 rounded-xl focus:border-green-500 focus:outline-none">
                        @error('password')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-3 border-2 rounded-xl focus:border-green-500 focus:outline-none">
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="button" onclick="goBack()" class="flex-1 bg-gray-200 py-3 rounded-xl font-semibold hover:bg-gray-300">
                        Back
                    </button>
                    <button type="submit" class="flex-1 bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 shadow-lg">
                        Sign Up
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selectRole(role) {
            document.getElementById('roleModal').classList.add('hidden');
            document.getElementById('registrationForm').classList.remove('hidden');
            document.getElementById('roleInput').value = role;
            
            if (role === 'landlord') {
                document.getElementById('validIdSection').classList.remove('hidden');
            }
        }

        function goBack() {
            document.getElementById('roleModal').classList.remove('hidden');
            document.getElementById('registrationForm').classList.add('hidden');
        }

        function displayFileName(input) {
            if (input.files[0]) {
                document.getElementById('fileName').textContent = '✓ ' + input.files[0].name;
            }
        }
    </script>

</body>
</html>