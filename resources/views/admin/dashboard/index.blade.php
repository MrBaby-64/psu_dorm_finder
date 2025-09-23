@extends('layouts.account')

@section('content')
<div class="py-8">
    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Pending Approval -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm mb-2">Pending Approval</h3>
            <p class="text-4xl font-bold text-orange-500">{{ $stats['pending_properties'] }}</p>
            <a href="{{ route('admin.properties.pending') }}" class="text-blue-600 text-sm mt-2 inline-block">Review now →</a>
        </div>

        <!-- Approved Properties -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm mb-2">Approved Properties</h3>
            <p class="text-4xl font-bold text-green-500">{{ $stats['approved_properties'] }}</p>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm mb-2">Total Users</h3>
            <p class="text-4xl font-bold text-blue-500">{{ $stats['total_users'] }}</p>
            <p class="text-sm text-gray-500 mt-2">{{ $stats['landlords'] }} landlords, {{ $stats['tenants'] }} tenants</p>
        </div>

        <!-- Total Bookings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm mb-2">Total Bookings</h3>
            <p class="text-4xl font-bold text-purple-500">{{ $stats['total_bookings'] }}</p>
            <p class="text-sm text-gray-500 mt-2">{{ $stats['pending_bookings'] }} pending</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Properties -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">Recent Properties</h2>
            </div>
            <div class="divide-y">
                @foreach($recentProperties as $property)
                <div class="p-6 flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold">{{ $property->title }}</h3>
                        <p class="text-sm text-gray-600">by {{ $property->landlord->name }}</p>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                        {{ ucfirst($property->approval_status) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">Recent Users</h2>
            </div>
            <div class="divide-y">
                @foreach($recentUsers as $user)
                <div class="p-6 flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    </div>
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection