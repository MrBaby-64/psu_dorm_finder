<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private function checkAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can access this area');
        }
    }

    public function index()
    {
        $this->checkAdmin();

        $users = User::latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $this->checkAdmin();

        $request->validate([
            'role' => 'required|in:admin,landlord,tenant'
        ]);

        $oldRole = $user->role;
        $user->update(['role' => $request->role]);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'change_user_role',
            'subject_type' => 'App\Models\User',
            'subject_id' => $user->id,
            'meta_json' => json_encode([
                'user_email' => $user->email,
                'old_role' => $oldRole,
                'new_role' => $request->role
            ])
        ]);

        return redirect()->back()->with('success', 'User role updated successfully!');
    }

    public function verify(User $user)
    {
        $this->checkAdmin();

        $user->update(['is_verified' => !$user->is_verified]);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $user->is_verified ? 'verify_user' : 'unverify_user',
            'subject_type' => 'App\Models\User',
            'subject_id' => $user->id,
            'meta_json' => json_encode(['user_email' => $user->email])
        ]);

        return redirect()->back()->with('success', 'User verification updated!');
    }

    public function show(User $user)
    {
        $this->checkAdmin();

        try {
            // Load user with related data for comprehensive view
            $user->load(['properties', 'bookings.property', 'reviews.property', 'inquiries.property']);

            // Get user statistics safely with null checks
            $stats = [
                'total_properties' => $user->properties ? $user->properties->count() : 0,
                'active_properties' => $user->properties ? $user->properties->where('status', 'approved')->count() : 0,
                'total_bookings' => $user->bookings ? $user->bookings->count() : 0,
                'total_reviews' => $user->reviews ? $user->reviews->count() : 0,
                'total_inquiries' => $user->inquiries ? $user->inquiries->count() : 0,
                'unread_notifications' => 0, // Simplified for now
                'account_age_days' => $user->created_at ? $user->created_at->diffInDays(now()) : 0,
            ];

            // Log the view action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'view_user_details',
                'subject_type' => 'App\Models\User',
                'subject_id' => $user->id,
                'meta_json' => json_encode(['viewed_user_email' => $user->email])
            ]);

            // Render the view
            $html = view('admin.users.show', compact('user', 'stats'))->render();

            return response()->json([
                'success' => true,
                'user' => $user,
                'stats' => $stats,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading user details: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load user details: ' . $e->getMessage()
            ], 500);
        }
    }
}