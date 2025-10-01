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

        try {
            // PostgreSQL-compatible query with explicit column selection
            $users = User::select('users.*')
                ->orderBy('users.created_at', 'desc')
                ->paginate(20);

            return view('admin.users.index', compact('users'));

        } catch (\Exception $e) {
            \Log::error('Admin users error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.dashboard')
                ->with('error', 'Unable to load users.');
        }
    }

    public function updateRole(Request $request, $id)
    {
        $this->checkAdmin();

        $request->validate([
            'role' => 'required|in:admin,landlord,tenant'
        ]);

        try {
            $user = User::findOrFail($id);

            $oldRole = $user->role;
            $userEmail = $user->email;

            // Update role
            $user->role = $request->role;
            $user->save();

            // Log the action
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'change_user_role',
                    'subject_type' => 'App\Models\User',
                    'subject_id' => $user->id,
                    'meta_json' => json_encode([
                        'user_email' => $userEmail,
                        'old_role' => $oldRole,
                        'new_role' => $request->role
                    ])
                ]);
            } catch (\Exception $e) {
                \Log::warning('Audit log failed: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'User role updated successfully!');

        } catch (\Exception $e) {
            \Log::error('User role update error', [
                'error' => $e->getMessage(),
                'user_id' => $id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to update user role: ' . $e->getMessage());
        }
    }

    public function verify($id)
    {
        $this->checkAdmin();

        try {
            $user = User::findOrFail($id);

            $userEmail = $user->email;

            // Toggle verification
            $user->is_verified = !$user->is_verified;
            $user->save();

            $action = $user->is_verified ? 'verify_user' : 'unverify_user';

            // Log the action
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => $action,
                    'subject_type' => 'App\Models\User',
                    'subject_id' => $user->id,
                    'meta_json' => json_encode(['user_email' => $userEmail])
                ]);
            } catch (\Exception $e) {
                \Log::warning('Audit log failed: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'User verification updated!');

        } catch (\Exception $e) {
            \Log::error('User verification error', [
                'error' => $e->getMessage(),
                'user_id' => $id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to update user verification: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->checkAdmin();

        try {
            $user = User::findOrFail($id);

            // Get user statistics using direct queries for better PostgreSQL compatibility
            $stats = [
                'total_properties' => 0,
                'active_properties' => 0,
                'total_bookings' => 0,
                'total_reviews' => 0,
                'total_inquiries' => 0,
                'unread_notifications' => 0,
                'account_age_days' => 0,
            ];

            try {
                $stats['total_properties'] = \DB::table('properties')->where('user_id', $user->id)->count();
                $stats['active_properties'] = \DB::table('properties')
                    ->where('user_id', $user->id)
                    ->where('approval_status', 'approved')
                    ->count();
            } catch (\Exception $e) {
                \Log::warning('Could not load property stats: ' . $e->getMessage());
            }

            try {
                $stats['total_bookings'] = \DB::table('bookings')->where('user_id', $user->id)->count();
            } catch (\Exception $e) {
                \Log::warning('Could not load booking stats: ' . $e->getMessage());
            }

            try {
                $stats['total_reviews'] = \DB::table('reviews')->where('user_id', $user->id)->count();
            } catch (\Exception $e) {
                \Log::warning('Could not load review stats: ' . $e->getMessage());
            }

            try {
                $stats['total_inquiries'] = \DB::table('inquiries')->where('user_id', $user->id)->count();
            } catch (\Exception $e) {
                \Log::warning('Could not load inquiry stats: ' . $e->getMessage());
            }

            try {
                $stats['account_age_days'] = $user->created_at ? $user->created_at->diffInDays(now()) : 0;
            } catch (\Exception $e) {
                \Log::warning('Could not calculate account age: ' . $e->getMessage());
            }

            // Load relationships separately to avoid cascade issues
            try {
                $user->load(['properties' => function($query) {
                    $query->select('id', 'user_id', 'title', 'price', 'approval_status', 'created_at');
                }]);
            } catch (\Exception $e) {
                \Log::warning('Could not load properties: ' . $e->getMessage());
            }

            try {
                $user->load(['bookings' => function($query) {
                    $query->select('id', 'user_id', 'property_id', 'status', 'created_at');
                }]);
            } catch (\Exception $e) {
                \Log::warning('Could not load bookings: ' . $e->getMessage());
            }

            try {
                $user->load(['reviews' => function($query) {
                    $query->select('id', 'user_id', 'property_id', 'rating', 'comment', 'created_at');
                }]);
            } catch (\Exception $e) {
                \Log::warning('Could not load reviews: ' . $e->getMessage());
            }

            try {
                $user->load(['inquiries' => function($query) {
                    $query->select('id', 'user_id', 'property_id', 'message', 'created_at');
                }]);
            } catch (\Exception $e) {
                \Log::warning('Could not load inquiries: ' . $e->getMessage());
            }

            // Log the view action
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'view_user_details',
                    'subject_type' => 'App\Models\User',
                    'subject_id' => $user->id,
                    'meta_json' => json_encode(['viewed_user_email' => $user->email])
                ]);
            } catch (\Exception $e) {
                \Log::warning('Audit log failed: ' . $e->getMessage());
            }

            // Render the view
            $html = view('admin.users.show', compact('user', 'stats'))->render();

            return response()->json([
                'success' => true,
                'user' => $user,
                'stats' => $stats,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading user details', [
                'user_id' => $id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load user details: ' . $e->getMessage()
            ], 500);
        }
    }
}