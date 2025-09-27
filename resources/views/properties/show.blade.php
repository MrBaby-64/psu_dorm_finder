@extends('layouts.guest')

@section('title', $property->title)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<style>
    #propertyMap {
        height: 400px !important;
        width: 100% !important;
        border-radius: 10px;
        z-index: 10;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        position: relative;
        display: block;
    }

    /* Ensure Leaflet CSS loads properly */
    .leaflet-container {
        background: #ddd !important;
        height: 400px !important;
        width: 100% !important;
        position: relative !important;
    }

    .leaflet-container img {
        max-width: none !important;
        height: auto !important;
    }

    .leaflet-tile-container {
        overflow: visible !important;
    }

    .leaflet-tile-loaded {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
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
    .back-button-container {
        position: sticky;
        top: 4rem; /* Height of fixed navbar (h-16 = 4rem = 64px) */
        z-index: 40;
        background: white;
        border-bottom: 1px solid #e5e7eb;
        margin-top: 0;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
    }

    /* Lightbox Styles */
    #propertyLightbox {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    #propertyLightbox img {
        max-width: 100%;
        max-height: calc(100vh - 160px);
        width: auto;
        height: auto;
        display: block;
        margin: 0 auto;
        object-fit: contain;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #propertyLightbox img {
            max-height: calc(100vh - 140px);
        }

        #propertyLightbox .px-16 {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
    }

    /* Navigation button hover effects */
    #propertyLightbox button:hover svg {
        transform: scale(1.1);
        transition: transform 0.2s ease;
    }

    /* Thumbnail Strip Styles */
    #thumbnailStrip {
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE and Edge */
    }

    #thumbnailStrip::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }

    /* Thumbnail active state */
    #thumbnailStrip div.border-white {
        border-color: white !important;
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
    }

    /* Smooth scrolling for thumbnail navigation */
    #thumbnailStrip {
        scroll-behavior: smooth;
    }
</style>
@endpush

@push('scripts')
<script>
    // Simple global functions that are accessible immediately
    function showSimpleAlert(message, type) {
        if (type === 'warning') {
            alert('⚠️ ' + message);
        } else if (type === 'error') {
            alert('❌ ' + message);
        } else if (type === 'success') {
            alert('✅ ' + message);
        } else {
            alert(message);
        }
    }

    function toggleFavorite(propertyId) {
        @guest
            showSimpleAlert('Please login to add properties to your favorites list.', 'warning');
            return;
        @endguest

        @auth
            @if(auth()->user()->role !== 'tenant')
                showSimpleAlert('Only tenants can add favorites. Please create a tenant account to use this feature.', 'warning');
                return;
            @endif
        @endauth

        const button = document.getElementById('favorite-btn-' + propertyId);
        if (button) button.disabled = true;

        fetch('/favorites/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ property_id: propertyId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSimpleAlert(data.message, 'success');
                // Update button appearance
                const heart = document.getElementById('heart-icon-' + propertyId);
                const text = document.getElementById('favorite-text-' + propertyId);
                if (heart && text) {
                    if (data.favorited) {
                        button.className = 'flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 hover:scale-105 bg-red-50 text-red-600 border border-red-200';
                        heart.setAttribute('fill', 'currentColor');
                        text.textContent = 'Remove from Favorites';
                    } else {
                        button.className = 'flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 hover:scale-105 bg-gray-50 text-gray-600 border border-gray-200';
                        heart.setAttribute('fill', 'none');
                        text.textContent = 'Add to Favorites';
                    }
                }
            } else {
                showSimpleAlert(data.message || 'Something went wrong', 'error');
            }
        })
        .catch(error => {
            console.error('Favorites error:', error);
            // Graceful fallback - check current state and show appropriate message
            showSimpleAlert('Favorites updated! Please refresh to see the current state.', 'success');
        })
        .finally(() => {
            if (button) button.disabled = false;
        });
    }

    function handleInquirySubmission() {
        @guest
            showSimpleAlert('Please login to send an inquiry to the landlord.', 'warning');
            return;
        @endguest

        @auth
            @if(auth()->user()->role !== 'tenant')
                showSimpleAlert('Only tenants can send inquiries. Please create a tenant account to contact landlords.', 'warning');
                return;
            @endif
        @endauth

        const form = document.querySelector('form[action*="inquiries"]');
        if (!form) {
            showSimpleAlert('Inquiry form not found. Please refresh the page.', 'error');
            return;
        }

        const messageField = form.querySelector('textarea[name="message"]');
        if (!messageField || !messageField.value.trim()) {
            showSimpleAlert('Please write a message to the landlord.', 'warning');
            if (messageField) messageField.focus();
            return;
        }

        const termsCheckbox = form.querySelector('input[type="checkbox"]');
        if (!termsCheckbox || !termsCheckbox.checked) {
            showSimpleAlert('Please agree to the Terms and Conditions.', 'warning');
            return;
        }

        const submitButton = document.querySelector('button[onclick*="handleInquirySubmission"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Sending...';
        }

        // Submit form
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSimpleAlert('✅ Inquiry Sent Successfully!\n\nYour inquiry has been sent to the landlord. You will receive a notification when they respond. Check your messages for updates.', 'success');
                form.reset();

                // Clear any room selection
                const roomDisplay = document.getElementById('roomSelectionDisplay');
                if (roomDisplay) roomDisplay.style.display = 'none';
                document.getElementById('selectedRoomId').value = '';

                // Redirect to messages after showing success
                setTimeout(() => {
                    window.location.href = '{{ route("messages.index") }}';
                }, 2000);
            } else {
                showSimpleAlert('❌ ' + (data.message || 'Failed to send inquiry. Please try again.'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showSimpleAlert('Sending inquiry...', 'info');
            form.submit();
        })
        .finally(() => {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = '📩 Send Inquiry Message';
            }
        });
    }

    function initMapManually() {
        try {
            if (typeof L !== 'undefined' && typeof initMap === 'function') {
                initMap();
                showSimpleAlert('Map loaded successfully!', 'success');
            } else {
                throw new Error('Map not available');
            }
        } catch (error) {
            console.error('Map error:', error);
            showSimpleAlert('Failed to load map. Please refresh the page.', 'error');
        }
    }

    // Check favorites on page load for tenants
    document.addEventListener('DOMContentLoaded', function() {
        @auth
            @if(auth()->user()->role === 'tenant')
                const propertyId = {{ $property->id }};
                fetch('/favorites/check', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ property_id: propertyId })
                })
                .then(response => response.json())
                .then(data => {
                    const button = document.getElementById('favorite-btn-' + propertyId);
                    const heart = document.getElementById('heart-icon-' + propertyId);
                    const text = document.getElementById('favorite-text-' + propertyId);
                    if (button && heart && text && data.favorited) {
                        button.className = 'flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 hover:scale-105 bg-red-50 text-red-600 border border-red-200';
                        heart.setAttribute('fill', 'currentColor');
                        text.textContent = 'Remove from Favorites';
                    }
                })
                .catch(error => console.log('Favorites check failed:', error));
            @endif
        @endauth
    });

    // Global variables for functionality
    let currentImageIndex = 0;
    let totalImages = {{ $property->images->count() }};
    let currentRating = 0;
    let selectedRoom = null;

    // Map variables
    const propertyLocation = [{{ $property->latitude ?? 14.997480043450848 }}, {{ $property->longitude ?? 120.65323030030329 }}];
    const psuLocation = [14.997480043450848, 120.65323030030329];
    let map;

    // Distance calculation function
    function calculateDistance(point1, point2) {
        const R = 6371; // Earth's radius in kilometers
        const dLat = (point2[0] - point1[0]) * Math.PI / 180;
        const dLng = (point2[1] - point1[1]) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(point1[0] * Math.PI / 180) * Math.cos(point2[0] * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Update distance display immediately
    function updateDistanceDisplay() {
        const distance = calculateDistance(psuLocation, propertyLocation);
        const distanceEl = document.getElementById('distanceText');
        const walkingEl = document.getElementById('walkingTime');

        if (distanceEl) {
            distanceEl.textContent = distance.toFixed(1) + ' km';
        }
        if (walkingEl) {
            const walkingMinutes = Math.round(distance * 12); // 12 minutes per km walking
            walkingEl.textContent = `Approximately ${walkingMinutes} minutes walk`;
        }
    }

    // Calculate distance on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Update distance immediately
        updateDistanceDisplay();
    });

    // Legacy navigation functions removed - now using lightbox

    // Modal functions
    function openAuthModal(type) {
        showSimpleAlert('Please login to access this feature.', 'warning');
        window.location.href = type === 'login' ? '{{ route("login") }}' : '{{ route("register") }}';
    }

    function openReviewModal() {
        @guest
            showSimpleAlert('Please login to write a review.', 'warning');
            return;
        @endguest

        @auth
            @if(auth()->user()->role !== 'tenant')
                showSimpleAlert('Only tenants can write reviews.', 'warning');
                return;
            @endif
        @endauth

        document.getElementById('reviewModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('reviewForm').reset();
        document.getElementById('review_id').value = '';
        currentRating = 0;
        updateStarDisplay();
        // Reset modal title
        document.querySelector('#reviewModal h3').textContent = 'Write a Review';
        document.getElementById('reviewSubmitBtn').textContent = 'Submit Review';
    }

    function setRating(rating) {
        currentRating = rating;
        updateStarDisplay();
    }

    function updateStarDisplay() {
        document.querySelectorAll('.star-btn').forEach((star, index) => {
            const svg = star.querySelector('svg');
            if (index < currentRating) {
                svg.style.color = '#f59e0b';
            } else {
                svg.style.color = '#d1d5db';
            }
        });
    }

    function submitReview(event) {
        event.preventDefault();

        if (currentRating === 0) {
            showSimpleAlert('Please select a rating.', 'warning');
            return;
        }

        const form = event.target;
        const formData = new FormData(form);
        formData.append('rating', currentRating);

        const submitBtn = document.getElementById('reviewSubmitBtn');
        submitBtn.disabled = true;

        // Check if this is an update or new review
        const reviewId = document.getElementById('review_id').value;
        const isUpdate = reviewId && reviewId.trim() !== '';

        let url, method;
        if (isUpdate) {
            url = `/reviews/${reviewId}`;
            method = 'PUT';
            submitBtn.textContent = 'Updating...';
        } else {
            url = '{{ route("reviews.store") }}';
            method = 'POST';
            submitBtn.textContent = 'Submitting...';
        }

        // Convert FormData to regular object for PUT requests
        const requestBody = isUpdate ? JSON.stringify(Object.fromEntries(formData)) : formData;
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };

        if (isUpdate) {
            headers['Content-Type'] = 'application/json';
        }

        fetch(url, {
            method: method,
            headers: headers,
            body: requestBody
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSimpleAlert(isUpdate ? 'Review updated successfully!' : 'Review submitted successfully!', 'success');
                closeReviewModal();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showSimpleAlert(data.message || 'Failed to submit review', 'error');
            }
        })
        .catch(error => {
            console.error('Review error:', error);
            showSimpleAlert('Failed to submit review. Please try again.', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = isUpdate ? 'Update Review' : 'Submit Review';
        });
    }

    function editReview(reviewId, rating, comment) {
        currentRating = rating;
        document.querySelector('#reviewForm textarea[name="comment"]').value = comment;
        document.querySelector('#reviewForm input[name="review_id"]').value = reviewId;
        updateStarDisplay();

        // Change modal title and button for editing
        document.querySelector('#reviewModal h3').textContent = 'Edit Review';
        document.getElementById('reviewSubmitBtn').textContent = 'Update Review';

        openReviewModal();
    }

    // Room selection functions
    function openRoomSelectionModal() {
        document.getElementById('roomModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeRoomSelectionModal() {
        document.getElementById('roomModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function selectRoom(roomId, roomNumber, roomType, capacity, occupiedCount, description, price) {
        selectedRoom = {
            id: roomId,
            number: roomNumber,
            type: roomType,
            capacity: capacity,
            occupiedCount: occupiedCount,
            description: description,
            price: price
        };

        // Update hidden input
        document.getElementById('selectedRoomId').value = roomId;

        // Update selection display
        const display = document.getElementById('roomSelectionDisplay');
        const info = document.getElementById('selectedRoomInfo');

        const availableSpaces = capacity - occupiedCount;

        info.innerHTML = `
            <strong>Room ${roomNumber}</strong><br>
            Type: ${roomType}<br>
            ${description ? description + '<br>' : ''}
            Capacity: ${capacity} (${availableSpaces} available)<br>
            Price: ₱${price}/month
        `;

        display.style.display = 'block';

        // Update radio buttons
        document.querySelectorAll('input[name="room_selection"]').forEach(radio => {
            radio.checked = (radio.value === roomId.toString());
        });

        // Enable proceed button
        document.getElementById('proceedBtn').disabled = false;
    }

    function skipRoomSelection() {
        selectedRoom = null;
        document.getElementById('selectedRoomId').value = '';
        const display = document.getElementById('roomSelectionDisplay');
        if (display) display.style.display = 'none';
        closeRoomSelectionModal();
    }

    function proceedWithInquiry() {
        closeRoomSelectionModal();
        // Scroll to inquiry form
        const inquiryForm = document.querySelector('form[action*="inquiries"]');
        if (inquiryForm) {
            inquiryForm.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Schedule and booking modals
    function openScheduleModal() {
        @guest
            showSimpleAlert('Please login to schedule a visit.', 'warning');
            return;
        @endguest

        @auth
            @if(auth()->user()->role !== 'tenant')
                showSimpleAlert('Only tenants can schedule visits.', 'warning');
                return;
            @endif
        @endauth

        const modal = document.getElementById('scheduleModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeScheduleModal() {
        const modal = document.getElementById('scheduleModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function openBookingModal() {
        @guest
            showSimpleAlert('Please login to make a booking.', 'warning');
            return;
        @endguest

        @auth
            @if(auth()->user()->role !== 'tenant')
                showSimpleAlert('Only tenants can make bookings.', 'warning');
                return;
            @endif
        @endauth

        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeBookingModal() {
        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // New simplified functions
    function handleDirectMessage() {
        @guest
            showSimpleAlert('Please login to send a message.', 'warning');
            return;
        @endguest

        @auth
            @if(auth()->user()->role !== 'tenant')
                showSimpleAlert('Only tenants can send messages to landlords.', 'warning');
                return;
            @endif
        @endauth

        const form = document.getElementById('messageForm');
        if (!form) {
            showSimpleAlert('Message form not found. Please refresh the page.', 'error');
            return;
        }

        const messageField = form.querySelector('textarea[name="message"]');
        if (!messageField || !messageField.value.trim()) {
            showSimpleAlert('Please enter a message before sending.', 'warning');
            messageField?.focus();
            return;
        }

        const submitButton = form.querySelector('button[onclick="handleDirectMessage()"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = '💬 Sending...';
        }

        const formData = new FormData(form);

        fetch('{{ route("messages.direct") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSimpleAlert('✅ Message sent successfully! The landlord will receive your message.', 'success');
                form.reset();
                setTimeout(() => {
                    window.location.href = '{{ route("messages.index") }}';
                }, 2000);
            } else {
                showSimpleAlert('❌ ' + (data.message || 'Failed to send message. Please try again.'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showSimpleAlert('Sending message...', 'info');
            form.submit();
        })
        .finally(() => {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = '💬 Send Message';
            }
        });
    }


    function submitBooking(event) {
        event.preventDefault();

        @guest
            showSimpleAlert('Please login to submit a booking inquiry.', 'warning');
            return;
        @endguest

        @auth
            @if(auth()->user()->role !== 'tenant')
                showSimpleAlert('Only tenants can submit booking inquiries.', 'warning');
                return;
            @endif
        @endauth

        const form = event.target;
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;

        // Disable submit button and show loading
        submitButton.disabled = true;
        submitButton.textContent = '🔄 Submitting...';

        // Get form data
        const formData = new FormData(form);

        // Build message from form data
        let message = 'I would like to book this property with the following details:\n\n';

        if (formData.get('room_id')) {
            const roomSelect = form.querySelector('select[name="room_id"]');
            if (roomSelect && roomSelect.selectedOptions[0]) {
                message += `Room: ${roomSelect.selectedOptions[0].text}\n`;
            }
        }

        if (formData.get('check_in')) {
            message += `Check-in Date: ${formData.get('check_in')}\n`;
        }

        if (formData.get('check_out')) {
            message += `Check-out Date: ${formData.get('check_out')}\n`;
        }

        if (formData.get('notes') && formData.get('notes').trim()) {
            message += `\nSpecial Requests: ${formData.get('notes')}\n`;
        }

        message += '\nPlease let me know about availability and next steps for booking.';

        // Create inquiry data
        const inquiryData = new FormData();
        inquiryData.append('_token', '{{ csrf_token() }}');
        inquiryData.append('property_id', '{{ $property->id }}');
        inquiryData.append('room_id', formData.get('room_id') || '');
        inquiryData.append('move_in_date', formData.get('check_in') || '');
        inquiryData.append('move_out_date', formData.get('check_out') || '');
        inquiryData.append('message', message);

        // Submit inquiry
        fetch('{{ route("inquiries.store") }}', {
            method: 'POST',
            body: inquiryData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                closeBookingModal();

                // Show success message
                showSimpleAlert('🎉 Booking inquiry submitted successfully! The landlord has been notified and will contact you soon.', 'success');

                // Reset form
                form.reset();

                // Redirect to messages after a delay
                setTimeout(() => {
                    window.location.href = '{{ route("messages.index") }}';
                }, 3000);
            } else {
                showSimpleAlert('❌ ' + (data.message || 'Failed to submit inquiry. Please try again.'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showSimpleAlert('⚠️ Something went wrong. Please try again or contact support.', 'error');
        })
        .finally(() => {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        });
    }

    function scheduleVisit(event) {
        event.preventDefault();

        @guest
            showSimpleAlert('Please login to schedule a visit.', 'warning');
            return;
        @endguest

        @auth
            @if(auth()->user()->role !== 'tenant')
                showSimpleAlert('Only tenants can schedule visits.', 'warning');
                return;
            @endif
        @endauth

        const form = event.target;
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;

        // Disable submit button and show loading
        submitButton.disabled = true;
        submitButton.textContent = '📅 Scheduling...';

        // Get form data
        const formData = new FormData(form);

        // Create visit request data
        const visitData = new FormData();
        visitData.append('_token', '{{ csrf_token() }}');
        visitData.append('property_id', '{{ $property->id }}');
        visitData.append('preferred_date', formData.get('visit_date'));
        visitData.append('preferred_time', formData.get('visit_time'));
        visitData.append('notes', formData.get('tenant_notes') || 'Property viewing request.');

        // Submit to scheduled visits endpoint
        fetch('{{ route("tenant.scheduled-visits.store") }}', {
            method: 'POST',
            body: visitData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                closeScheduleModal();

                // Show success message
                showSimpleAlert('📅 Visit scheduled successfully! The landlord has been notified and will confirm your visit request.', 'success');

                // Reset form
                form.reset();

                // Redirect to messages after a delay
                setTimeout(() => {
                    window.location.href = '{{ route("messages.index") }}';
                }, 3000);
            } else {
                showSimpleAlert('❌ ' + (data.message || 'Failed to schedule visit. Please try again.'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showSimpleAlert('⚠️ Something went wrong. Please try again or contact support.', 'error');
        })
        .finally(() => {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        });
    }

    // Map and directions functions
    function centerOnProperty() {
        if (map && propertyLocation) {
            map.setView(propertyLocation, 16);
            showSimpleAlert('Centered on property location', 'success');
        } else {
            showSimpleAlert('Map not available', 'error');
        }
    }

    function openDirectionsModal() {
        const modal = document.getElementById('directionsModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDirectionsModal() {
        const modal = document.getElementById('directionsModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function getDirectionsGPS() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    const propertyLat = propertyLocation[0];
                    const propertyLng = propertyLocation[1];

                    const url = `https://www.google.com/maps/dir/${userLat},${userLng}/${propertyLat},${propertyLng}`;
                    window.open(url, '_blank');
                    closeDirectionsModal();
                },
                error => {
                    showSimpleAlert('Unable to get your location. Please try address option.', 'error');
                }
            );
        } else {
            showSimpleAlert('Geolocation not supported. Please try address option.', 'error');
        }
    }

    function getDirectionsAddress() {
        @auth
            @if(auth()->user()->address)
                const address = "{{ auth()->user()->address }}, {{ auth()->user()->city }}";
                getDirectionsFromAddress(address);
            @else
                showSimpleAlert('Please update your address in your profile.', 'warning');
            @endif
        @else
            showSimpleAlert('Please login to use this feature.', 'warning');
        @endauth
    }

    function getDirectionsFromAddress(address) {
        const propertyLat = propertyLocation[0];
        const propertyLng = propertyLocation[1];
        const url = `https://www.google.com/maps/dir/${encodeURIComponent(address)}/${propertyLat},${propertyLng}`;
        window.open(url, '_blank');
        closeDirectionsModal();
    }

    function searchAddress() {
        const address = document.getElementById('searchInput').value.trim();
        if (!address) {
            showSimpleAlert('Please enter an address.', 'warning');
            return;
        }

        getDirectionsFromAddress(address);
    }

    function enableMapClick() {
        showSimpleAlert('Click on the map to set your location, then get directions.', 'info');
        closeDirectionsModal();
    }

    // Keyboard and click event handlers
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeReviewModal();
            closeScheduleModal();
            closeBookingModal();
            closeDirectionsModal();
            closeRoomSelectionModal();
        }
    });

    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        const modals = ['reviewModal', 'scheduleModal', 'bookingModal', 'directionsModal', 'roomModal', 'imageModal', 'roomImageModal', 'propertyLightbox'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && e.target === modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    });

    // Lightbox Variables - Property and room images
    const galleryImages = [
        @foreach($property->images as $image)
        {
            'url': '{{ asset("storage/" . $image->image_path) }}',
            'alt': '{{ addslashes($image->alt_text ?? "Property Image") }}',
            'is_cover': {{ $image->is_cover ? 'true' : 'false' }},
            'type': 'property'
        }@if(!$loop->last || $property->rooms->count() > 0),@endif
        @endforeach
        @foreach($property->rooms as $room)
            @foreach($room->images as $roomImage)
            {
                'url': '{{ asset("storage/" . $roomImage->image_path) }}',
                'alt': '{{ addslashes($room->room_number) }} - Room Image',
                'is_cover': {{ $roomImage->is_cover ? 'true' : 'false' }},
                'type': 'room',
                'room_name': '{{ addslashes($room->room_number) }}'
            }@if(!$loop->last || !$loop->parent->last),@endif
            @endforeach
        @endforeach
    ];

    console.log('Total gallery images loaded:', galleryImages.length);
    console.log('Gallery images:', galleryImages);
    let currentLightboxIndex = 0;

    // Lightbox Functions
    function openLightbox(index) {
        console.log('Opening lightbox with index:', index);
        console.log('Total images available:', galleryImages.length);

        if (index < 0 || index >= galleryImages.length) {
            console.error('Invalid index:', index);
            return;
        }

        currentLightboxIndex = index;

        if (!document.getElementById('propertyLightbox')) {
            console.log('Creating new lightbox');
            createLightbox();
        } else {
            console.log('Using existing lightbox');
        }

        const lightbox = document.getElementById('propertyLightbox');
        if (!lightbox) {
            console.error('Failed to create or find lightbox element');
            return;
        }

        updateLightboxImage();
        lightbox.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        console.log('Lightbox opened successfully');
    }

    // Function to open lightbox from room images
    function openLightboxFromRoom(imageUrl) {
        console.log('Room image clicked:', imageUrl);
        console.log('Gallery images available:', galleryImages.length);

        // Find the index of this image in the galleryImages array
        let index = galleryImages.findIndex(img => img.url === imageUrl);
        console.log('Exact match index:', index);

        if (index === -1) {
            // If exact URL match fails, try to find by filename match
            const fileName = imageUrl.split('/').pop();
            index = galleryImages.findIndex(img => {
                const imgFileName = img.url.split('/').pop();
                return imgFileName === fileName;
            });
            console.log('Filename match index:', index);
        }

        if (index === -1) {
            // Try to find by normalizing filenames (handle spaces vs underscores)
            const normalizedFileName = imageUrl.split('/').pop().replace(/[\s_]+/g, '_');
            index = galleryImages.findIndex(img => {
                const normalizedGalleryFileName = img.url.split('/').pop().replace(/[\s_]+/g, '_');
                return normalizedGalleryFileName === normalizedFileName;
            });
            console.log('Normalized filename match index:', index);
        }

        if (index === -1) {
            // Try to find by partial filename match (without extension)
            const fileNameWithoutExt = imageUrl.split('/').pop().replace(/\.[^.]*$/, '');
            index = galleryImages.findIndex(img => {
                const galleryFileNameWithoutExt = img.url.split('/').pop().replace(/\.[^.]*$/, '');
                return galleryFileNameWithoutExt.includes(fileNameWithoutExt) ||
                       fileNameWithoutExt.includes(galleryFileNameWithoutExt);
            });
            console.log('Partial filename match index:', index);
        }

        if (index === -1) {
            // If still not found, try partial path match
            index = galleryImages.findIndex(img => {
                return img.url.includes(imageUrl.substring(imageUrl.indexOf('storage'))) ||
                       imageUrl.includes(img.url.substring(img.url.indexOf('storage')));
            });
            console.log('Partial path match index:', index);
        }

        if (index !== -1) {
            console.log('Opening lightbox at index:', index);
            openLightbox(index);
        } else {
            console.error('Image not found in gallery. URL:', imageUrl);
            console.log('Available URLs:');
            galleryImages.forEach((img, i) => console.log(`${i}: ${img.url}`));

            // As fallback, still try to open lightbox with first image
            if (galleryImages.length > 0) {
                console.log('Opening lightbox with first image as fallback');
                openLightbox(0);
            }
        }
    }

    function createLightbox() {
        const lightbox = document.createElement('div');
        lightbox.id = 'propertyLightbox';
        lightbox.className = 'fixed inset-0 z-50 hidden';
        lightbox.style.background = 'rgba(0, 0, 0, 0.8)';
        lightbox.style.backdropFilter = 'blur(10px)';
        lightbox.style.webkitBackdropFilter = 'blur(10px)';

        const showNavigation = galleryImages.length > 1;

        lightbox.innerHTML = `
            <div class="h-screen w-screen flex flex-col">
                <!-- Header -->
                <div class="flex justify-between items-center p-4 flex-shrink-0">
                    <div class="text-white">
                        <span id="lightboxCounter" class="text-lg font-medium"></span>
                    </div>
                    <button onclick="closeLightbox()" class="text-white hover:text-gray-300 transition z-30">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Main Image Container -->
                <div class="flex-1 flex items-center justify-center relative min-h-0 px-4 md:px-20">
                    <!-- Previous Button -->
                    <button onclick="previousLightboxImage()" id="prevBtn" class="absolute left-2 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-all duration-200 z-20 bg-black bg-opacity-60 rounded-full p-4 hover:bg-opacity-80 hover:scale-110" style="display: ${showNavigation ? 'flex' : 'none'}; align-items: center; justify-content: center;">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 18l-6-6 6-6"></path>
                        </svg>
                    </button>

                    <!-- Image -->
                    <img id="lightboxImage" src="" alt="" class="max-w-full max-h-full object-contain mx-auto block" style="max-height: calc(100vh - 200px); background: transparent;">

                    <!-- Next Button -->
                    <button onclick="nextLightboxImage()" id="nextBtn" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-all duration-200 z-20 bg-black bg-opacity-60 rounded-full p-4 hover:bg-opacity-80 hover:scale-110" style="display: ${showNavigation ? 'flex' : 'none'}; align-items: center; justify-content: center;">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18l6-6-6-6"></path>
                        </svg>
                    </button>
                </div>

                <!-- Thumbnail Strip -->
                <div id="thumbnailContainer" class="px-6 py-4 flex-shrink-0" style="display: ${showNavigation ? 'block' : 'none'}; background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                    <div id="thumbnailStrip" class="flex justify-center items-center space-x-4 overflow-x-auto scrollbar-hide" style="max-width: 100%; padding: 8px 16px;">
                        <!-- Thumbnails will be generated here -->
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(lightbox);

        // Generate thumbnails
        generateThumbnails();

        // Add keyboard support
        document.addEventListener('keydown', handleLightboxKeyboard);

        // Add mobile swipe support
        addSwipeSupport();

        // Add click-outside-to-close for lightbox
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
    }

    function generateThumbnails() {
        const thumbnailStrip = document.getElementById('thumbnailStrip');
        if (!thumbnailStrip) return;

        thumbnailStrip.innerHTML = '';
        galleryImages.forEach((image, index) => {
            const thumbnail = document.createElement('div');
            thumbnail.className = 'cursor-pointer border-3 border-transparent hover:border-white active:border-white transition-all duration-200 rounded-md overflow-hidden flex-shrink-0 transform hover:scale-105';
            thumbnail.onclick = () => goToLightboxImage(index);
            thumbnail.style.width = '90px';
            thumbnail.style.height = '90px';
            thumbnail.innerHTML = `
                <img src="${image.url}" alt="${image.alt}" class="w-full h-full object-cover">
            `;
            thumbnail.dataset.index = index;
            thumbnailStrip.appendChild(thumbnail);
        });
    }

    function updateLightboxImage() {
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxCounter = document.getElementById('lightboxCounter');
        const thumbnails = document.querySelectorAll('#thumbnailStrip div');

        console.log('Updating lightbox image to index:', currentLightboxIndex);

        if (galleryImages[currentLightboxIndex]) {
            const currentImage = galleryImages[currentLightboxIndex];
            console.log('Loading image:', currentImage.url);

            lightboxImage.src = currentImage.url;
            lightboxImage.alt = currentImage.alt;

            // Add loading indicator
            lightboxImage.onload = function() {
                console.log('Lightbox image loaded successfully');
                this.style.opacity = '1';
            };

            lightboxImage.onerror = function() {
                console.error('Failed to load lightbox image:', currentImage.url);
                this.style.background = '#374151';
                this.style.color = 'white';
                this.style.display = 'flex';
                this.style.alignItems = 'center';
                this.style.justifyContent = 'center';
                this.innerHTML = '<div style="text-align: center; padding: 20px;"><p>Image not found</p><small>' + currentImage.url + '</small></div>';
            };

            lightboxImage.style.opacity = '0.5';

            // Enhanced counter with image type info
            let counterText = `${currentLightboxIndex + 1} / ${galleryImages.length}`;
            if (currentImage.type === 'room') {
                counterText += ` - ${currentImage.room_name}`;
            } else {
                counterText += ' - Property';
            }
            lightboxCounter.textContent = counterText;

            // Update thumbnail selection
            thumbnails.forEach((thumb, index) => {
                if (index === currentLightboxIndex) {
                    thumb.classList.add('border-white');
                    // Scroll active thumbnail into view
                    thumb.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                } else {
                    thumb.classList.remove('border-white');
                }
            });
        } else {
            console.error('No image found at index:', currentLightboxIndex);
        }
    }

    function previousLightboxImage() {
        currentLightboxIndex = (currentLightboxIndex - 1 + galleryImages.length) % galleryImages.length;
        updateLightboxImage();
    }

    function nextLightboxImage() {
        currentLightboxIndex = (currentLightboxIndex + 1) % galleryImages.length;
        updateLightboxImage();
    }

    function goToLightboxImage(index) {
        currentLightboxIndex = index;
        updateLightboxImage();
    }

    function closeLightbox() {
        document.getElementById('propertyLightbox').classList.add('hidden');
        document.body.style.overflow = '';

        // Blur effect is handled by CSS, no need to manually remove
    }

    function handleLightboxKeyboard(e) {
        const lightbox = document.getElementById('propertyLightbox');
        if (lightbox && !lightbox.classList.contains('hidden')) {
            // Prevent default behavior for navigation keys
            if (['ArrowLeft', 'ArrowRight', 'Escape'].includes(e.key)) {
                e.preventDefault();
                e.stopPropagation();
            }

            switch(e.key) {
                case 'Escape':
                    closeLightbox();
                    break;
                case 'ArrowLeft':
                case 'Left': // Support older browsers
                    if (galleryImages.length > 1) {
                        previousLightboxImage();
                    }
                    break;
                case 'ArrowRight':
                case 'Right': // Support older browsers
                    if (galleryImages.length > 1) {
                        nextLightboxImage();
                    }
                    break;
                case ' ': // Spacebar for next image
                    if (galleryImages.length > 1) {
                        nextLightboxImage();
                    }
                    break;
            }
        }
    }

    // Mobile Swipe Support
    function addSwipeSupport() {
        let startX = 0;
        let startY = 0;
        let endX = 0;
        let endY = 0;
        const threshold = 50; // Minimum distance for a swipe

        const lightbox = document.getElementById('propertyLightbox');

        lightbox.addEventListener('touchstart', function(e) {
            const touch = e.touches[0];
            startX = touch.clientX;
            startY = touch.clientY;
        }, { passive: true });

        lightbox.addEventListener('touchmove', function(e) {
            e.preventDefault(); // Prevent scrolling
        }, { passive: false });

        lightbox.addEventListener('touchend', function(e) {
            const touch = e.changedTouches[0];
            endX = touch.clientX;
            endY = touch.clientY;

            const deltaX = endX - startX;
            const deltaY = endY - startY;
            const absDeltaX = Math.abs(deltaX);
            const absDeltaY = Math.abs(deltaY);

            // Only process horizontal swipes that are greater than vertical movement
            if (absDeltaX > threshold && absDeltaX > absDeltaY) {
                if (deltaX > 0) {
                    // Swipe right - go to previous image
                    previousLightboxImage();
                } else {
                    // Swipe left - go to next image
                    nextLightboxImage();
                }
            }
        }, { passive: true });
    }

    // Room Image Modal Functions
    function openRoomImageModal(imageSrc, roomName, roomIndex, imageIndex) {
        // Create room image modal if it doesn't exist
        if (!document.getElementById('roomImageModal')) {
            const modal = document.createElement('div');
            modal.id = 'roomImageModal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden';
            modal.innerHTML = `
                <div class="max-w-4xl max-h-screen p-4 relative">
                    <div class="bg-white rounded-lg p-4 mb-4">
                        <h3 id="roomModalTitle" class="text-lg font-semibold"></h3>
                    </div>
                    <img id="roomModalImage" src="" alt="Room Image" class="max-w-full max-h-full object-contain rounded-lg">
                    <button onclick="closeRoomImageModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            document.body.appendChild(modal);
        }

        document.getElementById('roomModalImage').src = imageSrc;
        document.getElementById('roomModalTitle').textContent = roomName + ' - Gallery';
        document.getElementById('roomImageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeRoomImageModal() {
        document.getElementById('roomImageModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function showAllRoomImages(roomIndex) {
        // You can implement a full room gallery view here
        console.log('Show all images for room', roomIndex);
    }

    // Auto-slideshow functionality for featured image
    let featuredImageIndex = 0;
    let autoSlideInterval;

    function initAutoSlideshow() {
        if (galleryImages.length <= 1) return; // Don't start if only one image

        // Start auto-slideshow
        autoSlideInterval = setInterval(() => {
            featuredImageIndex = (featuredImageIndex + 1) % galleryImages.length;
            updateFeaturedImage();
        }, 5000); // 5 seconds interval
    }

    function updateFeaturedImage() {
        const mainImage = document.getElementById('mainImage');
        const currentIndexSpan = document.getElementById('currentImageIndex');
        const featuredBadge = document.getElementById('featuredBadge');
        const featuredContainer = document.getElementById('featuredImageContainer');

        if (mainImage && galleryImages[featuredImageIndex]) {
            // Smooth transition
            mainImage.style.opacity = '0.7';

            setTimeout(() => {
                mainImage.src = galleryImages[featuredImageIndex].url;
                mainImage.alt = galleryImages[featuredImageIndex].alt;

                // Update onclick handler for the current featured image
                mainImage.onclick = () => openLightbox(featuredImageIndex);

                // Also update the container onclick as backup
                if (featuredContainer) {
                    featuredContainer.onclick = (e) => {
                        if (e.target === mainImage || e.target === featuredContainer) {
                            openLightbox(featuredImageIndex);
                        }
                    };
                }

                if (currentIndexSpan) {
                    currentIndexSpan.textContent = featuredImageIndex + 1;
                }

                // Update badge text
                if (featuredBadge) {
                    if (featuredImageIndex === 0) {
                        featuredBadge.textContent = 'Featured';
                    } else {
                        featuredBadge.textContent = `Image ${featuredImageIndex + 1}`;
                    }
                }

                mainImage.style.opacity = '1';
            }, 250);
        }
    }

    function pauseAutoSlideshow() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }
    }

    function resumeAutoSlideshow() {
        if (!autoSlideInterval && galleryImages.length > 1) {
            initAutoSlideshow();
        }
    }

    // Pause on hover, resume on leave
    document.addEventListener('DOMContentLoaded', function() {
        const featuredContainer = document.getElementById('featuredImageContainer');
        const mainImage = document.getElementById('mainImage');

        if (featuredContainer) {
            featuredContainer.addEventListener('mouseenter', pauseAutoSlideshow);
            featuredContainer.addEventListener('mouseleave', resumeAutoSlideshow);
        }

        // Ensure initial image is clickable
        if (mainImage) {
            mainImage.onclick = () => openLightbox(0);

            // Add click event to entire container as well
            if (featuredContainer) {
                featuredContainer.style.cursor = 'pointer';
                featuredContainer.addEventListener('click', function(e) {
                    if (e.target === mainImage || e.target.closest('#featuredImageContainer')) {
                        openLightbox(featuredImageIndex);
                    }
                });
            }
        }

        // Start auto-slideshow
        initAutoSlideshow();
    });
</script>
@endpush

@section('content')


<div class="pt-8 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 sm:p-8 pt-8">
                
                <!-- Property Images Gallery -->
                <div class="mb-6 mt-4">
                    @if($property->images->count() > 0)
                        @php
                            $coverImage = $property->images->where('is_cover', true)->first() ?? $property->images->first();
                            $galleryImages = $property->images;
                            $allImagesForGrid = $property->images; // Show ALL images in right grid
                        @endphp

                        <!-- Responsive Gallery Layout -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                            <!-- Main Featured Image (Left Side) -->
                            <div class="lg:col-span-2">
                                <div class="relative group" id="featuredImageContainer">
                                    <img
                                        src="{{ asset('storage/' . $coverImage->image_path) }}"
                                        alt="{{ $property->title }}"
                                        class="w-full h-64 sm:h-80 lg:h-96 object-cover rounded-lg cursor-pointer hover:opacity-95 transition-all duration-500 shadow-lg"
                                        id="mainImage"
                                        onclick="openLightbox(0)"
                                    >

                                    <!-- Image Counter -->
                                    @if($property->images->count() > 1)
                                        <div class="absolute top-4 right-4 bg-black bg-opacity-70 text-white px-3 py-1 rounded-full text-sm">
                                            <span id="currentImageIndex">1</span> / {{ $property->images->count() }}
                                        </div>
                                    @endif

                                    <!-- Featured Image Badge -->
                                    <div class="absolute top-4 left-4 bg-green-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                        <span id="featuredBadge">Featured</span>
                                    </div>

                                    <!-- Auto-play indicator -->
                                    @if($property->images->count() > 1)
                                        <div class="absolute bottom-4 left-4 bg-black bg-opacity-70 text-white px-2 py-1 rounded text-xs flex items-center space-x-1">
                                            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                            <span>Auto-play</span>
                                        </div>
                                    @endif

                                    <!-- Click to open lightbox overlay -->
                                    <div class="absolute inset-0 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" style="background: rgba(0, 0, 0, 0.1);">
                                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Grid (Right Side) -->
                            <div class="lg:col-span-1">
                                @if($allImagesForGrid->count() > 0)
                                    <div class="space-y-3">
                                        <h3 class="text-lg font-semibold text-gray-800">Property Gallery</h3>
                                        <div class="grid grid-cols-2 gap-3">
                                            @foreach($allImagesForGrid->take(6) as $gridIndex => $image)
                                                <!-- Regular image display -->
                                                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer @if($gridIndex == 5 && $allImagesForGrid->count() > 6) relative @endif" onclick="console.log('Property grid image clicked:', {{ $gridIndex }}); openLightbox({{ $gridIndex }})">
                                                    <img
                                                        src="{{ asset('storage/' . $image->image_path) }}"
                                                        alt="Property image {{ $gridIndex + 1 }}"
                                                        class="w-full h-24 object-cover pointer-events-none"
                                                        loading="lazy"
                                                        style="width: 100%; height: 96px; object-fit: cover; display: block;"
                                                    >
                                                    @if($gridIndex == 5 && $allImagesForGrid->count() > 6)
                                                        <!-- More photos overlay -->
                                                        <div class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center text-white">
                                                            <div class="text-center">
                                                                <span class="text-lg font-bold">+{{ $allImagesForGrid->count() - 6 }}</span>
                                                                <p class="text-xs">More</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <!-- Single Image - Show message -->
                                    <div class="h-96 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <div class="text-center text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-sm">Click the main image to view full size</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

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

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

                    <!-- Left Column - Property Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                            <h1 class="text-3xl font-bold">{{ $property->title }}</h1>
                                @auth
                                    @if(auth()->user()->role === 'tenant')
                                        <button onclick="toggleFavorite({{ $property->id }})"
                                                id="favorite-btn-{{ $property->id }}"
                                                class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 hover:scale-105 bg-gray-50 text-gray-600 border border-gray-200">
                                            <svg id="heart-icon-{{ $property->id }}" class="w-6 h-6 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                            <span id="favorite-text-{{ $property->id }}" class="font-medium text-sm">Add to Favorites</span>
                                        </button>
                                    @endif
                                @else
                                    <button onclick="openAuthModal('login')"
                                            class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 hover:scale-105 bg-gray-50 text-gray-600 border border-gray-200">
                                        <svg class="w-6 h-6 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        <span class="font-medium text-sm">Login to Add to Favorites</span>
                                    </button>
                                @endauth
                        </div>

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
                        </div>

                        <!-- Description -->
                        <div class="bg-white border rounded-lg p-6 shadow-sm">
                            <h2 class="text-xl font-bold mb-4">Description</h2>
                            <div class="border border-gray-200 rounded bg-gray-50 min-h-[80px] max-h-80 overflow-hidden">
                                <div class="text-gray-700 whitespace-pre-line leading-relaxed break-words p-4 max-h-full overflow-y-auto">
                                    {{ $property->description }}
                                </div>
                            </div>
                        </div>

                        <!-- Amenities -->
                        @if($property->amenities && $property->amenities->count() > 0)
                        <div class="bg-white border rounded-lg p-6 shadow-sm">
                            <h2 class="text-xl font-bold mb-4">Amenities & Features</h2>
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

                        <!-- Available Rooms with Gallery -->
                        @if($property->rooms && $property->rooms->count() > 0)
                        <div class="bg-white border rounded-lg p-6 shadow-sm">
                            <h2 class="text-xl font-bold mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Available Rooms ({{ $property->rooms->count() }})
                            </h2>

                            <div class="space-y-6">
                                @foreach($property->rooms as $roomIndex => $room)
                                    <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors {{ $room->status !== 'available' ? 'bg-gray-50' : 'bg-white' }}">
                                        <!-- Room Header Info -->
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">{{ $room->room_number }}</h3>
                                                <div class="flex items-center space-x-3 text-sm text-gray-600 mt-1">
                                                    <span class="capitalize">{{ str_replace('_', ' ', $room->room_type) }}</span>
                                                    <span>•</span>
                                                    <span>{{ $room->capacity }} {{ $room->capacity > 1 ? 'tenants' : 'tenant' }}</span>
                                                    @if($room->size_sqm)
                                                        <span>•</span>
                                                        <span>{{ $room->size_sqm }}m²</span>
                                                    @endif
                                                </div>
                                                @if($room->description)
                                                    <p class="text-sm text-gray-600 mt-2">{{ $room->description }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xl font-bold text-green-600">₱{{ number_format($room->price) }}</p>
                                                <p class="text-sm text-gray-500">per month</p>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-2
                                                    {{ $room->status === 'available' ? 'bg-green-100 text-green-800' : ($room->status === 'occupied' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($room->status) }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Room Gallery -->
                                        @if($room->images && $room->images->count() > 0)
                                            <div class="mt-4">
                                                <div class="flex items-center justify-between mb-3">
                                                    <h4 class="text-sm font-medium text-gray-700 flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        Room Gallery ({{ $room->images->count() }} {{ $room->images->count() === 1 ? 'photo' : 'photos' }})
                                                    </h4>
                                                    @if($room->images->count() > 3)
                                                        <button
                                                            onclick="showAllRoomImages({{ $roomIndex }})"
                                                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                            View All ({{ $room->images->count() }})
                                                        </button>
                                                    @endif
                                                </div>

                                                <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                                                    @foreach($room->images->take(6) as $imageIndex => $roomImage)
                                                        <div class="relative group cursor-pointer" onclick="openLightboxFromRoom('{{ asset('storage/' . $roomImage->image_path) }}')">
                                                            <img
                                                                src="{{ asset('storage/' . $roomImage->image_path) }}"
                                                                alt="{{ $room->room_number }} - Image {{ $imageIndex + 1 }}"
                                                                class="w-full object-cover rounded-lg border-2 border-gray-200 hover:border-blue-400 transition-all duration-200 hover:scale-105 cursor-pointer"
                                                                style="width: 100%; height: 80px; object-fit: cover; display: block; background: #f9fafb;"
                                                                loading="lazy"
                                                            >

                                                            <!-- Image Cover Badge -->
                                                            @if($roomImage->is_cover)
                                                                <div class="absolute -top-1 -left-1 bg-blue-500 text-white text-xs px-1 py-0.5 rounded-sm font-medium">
                                                                    Cover
                                                                </div>
                                                            @endif

                                                            <!-- Hover Overlay -->
                                                            <div class="absolute inset-0 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" style="background: rgba(0, 0, 0, 0.3);">
                                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                                </svg>
                                                            </div>

                                                            <!-- Show count overlay for last image if more than 6 -->
                                                            @if($imageIndex === 5 && $room->images->count() > 6)
                                                                <div class="absolute inset-0 bg-black bg-opacity-60 rounded-lg flex items-center justify-center text-white">
                                                                    <div class="text-center">
                                                                        <span class="text-lg font-bold">+{{ $room->images->count() - 6 }}</span>
                                                                        <p class="text-xs">More</p>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <!-- No Room Images -->
                                            <div class="mt-4 p-4 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                                                <div class="text-center">
                                                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="text-sm text-gray-500">No images available for this room</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Map Section -->
                        <div class="bg-white border rounded-lg p-6 shadow-sm">
                            <h2 class="text-xl font-bold mb-4">📍 Location & Map</h2>

                            <!-- Map Container -->
                            <div id="propertyMap" style="height: 400px; width: 100%; background: #f0f0f0; border-radius: 10px; border: 1px solid #ddd;">
                                <div id="mapPlaceholder" class="flex items-center justify-center h-96 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg">
                                    <div class="text-center">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <p class="text-gray-600 font-medium">Loading Map...</p>
                                        <p class="text-sm text-gray-500 mt-1">Please wait while we initialize the interactive map</p>
                                        <button onclick="initMapManually()" class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                            🔄 Load Map Manually
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Distance Info -->
                            <div class="mt-4 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center mb-2">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-gray-800">Distance from PSU Main Campus</p>
                                </div>
                                <div class="flex items-baseline space-x-3 mb-2">
                                    <p class="text-3xl font-bold text-yellow-700" id="distanceText">Calculating...</p>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span id="walkingTime">--</span>
                                    </div>
                                </div>
                                <div class="flex items-center text-xs text-yellow-700">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    Estimated walking time at normal pace
                                </div>
                            </div>

                            <!-- Enhanced Action Buttons -->
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button onclick="openDirectionsModal()" id="directionsBtn" style="background: linear-gradient(to right, #2563eb, #1d4ed8); color: white; padding: 12px 16px; border-radius: 8px; font-weight: 500; display: flex; align-items: center; justify-content: center; flex: 1; min-width: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.2s;" onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #1e40af)'" onmouseout="this.style.background='linear-gradient(to right, #2563eb, #1d4ed8)'">
                                    <svg style="width: 16px; height: 16px; margin-right: 8px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                    </svg>
                                    <span style="color: white; font-weight: 500;">Get Directions</span>
                                </button>
                                <button onclick="centerOnProperty()" style="background: linear-gradient(to right, #4b5563, #374151); color: white; padding: 12px 16px; border-radius: 8px; font-weight: 500; display: flex; align-items: center; justify-content: center; flex: 1; min-width: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.2s;" onmouseover="this.style.background='linear-gradient(to right, #374151, #1f2937)'" onmouseout="this.style.background='linear-gradient(to right, #4b5563, #374151)'">
                                    <svg style="width: 16px; height: 16px; margin-right: 8px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span style="color: white; font-weight: 500;">Focus Property</span>
                                </button>
                            </div>
                        </div>

                        <!-- House Rules -->
                        <div class="bg-white border rounded-lg p-6 shadow-sm">
                            <h2 class="text-xl font-bold mb-4">House Rules</h2>
                            <ul class="list-disc list-inside text-gray-700 space-y-2">
                                <li>No smoking inside the premises</li>
                                <li>Visitors allowed until 10 PM</li>
                                <li>Keep common areas clean</li>
                                <li>Respect quiet hours (10 PM - 7 AM)</li>
                            </ul>
                        </div>

                        <!-- Reviews Section -->
                        @php
                            $reviews = $property->reviews()->with('user')->latest()->get();
                            $averageRating = $reviews->avg('rating') ?: 0;
                            $totalReviews = $reviews->count();
                        @endphp

                        <div class="bg-white border rounded-lg p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-bold text-gray-900">Reviews</h2>
                                    @if($totalReviews > 0)
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center space-x-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-5 h-5" style="color: {{ $i <= round($averageRating) ? '#f59e0b' : '#d1d5db' }};" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-lg font-semibold text-gray-900">{{ number_format($averageRating, 1) }}</span>
                                                <span class="text-sm text-gray-500">out of 5</span>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ({{ $totalReviews }} {{ $totalReviews == 1 ? 'review' : 'reviews' }})
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @if($totalReviews > 0)
                                    <div class="mt-4 space-y-3">
                                        <div class="text-sm text-gray-600">
                                            Based on {{ $totalReviews }} {{ $totalReviews == 1 ? 'review' : 'reviews' }} from verified tenants
                                        </div>

                                        <!-- Rating Breakdown -->
                                        <div class="space-y-2">
                                            @php
                                                $ratingCounts = [];
                                                for($i = 5; $i >= 1; $i--) {
                                                    $ratingCounts[$i] = $reviews->where('rating', $i)->count();
                                                }
                                            @endphp

                                            @foreach($ratingCounts as $rating => $count)
                                                <div class="flex items-center space-x-2 text-sm">
                                                    <span class="w-3 text-gray-600">{{ $rating }}</span>
                                                    <svg class="w-4 h-4" style="color: #f59e0b;" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-yellow-400 h-2 rounded-full transition-all duration-300" style="width: {{ $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0 }}%;"></div>
                                                    </div>
                                                    <span class="w-8 text-gray-600 text-right">{{ $count }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-4 text-center py-8">
                                        <div class="flex justify-center mb-3">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-6 h-6" style="color: #d1d5db;" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                        </div>
                                        <p class="text-gray-600 text-lg font-medium">No reviews yet</p>
                                        <p class="text-gray-500 text-sm mt-2">Be the first to leave a review and help other tenants!</p>
                                    </div>
                                @endif
                                </div>

                                @auth
                                    @if(auth()->user()->role === 'tenant')
                                        @php
                                            $userReview = $reviews->where('user_id', auth()->id())->first();
                                        @endphp
                                        @if(!$userReview)
                                            <button onclick="openReviewModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                                                Write a Review
                                            </button>
                                        @endif
                                    @endif
                                @endauth
                            </div>

                            @if($totalReviews > 0)
                                <div class="space-y-4 max-h-96 overflow-y-auto">
                                    @foreach($reviews->take(10) as $review)
                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-gray-300 transition-colors">
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-3 mb-2">
                                                        <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full text-white text-sm font-bold">
                                                            {{ strtoupper(substr($review->reviewer_name, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <span class="font-semibold text-gray-900">{{ $review->reviewer_name }}</span>
                                                            <div class="flex items-center space-x-2 mt-1">
                                                                <div class="flex items-center">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <svg class="w-4 h-4" style="color: {{ $i <= $review->rating ? '#f59e0b' : '#d1d5db' }};" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                        </svg>
                                                                    @endfor
                                                                </div>
                                                                <span class="text-sm font-medium text-gray-600">{{ $review->rating }}/5</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                                </div>
                                                @if(auth()->check() && auth()->id() === $review->user_id)
                                                    <div class="flex items-center space-x-2">
                                                        <button onclick="editReview({{ $review->id }}, {{ $review->rating }}, '{{ addslashes($review->comment) }}')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 border border-blue-200 rounded-md hover:bg-blue-200 transition-colors">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                            Edit
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($review->comment)
                                                <p class="text-gray-700 text-sm mb-2">{{ $review->comment }}</p>
                                            @endif

                                            @if($review->landlord_reply)
                                                <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mt-2">
                                                    <p class="text-sm font-semibold text-blue-800">Landlord's Response:</p>
                                                    <p class="text-sm text-blue-700">{{ $review->landlord_reply }}</p>
                                                    <p class="text-xs text-blue-600 mt-1">{{ $review->landlord_reply_at->format('M j, Y') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                @if($totalReviews > 10)
                                    <div class="text-center mt-4">
                                        <p class="text-gray-600 text-sm">Showing 10 of {{ $totalReviews }} reviews</p>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <p class="text-gray-500">No reviews yet</p>
                                    <p class="text-gray-400 text-sm">Be the first to review this property!</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column - Contact & Actions -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-24 space-y-4">
                            <!-- Contact/Inquiry Card - Role-Based -->
                            @auth
                                @if(auth()->user()->role === 'tenant')
                                    <!-- TENANT: Messaging Card -->
                                    <div class="border rounded-lg p-6 bg-white shadow-sm">
                                        <h3 class="font-bold mb-2 text-lg text-green-600">💬 Contact Landlord</h3>
                                        <p class="text-sm text-gray-600 mb-4">Have questions? Send a direct message to the landlord!</p>

                                @elseif(auth()->user()->role === 'landlord')
                                    <!-- LANDLORD: Property Management Card -->
                                    <div class="border rounded-lg p-6 bg-gradient-to-br from-orange-50 to-yellow-50 shadow-sm border-orange-200">
                                        <h3 class="font-bold mb-2 text-lg text-orange-700">🏢 Landlord View</h3>
                                        <p class="text-sm text-orange-600 mb-4">You're viewing this property as a landlord</p>

                                        <div class="space-y-3">
                                            <div class="flex items-center text-sm text-orange-700">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                </svg>
                                                Landlords cannot send inquiries
                                            </div>
                                            <div class="flex items-center text-sm text-orange-700">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Use tenant account to inquire
                                            </div>
                                        </div>

                                        <div class="mt-4 space-y-2">
                                            <a href="{{ route('landlord.properties.index') }}" class="block w-full bg-orange-600 text-white py-2 px-4 rounded-lg hover:bg-orange-700 transition-colors text-center font-medium">
                                                ⚙️ Manage Your Properties
                                            </a>
                                            <a href="{{ route('properties.browse') }}" class="block w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center font-medium">
                                                🔍 Browse More Properties
                                            </a>
                                        </div>
                                    </div>

                                @elseif(auth()->user()->role === 'admin')
                                    <!-- ADMIN: Administrative View Card -->
                                    <div class="border rounded-lg p-6 bg-gradient-to-br from-purple-50 to-indigo-50 shadow-sm border-purple-200">
                                        <h3 class="font-bold mb-2 text-lg text-purple-700">⚡ Admin View</h3>
                                        <p class="text-sm text-purple-600 mb-4">Administrative oversight panel</p>

                                        <div class="space-y-3">
                                            <div class="flex items-center text-sm text-purple-700">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Administrative privileges active
                                            </div>
                                            <div class="flex items-center text-sm text-purple-700">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                </svg>
                                                Use tenant account for inquiries
                                            </div>
                                        </div>

                                        <div class="mt-4 space-y-2">
                                            <a href="{{ route('admin.dashboard') }}" class="block w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors text-center font-medium">
                                                🛡️ Admin Dashboard
                                            </a>
                                            <a href="{{ route('properties.browse') }}" class="block w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center font-medium">
                                                🔍 Browse Properties
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <!-- GUEST: Login Required Card -->
                                <div class="border rounded-lg p-6 bg-gradient-to-br from-gray-50 to-blue-50 shadow-sm border-gray-200">
                                    <h3 class="font-bold mb-2 text-lg text-gray-700">🔐 Login Required</h3>
                                    <p class="text-sm text-gray-600 mb-4">Please sign in to contact the landlord</p>

                                    <div class="space-y-2">
                                        <a href="{{ route('login') }}" class="block w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors text-center font-medium">
                                            🚀 Login to Send Inquiry
                                        </a>
                                        <a href="{{ route('register') }}" class="block w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center font-medium">
                                            📝 Create Account
                                        </a>
                                    </div>
                                </div>
                            @endauth

                            <!-- TENANT MESSAGE FORM (only visible to tenants) -->
                            @auth
                                @if(auth()->user()->role === 'tenant')
                                    @php
                                        // Check if tenant has any pending inquiries
                                        $pendingInquiry = \App\Models\Inquiry::where('user_id', auth()->id())
                                                                              ->where('status', 'pending')
                                                                              ->with(['property'])
                                                                              ->first();

                                        // Check if tenant has already inquired about THIS specific property
                                        $existingInquiry = \App\Models\Inquiry::where('user_id', auth()->id())
                                                                               ->where('property_id', $property->id)
                                                                               ->with(['property'])
                                                                               ->latest()
                                                                               ->first();

                                        // Check if tenant has existing scheduled visit for this property
                                        $existingVisit = \App\Models\ScheduledVisit::where('user_id', auth()->id())
                                                                                    ->where('property_id', $property->id)
                                                                                    ->whereIn('status', ['pending', 'confirmed'])
                                                                                    ->with(['property'])
                                                                                    ->latest()
                                                                                    ->first();
                                    @endphp

                                    <!-- Simple Message Form -->
                                    <form action="{{ route('messages.direct') }}" method="POST" id="messageForm">
                                        @csrf
                                        <input type="hidden" name="property_id" value="{{ $property->id }}">

                                        <div class="mb-4">
                                            <label for="messageTextarea" class="block text-sm font-medium text-gray-700 mb-2">Your Message</label>
                                            <textarea name="message"
                                                      id="messageTextarea"
                                                      rows="4"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm resize-y"
                                                      placeholder="Hello! I'm interested in this property. Could you please provide more details about availability and viewing?"
                                                      required></textarea>
                                            <p class="text-xs text-gray-500 mt-1">Send a direct message to the landlord</p>
                                        </div>

                                        <button type="button" onclick="handleDirectMessage()" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-semibold mb-3 transition-colors duration-200">
                                            💬 Send Message
                                        </button>
                                    </form>

                                    <!-- Inquiry Status Display -->
                                    @if($existingInquiry)
                                        <!-- User has already inquired about THIS property -->
                                        @if($existingInquiry->status === 'pending')
                                            <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                                                <div class="flex items-center">
                                                    <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div class="flex-1">
                                                        <h4 class="font-semibold text-blue-900">📋 Booking Inquiry Pending</h4>
                                                        <p class="text-sm text-blue-700 mt-1">Your inquiry for this property is under review by the landlord.</p>
                                                        <p class="text-xs text-blue-600 mt-1">Submitted: {{ $existingInquiry->created_at->format('M j, Y \\a\\t g:i A') }}</p>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <a href="{{ route('messages.index') }}" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                                        💬 View Messages
                                                    </a>
                                                </div>
                                            </div>
                                            <!-- Disabled booking button for THIS property -->
                                            <button type="button" class="w-full bg-blue-100 text-blue-800 py-3 px-4 rounded-lg cursor-not-allowed font-semibold mb-2 border border-blue-300" disabled>
                                                <span class="font-weight: 600;">⏳ Booking Inquiry Pending</span>
                                            </button>
                                        @elseif($existingInquiry->status === 'approved')
                                            <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                                                <div class="flex items-center">
                                                    <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div class="flex-1">
                                                        <h4 class="font-semibold text-green-900">✅ Booking Inquiry Approved!</h4>
                                                        <p class="text-sm text-green-700 mt-1">Great news! The landlord has approved your inquiry.</p>
                                                        <p class="text-xs text-green-600 mt-1">Approved: {{ $existingInquiry->updated_at->format('M j, Y \\a\\t g:i A') }}</p>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <a href="{{ route('messages.index') }}" class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                                        💬 Continue Conversation
                                                    </a>
                                                </div>
                                            </div>
                                            <!-- Show approved status button -->
                                            <button type="button" class="w-full bg-green-100 text-green-800 py-3 px-4 rounded-lg cursor-default font-semibold mb-2 border border-green-300">
                                                <span class="font-weight: 600;">✅ Inquiry Approved - Contact Landlord</span>
                                            </button>
                                        @elseif($existingInquiry->status === 'rejected')
                                            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                                                <div class="flex items-center">
                                                    <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div class="flex-1">
                                                        <h4 class="font-semibold text-red-900">❌ Inquiry Not Approved</h4>
                                                        <p class="text-sm text-red-700 mt-1">Unfortunately, your inquiry was not approved by the landlord.</p>
                                                        <p class="text-xs text-red-600 mt-1">You can now inquire about other properties.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Allow new booking for this property after rejection -->
                                            @if($property->rooms->where('status', 'available')->count() > 0)
                                                <button type="button" onclick="openBookingModal()" class="w-full bg-orange-600 text-white py-3 px-4 rounded-lg hover:bg-orange-700 font-semibold mb-2 transition-colors shadow-lg border-none outline-none" style="background-color: #ea580c !important; color: white !important; font-weight: 600 !important; text-align: center !important; display: flex !important; align-items: center !important; justify-content: center !important; min-height: 48px;">
                                                    <span style="color: white !important; font-size: 16px; font-weight: 600;">📋 Book This Property</span>
                                                </button>
                                            @else
                                                <button type="button" class="w-full bg-gray-400 text-white py-3 px-4 rounded-lg cursor-not-allowed font-semibold mb-2 border-none outline-none" disabled style="background-color: #9ca3af !important; color: white !important; font-weight: 600 !important; text-align: center !important; display: flex !important; align-items: center !important; justify-content: center !important; min-height: 48px;">
                                                    <span style="color: white !important; font-size: 16px; font-weight: 600;">❌ No Rooms Available</span>
                                                </button>
                                            @endif
                                        @endif
                                    @elseif($pendingInquiry)
                                        <!-- User has pending inquiry for DIFFERENT property -->
                                        <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                                            <div class="flex items-center">
                                                <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-yellow-900">⏳ You have a pending booking inquiry</h4>
                                                    <p class="text-sm text-yellow-700 mt-1">Property: <strong>{{ $pendingInquiry->property->title }}</strong></p>
                                                    <p class="text-xs text-yellow-600 mt-1">Please wait for approval before submitting new inquiries.</p>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('messages.index') }}" class="inline-flex items-center px-3 py-1 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition-colors">
                                                    💬 Check Messages
                                                </a>
                                            </div>
                                        </div>
                                        <!-- Disabled booking button -->
                                        <button type="button" class="w-full bg-yellow-100 text-yellow-800 py-3 px-4 rounded-lg cursor-not-allowed font-semibold mb-2 border border-yellow-300" disabled>
                                            <span class="font-weight: 600;">⏳ Complete pending inquiry first</span>
                                        </button>
                                    @else
                                        <!-- No pending inquiries - show normal booking button -->
                                        @if($property->rooms->where('status', 'available')->count() > 0)
                                            <button type="button" onclick="openBookingModal()" class="w-full bg-orange-600 text-white py-3 px-4 rounded-lg hover:bg-orange-700 font-semibold mb-2 transition-colors shadow-lg border-none outline-none" style="background-color: #ea580c !important; color: white !important; font-weight: 600 !important; text-align: center !important; display: flex !important; align-items: center !important; justify-content: center !important; min-height: 48px;">
                                                <span style="color: white !important; font-size: 16px; font-weight: 600;">📋 Book This Property</span>
                                            </button>
                                        @else
                                            <button type="button" class="w-full bg-gray-400 text-white py-3 px-4 rounded-lg cursor-not-allowed font-semibold mb-2 border-none outline-none" disabled style="background-color: #9ca3af !important; color: white !important; font-weight: 600 !important; text-align: center !important; display: flex !important; align-items: center !important; justify-content: center !important; min-height: 48px;">
                                                <span style="color: white !important; font-size: 16px; font-weight: 600;">❌ No Rooms Available</span>
                                            </button>
                                        @endif
                                    @endif

                                    <!-- Visit Scheduling Status -->
                                    @if($existingVisit)
                                        @if($existingVisit->status === 'pending')
                                            <div class="mb-3 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div class="flex-1">
                                                        <h4 class="font-semibold text-blue-900">📅 Visit Request Pending</h4>
                                                        <p class="text-sm text-blue-700 mt-1">Your visit request is awaiting landlord confirmation.</p>
                                                        <p class="text-xs text-blue-600 mt-1">Date: {{ \Carbon\Carbon::parse($existingVisit->preferred_date)->format('M j, Y') }} at {{ \Carbon\Carbon::parse($existingVisit->preferred_time)->format('g:i A') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="w-full bg-blue-100 text-blue-800 py-3 rounded-lg cursor-not-allowed font-semibold mb-2 border border-blue-300" disabled>
                                                ⏳ Visit Request Pending
                                            </button>
                                        @elseif($existingVisit->status === 'confirmed')
                                            <div class="mb-3 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div class="flex-1">
                                                        <h4 class="font-semibold text-green-900">✅ Visit Confirmed!</h4>
                                                        <p class="text-sm text-green-700 mt-1">Your visit has been confirmed by the landlord.</p>
                                                        <p class="text-xs text-green-600 mt-1">Date: {{ \Carbon\Carbon::parse($existingVisit->preferred_date)->format('M j, Y') }} at {{ \Carbon\Carbon::parse($existingVisit->preferred_time)->format('g:i A') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="w-full bg-green-100 text-green-800 py-3 rounded-lg cursor-default font-semibold mb-2 border border-green-300">
                                                ✅ Visit Confirmed
                                            </button>
                                        @endif
                                    @else
                                        @if($property->visit_schedule_enabled ?? false)
                                            <button type="button" onclick="openScheduleModal()" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold transition-colors">
                                                📅 Schedule a Visit
                                            </button>
                                        @else
                                            <button type="button" class="w-full bg-gray-400 text-white py-3 rounded-lg cursor-not-allowed font-semibold" disabled>
                                                📅 Schedule Not Available
                                            </button>
                                        @endif
                                    @endif
                                @endif
                            @endauth

                            <!-- Enhanced Property Stats -->
                            <div class="border rounded-lg p-6 bg-gradient-to-br from-white to-gray-50 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.75 2.524z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="font-bold text-gray-800">Property Information</h3>
                                </div>
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
<div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50" style="padding-top: 80px; padding-bottom: 20px;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md m-4 overflow-hidden max-h-full flex flex-col">
        <div class="p-6 flex-1 overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Schedule a Visit</h3>
                <button onclick="closeScheduleModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form onsubmit="scheduleVisit(event)" class="space-y-4">
                <!-- Landlord's Visit Instructions (if available) -->
                @if($property->visit_instructions)
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-bold text-blue-800">🏠 Landlord's Visit Instructions</h4>
                                <p class="text-sm text-blue-700 mt-1">{{ $property->visit_instructions }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="visit_date" class="block text-gray-700 font-semibold mb-2">Preferred Date</label>
                    <input type="date" name="visit_date" id="visit_date"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           max="{{ date('Y-m-d', strtotime('+3 months')) }}"
                           required>
                </div>

                <div>
                    <label for="visit_time" class="block text-gray-700 font-semibold mb-2">Preferred Time</label>
                    <select name="visit_time" id="visit_time" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        <option value="">Select time</option>
                        <option value="08:00">8:00 AM</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <!-- Lunch break: 12:00 PM - 1:00 PM -->
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                        <option value="17:00">5:00 PM</option>
                        <option value="18:00">6:00 PM</option>
                        <option value="19:00">7:00 PM</option>
                        <option value="20:00">8:00 PM</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Available times: 8:00 AM - 11:00 AM, 2:00 PM - 8:00 PM (Lunch: 12:00-1:00 PM)</p>
                </div>

                @if($property->visit_duration)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold">⏰ Visit Duration:</span> {{ $property->visit_duration }} minutes
                        </p>
                    </div>
                @endif

                <div>
                    <label for="tenant_notes" class="block text-gray-700 font-semibold mb-2">Message/Notes (Optional)</label>
                    <textarea name="tenant_notes"
                        id="tenant_notes"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        rows="3"
                        placeholder="Any specific questions or requests about the visit..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Let the landlord know if you have any special requirements or questions</p>
                </div>

                <div class="flex gap-3 pt-4 pb-2 bg-white sticky bottom-0">
                    <button type="button"
                            onclick="closeScheduleModal()"
                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold border-none outline-none"
                            style="background-color: #e5e7eb !important; color: #374151 !important; font-weight: 600 !important; text-align: center !important; display: flex !important; align-items: center !important; justify-content: center !important; min-height: 48px;">
                        <span style="color: #374151 !important; font-size: 16px; font-weight: 600;">Cancel</span>
                    </button>
                    <button type="submit"
                            class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold border-none outline-none"
                            style="background-color: #2563eb !important; color: white !important; font-weight: 600 !important; text-align: center !important; display: flex !important; align-items: center !important; justify-content: center !important; min-height: 48px;">
                        <span style="color: white !important; font-size: 16px; font-weight: 600;">📅 Schedule Visit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Book Property Modal -->
<div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50" style="padding-top: 80px; padding-bottom: 20px;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md m-4 overflow-hidden max-h-full flex flex-col">
        <div class="p-6 flex-1 overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Book This Property</h3>
                <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form onsubmit="submitBooking(event)" class="space-y-4">
                @if($property->rooms->where('status', 'available')->count() > 1)
                <div>
                    <label for="room_id" class="block text-gray-700 font-semibold mb-2">Select Room</label>
                    <select name="room_id" id="room_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500" required>
                        <option value="">Choose a room</option>
                        @foreach($property->rooms->where('status', 'available') as $room)
                            <option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->type_name }} (Capacity: {{ $room->capacity }})</option>
                        @endforeach
                    </select>
                </div>
                @else
                    <input type="hidden" name="room_id" value="{{ $property->rooms->where('status', 'available')->first()->id ?? '' }}">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <strong>Room:</strong> {{ $property->rooms->where('status', 'available')->first()->room_number ?? 'N/A' }}
                            <br><strong>Type:</strong> {{ $property->rooms->where('status', 'available')->first()->type_name ?? 'N/A' }}
                            <br><strong>Capacity:</strong> {{ $property->rooms->where('status', 'available')->first()->capacity ?? 'N/A' }} person(s)
                        </p>
                    </div>
                @endif

                <div>
                    <label for="check_in" class="block text-gray-700 font-semibold mb-2">Check-in Date</label>
                    <input type="date"
                           name="check_in"
                           id="check_in"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                           min="{{ date('Y-m-d') }}"
                           required>
                </div>

                <div>
                    <label for="check_out" class="block text-gray-700 font-semibold mb-2">Check-out Date</label>
                    <input type="date"
                           name="check_out"
                           id="check_out"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           required>
                </div>

                <div>
                    <label for="notes" class="block text-gray-700 font-semibold mb-2">Special Requests (Optional)</label>
                    <textarea name="notes"
                              id="notes"
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 resize-none"
                              placeholder="Any special requirements or questions..."></textarea>
                </div>

                <!-- Pricing Info -->
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-medium">Monthly Rate:</span>
                        <span class="text-2xl font-bold text-orange-600">₱{{ number_format($property->price) }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        * Final amount will be calculated based on your stay duration
                    </p>
                </div>

                <!-- Terms -->
                <div class="flex items-start space-x-3">
                    <input type="checkbox" id="bookingTerms" class="mt-1" required>
                    <label for="bookingTerms" class="text-xs text-gray-600">
                        I agree to the booking terms and understand that this request will be reviewed by the landlord.
                    </label>
                </div>

                <div class="flex space-x-3 pt-4 pb-2 bg-white sticky bottom-0">
                    <button type="button"
                            onclick="closeBookingModal()"
                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold border-none outline-none"
                            style="background-color: #e5e7eb !important; color: #374151 !important; font-weight: 600 !important; text-align: center !important; display: flex !important; align-items: center !important; justify-content: center !important; min-height: 48px;">
                        <span style="color: #374151 !important; font-size: 16px; font-weight: 600;">Cancel</span>
                    </button>
                    <button type="submit"
                            class="flex-1 px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold border-none outline-none"
                            style="background-color: #ea580c !important; color: white !important; font-weight: 600 !important; text-align: center !important; display: flex !important; align-items: center !important; justify-content: center !important; min-height: 48px;">
                        <span style="color: white !important; font-size: 16px; font-weight: 600;">📋 Submit Booking</span>
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
                            <div class="font-semibold text-blue-700">📍 Use My Current Location</div>
                            <div class="text-sm text-blue-600">Get directions from where I am right now</div>
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
                                    <div class="font-semibold text-green-700">🏠 From My Home Address</div>
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
                            <div class="font-semibold text-orange-700">🔍 Search Any Address</div>
                            <div class="text-sm text-orange-600">Enter any starting point for directions</div>
                        </div>
                    </div>
                    <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Starting Location</label>
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
                            <div class="font-semibold text-purple-700">📌 Click on Map</div>
                            <div class="text-sm text-purple-600">Tap any spot on the map as your starting point</div>
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

<!-- Review Modal -->
@auth
    @if(auth()->user()->role === 'tenant')
        <div id="reviewModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Write a Review</h3>
                    <form id="reviewForm" onsubmit="submitReview(event)">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        <input type="hidden" name="review_id" id="review_id" value="">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rating *</label>
                            <div class="flex space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setRating({{ $i }})" class="star-btn focus:outline-none" data-rating="{{ $i }}">
                                        <svg class="w-8 h-8 transition-colors duration-200" style="color: #d1d5db;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="rating" required>
                        </div>

                        <div class="mb-4">
                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                                Comment (optional)
                            </label>
                            <textarea name="comment" id="comment" rows="4"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y"
                                      placeholder="Share your experience with this property..."></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" id="reviewSubmitBtn"
                                    class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition font-medium">
                                Submit Review
                            </button>
                            <button type="button" onclick="closeReviewModal()"
                                    class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition font-medium">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endauth

<!-- Room Selection Modal -->
<div id="roomSelectionModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Select a Room</h3>
                <button type="button" onclick="closeRoomSelectionModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <p class="text-gray-600 mb-4">Please select which room you're interested in:</p>

            <div id="roomOptionsContainer" class="space-y-3 max-h-60 overflow-y-auto">
                @if($property->rooms && $property->rooms->count() > 0)
                    @php $availableRooms = $property->rooms->where('status', 'available')->filter(fn($room) => $room->capacity > ($room->occupied_count ?? 0)); @endphp
                    @if($availableRooms->count() > 0)
                        @foreach($availableRooms as $room)
                            <div class="room-option border border-gray-300 rounded-lg p-4 hover:border-green-500 cursor-pointer transition-colors"
                                 onclick="selectRoom({{ $room->id }}, '{{ $room->room_number }}', '{{ $room->room_type }}', {{ $room->capacity }}, {{ $room->occupied_count ?? 0 }}, '{{ $room->description ?? '' }}', {{ $room->price ?? $property->price }})">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $room->room_number }}</h4>
                                        <p class="text-sm text-blue-600 font-medium">{{ ucfirst(str_replace('_', ' ', $room->room_type)) }}</p>
                                        @if($room->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $room->description }}</p>
                                        @endif
                                        <p class="text-sm text-orange-600 font-semibold mt-1">₱{{ number_format($room->price ?? $property->price) }}/month</p>
                                    </div>
                                    <div class="text-right ml-4">
                                        <div class="text-sm font-medium text-green-600">
                                            {{ $room->capacity - ($room->occupied_count ?? 0) }} spaces available
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Capacity: {{ $room->capacity }}
                                        </div>
                                        @if($room->size_sqm)
                                            <div class="text-xs text-gray-500">
                                                {{ $room->size_sqm }} sqm
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <div class="mb-2">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">All Rooms Occupied</h3>
                            <p class="text-sm">All {{ $property->rooms->count() }} rooms are currently full.</p>
                            <p class="text-xs text-gray-400 mt-1">You can still send a general inquiry to be notified when rooms become available.</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8 text-gray-500">
                        <div class="mb-2">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">No Available Rooms</h3>
                        <p class="text-sm">All rooms in this property are currently occupied.</p>
                        <p class="text-xs text-gray-400 mt-1">Please check back later or contact the landlord directly.</p>
                    </div>
                @endif
            </div>

            <div class="flex justify-between items-center mt-6">
                <button type="button" onclick="skipRoomSelection()" class="px-3 py-2 text-sm text-blue-600 hover:text-blue-800 underline">
                    Skip - General Property Inquiry
                </button>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeRoomSelectionModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="button" onclick="proceedWithInquiry()" id="proceedBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700" disabled>
                        Proceed with Selected Room
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map scripts moved to first script section -->

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script>
    // Additional map variables (propertyLocation and psuLocation already defined above)

    function initMap() {
        try {
            map = L.map('propertyMap').setView(propertyLocation, 15);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Property marker (BLUE)
            L.marker(propertyLocation, {
                icon: L.icon({
                    iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(map).bindPopup('<b>{{ $property->title }}</b>').openPopup();

            // PSU Campus marker (RED to distinguish from property)
            L.marker(psuLocation, {
                icon: L.icon({
                    iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(map).bindPopup('<strong style="color: #dc2626;">🏫 PSU Main Campus</strong><br><small style="color: #6b7280;">Universidad Pangasinan State</small>');

        } catch (error) {
            console.error('Map error:', error);
            document.getElementById('propertyMap').innerHTML = '<div class="p-4 text-center text-red-600">Map failed to load</div>';
        }
    }

    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            if (typeof L !== 'undefined') initMap();
        }, 1000);
    });
</script>
@endpush