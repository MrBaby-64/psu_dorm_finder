@extends('layouts.account')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="" />
<style>
    #propertyMap {
        height: 400px;
        width: 100%;
        border-radius: 10px;
        z-index: 10;
        background-color: #f0f0f0;
        border: 2px solid #ddd;
    }
    .image-preview {
        position: relative;
        display: inline-block;
        margin: 5px;
    }
    .remove-image {
        position: absolute;
        top: -5px;
        right: -5px;
        background: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        cursor: pointer;
        font-size: 12px;
    }
    .step-section {
        background: white;
        border-radius: 8px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .step-header {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e5e7eb;
    }
</style>
@endpush

@section('title', 'Add New Property')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Add New Property</h1>
            <p class="text-gray-600 mt-2">Create a comprehensive listing for your property</p>
        </div>

        <!-- Error Summary -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <h3 class="text-red-800 font-medium">Please fix the following errors:</h3>
                <ul class="mt-2 text-red-700 text-sm list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('landlord.properties.store') }}" enctype="multipart/form-data" id="propertyForm">
            @csrf
            <input type="hidden" name="form_token" value="{{ $formToken }}">

            <!-- Step 1: Basic Information -->
            <div class="step-section">
                <h2 class="step-header">📝 Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700">Property Title *</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                               placeholder="e.g., Cozy Student Apartment Near PSU"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                        <textarea name="description" id="description" rows="4" required
                                  placeholder="Provide a detailed description of your property (minimum 50 characters)"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Minimum 50 characters required</p>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Monthly Rent (₱) *</label>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" min="500" max="50000" required
                               placeholder="5000"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="room_count" class="block text-sm font-medium text-gray-700">Number of Rooms *</label>
                        <input type="number" name="room_count" id="room_count" value="{{ old('room_count') }}" min="1" max="100" required
                               placeholder="1"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Step 2: Location Details -->
            <div class="step-section">
                <h2 class="step-header">📍 Location Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="location_text" class="block text-sm font-medium text-gray-700">Location Description *</label>
                        <input type="text" name="location_text" id="location_text" value="{{ old('location_text') }}" required
                               placeholder="e.g., 5 minutes walk from PSU Main Campus"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="address_line" class="block text-sm font-medium text-gray-700">Street Address *</label>
                        <input type="text" name="address_line" id="address_line" value="{{ old('address_line') }}" required
                               placeholder="e.g., 123 Main Street"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="barangay" class="block text-sm font-medium text-gray-700">Barangay *</label>
                        <input type="text" name="barangay" id="barangay" value="{{ old('barangay') }}" required
                               placeholder="e.g., Lingsat"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City *</label>
                        <select name="city" id="city" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select City</option>
                            <option value="Bacolor" {{ old('city') === 'Bacolor' ? 'selected' : '' }}>Bacolor</option>
                            <option value="San Fernando" {{ old('city') === 'San Fernando' ? 'selected' : '' }}>San Fernando</option>
                        </select>
                    </div>

                    <!-- Map Section -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Property Location on Map *</label>
                        <div id="propertyMap"></div>
                        <p class="mt-2 text-sm text-gray-600">Click on the map to set your property's exact location</p>
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                    </div>
                </div>
            </div>

            <!-- Step 3: Property Images -->
            <div class="step-section">
                <h2 class="step-header">📸 Property Images</h2>
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700">Upload Images *</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">
                        Upload 1-10 images (JPEG, PNG, WebP). Maximum 5MB per image. First image will be the cover photo.
                    </p>
                    <div id="imagePreview" class="mt-4 flex flex-wrap"></div>
                </div>
            </div>

            <!-- Step 4: Amenities -->
            @if($amenities->isNotEmpty())
            <div class="step-section">
                <h2 class="step-header">🏠 Amenities</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($amenities as $amenity)
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                                   {{ is_array(old('amenities')) && in_array($amenity->id, old('amenities')) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">
                                @if($amenity->icon)
                                    <span class="mr-1">{{ $amenity->icon }}</span>
                                @endif
                                {{ $amenity->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Step 5: Visit Scheduling (Optional) -->
            <div class="step-section">
                <h2 class="step-header">📅 Visit Scheduling (Optional)</h2>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="visit_schedule_enabled" id="visit_schedule_enabled" value="1"
                               {{ old('visit_schedule_enabled') ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="visit_schedule_enabled" class="ml-2 text-sm font-medium text-gray-700">
                            Enable visit scheduling for this property
                        </label>
                    </div>

                    <div id="visitScheduleOptions" style="{{ old('visit_schedule_enabled') ? '' : 'display: none;' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Available Days</label>
                                <div class="space-y-2">
                                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="visit_days[]" value="{{ $day }}"
                                                   {{ is_array(old('visit_days')) && in_array($day, old('visit_days')) ? 'checked' : '' }}
                                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                            <span class="ml-2 text-sm capitalize">{{ $day }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="visit_time_start" class="block text-sm font-medium text-gray-700">Start Time</label>
                                    <input type="time" name="visit_time_start" id="visit_time_start" value="{{ old('visit_time_start') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="visit_time_end" class="block text-sm font-medium text-gray-700">End Time</label>
                                    <input type="time" name="visit_time_end" id="visit_time_end" value="{{ old('visit_time_end') }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="visit_duration" class="block text-sm font-medium text-gray-700">Visit Duration</label>
                                    <select name="visit_duration" id="visit_duration"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Duration</option>
                                        <option value="30" {{ old('visit_duration') == '30' ? 'selected' : '' }}>30 minutes</option>
                                        <option value="45" {{ old('visit_duration') == '45' ? 'selected' : '' }}>45 minutes</option>
                                        <option value="60" {{ old('visit_duration') == '60' ? 'selected' : '' }}>1 hour</option>
                                        <option value="90" {{ old('visit_duration') == '90' ? 'selected' : '' }}>1.5 hours</option>
                                        <option value="120" {{ old('visit_duration') == '120' ? 'selected' : '' }}>2 hours</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="visit_instructions" class="block text-sm font-medium text-gray-700">Special Instructions</label>
                                    <textarea name="visit_instructions" id="visit_instructions" rows="3"
                                              placeholder="Any special instructions for visitors..."
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('visit_instructions') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('landlord.properties.index') }}"
                   class="px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Property
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    const map = L.map('propertyMap').setView([15.0516, 120.6527], 13); // PSU coordinates

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;

    // Set initial marker if coordinates exist
    const lat = document.getElementById('latitude').value;
    const lng = document.getElementById('longitude').value;
    if (lat && lng) {
        marker = L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], 15);
    }

    // Map click handler
    map.on('click', function(e) {
        const { lat, lng } = e.latlng;

        if (marker) {
            map.removeLayer(marker);
        }

        marker = L.marker([lat, lng]).addTo(map);
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
    });

    // Visit scheduling toggle
    const scheduleCheckbox = document.getElementById('visit_schedule_enabled');
    const scheduleOptions = document.getElementById('visitScheduleOptions');

    scheduleCheckbox.addEventListener('change', function() {
        scheduleOptions.style.display = this.checked ? 'block' : 'none';
    });

    // Image preview
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');

    imageInput.addEventListener('change', function() {
        imagePreview.innerHTML = '';

        for (let i = 0; i < this.files.length; i++) {
            const file = this.files[i];
            const reader = new FileReader();

            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'image-preview';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="w-20 h-20 object-cover rounded-lg">
                    ${i === 0 ? '<span class="absolute bottom-0 left-0 bg-blue-600 text-white text-xs px-1 rounded">Cover</span>' : ''}
                `;
                imagePreview.appendChild(div);
            };

            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush