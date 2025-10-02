@extends('layouts.account')

@section('content')
<div class="max-w-4xl mx-auto">
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-400 text-green-800 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-400 text-red-800 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="text-center mb-8">
        <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center overflow-hidden relative">
            @if(auth()->user()->profile_picture)
                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" class="w-full h-full object-cover">
            @else
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            @endif
        </div>
        <form action="{{ route('admin.account.upload-picture') }}" method="POST" enctype="multipart/form-data" id="profilePictureForm">
            @csrf
            <input type="file" name="profile_picture" id="profilePictureInput" accept="image/*" class="hidden" onchange="this.form.submit()">
            <button type="button" onclick="document.getElementById('profilePictureInput').click()" class="text-green-600 text-sm hover:underline">
                📷 Change Picture
            </button>
        </form>
        <h1 class="text-2xl font-bold mt-4">Welcome, {{ auth()->user()->name }}!</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Name</label>
                <p class="font-medium">{{ auth()->user()->name }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Mobile Number</label>
                <p class="font-medium">{{ auth()->user()->phone ?? 'Not Set' }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Email Address</label>
                <p class="text-sm font-medium break-all">{{ auth()->user()->email }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Account Type</label>
                <p class="font-medium uppercase">{{ auth()->user()->role }}</p>
            </div>
        </div>
    </div>
</div>
@endsection