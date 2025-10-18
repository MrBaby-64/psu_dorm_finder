<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;

/**
 * AccountController
 *
 * Handles admin account management operations including
 * profile viewing and profile picture upload/update.
 */
class AccountController extends Controller
{
    /**
     * Display the admin account settings page
     */
    public function index()
    {
        return view('admin.account.index');
    }

    /**
     * Upload or update the admin's profile picture
     * Supports both Cloudinary (production) and local storage (development)
     */
    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = auth()->user();
        $useCloudinary = !empty(config('cloudinary.cloud_name'));

        try {
            if ($useCloudinary) {
                // Initialize Cloudinary client for cloud storage
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => config('cloudinary.cloud_name'),
                        'api_key' => config('cloudinary.api_key'),
                        'api_secret' => config('cloudinary.api_secret'),
                    ]
                ]);

                // Clean up previous profile picture from Cloudinary if it exists
                if ($user->profile_picture && str_starts_with($user->profile_picture, 'http')) {
                    // Note: Cloudinary public_id extraction would be needed for deletion
                    // Currently retaining old images to prevent data loss
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
                // Fallback to local file storage for development environment
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }

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