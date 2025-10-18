<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Cloudinary\Cloudinary;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        Log::info('Profile update attempt', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'validated_keys' => array_keys($validated),
            'has_profile_picture_file' => $request->hasFile('profile_picture'),
            'remove_profile_picture' => $request->has('remove_profile_picture')
        ]);

        try {
            DB::transaction(function () use ($user, $validated, $request) {
                // Handle profile picture upload
                if ($request->hasFile('profile_picture')) {
                    // Initialize Cloudinary
                    $cloudinary = new Cloudinary([
                        'cloud' => [
                            'cloud_name' => config('cloudinary.cloud_name'),
                            'api_key' => config('cloudinary.api_key'),
                            'api_secret' => config('cloudinary.api_secret'),
                        ]
                    ]);

                    // Delete old profile picture from Cloudinary if exists and it's a Cloudinary URL
                    if ($user->profile_picture) {
                        if (str_starts_with($user->profile_picture, 'http://') || str_starts_with($user->profile_picture, 'https://')) {
                            // Extract public_id from Cloudinary URL and delete
                            try {
                                // Extract public_id: typical URL format is https://res.cloudinary.com/{cloud_name}/image/upload/{version}/{public_id}.{format}
                                $urlParts = parse_url($user->profile_picture);
                                $pathParts = explode('/', $urlParts['path']);
                                // Find 'upload' index and get everything after it (except last which is filename.ext)
                                $uploadIndex = array_search('upload', $pathParts);
                                if ($uploadIndex !== false) {
                                    $publicIdParts = array_slice($pathParts, $uploadIndex + 2); // Skip 'upload' and version
                                    $publicIdWithExt = implode('/', $publicIdParts);
                                    $publicId = pathinfo($publicIdWithExt, PATHINFO_DIRNAME) . '/' . pathinfo($publicIdWithExt, PATHINFO_FILENAME);
                                    $cloudinary->uploadApi()->destroy($publicId);
                                }
                            } catch (\Exception $e) {
                                Log::warning('Failed to delete old Cloudinary image: ' . $e->getMessage());
                            }
                        } else {
                            // Delete from local storage (legacy)
                            if (Storage::disk('public')->exists($user->profile_picture)) {
                                Storage::disk('public')->delete($user->profile_picture);
                            }
                        }
                    }

                    // Upload new profile picture to Cloudinary
                    $file = $request->file('profile_picture');
                    try {
                        $uploadResult = $cloudinary->uploadApi()->upload(
                            $file->getRealPath(),
                            [
                                'folder' => config('cloudinary.folders.profile_pictures'),
                                'public_id' => 'user_' . $user->id . '_' . time(),
                                'resource_type' => 'image',
                                'transformation' => [
                                    'width' => 400,
                                    'height' => 400,
                                    'crop' => 'fill',
                                    'gravity' => 'face',
                                    'quality' => 'auto:good', // Automatic quality optimization
                                    'fetch_format' => 'auto' // Automatic format selection (WebP when supported)
                                ]
                            ]
                        );

                        // Store Cloudinary URL
                        $validated['profile_picture'] = $uploadResult['secure_url'];
                    } catch (\Exception $e) {
                        Log::error('Cloudinary profile picture upload failed: ' . $e->getMessage());
                        throw new \Exception('Failed to upload profile picture. Please try again.');
                    }
                }

                // Handle profile picture removal
                if ($request->has('remove_profile_picture') && $user->profile_picture) {
                    // Delete from Cloudinary if it's a Cloudinary URL
                    if (str_starts_with($user->profile_picture, 'http://') || str_starts_with($user->profile_picture, 'https://')) {
                        try {
                            $cloudinary = new Cloudinary([
                                'cloud' => [
                                    'cloud_name' => config('cloudinary.cloud_name'),
                                    'api_key' => config('cloudinary.api_key'),
                                    'api_secret' => config('cloudinary.api_secret'),
                                ]
                            ]);

                            $urlParts = parse_url($user->profile_picture);
                            $pathParts = explode('/', $urlParts['path']);
                            $uploadIndex = array_search('upload', $pathParts);
                            if ($uploadIndex !== false) {
                                $publicIdParts = array_slice($pathParts, $uploadIndex + 2);
                                $publicIdWithExt = implode('/', $publicIdParts);
                                $publicId = pathinfo($publicIdWithExt, PATHINFO_DIRNAME) . '/' . pathinfo($publicIdWithExt, PATHINFO_FILENAME);
                                $cloudinary->uploadApi()->destroy($publicId);
                            }
                        } catch (\Exception $e) {
                            Log::warning('Failed to delete Cloudinary image on removal: ' . $e->getMessage());
                        }
                    } else {
                        // Delete from local storage (legacy)
                        if (Storage::disk('public')->exists($user->profile_picture)) {
                            Storage::disk('public')->delete($user->profile_picture);
                        }
                    }
                    $validated['profile_picture'] = null;
                }

                $user->fill($validated);

                if ($user->isDirty('email')) {
                    $user->email_verified_at = null;
                }

                $saved = $user->save();

                if (!$saved) {
                    throw new \Exception('Profile update failed to save');
                }
            });

            Log::info('Profile update successful', [
                'user_id' => $user->id,
                'updated_fields' => array_keys($validated)
            ]);

            return Redirect::route('profile.edit')->with('status', 'profile-updated');

        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'first_error' => $e->getMessage(),
                'validated_keys' => array_keys($validated)
            ]);

            return back()->withInput()->withErrors(['general' => 'Profile update failed. Please try again.']);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}