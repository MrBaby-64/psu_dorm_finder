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
                        <img src="{{ $image->full_url }}"
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- LEFT COLUMN -->
                        <div class="space-y-6">
                            <!-- Property Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Property Title *</label>
                                <input type="text" name="title" value="{{ old('title', $property->title) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>

                            <!-- House Rules -->
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-3">
                                🏠 House Rules
                            </label>

                            @php
                                $existingRules = $property->house_rules ?? [];
                                $defaultRules = \App\Models\Property::getDefaultHouseRules();
                            @endphp

                            <!-- Default Rules -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-3">
                                <p class="text-sm font-medium text-blue-900 mb-3">Default Rules (Check the rules that apply):</p>
                                <div class="space-y-2">
                                    @foreach($defaultRules as $rule)
                                        <label class="flex items-start cursor-pointer hover:bg-blue-100 p-2 rounded transition">
                                            <input type="checkbox"
                                                   name="house_rules[]"
                                                   value="{{ $rule }}"
                                                   {{ in_array($rule, $existingRules) ? 'checked' : '' }}
                                                   class="mt-1 h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                            <span class="ml-3 text-sm text-gray-700">{{ $rule }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Custom Rules -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <p class="text-sm font-medium text-green-900 mb-3">Your Custom Rules:</p>
                                <div id="customRulesContainer" class="space-y-2 mb-3">
                                    @php
                                        $customRules = array_filter($existingRules, function($rule) use ($defaultRules) {
                                            return !in_array($rule, $defaultRules);
                                        });
                                    @endphp

                                    @foreach($customRules as $index => $customRule)
                                        <div class="flex items-start gap-2" id="customRule{{ $index }}">
                                            <input type="text"
                                                   name="house_rules[]"
                                                   value="{{ $customRule }}"
                                                   placeholder="Enter your custom rule..."
                                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                   required>
                                            <button type="button"
                                                    onclick="removeCustomRule({{ $index }})"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                                    title="Remove this rule">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button"
                                        onclick="addCustomRule()"
                                        class="inline-flex items-center px-3 py-2 border border-green-300 rounded-lg text-sm font-medium text-green-700 bg-white hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Custom Rule
                                </button>
                            </div>

                            <p class="mt-2 text-xs text-gray-500">
                                💡 Tip: Clear house rules help tenants understand expectations and maintain a harmonious living environment.
                            </p>
                        </div>
                        </div>

                        <!-- RIGHT COLUMN -->
                        <div class="space-y-6">
                            <!-- Monthly Rent -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Rent (₱) *</label>
                                <input type="number" name="price" value="{{ old('price', $property->price) }}" min="0" step="0.01" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>

                            <!-- Number of Rooms -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Rooms *</label>
                                <input type="number" name="room_count" value="{{ old('room_count', $property->room_count) }}" min="1" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                <textarea name="description" rows="6" required
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md">{{ old('description', $property->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 mt-6">
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Latitude * <span class="text-xs text-gray-500">(Auto-filled from map)</span>
                                </label>
                                <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $property->latitude) }}" required readonly
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Longitude * <span class="text-xs text-gray-500">(Auto-filled from map)</span>
                                </label>
                                <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $property->longitude) }}" required readonly
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed">
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

@push('scripts')
<script>
    // House Rules Management
    let customRuleCounter = {{ count($customRules ?? []) }};

    function addCustomRule() {
        customRuleCounter++;
        const container = document.getElementById('customRulesContainer');
        const ruleDiv = document.createElement('div');
        ruleDiv.className = 'flex items-start gap-2';
        ruleDiv.id = `customRule${customRuleCounter}`;

        ruleDiv.innerHTML = `
            <input type="text"
                   name="house_rules[]"
                   placeholder="Enter your custom rule..."
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                   required>
            <button type="button"
                    onclick="removeCustomRule(${customRuleCounter})"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                    title="Remove this rule">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;

        container.appendChild(ruleDiv);
    }

    function removeCustomRule(id) {
        const ruleDiv = document.getElementById(`customRule${id}`);
        if (ruleDiv) {
            ruleDiv.remove();
        }
    }
</script>
@endpush
@endsection