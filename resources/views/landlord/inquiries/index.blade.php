@extends('layouts.account')

@section('content')
<div class="py-8">
    <h1 class="text-3xl font-bold mb-6">Inquiries</h1>

    @if($inquiries->count() > 0)
    <div class="space-y-4">
        @foreach($inquiries as $inquiry)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold">{{ $inquiry->property->title }}</h3>
                    <p class="text-gray-600 text-sm mt-1">From: {{ $inquiry->user->name }}</p>
                    <p class="text-gray-700 mt-3">{{ $inquiry->message }}</p>
                    <p class="text-gray-500 text-sm mt-2">{{ $inquiry->created_at->diffForHumans() }}</p>
                </div>
                <span class="px-3 py-1 bg-{{ $inquiry->status === 'pending' ? 'yellow' : 'green' }}-100 text-{{ $inquiry->status === 'pending' ? 'yellow' : 'green' }}-800 rounded-full text-sm">
                    {{ ucfirst($inquiry->status) }}
                </span>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $inquiries->links() }}
    </div>

    @else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <h3 class="text-lg font-medium text-gray-900">No inquiries yet</h3>
        <p class="mt-1 text-sm text-gray-500">Inquiries from tenants will appear here</p>
    </div>
    @endif
</div>
@endsection