@extends('layouts.account')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="text-center mb-8">
        <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center">
            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <button class="text-green-600 text-sm hover:underline">📷 Change Picture</button>
        <h1 class="text-2xl font-bold mt-4">Welcome, {{ auth()->user()->name }}!</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Name</label>
                <p class="font-medium">{{ auth()->user()->name }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Email Address</label>
                <p class="font-medium">{{ auth()->user()->email }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Mobile Number</label>
                <p class="font-medium">{{ auth()->user()->phone ?? 'Not Set' }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Account Type</label>
                <p class="font-medium uppercase">{{ auth()->user()->role }}</p>
            </div>
        </div>
    </div>

    @if(!auth()->user()->phone)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="font-bold text-blue-900">Complete Your Profile</h3>
                <p class="text-sm text-blue-700 mt-1">Add your mobile number to receive important notifications.</p>
                <a href="{{ route('profile.edit') }}" class="inline-block mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                    Update Profile
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection