@extends('layouts.guest')

@section('title', $property->title)

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
<style>
    #propertyMap {
        height: 400px;
        width: 100%;
        border-radius: 10px;
        z-index: 10;
    }
    .property-image-gallery {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }
    .property-image-gallery img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
    }
    .main-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 10px;
    }
    nav {
        position: fixed !important;
        width: 100%;
        z-index: 40;
    }
</style>
@endpush

@section('content')
<div class="sticky top-16 z-30 bg-white border-b px-4 py-2">
    <button onclick="window.history.back()" class="flex items-center text-gray-600 hover:text-green-600">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back
    </button>
</div>
<div class="pt-32 pb-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                
                <!-- Property Images -->
                <div class="mb-6">
                    @if($property->images->count() > 0)
                        @php
                            $coverImage = $property->images->where('is_cover', true)->first() ?? $property->images->first();
                            $galleryImages = $property->images;
                        @endphp
                        
                        <!-- Main Image -->
                        <div class="relative mb-4">
                            <img 
                                src="{{ asset('storage/' . $coverImage->image_path) }}" 
                                alt="{{ $property->title }}" 
                                class="main-image w-full h-96 object-cover rounded-lg" 
                                id="mainImage"
                            >
                            
                            <!-- Image Counter -->
                            @if($property->images->count() > 1)
                                <div class="absolute top-4 right-4 bg-black bg-opacity-70 text-white px-3 py-1 rounded-full text-sm">
                                    <span id="currentImageIndex">1</span> / {{ $property->images->count() }}
                                </div>
                            @endif
                            
                            <!-- Navigation Arrows -->
                            @if($property->images->count() > 1)
                                <button onclick="previousImage()" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <button onclick="nextImage()" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        <!-- Image Gallery Thumbnails -->
                        @if($property->images->count() > 1)
                            <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
                                @foreach($galleryImages as $index => $image)
                                    <button 
                                        onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}', {{ $index }})" 
                                        class="relative group">
                                        <img 
                                            src="{{ asset('storage/' . $image->image_path) }}" 
                                            alt="Property image {{ $index + 1 }}" 
                                            class="w-full h-16 object-cover rounded border-2 hover:border-green-500 transition {{ $loop->first ? 'border-green-500' : 'border-gray-200' }}"
                                            data-index="{{ $index }}"
                                        >
                                        @if($image->is_cover)
                                            <div class="absolute -top-1 -left-1 bg-green-500 text-white text-xs px-1 rounded">
                                                Main
                                            </div>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <!-- No Images Available -->
                        <div class="bg-gray-100 rounded-lg h-96 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-500">No images available for this property</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left Column - Property Details -->
                    <div class="lg:col-span-2">
                        <h1 class="text-3xl font-bold mb-4">{{ $property->title }}</h1>
                        
                        <div class="flex items-center text-gray-600 mb-4">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a6 6 0 00-6 6c0 4.5 6 10 6 10s6-5.5 6-10a6 6 0 00-6-6z"/>
                            </svg>
                            <span>{{ $property->address_line }}, {{ $property->barangay }}, {{ $property->city }}</span>
                        </div>

                        <!-- Price -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <p class="text-gray-600">Monthly Rate</p>
                            <p class="text-3xl font-bold text-green-600">₱{{ number_format($property->price) }}</p>
                            <p class="text-sm text-gray-500">per month</p>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <h2 class="text-xl font-bold mb-3">Description</h2>
                            <p class="text-gray-700">{{ $property->description }}</p>
                        </div>

                        <!-- Amenities -->
                        @if($property->amenities && $property->amenities->count() > 0)
                        <div class="mb-6">
                            <h2 class="text-xl font-bold mb-3">Amenities & Features</h2>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($property->amenities as $amenity)
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                    <span>{{ $amenity->name }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Available Rooms -->
                        @if($property->rooms && $property->rooms->count() > 0)
                        <div class="mb-6">
                            <h2 class="text-xl font-bold mb-3">Available Rooms</h2>
                            <div class="space-y-3">
                                @foreach($property->rooms as $room)
                                <div class="border rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h3 class="font-semibold">{{ $room->room_number }}</h3>
                                            <p class="text-sm text-gray-600">{{ $room->room_type }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-green-600">₱{{ number_format($room->price) }}/month</p>
                                            <span class="text-xs {{ $room->status === 'available' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ ucfirst($room->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Map Section -->
                        <div class="mb-6">
                            <h2 class="text-xl font-bold mb-3">Location & Map</h2>
                            <div id="propertyMap"></div>
                            
                            <!-- Distance Info -->
                            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="font-semibold">📍 Distance from PSU Main Campus</p>
                                <p class="text-2xl font-bold text-yellow-700" id="distanceText">Calculating...</p>
                                <p class="text-sm text-gray-600" id="walkingTime">--</p>
                            </div>
                            
                            <!-- Directions -->
                            <div class="mt-4">
                                <button onclick="openDirectionsModal()" id="directionsBtn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 mr-2">
                                    📍 Get Directions
                                </button>
                                <button onclick="centerOnProperty()" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                                    🎯 Center on Property
                                </button>
                            </div>
                        </div>

                        <!-- House Rules -->
                        <div class="mb-6">
                            <h2 class="text-xl font-bold mb-3">House Rules</h2>
                            <ul class="list-disc list-inside text-gray-700 space-y-1">
                                <li>No smoking inside the premises</li>
                                <li>Visitors allowed until 10 PM</li>
                                <li>Keep common areas clean</li>
                                <li>Respect quiet hours (10 PM - 7 AM)</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Right Column - Contact & Actions -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-6">
                            <!-- Contact/Inquiry Card -->
                            <div class="border rounded-lg p-6 mb-4 bg-white shadow-sm">
                                <h3 class="font-bold mb-2 text-lg">Send an Inquiry</h3>
                                <p class="text-sm text-gray-600 mb-4">Need clarifications about this listing?</p>
                                
                                @auth
                                    <form action="{{ route('inquiries.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                                        
                                        <!-- Select Subunit -->
                                        @if($property->rooms && $property->rooms->count() > 0)
                                        <div class="mb-4">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Select Subunit</label>
                                            <select name="room_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                                                <option value="">Select a Subunit</option>
                                                @foreach($property->rooms->where('status', 'available') as $room)
                                                <option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->room_type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif
                                        
                                        <!-- Target Move-In and Move-Out -->
                                        <div class="mb-4">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Target Move-In and Move-Out</label>
                                            <div class="grid grid-cols-2 gap-2">
                                                <input type="date" name="move_in_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Move-In">
                                                <input type="date" name="move_out_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Move-Out">
                                            </div>
                                        </div>
                                        
                                        <!-- Your Message -->
                                        <div class="mb-4">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Your Message to the Landlord</label>
                                            <textarea name="message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm" placeholder="Hi, I'm interested in this property..." required></textarea>
                                        </div>
                                        
                                        <!-- Terms Agreement -->
                                        <div class="mb-4">
                                            <label class="flex items-start text-xs text-gray-600">
                                                <input type="checkbox" class="mt-1 mr-2" required>
                                                <span>I have read and agreed to the <a href="#" class="text-green-600 underline">Terms</a>, <a href="#" class="text-green-600 underline">Privacy Policy</a>, and <a href="#" class="text-green-600 underline">Safety Guidelines</a>.</span>
                                            </label>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-semibold mb-2">
                                            Send Message
                                        </button>
                                        
                                        @if($property->visit_schedule_enabled ?? false)
                                            <button type="button" onclick="openScheduleModal()" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
                                                Schedule a Visit
                                            </button>
                                        @else
                                            <button type="button" class="w-full bg-gray-400 text-white py-3 rounded-lg cursor-not-allowed font-semibold" disabled>
                                                Schedule Not Available
                                            </button>
                                        @endif
                                    </form>
                                @else
                                    <div class="text-center py-4">
                                        <p class="text-gray-600 mb-4">Please login to send an inquiry</p>
                                        <button onclick="openAuthModal('login')" class="block w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-semibold">
                                            Login to Send Message
                                        </button>
                                    </div>
                                @endauth
                            </div>

                            <!-- Quick Stats -->
                            <div class="border rounded-lg p-6 bg-white shadow-sm">
                                <h3 class="font-bold mb-4">Property Info</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Rooms:</span>
                                        <span class="font-semibold">{{ $property->room_count }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Available:</span>
                                        <span class="font-semibold text-green-600">
                                            {{ $property->rooms->where('status', 'available')->count() }}
                                        </span>
                                    </div>
                                    @if($property->rating_count > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Rating:</span>
                                        <span class="font-semibold">⭐ {{ number_format($property->rating_avg, 1) }}</span>
                                    </div>
                                    @endif
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Posted:</span>
                                        <span class="font-semibold">{{ $property->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Visit Modal -->
<div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md m-4 overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Schedule a Visit</h3>
                <button onclick="closeScheduleModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form onsubmit="scheduleVisit(event)" class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Preferred Date</label>
                    <input type="date"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                           min="{{ date('Y-m-d') }}"
                           required>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Preferred Time</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        <option value="">Select time</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Additional Notes</label>
                    <textarea
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        rows="3"
                        placeholder="Any specific requests..."></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button"
                            onclick="closeScheduleModal()"
                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                        Schedule Visit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Directions Modal -->
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
                    @if(auth()->user()->role === 'tenant' && auth()->user()->address)
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
                        Search & Get Directions
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
<script>
    const propertyLocation = [{{ $property->latitude ?? '14.997480043450848' }}, {{ $property->longitude ?? '120.65323030030329' }}];
    const psuLocation = [14.997480043450848, 120.65323030030329];
    
    let map, propertyMarker, userMarker, psuMarker, routeLayer;
    
    function initMap() {
        try {
            map = L.map('propertyMap').setView(propertyLocation, 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            // Property marker (GREEN)
            propertyMarker = L.marker(propertyLocation, {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41]
                })
            }).addTo(map).bindPopup('<b>{{ $property->title }}</b>').openPopup();
            
            // PSU marker (BLUE)
            psuMarker = L.marker(psuLocation, {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41]
                })
            }).addTo(map).bindPopup('<b>PSU Main Campus</b>');
            
            const distance = calculateDistance(psuLocation, propertyLocation);
            document.getElementById('distanceText').textContent = distance.toFixed(2) + ' km';
            document.getElementById('walkingTime').textContent = `Approximately ${Math.round(distance * 15)} minutes walk`;
            
        } catch (error) {
            console.error('Map initialization error:', error);
            document.getElementById('propertyMap').innerHTML = '<div class="p-4 text-center text-red-600">Map failed to load. Please refresh the page.</div>';
        }
    }
    
    function calculateDistance(point1, point2) {
        const R = 6371;
        const dLat = (point2[0] - point1[0]) * Math.PI / 180;
        const dLng = (point2[1] - point1[1]) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(point1[0] * Math.PI / 180) * Math.cos(point2[0] * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }
    
    // Use stored tenant address instead of GPS/IP detection
    function startTracking() {
        @auth
            @if(auth()->user()->role === 'tenant')
                @if(auth()->user()->address)
                    // Use stored tenant address
                    const userAddress = "{{ auth()->user()->address }}, {{ auth()->user()->city }}, {{ auth()->user()->province }}";
                    searchLocationByAddress(userAddress);
                @else
                    alert('Please update your address in your profile to use this feature.\n\nGo to Account > Profile to add your address.');
                    window.open('{{ route("profile.edit") }}', '_blank');
                @endif
            @else
                searchManualLocation();
            @endif
        @else
            alert('Please login to use location features.');
            window.location.href = "{{ route('login') }}";
        @endauth
    }
    
    function searchLocationByAddress(address) {
        document.getElementById('trackBtn').textContent = 'Finding your location...';
        document.getElementById('trackBtn').disabled = true;
        
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    showUserLocationAndRoute([lat, lng], `Your Address: {{ auth()->user()->address ?? 'Your Location' }}`);
                } else {
                    alert('Could not find your address on the map. Please update it in your profile.');
                    document.getElementById('trackBtn').textContent = 'Get Directions from My Location';
                    document.getElementById('trackBtn').disabled = false;
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                alert('Failed to find your location. Please try again.');
                document.getElementById('trackBtn').textContent = 'Get Directions from My Location';
                document.getElementById('trackBtn').disabled = false;
            });
    }
    
    function searchManualLocation() {
        const address = prompt('Enter your location:\n\nExamples:\n• SM City Pampanga\n• Bacolor Town Hall\n• Angeles City\n• Your street, barangay, city');
        
        if (!address) return;
        
        document.getElementById('trackBtn').textContent = 'Searching location...';
        document.getElementById('trackBtn').disabled = true;
        
        const searchQuery = address.includes('Pampanga') ? address : `${address}, Pampanga, Philippines`;
        
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery)}&limit=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    showUserLocationAndRoute([lat, lng], `Your Location: ${address}`);
                } else {
                    alert('Location not found. Try: SM City Pampanga, Angeles City, or Bacolor Town Hall');
                    document.getElementById('trackBtn').textContent = 'Get Directions from My Location';
                    document.getElementById('trackBtn').disabled = false;
                }
            })
            .catch(error => {
                alert('Search failed. Please try again.');
                document.getElementById('trackBtn').textContent = 'Get Directions from My Location';
                document.getElementById('trackBtn').disabled = false;
            });
    }

    function setManualLocation() {
        searchManualLocation();
    }

    function showUserLocationAndRoute(userLocation, locationLabel) {
        if (userMarker) map.removeLayer(userMarker);
        if (routeLayer) map.removeLayer(routeLayer);
        
        // User marker (RED)
        userMarker = L.marker(userLocation, {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            })
        }).addTo(map).bindPopup(`<b>${locationLabel}</b>`).openPopup();
        
        // Route line
        routeLayer = L.polyline([userLocation, propertyLocation], {
            color: '#4CAF50',
            weight: 4
        }).addTo(map);
        
        map.fitBounds(routeLayer.getBounds());
        
        const distance = calculateDistance(userLocation, propertyLocation);
        alert(`Distance: ${distance.toFixed(2)} km (${Math.round(distance * 15)} min walk)\nFrom: ${locationLabel}`);
        
        document.getElementById('trackBtn').textContent = 'Get Directions from My Location';
        document.getElementById('trackBtn').disabled = false;
    }
    
    function centerOnProperty() {
        map.setView(propertyLocation, 16);
        propertyMarker.openPopup();
    }
    
    function changeMainImage(src) {
        document.getElementById('mainImage').src = src;
    }
    
    function openScheduleModal() {
        @guest
            window.location.href = "{{ route('login') }}";
            return;
        @endguest
        document.getElementById('scheduleModal').classList.remove('hidden');
    }
    
    function closeScheduleModal() {
        document.getElementById('scheduleModal').classList.add('hidden');
    }
    
    function scheduleVisit(event) {
        event.preventDefault();
        alert('Visit request sent! The property owner will confirm your appointment.');
        closeScheduleModal();
    }

    // Directions Modal Functions
    function openDirectionsModal() {
        @guest
            alert('Please login to use location features.');
            window.location.href = "{{ route('login') }}";
            return;
        @endguest
        document.getElementById('directionsModal').classList.remove('hidden');
        // Disable map interactions while modal is open
        map.dragging.disable();
        map.getContainer().style.pointerEvents = 'none';
        map.keyboard.disable();
        map.scrollWheelZoom.disable();
    }

    function closeDirectionsModal() {
        document.getElementById('directionsModal').classList.add('hidden');
        // Re-enable map interactions
        map.dragging.enable();
        map.getContainer().style.pointerEvents = 'auto';
        map.keyboard.enable();
        map.scrollWheelZoom.enable();
        // Reset any map click listeners
        map.off('click');
    }

    // GPS Location
    function getDirectionsGPS() {
        console.log('Attempting to get GPS location');
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by this browser.');
            console.error('Geolocation not supported');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                console.log('GPS location obtained:', lat, lng);
                showUserLocationAndRoute([lat, lng], 'Your Current Location (GPS)');
                closeDirectionsModal();
            },
            function(error) {
                console.error('GPS error:', error);
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
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 300000 }
        );
    }

    // Saved Address
    function getDirectionsAddress() {
        @auth
            @if(auth()->user()->role === 'tenant' && auth()->user()->address)
                // Fetch latest persisted values from user model (same source as profile page)
                const address = "{{ auth()->user()->address }}".trim();
                const city = "{{ auth()->user()->city }}".trim();
                const province = "{{ auth()->user()->province }}".trim();

                // Build clean full address string with proper ordering and country suffix
                let fullAddress = '';
                if (address) fullAddress += address;
                if (city) fullAddress += (fullAddress ? ', ' : '') + city;
                if (province) fullAddress += (fullAddress ? ', ' : '') + province;
                fullAddress += ', Philippines'; // Add country suffix for better match rates

                console.log('Request payload: address=' + address + ', city=' + city + ', province=' + province);
                console.log('Geocoding query:', fullAddress);

                // Do not use cached state, always geocode fresh
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullAddress)}&limit=1`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Geocoding response:', data);
                        if (data && data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lng = parseFloat(data[0].lon);
                            console.log('Final latitude:', lat, 'longitude:', lng);
                            showUserLocationAndRoute([lat, lng], 'Your Saved Address');
                            closeDirectionsModal();
                        } else {
                            console.error('Address geocoding failed: no results');
                            alert('Could not find your saved address on the map. Please update it in your profile.');
                            window.open('{{ route("profile.edit") }}', '_blank');
                        }
                    })
                    .catch(error => {
                        console.error('Geocoding error:', error);
                        alert('Failed to find your address. Please try again.');
                    });
            @else
                alert('Please update your address in your profile.');
                window.open('{{ route("profile.edit") }}', '_blank');
            @endif
        @endauth
    }

    // Search Address
    function searchAddress() {
        const query = document.getElementById('searchInput').value.trim();
        if (!query) {
            alert('Please enter an address to search.');
            return;
        }

        console.log('Searching address:', query);
        const searchQuery = query.includes('Pampanga') ? query : `${query}, Pampanga, Philippines`;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery)}&limit=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    console.log('Address found:', lat, lng);
                    showUserLocationAndRoute([lat, lng], `Searched Location: ${query}`);
                    closeDirectionsModal();
                } else {
                    console.error('Address not found');
                    alert('Address not found. Try a different search term.');
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                alert('Search failed. Please try again.');
            });
    }

    // Click on Map
    let clickMarker = null;
    function enableMapClick() {
        console.log('Enabling map click for location selection');
        alert('Click on the map to set your location.');
        closeDirectionsModal();

        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            // Remove previous click marker
            if (clickMarker) {
                map.removeLayer(clickMarker);
            }

            // Add new marker
            clickMarker = L.marker([lat, lng], {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41]
                })
            }).addTo(map).bindPopup('Selected Location').openPopup();

            console.log('Map location selected:', lat, lng);
            showUserLocationAndRoute([lat, lng], 'Selected Location on Map');

            // Disable further clicks
            map.off('click');
        });
    }

    // Updated searchLocationByAddress to accept custom label
    function searchLocationByAddress(address, customLabel = null) {
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    const label = customLabel || `Address: ${address}`;
                    console.log('Address geocoded:', lat, lng);
                    showUserLocationAndRoute([lat, lng], label);
                } else {
                    console.error('Address geocoding failed');
                    alert('Could not find the specified address.');
                }
            })
            .catch(error => {
                console.error('Geocoding error:', error);
                alert('Failed to find location. Please try again.');
            });
    }

    // Updated showUserLocationAndRoute with logging
    function showUserLocationAndRoute(userLocation, locationLabel) {
        if (userMarker) map.removeLayer(userMarker);
        if (routeLayer) map.removeLayer(routeLayer);

        // User marker (RED)
        userMarker = L.marker(userLocation, {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            })
        }).addTo(map).bindPopup(`<b>${locationLabel}</b>`).openPopup();

        // Route line
        routeLayer = L.polyline([userLocation, propertyLocation], {
            color: '#4CAF50',
            weight: 4
        }).addTo(map);

        map.fitBounds(routeLayer.getBounds());

        const distance = calculateDistance(userLocation, propertyLocation);
        console.log('Route calculated:', { distance: distance.toFixed(2), from: locationLabel });

        // Update distance display
        document.getElementById('distanceText').textContent = distance.toFixed(2) + ' km from ' + locationLabel;
        document.getElementById('walkingTime').textContent = `Approximately ${Math.round(distance * 15)} minutes walk`;

        alert(`Distance: ${distance.toFixed(2)} km (${Math.round(distance * 15)} min walk)\nFrom: ${locationLabel}`);
    }

    window.addEventListener('scroll', function() {
        const nav = document.querySelector('nav');
        if (window.scrollY > 0) {
            nav.classList.add('shadow-lg');
        } else {
            nav.classList.remove('shadow-lg');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initMap, 100);
    });
</script>
@endpush
