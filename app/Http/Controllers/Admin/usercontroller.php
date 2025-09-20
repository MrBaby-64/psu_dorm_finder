<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
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
}