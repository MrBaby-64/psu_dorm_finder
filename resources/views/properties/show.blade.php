@extends('layouts.app')

@section('title', $property->title)

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
<style>
    #propertyMap {
        height: 400px;
        width: 100%;
        border-radius: 10px;
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
</style>
@endpush

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                
                <!-- Property Images -->
                <div class="mb-6">
                    @php
                        $mainImage = $property->images->where('is_cover', true)->first();
                        if (!$mainImage) {
                            $mainImage = $property->images->first();
                        }
                    @endphp
                    
                    <img 
                        src="{{ $mainImage ? asset('storage/' . $mainImage->image_path) : 'https://via.placeholder.com/800x400?text=No+Image' }}" 
                        alt="{{ $property->title }}" 
                        class="main-image" 
                        id="mainImage"
                    >
                    
                    @if($property->images->count() > 1)
                    <div class="property-image-gallery">
                        @foreach($property->images->take(4) as $image)
                        <img 
                            src="{{ asset('storage/' . $image->image_path) }}" 
                            alt="Property image" 
                            onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}')"
                        >
                        @endforeach
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
                            
                            <!-- Live Tracking -->
                            <div class="mt-4">
                                <button onclick="startTracking()" id="trackBtn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 mr-2">
                                    📍 Get Directions from My Location
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
                                        <a href="{{ route('login') }}" class="block w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-semibold">
                                            Login to Send Message
                                        </a>
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

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
<script>
    const propertyLocation = [{{ $property->latitude ?? '14.6705' }}, {{ $property->longitude ?? '120.6298' }}];
    const psuLocation = [15.8714, 120.2869];
    
    let map, propertyMarker, userMarker, routeLayer;
    
    function initMap() {
        map = L.map('propertyMap').setView(propertyLocation, 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        propertyMarker = L.marker(propertyLocation, {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            })
        }).addTo(map).bindPopup('<b>{{ $property->title }}</b>').openPopup();
        
        L.marker(psuLocation, {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            })
        }).addTo(map).bindPopup('<b>PSU Main Campus</b>');
        
        const distance = calculateDistance(psuLocation, propertyLocation);
        document.getElementById('distanceText').textContent = distance.toFixed(2) + ' km';
        document.getElementById('walkingTime').textContent = `Approximately ${Math.round(distance * 15)} minutes walk`;
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
    
    function startTracking() {
        if (!navigator.geolocation) {
            alert('Geolocation not supported');
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            position => {
                const userLocation = [position.coords.latitude, position.coords.longitude];
                
                if (userMarker) map.removeLayer(userMarker);
                if (routeLayer) map.removeLayer(routeLayer);
                
                userMarker = L.marker(userLocation, {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41]
                    })
                }).addTo(map).bindPopup('Your Location').openPopup();
                
                routeLayer = L.polyline([userLocation, propertyLocation], {
                    color: '#4CAF50',
                    weight: 4
                }).addTo(map);
                
                map.fitBounds(routeLayer.getBounds());
                
                const distance = calculateDistance(userLocation, propertyLocation);
                alert(`You are ${distance.toFixed(2)} km away (${Math.round(distance * 15)} min walk)`);
            }
        );
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
    
    document.addEventListener('DOMContentLoaded', initMap);
</script>
@endpush