<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - PSU Dorm Finder</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-lg mx-auto text-center">
        <div class="bg-white rounded-lg shadow-lg p-6 sm:p-8">
            <div class="mx-auto w-20 h-20 sm:w-24 sm:h-24 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h1 class="text-4xl sm:text-6xl font-bold text-gray-900 mb-4">419</h1>
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-700 mb-4">Session Expired</h2>

            <p class="text-gray-600 mb-6 text-sm sm:text-base">
                Your session has expired due to inactivity. This happens when you've been away for a while or if your browser was closed.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-left">
                <h3 class="font-semibold text-blue-900 mb-2 text-sm sm:text-base">What should you do?</h3>
                <ul class="text-blue-800 space-y-2 text-xs sm:text-sm">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Click the "Refresh Page" button below</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Log in again if needed</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Continue with your task</span>
                    </li>
                </ul>
            </div>

            <div class="space-y-3">
                <button onclick="location.reload()"
                        class="w-full sm:w-auto inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    🔄 Refresh Page
                </button>

                <div class="text-xs sm:text-sm text-gray-500">
                    <p>Or go to:</p>
                    <div class="mt-2 flex flex-wrap justify-center gap-2 sm:gap-4">
                        <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-800">Homepage</a>
                        <span class="text-gray-400">•</span>
                        <a href="{{ url('/browse') }}" class="text-blue-600 hover:text-blue-800">Browse</a>
                        <span class="text-gray-400">•</span>
                        <a href="{{ url('/login') }}" class="text-blue-600 hover:text-blue-800">Login</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <p class="text-xs sm:text-sm text-gray-600 mb-2">
                <span id="countdown">Automatically refreshing in <strong>5</strong> seconds...</span>
            </p>
            <button onclick="clearInterval(autoRefreshTimer); document.getElementById('countdown').innerHTML = 'Auto-refresh cancelled';"
                    class="text-gray-600 hover:text-gray-800 font-medium text-sm">
                Cancel Auto-Refresh
            </button>
        </div>
    </div>

    <script>
        // Auto-refresh after 5 seconds
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');

        const autoRefreshTimer = setInterval(() => {
            countdown--;
            if (countdown > 0) {
                countdownElement.innerHTML = `Automatically refreshing in <strong>${countdown}</strong> second${countdown > 1 ? 's' : ''}...`;
            } else {
                countdownElement.innerHTML = 'Refreshing now...';
                location.reload();
            }
        }, 1000);
    </script>
</body>
</html>
