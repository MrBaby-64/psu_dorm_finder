@extends('layouts.account')

@section('title', 'Add New Property')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Add New Property</h1>
        <p class="text-sm text-gray-600">Create a new property listing for approval.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('landlord.properties.store') }}" class="space-y-6">
        @csrf

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Property Title</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" rows="4" required
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Monthly Rent (₱)</label>
                <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" step="0.01" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="room_count" class="block text-sm font-medium text-gray-700">Number of Rooms</label>
                <input type="number" name="room_count" id="room_count" value="{{ old('room_count') }}" min="1" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div>
            <label for="location_text" class="block text-sm font-medium text-gray-700">Location</label>
            <input type="text" name="location_text" id="location_text" value="{{ old('location_text') }}" required
                   placeholder="e.g., Near PSU Main Campus, Brgy. Lingsat, San Carlos City"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('landlord.properties.index') }}"
               class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                Create Property
            </button>
        </div>
    </form>
</div>
@endsection