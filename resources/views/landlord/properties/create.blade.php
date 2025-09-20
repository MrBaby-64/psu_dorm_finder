@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('landlord.properties.index') }}" class="text-green-600 hover:text-green-700">
            ← Back to My Properties
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Add New Property</h1>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('landlord.properties.store') }}" method="POST">
            @csrf

            <!-- Title -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Property Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required 
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Description *</label>
                <textarea name="description" rows="4" required 
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">{{ old('description') }}</textarea>
            </div>

            <!-- Location Details -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Address Line *</label>
                    <input type="text" name="address_line" value="{{ old('address_line') }}" required 
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Barangay *</label>
                    <input type="text" name="barangay" value="{{ old('barangay') }}" required 
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">City *</label>
                    <select name="city" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">Select City</option>
                        <option value="Bacolor" {{ old('city') == 'Bacolor' ? 'selected' : '' }}>Bacolor</option>
                        <option value="San Fernando" {{ old('city') == 'San Fernando' ? 'selected' : '' }}>San Fernando</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Location Text *</label>
                    <input type="text" name="location_text" value="{{ old('location_text') }}" required 
                        placeholder="e.g., 5 minutes walk to PSU"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>

            <!-- Coordinates -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Latitude *</label>
                    <input type="number" step="0.000001" name="latitude" value="{{ old('latitude') }}" required 
                        placeholder="14.6705"
                        class="w-full px-4 py-2 border rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Example: 14.6705</p>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Longitude *</label>
                    <input type="number" step="0.000001" name="longitude" value="{{ old('longitude') }}" required 
                        placeholder="120.6298"
                        class="w-full px-4 py-2 border rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Example: 120.6298</p>
                </div>
            </div>

            <!-- Price & Rooms -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Monthly Rate (₱) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price') }}" required 
                        placeholder="3500"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Number of Rooms *</label>
                    <input type="number" name="room_count" value="{{ old('room_count') }}" required 
                        min="1"
                        placeholder="2"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>

            <!-- Visit Schedule Checkbox -->
            <div class="mb-6 border-t pt-4">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="visit_schedule_enabled" 
                        value="1"
                        {{ old('visit_schedule_enabled') ? 'checked' : '' }}
                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                    >
                    <span class="ml-2 text-sm text-gray-700">
                        <strong>Enable visit scheduling</strong> - Allow tenants to request property visits
                    </span>
                </label>
                <p class="text-xs text-gray-500 mt-1 ml-6">
                    When enabled, tenants can schedule visits directly through the property page
                </p>
            </div>

            <!-- Amenities -->
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Amenities</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($amenities as $amenity)
                    <label class="flex items-center">
                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" 
                            {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-green-600 mr-2">
                        {{ $amenity->name }}
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('landlord.properties.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                    Create Property
                </button>
            </div>
        </form>
    </div>
</div>
@endsection