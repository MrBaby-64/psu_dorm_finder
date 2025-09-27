@extends('layouts.account')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
                        <p class="text-gray-600 mt-1">{{ ucfirst(auth()->user()->role) }} Dashboard</p>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ now()->format('F j, Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats or Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.account') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md hover:bg-blue-50 transition-all duration-200 cursor-pointer group">
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-blue-600 group-hover:text-blue-800">Admin</div>
                        <div class="text-gray-600 mt-2 group-hover:text-blue-700">System Management</div>
                    </div>
                </a>
            @elseif(auth()->user()->role === 'landlord')
                <a href="{{ route('landlord.account') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md hover:bg-green-50 transition-all duration-200 cursor-pointer group">
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-green-600 group-hover:text-green-800">Landlord</div>
                        <div class="text-gray-600 mt-2 group-hover:text-green-700">Property Management</div>
                    </div>
                </a>
            @else
                <a href="{{ route('tenant.account') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md hover:bg-purple-50 transition-all duration-200 cursor-pointer group">
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-purple-600 group-hover:text-purple-800">Tenant</div>
                        <div class="text-gray-600 mt-2 group-hover:text-purple-700">Find Your Home</div>
                    </div>
                </a>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="text-2xl font-bold text-gray-700">{{ now()->format('g:i A') }}</div>
                    <div class="text-gray-600 mt-2">Current Time</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="text-2xl font-bold text-gray-700">Active</div>
                    <div class="text-gray-600 mt-2">Account Status</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection