<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Unavailable - PSU Dorm Finder</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-lg mx-auto text-center">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="mx-auto w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-4">Service Unavailable</h1>

            <p class="text-gray-600 mb-6">
                {{ $message ?? 'PSU Dorm Finder is temporarily unavailable for maintenance. We\'ll be back soon!' }}
            </p>

            <div class="space-y-4">
                <button onclick="window.location.reload()"
                        class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Try Again
                </button>

                <div class="text-sm text-gray-500">
                    <p>Expected downtime: A few minutes</p>
                    <p>For urgent inquiries: support@psu-dorm-finder.com</p>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                Return to Homepage
            </a>
        </div>
    </div>
</body>
</html>