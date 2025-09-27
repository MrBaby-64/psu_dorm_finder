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
                    // Delete old profile picture if exists
                    if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                        Storage::disk('public')->delete($user->profile_picture);
                    }

                    // Store new profile picture
                    $file = $request->file('profile_picture');
                    $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('profile_pictures', $filename, 'public');
                    $validated['profile_picture'] = $path;
                }

                // Handle profile picture removal
                if ($request->has('remove_profile_picture') && $user->profile_picture) {
                    if (Storage::disk('public')->exists($user->profile_picture)) {
                        Storage::disk('public')->delete($user->profile_picture);
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