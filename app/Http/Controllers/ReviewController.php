<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Property;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $property = Property::findOrFail($request->property_id);

        // Check if user already reviewed
        $existing = Review::where('property_id', $property->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            return redirect()->back()->withErrors(['error' => 'You have already reviewed this property.']);
        }

        $review = Review::create([
            'property_id' => $property->id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Update property rating cache
        $property->updateRatingCache();

        return redirect()->back()->with('success', 'Review posted successfully!');
    }

    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $review->property->updateRatingCache();

        return redirect()->back()->with('success', 'Review updated!');
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        $property = $review->property;
        $review->delete();

        $property->updateRatingCache();

        return redirect()->back()->with('success', 'Review deleted.');
    }
}