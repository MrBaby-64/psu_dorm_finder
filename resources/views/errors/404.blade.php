<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - PSU Dorm Finder</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-lg mx-auto text-center">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="mx-auto w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>

            <h1 class="text-6xl font-bold text-gray-900 mb-4">404</h1>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page Not Found</h2>

            <p class="text-gray-600 mb-6">
                The page you're looking for doesn't exist or has been moved.
            </p>

            <div class="space-y-4">
                <a href="{{ url('/') }}"
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Return to Homepage
                </a>

                <div class="text-sm text-gray-500">
                    <p>Looking for something specific?</p>
                    <div class="mt-2 space-x-4">
                        <a href="{{ url('/browse') }}" class="text-blue-600 hover:text-blue-800">Browse Properties</a>
                        <a href="{{ url('/login') }}" class="text-blue-600 hover:text-blue-800">Login</a>
                        <a href="{{ url('/register') }}" class="text-blue-600 hover:text-blue-800">Register</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <button onclick="window.history.back()"
                    class="text-blue-600 hover:text-blue-800 font-medium">
                Go Back
            </button>
        </div>
    </div>
</body>
</html>