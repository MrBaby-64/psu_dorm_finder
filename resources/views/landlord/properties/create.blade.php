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
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Add New Property</h1>
            <p class="text-gray-600">Fill in all the details below to list your property</p>
        </div>

        <!-- Validation Errors Summary -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-red-800 font-semibold">Please complete the following required fields:</h3>
                </div>
                <ul class="list-disc list-inside text-red-700 space-y-1">
                    @error('title')<li>Property Title is required</li>@enderror
                    @error('description')<li>Property Description is required</li>@enderror
                    @error('address_line')<li>Street Address is required</li>@enderror
                    @error('barangay')<li>Barangay is required</li>@enderror
                    @error('city')<li>City is required</li>@enderror
                    @error('location_text')<li>Location Description is required</li>@enderror
                    @error('latitude')<li>Map coordinates (latitude) are required</li>@enderror
                    @error('longitude')<li>Map coordinates (longitude) are required</li>@enderror
                    @error('price')<li>Monthly Rate is required</li>@enderror
                    @error('room_count')<li>Number of Rooms is required</li>@enderror
                    @error('amenities')<li>At least one amenity must be selected</li>@enderror
                    @error('images')<li>At least one property image is required</li>@enderror
                    @error('images.*')<li>Images must be JPEG, JPG, PNG, or WEBP format and under 5MB</li>@enderror
                </ul>
            </div>
        @endif

        <form action="{{ route('landlord.properties.store') }}" method="POST" enctype="multipart/form-data" id="propertyForm" onsubmit="return validateForm()">
            @csrf
            
            <!-- Images Section -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Property Images</h2>
                <p class="text-sm text-gray-600 mb-4">Add photos of your property. The first image will be used as the cover photo.</p>
                
                <!-- Image Upload -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Choose Property Images <span class="text-red-500">*</span>
                    </label>
                    <input type="file"
                           name="images[]"
                           id="imageInput"
                           multiple
                           accept="image/jpeg,image/jpg,image/png,image/webp"
                           required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    <p class="text-sm text-gray-500 mt-1">Select 1-10 images (JPEG, JPG, PNG, WEBP, max 5MB each)</p>
                    @error('images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Previews -->
                <div id="imagePreviews" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
            </div>

            <!-- Property Information -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Property Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Property Title -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Property Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               value="{{ old('title') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('title') border-red-300 @enderror"
                               placeholder="e.g., Cozy Student Dormitory near PSU">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" 
                                  rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('description') border-red-300 @enderror"
                                  placeholder="Describe your property, its features, and what makes it special...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Monthly Rate -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Monthly Rate (₱) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="price" 
                               value="{{ old('price') }}"
                               min="0" 
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('price') border-red-300 @enderror"
                               placeholder="e.g., 5000">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Number of Rooms -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Number of Rooms <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="room_count" 
                               value="{{ old('room_count') }}"
                               min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('room_count') border-red-300 @enderror"
                               placeholder="e.g., 10">
                        @error('room_count')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Location Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Street Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Street Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="address_line" 
                               value="{{ old('address_line') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('address_line') border-red-300 @enderror"
                               placeholder="e.g., 123 Main Street">
                        @error('address_line')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Barangay -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Barangay <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="barangay" 
                               value="{{ old('barangay') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('barangay') border-red-300 @enderror"
                               placeholder="e.g., Barangay Poblacion">
                        @error('barangay')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            City <span class="text-red-500">*</span>
                        </label>
                        <select name="city" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('city') border-red-300 @enderror">
                            <option value="">Select City</option>
                            <option value="Bacolor" {{ old('city') === 'Bacolor' ? 'selected' : '' }}>Bacolor</option>
                            <option value="San Fernando" {{ old('city') === 'San Fernando' ? 'selected' : '' }}>San Fernando</option>
                        </select>
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Location Description <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="location_text" 
                               value="{{ old('location_text') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('location_text') border-red-300 @enderror"
                               placeholder="e.g., 5 minutes walk from PSU Main Gate, near Jollibee">
                        @error('location_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Map Coordinates -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Latitude <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="latitude" 
                               id="latitudeInput"
                               value="{{ old('latitude') }}"
                               step="any"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('latitude') border-red-300 @enderror"
                               placeholder="e.g., 14.997480">
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Longitude <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="longitude" 
                               id="longitudeInput"
                               value="{{ old('longitude') }}"
                               step="any"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('longitude') border-red-300 @enderror"
                               placeholder="e.g., 120.653230">
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Map Section -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Property Location</h2>
                <p class="text-sm text-gray-600 mb-4">Click on the map to set your property's exact location</p>
                
                <!-- Map Controls -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <button type="button" onclick="findOnMap()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                        🗺️ Find on Map
                    </button>
                    <button type="button" onclick="useMyLocation()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                        📍 Use My Current Location
                    </button>
                    <button type="button" onclick="openDirectionsModal()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm">
                        🧭 Get Directions
                    </button>
                </div>

                <!-- Map Container -->
                <div id="propertyMap" class="border border-gray-300 rounded-lg">
                    <div id="mapFallback" class="h-full w-full flex items-center justify-center bg-gray-100 text-gray-600">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="text-lg font-semibold">Interactive Map</p>
                            <p class="text-sm">Loading map... Please wait</p>
                            <p class="text-xs mt-2">Click "Use My Location" or manually enter coordinates below</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-700">
                        <strong>Tip:</strong> You can drag the marker to fine-tune the location or use the buttons above to automatically set coordinates.
                    </p>
                </div>
            </div>

            <!-- Amenities -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Amenities & Features <span class="text-red-500">*</span>
                </h2>
                <p class="text-sm text-gray-600 mb-4">Select all amenities available at your property</p>
                
                @if($amenities->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($amenities as $amenity)
                            <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" 
                                       name="amenities[]" 
                                       value="{{ $amenity->id }}"
                                       class="text-green-600 border-gray-300 rounded focus:ring-green-500"
                                       {{ (is_array(old('amenities')) && in_array($amenity->id, old('amenities'))) ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-gray-700">{{ $amenity->name }}</span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No amenities available. Please contact admin.</p>
                @endif
                
                @error('amenities')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Additional Settings -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Settings</h2>
                
                <div class="flex items-center space-x-3">
                    <input type="checkbox"
                           name="visit_schedule_enabled"
                           id="visit_schedule_enabled"
                           class="text-green-600 border-gray-300 rounded focus:ring-green-500"
                           onchange="toggleVisitSchedule()"
                           {{ old('visit_schedule_enabled') ? 'checked' : '' }}>
                    <label for="visit_schedule_enabled" class="text-sm font-medium text-gray-700">
                        Enable visit scheduling
                    </label>
                </div>
                <p class="text-sm text-gray-500 mt-1">Allow tenants to request scheduled visits to your property</p>

                <!-- Visit Schedule Settings -->
                <div id="visitScheduleSettings" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg" style="display: {{ old('visit_schedule_enabled') ? 'block' : 'none' }};">
                    <h3 class="text-md font-semibold text-gray-800 mb-3">Visit Schedule Settings</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Available Days -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Available Days</label>
                            <div class="space-y-2">
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                    <label class="flex items-center">
                                        <input type="checkbox"
                                               name="visit_days[]"
                                               value="{{ strtolower($day) }}"
                                               class="text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                               {{ (is_array(old('visit_days')) && in_array(strtolower($day), old('visit_days'))) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">{{ $day }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Time Settings -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Available From</label>
                                <input type="time"
                                       name="visit_time_start"
                                       value="{{ old('visit_time_start', '09:00') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Available Until</label>
                                <input type="time"
                                       name="visit_time_end"
                                       value="{{ old('visit_time_end', '17:00') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Visit Duration (minutes)</label>
                                <select name="visit_duration" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="30" {{ old('visit_duration') == '30' ? 'selected' : '' }}>30 minutes</option>
                                    <option value="45" {{ old('visit_duration') == '45' ? 'selected' : '' }}>45 minutes</option>
                                    <option value="60" {{ old('visit_duration') == '60' ? 'selected' : '' }}>1 hour</option>
                                    <option value="90" {{ old('visit_duration') == '90' ? 'selected' : '' }}>1.5 hours</option>
                                    <option value="120" {{ old('visit_duration') == '120' ? 'selected' : '' }}>2 hours</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Instructions</label>
                        <textarea name="visit_instructions"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="e.g., Please call 30 minutes before visit, Meet at the front gate, etc.">{{ old('visit_instructions') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <a href="{{ route('landlord.properties.index') }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition font-medium">
                    Cancel
                </a>
                
                <button type="submit" 
                        class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition font-medium">
                    Create Property
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Get Directions Modal -->
<div id="directionsModal" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md m-4 overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Get Directions</h3>
                <button onclick="closeDirectionsModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-3">
                <!-- My Location -->
                <button onclick="getDirectionsGPS()" class="w-full p-4 border-2 border-blue-300 bg-blue-50 rounded-xl hover:bg-blue-100 hover:shadow-lg transition-all text-left">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <div class="font-semibold text-blue-700">My Location</div>
                            <div class="text-sm text-blue-600">Use device GPS</div>
                        </div>
                    </div>
                </button>

                <!-- My Address -->
                @auth
                    @if(auth()->user()->role === 'landlord' && auth()->user()->address)
                        <button onclick="getDirectionsAddress()" class="w-full p-4 border-2 border-green-300 bg-green-50 rounded-xl hover:bg-green-100 hover:shadow-lg transition-all text-left">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <div>
                                    <div class="font-semibold text-green-700">My Address</div>
                                    <div class="text-sm text-green-600">{{ auth()->user()->address }}, {{ auth()->user()->city }}</div>
                                </div>
                            </div>
                        </button>
                    @endif
                @endauth

                <!-- Search Address -->
                <div class="border-2 border-orange-300 bg-orange-50 rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <div>
                            <div class="font-semibold text-orange-700">Search Address</div>
                            <div class="text-sm text-orange-600">Type to find location</div>
                        </div>
                    </div>
                    <input type="text" id="searchInput" placeholder="e.g., SM City Pampanga, Bacolor Town Hall"
                           class="w-full px-3 py-2 border border-orange-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-sm">
                    <button onclick="searchAddress()" class="mt-2 w-full bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 text-sm font-semibold">
                        Search & Set Location
                    </button>
                </div>

                <!-- Click on Map -->
                <button onclick="enableMapClick()" class="w-full p-4 border-2 border-purple-300 bg-purple-50 rounded-xl hover:bg-purple-100 hover:shadow-lg transition-all text-left">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <div>
                            <div class="font-semibold text-purple-700">Click on Map</div>
                            <div class="text-sm text-purple-600">Drop a pin to set location</div>
                        </div>
                    </div>
                </button>
            </div>

            <div class="mt-6 text-center">
                <button onclick="closeDirectionsModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script>
    function validateForm() {
        // Check if at least one image is available (either uploaded or existing temp images)
        const imageInput = document.getElementById('imageInput');
        const imagePreviews = document.getElementById('imagePreviews');
        const hasNewImages = imageInput.files.length > 0;
        const hasTempImages = imagePreviews.children.length > 0;

        if (!hasNewImages && !hasTempImages) {
            alert('Please select at least one property image.');
            imageInput.focus();
            return false;
        }

        // Check if at least one amenity is selected
        const amenityCheckboxes = document.querySelectorAll('input[name="amenities[]"]:checked');
        if (amenityCheckboxes.length === 0) {
            alert('Please select at least one amenity.');
            document.querySelector('input[name="amenities[]"]').focus();
            return false;
        }

        // Check if latitude and longitude are set
        const latInput = document.getElementById('latitudeInput');
        const lngInput = document.getElementById('longitudeInput');
        if (!latInput.value || !lngInput.value) {
            alert('Please set the property location on the map.');
            document.getElementById('propertyMap').scrollIntoView({ behavior: 'smooth' });
            return false;
        }

        return true;
    }
    let map, propertyMarker;
    const psuLocation = [14.997480043450848, 120.65323030030329];
    
    // Image Upload Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('imageInput');
        const imagePreviews = document.getElementById('imagePreviews');

        // No temp images - simplified approach

        if (imageInput && imagePreviews) {
            imageInput.addEventListener('change', function(e) {
                imagePreviews.innerHTML = '';

                const files = Array.from(e.target.files);

                files.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewDiv = document.createElement('div');
                            previewDiv.className = 'relative image-preview';
                            const html = `
                                <img src="${e.target.result}"
                                     alt="Preview ${index + 1}"
                                     class="w-full h-24 object-cover rounded-lg border border-gray-300">
                                ${index === 0 ? '<span class="absolute top-1 left-1 bg-green-500 text-white text-xs px-2 py-1 rounded">Cover</span>' : ''}
                                <button type="button" class="remove-image" onclick="removeImage(this, ${index})">×</button>
                                <p class="text-xs text-gray-500 mt-1 truncate">${file.name}</p>
                            `;
                            previewDiv.innerHTML = html;
                            imagePreviews.appendChild(previewDiv);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        }

    // Initialize map with retry mechanism
    let mapInitAttempts = 0;
    function tryInitMap() {
        if (mapInitAttempts < 5) {
            try {
                initMap();
            } catch (error) {
                mapInitAttempts++;
                console.warn(`Map init attempt ${mapInitAttempts} failed:`, error);
                setTimeout(tryInitMap, 200 * mapInitAttempts);
            }
        } else {
            console.error('Map initialization failed after 5 attempts');
        }
    }

    setTimeout(tryInitMap, 100);

    // Focus on first error field
    @if($errors->any())
        setTimeout(() => {
            const firstErrorField = document.querySelector('.border-red-300');
            if (firstErrorField) {
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstErrorField.focus();
            }
            // Re-initialize map after error focus
            setTimeout(tryInitMap, 300);
        }, 100);
    @endif

    // Disable submit button during form submission
    const form = document.getElementById('propertyForm');
    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function() {
        submitButton.disabled = true;
        submitButton.textContent = 'Creating Property...';
    });
    });

    
    function removeImage(button, index) {
        button.parentElement.remove();
        // Note: This only removes the preview, not the actual file from input
        // For full removal, you'd need more complex file handling
    }
    
    // Map Functionality
    function initMap() {
        try {
            console.log('Initializing map...');

            // Check if Leaflet is loaded
            if (typeof L === 'undefined') {
                throw new Error('Leaflet library not loaded');
            }
            console.log('Leaflet library loaded');

            // Check if map container exists
            const mapContainer = document.getElementById('propertyMap');
            if (!mapContainer) {
                throw new Error('Map container not found');
            }
            console.log('Map container found:', mapContainer);

            // Clear any existing map
            if (map) {
                map.remove();
                map = null;
            }

            // Check if we have saved map position
            const savedLat = @json(session('map_latitude'));
            const savedLng = @json(session('map_longitude'));
            const oldLat = @json(old('latitude'));
            const oldLng = @json(old('longitude'));

            // Prioritize old values (from form), then saved session values, then PSU location
            const initialView = (oldLat && oldLng) ? [parseFloat(oldLat), parseFloat(oldLng)] :
                               (savedLat && savedLng) ? [savedLat, savedLng] : psuLocation;

            // Hide fallback
            const fallback = document.getElementById('mapFallback');
            if (fallback) {
                fallback.style.display = 'none';
            }

            console.log('Creating map with view:', initialView);
            map = L.map('propertyMap').setView(initialView, 15);
            console.log('Map created successfully');

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            console.log('Tile layer added');

            // PSU marker
            L.marker(psuLocation, {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41]
                })
            }).addTo(map).bindPopup('<b>PSU Main Campus</b>');

            // Restore marker if coordinates exist
            if (oldLat && oldLng) {
                setPropertyLocation(parseFloat(oldLat), parseFloat(oldLng));
            } else if (savedLat && savedLng) {
                setPropertyLocation(savedLat, savedLng);
            }

            // Map click handler
            map.on('click', function(e) {
                setPropertyLocation(e.latlng.lat, e.latlng.lng);
            });

        } catch (error) {
            console.error('Map initialization error:', error);

            // Show fallback message
            const fallback = document.getElementById('mapFallback');
            if (fallback) {
                fallback.innerHTML = `
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-lg font-semibold text-red-600">Map Loading Failed</p>
                        <p class="text-sm">Please manually enter coordinates below</p>
                        <p class="text-xs mt-2">PSU Location: 14.997480, 120.653230</p>
                    </div>
                `;
            }
        }
    }
    
    function setPropertyLocation(lat, lng) {
        if (propertyMarker) {
            map.removeLayer(propertyMarker);
        }

        propertyMarker = L.marker([lat, lng], {
            draggable: true,
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            })
        }).addTo(map);

        propertyMarker.bindPopup('<b>Your Property Location</b>').openPopup();

        // Update form inputs
        document.getElementById('latitudeInput').value = lat.toFixed(8);
        document.getElementById('longitudeInput').value = lng.toFixed(8);

        // Store in session for persistence
        fetch('/landlord/properties/store-map-position', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ latitude: lat, longitude: lng })
        });

        // Handle marker dragging
        propertyMarker.on('dragend', function(e) {
            const newPos = e.target.getLatLng();
            document.getElementById('latitudeInput').value = newPos.lat.toFixed(8);
            document.getElementById('longitudeInput').value = newPos.lng.toFixed(8);

            // Update session on drag
            fetch('/landlord/properties/store-map-position', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ latitude: newPos.lat, longitude: newPos.lng })
            });
        });
    }
    
    function findOnMap() {
        alert('Click on the map to set your property location. You can also drag the marker to fine-tune the position.');
    }
    
    function useMyLocation() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by this browser.');
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                setPropertyLocation(lat, lng);
                map.setView([lat, lng], 16);
            },
            function(error) {
                let message = 'Unable to get your location.';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Location access denied. Please enable location permissions.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        message = 'Location request timed out.';
                        break;
                }
                alert(message);
            }
        );
    }
    
    // Directions Modal Functions
    function openDirectionsModal() {
        document.getElementById('directionsModal').classList.remove('hidden');
        map.dragging.disable();
        map.getContainer().style.pointerEvents = 'none';
        map.keyboard.disable();
        map.scrollWheelZoom.disable();
    }
    
    function closeDirectionsModal() {
        document.getElementById('directionsModal').classList.add('hidden');
        map.dragging.enable();
        map.getContainer().style.pointerEvents = 'auto';
        map.keyboard.enable();
        map.scrollWheelZoom.enable();
        map.off('click');
    }
    
    function getDirectionsGPS() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by this browser.');
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                setPropertyLocation(lat, lng);
                map.setView([lat, lng], 16);
                closeDirectionsModal();
            },
            function(error) {
                alert('Unable to get your location. Please try another method.');
            }
        );
    }
    
    function getDirectionsAddress() {
        @auth
            @if(auth()->user()->role === 'landlord' && auth()->user()->address)
                const address = "{{ auth()->user()->address }}, {{ auth()->user()->city }}, {{ auth()->user()->province }}, Philippines";
                searchLocationByAddress(address);
            @else
                alert('Please update your address in your profile.');
            @endif
        @endauth
    }
    
    function searchAddress() {
        const query = document.getElementById('searchInput').value.trim();
        if (!query) {
            alert('Please enter an address to search.');
            return;
        }
        
        const searchQuery = query.includes('Pampanga') ? query : `${query}, Pampanga, Philippines`;
        searchLocationByAddress(searchQuery);
    }
    
    function searchLocationByAddress(address) {
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    setPropertyLocation(lat, lng);
                    map.setView([lat, lng], 16);
                    closeDirectionsModal();
                } else {
                    alert('Address not found. Please try a different search term.');
                }
            })
            .catch(error => {
                alert('Search failed. Please try again.');
            });
    }
    
    function enableMapClick() {
        alert('Click on the map to set your property location.');
        closeDirectionsModal();

        map.on('click', function(e) {
            setPropertyLocation(e.latlng.lat, e.latlng.lng);
            map.off('click');
        });
    }

    // Visit Schedule Toggle Function
    function toggleVisitSchedule() {
        const checkbox = document.getElementById('visit_schedule_enabled');
        const settings = document.getElementById('visitScheduleSettings');

        if (checkbox.checked) {
            settings.style.display = 'block';
        } else {
            settings.style.display = 'none';
        }
    }
</script>
@endpush
@endsection