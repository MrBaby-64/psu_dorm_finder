<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * TenantController
 *
 * Manages tenant ID verification including approval and rejection
 * of government-issued IDs submitted during registration.
 */
class TenantController extends Controller
{
    /**
     * Verify user has admin role before proceeding
     */
    private function checkAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can access this area');
        }
    }

    /**
     * Approve tenant's government ID and activate their account
     */
    public function approveId(User $tenant)
    {
        $this->checkAdmin();

        try {
            if ($tenant->role !== 'tenant') {
                return redirect()->back()->with('error', 'User is not a tenant');
            }

            DB::beginTransaction();

            $tenant->tenant_id_verification_status = 'approved';
            $tenant->is_verified = true;
            $tenant->save();

            DB::commit();

            Log::info('Tenant ID approved and account verified', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'admin_id' => auth()->id()
            ]);

            return redirect()->back()->with('success', "Tenant {$tenant->name}'s ID has been approved and account verified!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tenant ID approval error', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id ?? 'unknown'
            ]);
            return redirect()->back()->with('error', 'Failed to approve ID: ' . $e->getMessage());
        }
    }

    /**
     * Reject tenant's ID
     */
    public function rejectId(User $tenant)
    {
        $this->checkAdmin();

        try {
            if ($tenant->role !== 'tenant') {
                return redirect()->back()->with('error', 'User is not a tenant');
            }

            DB::beginTransaction();

            $tenant->tenant_id_verification_status = 'rejected';
            $tenant->save();

            DB::commit();

            Log::info('Tenant ID rejected', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'admin_id' => auth()->id()
            ]);

            return redirect()->back()->with('success', "Tenant {$tenant->name}'s ID has been rejected.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tenant ID rejection error', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id ?? 'unknown'
            ]);
            return redirect()->back()->with('error', 'Failed to reject ID: ' . $e->getMessage());
        }
    }
}
