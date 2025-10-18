<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * LandlordController
 *
 * Manages landlord document verification including approval and rejection
 * of property ownership documents submitted during registration.
 */
class LandlordController extends Controller
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
     * Display landlords with document verification status
     * Sorted by pending status first, then by registration date
     */
    public function verify()
    {
        $this->checkAdmin();

        // Retrieve landlords ordered by verification status (pending first)
        $landlords = User::where('role', 'landlord')
            ->orderByRaw("CASE
                WHEN document_verification_status = 'pending' THEN 1
                WHEN document_verification_status = 'approved' THEN 2
                WHEN document_verification_status = 'rejected' THEN 3
                ELSE 4
            END")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.landlords.verify', compact('landlords'));
    }

    /**
     * Approve landlord's property documents
     */
    public function approveDocuments(User $landlord)
    {
        $this->checkAdmin();

        try {
            if ($landlord->role !== 'landlord') {
                return redirect()->back()->with('error', 'User is not a landlord');
            }

            DB::beginTransaction();

            $landlord->document_verification_status = 'approved';
            $landlord->save();

            DB::commit();

            Log::info('Landlord documents approved', [
                'landlord_id' => $landlord->id,
                'landlord_name' => $landlord->name,
                'admin_id' => auth()->id()
            ]);

            return redirect()->back()->with('success', "Landlord {$landlord->name}'s property documents have been approved!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Landlord document approval error', [
                'error' => $e->getMessage(),
                'landlord_id' => $landlord->id ?? 'unknown'
            ]);
            return redirect()->back()->with('error', 'Failed to approve documents: ' . $e->getMessage());
        }
    }

    /**
     * Reject landlord's property documents
     */
    public function rejectDocuments(User $landlord)
    {
        $this->checkAdmin();

        try {
            if ($landlord->role !== 'landlord') {
                return redirect()->back()->with('error', 'User is not a landlord');
            }

            DB::beginTransaction();

            $landlord->document_verification_status = 'rejected';
            $landlord->save();

            DB::commit();

            Log::info('Landlord documents rejected', [
                'landlord_id' => $landlord->id,
                'landlord_name' => $landlord->name,
                'admin_id' => auth()->id()
            ]);

            return redirect()->back()->with('success', "Landlord {$landlord->name}'s property documents have been rejected.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Landlord document rejection error', [
                'error' => $e->getMessage(),
                'landlord_id' => $landlord->id ?? 'unknown'
            ]);
            return redirect()->back()->with('error', 'Failed to reject documents: ' . $e->getMessage());
        }
    }
}
