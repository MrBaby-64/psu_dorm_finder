<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardControllerSimplified extends Controller
{
    public function index()
    {
        try {
            // Get basic stats using simple queries
            $stats = [
                'total_users' => $this->safeCount('users'),
                'total_properties' => $this->safeCount('properties'),
                'pending_properties' => $this->safeCount('properties', ['approval_status' => 'pending']),
                'approved_properties' => $this->safeCount('properties', ['approval_status' => 'approved']),
                'total_landlords' => $this->safeCount('users', ['role' => 'landlord']),
                'total_tenants' => $this->safeCount('users', ['role' => 'tenant'])
            ];

            // Get recent properties (simplified)
            $recentProperties = $this->getRecentProperties();

            return view('admin.dashboard-simple', compact('stats', 'recentProperties'));

        } catch (\Exception $e) {
            Log::error('Admin dashboard error: ' . $e->getMessage());

            // Return minimal dashboard on error
            return view('admin.dashboard-error', [
                'error' => 'Unable to load dashboard statistics.',
                'details' => config('app.debug') ? $e->getMessage() : null
            ]);
        }
    }

    private function safeCount($table, $where = [])
    {
        try {
            $query = DB::table($table);

            foreach ($where as $column => $value) {
                $query->where($column, $value);
            }

            return $query->count();
        } catch (\Exception $e) {
            Log::warning("Failed to count {$table}: " . $e->getMessage());
            return 0;
        }
    }

    private function getRecentProperties()
    {
        try {
            return DB::table('properties')
                ->select([
                    'id',
                    'title',
                    'approval_status',
                    'price',
                    'created_at'
                ])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            Log::warning("Failed to get recent properties: " . $e->getMessage());
            return collect([]);
        }
    }
}