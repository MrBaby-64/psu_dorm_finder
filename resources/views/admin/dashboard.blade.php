{{-- resources/views/admin/dashboard.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PSU Dorm Finder</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    
    @include('layouts.navigation')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <h1 class="text-3xl font-bold mb-8">Admin Dashboard</h1>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Pending Approval</div>
                <div class="text-3xl font-bold text-orange-600">{{ $stats['pending_properties'] }}</div>
                <a href="{{ route('admin.properties.pending') }}" class="text-sm text-blue-600 hover:underline">Review now →</a>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Approved Properties</div>
                <div class="text-3xl font-bold text-green-600">{{ $stats['approved_properties'] }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Users</div>
                <div class="text-3xl font-bold text-blue-600">{{ $stats['total_users'] }}</div>
                <div class="text-sm text-gray-500">{{ $stats['landlords'] }} landlords, {{ $stats['tenants'] }} tenants</div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Bookings</div>
                <div class="text-3xl font-bold text-purple-600">{{ $stats['total_bookings'] }}</div>
                <div class="text-sm text-gray-500">{{ $stats['pending_bookings'] }} pending</div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Recent Properties --}}
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold">Recent Properties</h2>
                </div>
                <div class="p-6">
                    @foreach($recentProperties as $property)
                    <div class="flex items-center justify-between py-3 border-b last:border-0">
                        <div>
                            <div class="font-medium">{{ $property->title }}</div>
                            <div class="text-sm text-gray-500">by {{ $property->landlord->name }}</div>
                        </div>
                        <div>
                            @if($property->approval_status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">Pending</span>
                            @elseif($property->approval_status === 'approved')
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Approved</span>
                            @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded">Rejected</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent Users --}}
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold">Recent Users</h2>
                </div>
                <div class="p-6">
                    @foreach($recentUsers as $user)
                    <div class="flex items-center justify-between py-3 border-b last:border-0">
                        <div>
                            <div class="font-medium">{{ $user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                        </div>
                        <div>
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded capitalize">{{ $user->role }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Admin Actions --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.properties.pending') }}" 
               class="block bg-orange-100 border border-orange-200 rounded-lg p-6 hover:bg-orange-200 transition">
                <h3 class="font-semibold text-orange-800">Property Approval Queue</h3>
                <p class="text-sm text-orange-700 mt-1">Review and approve pending properties</p>
            </a>

            <a href="{{ route('admin.users.index') }}" 
               class="block bg-blue-100 border border-blue-200 rounded-lg p-6 hover:bg-blue-200 transition">
                <h3 class="font-semibold text-blue-800">User Management</h3>
                <p class="text-sm text-blue-700 mt-1">Manage users and roles</p>
            </a>

            <a href="{{ route('admin.reports.index') }}" 
               class="block bg-purple-100 border border-purple-200 rounded-lg p-6 hover:bg-purple-200 transition">
                <h3 class="font-semibold text-purple-800">Reports</h3>
                <p class="text-sm text-purple-700 mt-1">View analytics and export data</p>
            </a>
        </div>

    </div>

</body>
</html>