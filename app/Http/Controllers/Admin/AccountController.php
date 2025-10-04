<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;

/**
 * Admin Account Controller
 * Manages admin account settings and profile
 */
class AccountController extends Controller
{
    // Show admin account page
    public function index()
    {
        return view('admin.account.index');
    }

    // Upload or update admin profile picture
    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = auth()->user();
        $useCloudinary = !empty(config('cloudinary.cloud_name'));

        try {
            if ($useCloudinary) {
                // Upload to Cloudinary
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => config('cloudinary.cloud_name'),
                        'api_key' => config('cloudinary.api_key'),
                        'api_secret' => config('cloudinary.api_secret'),
                    ]
                ]);

                // Delete old Cloudinary image if exists
                if ($user->profile_picture && str_starts_with($user->profile_picture, 'http')) {
                    // Extract public_id from URL if needed for deletion
                    // For now, we'll just keep the old one
                }

                $uploadResult = $cloudinary->uploadApi()->upload(
                    $request->file('profile_picture')->getRealPath(),
                    [
                        'folder' => 'psu-dorm-finder/profile-pictures',
                        'public_id' => 'user_' . $user->id . '_' . time(),
                        'resource_type' => 'image',
                        'transformation' => [
                            'width' => 400,
                            'height' => 400,
                            'crop' => 'fill',
                            'gravity' => 'face',
                            'quality' => 'auto'
                        ]
                    ]
                );

                $user->profile_picture = $uploadResult['secure_url'];
            } else {
                // Use local storage
                // Delete old profile picture
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }

                // Save new profile picture
                $path = $request->file('profile_picture')->store('profile-pictures', 'public');
                $user->profile_picture = $path;
            }

            $user->save();
            return redirect()->back()->with('success', 'Profile picture updated successfully!');

        } catch (\Exception $e) {
            Log::error('Profile picture upload failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['profile_picture' => 'Failed to upload profile picture. Please try again.']);
        }
    }
}