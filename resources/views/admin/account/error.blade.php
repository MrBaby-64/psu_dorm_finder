@extends('layouts.account')

@section('title', 'Account Error')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="text-center">
        <svg class="mx-auto h-16 w-16 text-red-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-7.938 0h15.875c.621 0 1.125.504 1.125 1.125v12.75c0 .621-.504 1.125-1.125 1.125H4.062C3.441 24 2.937 23.496 2.937 22.875V10.125C2.937 9.504 3.441 9 4.062 9h15.875z"/>
        </svg>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Account Error</h1>
        <p class="text-gray-600 mb-6">{{ $error }}</p>

        @if(isset($details) && $details)
            <div class="bg-gray-100 p-4 rounded-lg text-left mb-6">
                <h3 class="font-medium text-gray-900 mb-2">Technical Details:</h3>
                <pre class="text-sm text-gray-600 overflow-auto">{{ $details }}</pre>
            </div>
        @endif

        <div class="flex justify-center space-x-4">
            <a href="{{ route('admin.account') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                Try Again
            </a>
            <a href="{{ route('admin.dashboard') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md">
                Go to Dashboard
            </a>
        </div>

        <div class="mt-6 text-sm text-gray-500">
            <p>If this problem persists, please check the system diagnostics.</p>
        </div>
    </div>
</div>
@endsection