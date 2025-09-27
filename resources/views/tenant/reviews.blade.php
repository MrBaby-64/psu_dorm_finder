@extends('layouts.account')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">My Reviews</h1>
            <p class="text-gray-600">Reviews you've posted for properties</p>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Property</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Property name..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <select name="rating" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        Filter
                    </button>
                </div>
            </form>

            @if(request()->hasAny(['search', 'rating']))
                <div class="mt-4">
                    <a href="{{ route('tenant.reviews') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                        Clear Filters
                    </a>
                </div>
            @endif
        </div>

        <!-- Reviews List -->
        @if($reviews->count() > 0)
            <div class="space-y-6">
                @foreach($reviews as $review)
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <a href="{{ route('properties.show', $review->property->slug) }}" class="hover:text-green-600">
                                    {{ $review->property->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-600">{{ $review->property->location_text }}</p>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                @endfor
                                <span class="ml-2 text-sm text-gray-600">{{ $review->rating }}/5</span>
                            </div>
                            <p class="text-xs text-gray-500">{{ $review->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    @if($review->comment)
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Your Review:</h4>
                            <p class="text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $review->comment }}</p>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-4 border-t">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('properties.show', $review->property->slug) }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                                View Property
                            </a>
                        </div>
                        <div class="flex items-center space-x-2">
                            <!-- Edit Review Button -->
                            <button onclick="openEditModal({{ $review->id }}, {{ $review->rating }}, '{{ addslashes($review->comment) }}')" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                Edit
                            </button>
                            <!-- Delete Review Button -->
                            <form method="POST" action="{{ route('reviews.destroy', $review) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this review?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $reviews->withQueryString()->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>

                @if(request()->hasAny(['search', 'rating']))
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews found</h3>
                    <p class="text-gray-600 mb-4">Try adjusting your search criteria</p>
                    <a href="{{ route('tenant.reviews') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Clear Filters
                    </a>
                @else
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews yet</h3>
                    <p class="text-gray-600 mb-4">You haven't reviewed any properties yet. Start browsing to find properties to review!</p>
                    <a href="{{ route('properties.browse') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Browse Properties
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Edit Review Modal -->
<div id="editReviewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Edit Review</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="editReviewForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" onclick="setEditRating({{ $i }})" class="edit-star text-gray-300 hover:text-yellow-400 transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="editRating" required>
                </div>

                <div class="mb-6">
                    <label for="editComment" class="block text-sm font-medium text-gray-700 mb-2">Comment (Optional)</label>
                    <textarea
                        name="comment"
                        id="editComment"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Share your experience..."
                    ></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Update Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentEditRating = 0;

function openEditModal(reviewId, rating, comment) {
    setEditRating(rating);
    document.getElementById('editComment').value = comment;
    document.getElementById('editReviewForm').action = `/reviews/${reviewId}`;
    document.getElementById('editReviewModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editReviewModal').classList.add('hidden');
    currentEditRating = 0;
    updateEditStarDisplay();
}

function setEditRating(rating) {
    currentEditRating = rating;
    document.getElementById('editRating').value = rating;
    updateEditStarDisplay();
}

function updateEditStarDisplay() {
    const stars = document.querySelectorAll('.edit-star');
    stars.forEach((star, index) => {
        if (index < currentEditRating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}

// Close modal when clicking outside
document.getElementById('editReviewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endsection