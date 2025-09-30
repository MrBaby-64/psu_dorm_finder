<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyControllerDebug extends Controller
{
    public function testConnection()
    {
        $results = [];

        // Test 1: Check database connection
        try {
            DB::connection()->getPdo();
            $results['database'] = 'Connected';
        } catch (\Exception $e) {
            $results['database'] = 'Failed: ' . $e->getMessage();
        }

        // Test 2: Check if properties table exists
        try {
            $count = DB::table('properties')->count();
            $results['properties_table'] = 'Exists, count: ' . $count;
        } catch (\Exception $e) {
            $results['properties_table'] = 'Failed: ' . $e->getMessage();
        }

        // Test 3: Check pending properties
        try {
            $pending = DB::table('properties')->where('approval_status', 'pending')->count();
            $results['pending_properties'] = $pending;
        } catch (\Exception $e) {
            $results['pending_properties'] = 'Failed: ' . $e->getMessage();
        }

        // Test 4: Check users table
        try {
            $count = DB::table('users')->count();
            $results['users_table'] = 'Exists, count: ' . $count;
        } catch (\Exception $e) {
            $results['users_table'] = 'Failed: ' . $e->getMessage();
        }

        // Test 5: Check property_deletion_requests table
        try {
            $count = DB::table('property_deletion_requests')->count();
            $results['deletion_requests_table'] = 'Exists, count: ' . $count;
        } catch (\Exception $e) {
            $results['deletion_requests_table'] = 'Failed: ' . $e->getMessage();
        }

        // Test 6: Check auth
        try {
            $results['auth_check'] = auth()->check() ? 'Logged in' : 'Not logged in';
            if (auth()->check()) {
                $results['user_role'] = auth()->user()->role;
                $results['user_id'] = auth()->id();
            }
        } catch (\Exception $e) {
            $results['auth_check'] = 'Failed: ' . $e->getMessage();
        }

        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
    }
}