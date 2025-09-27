<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Property;
use App\Models\Notification;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'tenant') {
                abort(403, 'Only tenants can post reviews.');
            }
            return $next($request);
        });
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'property_id' => 'required|exists:properties,id',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:500',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $property = Property::findOrFail($request->property_id);

        // Check if user already reviewed
        $existing = Review::where('property_id', $property->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this property.'
                ], 400);
            }
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

        // Create notification for landlord about new review
        Notification::create([
            'user_id' => $property->user_id, // Landlord
            'type' => Notification::TYPE_REVIEW_RECEIVED,
            'title' => 'New Review Received',
            'message' => auth()->user()->name . ' left a ' . $request->rating . '-star review for your property "' . $property->title . '".',
            'data' => [
                'review_id' => $review->id,
                'property_id' => $property->id,
                'tenant_name' => auth()->user()->name,
                'tenant_id' => auth()->id(),
                'rating' => $request->rating,
                'comment_preview' => $request->comment ? substr($request->comment, 0, 100) . (strlen($request->comment) > 100 ? '...' : '') : null
            ],
            'action_url' => route('properties.show', $property->slug)
        ]);

        // Create notification for tenant (confirmation)
        Notification::create([
            'user_id' => auth()->id(), // Tenant
            'type' => Notification::TYPE_REVIEW_RECEIVED,
            'title' => 'Review Posted Successfully',
            'message' => 'Your ' . $request->rating . '-star review for "' . $property->title . '" has been posted successfully.',
            'data' => [
                'review_id' => $review->id,
                'property_id' => $property->id,
                'landlord_name' => $property->user->name,
                'landlord_id' => $property->user_id,
                'rating' => $request->rating
            ],
            'action_url' => route('properties.show', $property->slug)
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Review posted successfully!',
                'review_id' => $review->id
            ]);
        }

        return redirect()->back()->with('success', 'Review posted successfully!');
    }

    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        try {
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:500',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $review->property->updateRatingCache();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully!',
                'data' => [
                    'review_id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment
                ]
            ]);
        }

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