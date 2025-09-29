<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountControllerSimplified extends Controller
{
    public function index()
    {
        try {
            // Get user data safely
            $userId = Auth::id();

            if (!$userId) {
                return redirect()->route('login')->with('error', 'Please log in to access your account.');
            }

            // Get user data directly from database
            $user = DB::table('users')->where('id', $userId)->first();

            if (!$user) {
                return redirect()->route('login')->with('error', 'User account not found.');
            }

            return view('admin.account.index-simple', compact('user'));

        } catch (\Exception $e) {
            Log::error('Admin account page error: ' . $e->getMessage());

            return response()->view('admin.account.error', [
                'error' => 'Unable to load account information.',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}