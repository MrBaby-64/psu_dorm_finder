<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Error - PSU Dorm Finder</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl mx-auto text-center px-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="mx-auto w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7M4 7c0-2 1-3 3-3h10c2 0 3 1 3 3M4 7h16M10 11v6m4-6v6"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-4">Database Connection Error</h1>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 text-left">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Local Development Issue:</strong><br>
                            {{ $message ?? 'Cannot connect to MySQL database.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-left bg-gray-50 rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">How to fix this:</h2>
                <ol class="list-decimal list-inside space-y-2 text-gray-700">
                    <li>Open <strong>XAMPP Control Panel</strong></li>
                    <li>Click the <strong>Start</strong> button next to <strong>MySQL</strong></li>
                    <li>Wait for MySQL to turn green (running)</li>
                    <li>Refresh this page</li>
                </ol>
            </div>

            <div class="space-y-4">
                <button onclick="window.location.reload()"
                        class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Refresh Page
                </button>

                <div class="text-sm text-gray-500">
                    <p>Still having issues? Make sure:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>XAMPP is installed and running</li>
                        <li>MySQL port 3306 is not blocked</li>
                        <li>Database '<strong>psu_dorm_finder</strong>' exists</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
