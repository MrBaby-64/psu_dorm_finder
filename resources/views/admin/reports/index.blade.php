{{-- resources/views/admin/reports/index.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    
    @include('layouts.navigation')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <h1 class="text-3xl font-bold mb-6">Reports & Analytics</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Properties</div>
                <div class="text-3xl font-bold">{{ $stats['total_properties'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Bookings</div>
                <div class="text-3xl font-bold">{{ $stats['total_bookings'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Messages</div>
                <div class="text-3xl font-bold">{{ $stats['total_messages'] }}</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Export Data</h2>
            <a href="{{ route('admin.reports.export') }}" 
               class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 inline-block">
                Download CSV Report
            </a>
        </div>

    </div>

</body>
</html>