@extends('layouts.account')

@section('content')
<div class="py-8">
    <div class="mb-6">
        <a href="{{ route('landlord.properties.index') }}" class="text-green-600 hover:text-green-700">
            ← Back to My Properties
        </a>
    </div>

    <h1 class="text-3xl font-bold mb-6">Edit Property</h1>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Form --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Property Images Section --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Property Images</h2>
                
                {{-- Current Images --}}
                @if($property->images->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                    @foreach($property->images as $image)
                    <div class="relative group">
                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                             alt="{{ $property->title }}"
                             class="w-full h-32 object-cover rounded-lg">
                        
                        @if($image->is_cover)
                        <span class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded">
                            Cover
                        </span>
                        @else
                        <form action="{{ route('landlord.properties.images.set-cover', [$property, $image]) }}" 
                              method="POST" class="absolute top-2 left-2">
                            @csrf
                            <button type="submit" class="bg-blue-500 text-white text-xs px-2 py-1 rounded hover:bg-blue-600">
                                Set as Cover
                            </button>
                        </form>
                        @endif
                        
                        <form action="{{ route('landlord.properties.images.delete', [$property, $image]) }}" 
                              method="POST" class="absolute top-2 right-2"
                              onsubmit="return confirm('Delete this image?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white text-xs px-2 py-1 rounded hover:bg-red-600">
                                Delete
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 mb-4">No images uploaded yet</p>
                @endif

                {{-- Upload New Images --}}
                <form action="{{ route('landlord.properties.images.upload', $property) }}" 
                      method="POST" 
                      enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Images</label>
                        <input type="file" name="images[]" multiple accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <p class="text-xs text-gray-500 mt-1">
                            You can select multiple images. Max 5MB per image. Formats: JPEG, PNG, WebP
                        </p>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Upload Images
                    </button>
                </form>
            </div>

            {{-- Property Details Form --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Property Details</h2>
                
                <form action="{{ route('landlord.properties.update', $property) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Title *</label>
                            <input type="text" name="title" value="{{ old('title', $property->title) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                            <textarea name="description" rows="5" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md">{{ old('description', $property->description) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location Description *</label>
                            <input type="text" name="location_text" value="{{ old('location_text', $property->location_text) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                                <input type="text" name="address_line" value="{{ old('address_line', $property->address_line) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Barangay *</label>
                                <input type="text" name="barangay" value="{{ old('barangay', $property->barangay) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                            <select name="city" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="Bacolor" {{ $property->city === 'Bacolor' ? 'selected' : '' }}>Bacolor</option>
                                <option value="San Fernando" {{ $property->city === 'San Fernando' ? 'selected' : '' }}>San Fernando</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Latitude *</label>
                                <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $property->latitude) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Longitude *</label>
                                <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $property->longitude) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Rent (₱) *</label>
                                <input type="number" name="price" value="{{ old('price', $property->price) }}" min="0" step="0.01" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Rooms *</label>
                                <input type="number" name="room_count" value="{{ old('room_count', $property->room_count) }}" min="1" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($amenities as $amenity)
                                <label class="flex items-center">
                                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                                           {{ $property->amenities->contains($amenity->id) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-green-600">
                                    <span class="ml-2">{{ $amenity->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    name="visit_schedule_enabled" 
                                    value="1"
                                    {{ old('visit_schedule_enabled', $property->visit_schedule_enabled ?? false) ? 'checked' : '' }}
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

                        <div class="flex gap-4 pt-4">
                            <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-semibold">
                                Update Property
                            </button>
                            <a href="{{ route('landlord.properties.index') }}" 
                               class="border border-gray-300 px-6 py-3 rounded-lg hover:bg-gray-50">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                <h3 class="font-semibold mb-4">Property Status</h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Approval Status:</span>
                        <span class="font-semibold capitalize">{{ $property->approval_status }}</span>
                    </div>
                    
                    @if($property->is_verified)
                    <div class="text-blue-600">✓ PSU Verified</div>
                    @endif
                    
                    @if($property->is_featured)
                    <div class="text-yellow-600">★ Featured Property</div>
                    @endif
                    
                    @if($property->rejection_reason)
                    <div class="text-red-600">
                        <strong>Rejection Reason:</strong>
                        <p>{{ $property->rejection_reason }}</p>
                    </div>
                    @endif
                </div>

                <div class="mt-6 pt-6 border-t">
                    <a href="{{ route('properties.show', $property) }}" 
                       target="_blank"
                       class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        View Public Page
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection