@extends('layouts.account')

@section('title', 'Admin Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="text-center mb-8">
        <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center">
            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <button class="text-green-600 text-sm hover:underline">📷 Change Picture</button>
        <h1 class="text-2xl font-bold mt-4">Welcome, {{ $user->name }}!</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Name</label>
                <p class="font-medium">{{ $user->name }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Email Address</label>
                <p class="font-medium">{{ $user->email }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Mobile Number</label>
                <p class="font-medium">{{ $user->phone ?? 'Not Set' }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Account Type</label>
                <p class="font-medium uppercase">{{ $user->role }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Account Created</label>
                <p class="font-medium">{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Last Updated</label>
                <p class="font-medium">{{ \Carbon\Carbon::parse($user->updated_at)->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Dashboard
                </a>
                <a href="{{ route('admin.properties.pending') }}"
                   class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                    Pending Properties
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    Manage Users
                </a>
            </div>
        </div>
    </div>
</div>
@endsection