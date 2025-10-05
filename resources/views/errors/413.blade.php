<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Too Large - PSU Dorm Finder</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="mx-auto w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
            </div>

            <h1 class="text-4xl font-bold text-gray-900 mb-4 text-center">📦 Upload Too Large</h1>
            <h2 class="text-xl font-semibold text-orange-600 mb-4 text-center">Your images exceed the server limit!</h2>

            <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mb-6">
                <p class="text-orange-800 font-semibold mb-2">🚫 What happened?</p>
                <p class="text-orange-700 text-sm">
                    The total size of your uploaded images exceeds the <strong>8MB server limit</strong>.
                    Each upload (all images combined) must be under 8MB.
                </p>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <p class="text-blue-800 font-semibold mb-3">💡 How to fix this:</p>
                <ol class="list-decimal list-inside text-blue-700 text-sm space-y-2">
                    <li><strong>Compress your images</strong> using <a href="https://tinypng.com" target="_blank" class="underline font-semibold">TinyPNG.com</a> (free tool)</li>
                    <li><strong>Upload in smaller batches:</strong>
                        <ul class="list-disc list-inside ml-6 mt-1 text-xs">
                            <li>Each image should be max <strong>2MB</strong></li>
                            <li>Upload <strong>3-4 images at a time</strong> (total ~6-8MB)</li>
                            <li>You can add more images after creating the property</li>
                        </ul>
                    </li>
                    <li><strong>Reduce image resolution</strong> before uploading (1920x1080 is enough)</li>
                </ol>
            </div>

            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                <p class="text-green-800 font-semibold mb-2">✅ Example:</p>
                <p class="text-green-700 text-sm">
                    Instead of uploading 10 images at once (15MB total), try:<br>
                    • <strong>Batch 1:</strong> Upload 3 images (6MB) ✓<br>
                    • <strong>Batch 2:</strong> Upload 3 more images (6MB) ✓<br>
                    • <strong>Batch 3:</strong> Upload remaining images (6MB) ✓
                </p>
            </div>

            <div class="text-center space-y-4">
                <button onclick="window.history.back()"
                        class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    ← Go Back & Try Again
                </button>

                <div class="text-sm text-gray-600">
                    <p>Need help compressing images?</p>
                    <a href="https://tinypng.com" target="_blank"
                       class="text-blue-600 hover:text-blue-800 font-semibold underline">
                        Visit TinyPNG (Free Image Compression)
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center text-gray-500 text-sm">
            <p>Server Upload Limit: 8MB per request</p>
        </div>
    </div>
</body>
</html>
