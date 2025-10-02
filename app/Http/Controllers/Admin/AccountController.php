<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    public function index()
    {
        return view('admin.account.index');
    }

    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = auth()->user();

        // Delete old profile picture if exists
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new profile picture
        $path = $request->file('profile_picture')->store('profile-pictures', 'public');

        // Update user profile picture
        $user->profile_picture = $path;
        $user->save();

        return redirect()->back()->with('success', 'Profile picture updated successfully!');
    }
}