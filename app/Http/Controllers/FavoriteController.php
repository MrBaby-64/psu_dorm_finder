<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = auth()->user()
            ->favorites()
            ->with('property.coverImage')
            ->latest()
            ->paginate(12);

        return view('favorites.index', compact('favorites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        Favorite::firstOrCreate([
            'user_id' => auth()->id(),
            'property_id' => $request->property_id,
        ]);

        return redirect()->back()->with('success', 'Added to favorites!');
    }

    public function destroy(Property $property)
    {
        Favorite::where('user_id', auth()->id())
            ->where('property_id', $property->id)
            ->delete();

        return redirect()->back()->with('success', 'Removed from favorites.');
    }
}