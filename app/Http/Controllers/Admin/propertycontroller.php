<?php
// app/Http/Controllers/Admin/PropertyController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyDeletionRequest;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    private function checkAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can access this area');
        }
    }

    public function pending()
    {
        $this->checkAdmin();

        try {
            // Use Eloquent like localhost
            $properties = Property::with('landlord:id,name,email')
                ->where('approval_status', 'pending')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('admin.properties.pending', ['properties' => $properties]);

        } catch (\Exception $e) {
            Log::error('Admin pending properties error: ' . $e->getMessage());

            // Fallback
            $properties = Property::where('id', 0)->paginate(10);

            return view('admin.properties.pending', ['properties' => $properties])
                ->with('error', 'Unable to load pending properties.');
        }
    }

    public function approve(Property $property)
    {
        $this->checkAdmin();

        try {
            DB::beginTransaction();

            // Use DB update for PostgreSQL compatibility
            DB::table('properties')
                ->where('id', $property->id)
                ->update([
                    'approval_status' => 'approved',
                    'rejection_reason' => null,
                    'updated_at' => now()
                ]);

            // Log the action
            try {
                DB::table('audit_logs')->insert([
                    'user_id' => auth()->id(),
                    'action' => 'approve_property',
                    'subject_type' => 'App\Models\Property',
                    'subject_id' => $property->id,
                    'meta_json' => json_encode(['property_title' => $property->title]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } catch (\Exception $e) {
                Log::warning('Audit log failed: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->back()->with('success', 'Property approved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Property approval error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve property. Please try again.');
        }
    }

    public function reject(Request $request, Property $property)
    {
        $this->checkAdmin();

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            DB::table('properties')
                ->where('id', $property->id)
                ->update([
                    'approval_status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                    'updated_at' => now()
                ]);

            // Log the action
            try {
                DB::table('audit_logs')->insert([
                    'user_id' => auth()->id(),
                    'action' => 'reject_property',
                    'subject_type' => 'App\Models\Property',
                    'subject_id' => $property->id,
                    'meta_json' => json_encode([
                        'property_title' => $property->title,
                        'reason' => $request->rejection_reason
                    ]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } catch (\Exception $e) {
                Log::warning('Audit log failed: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->back()->with('success', 'Property rejected.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Property rejection error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject property. Please try again.');
        }
    }

    public function verify(Property $property)
    {
        $this->checkAdmin();

        $property->update(['is_verified' => !$property->is_verified]);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $property->is_verified ? 'verify_property' : 'unverify_property',
            'subject_type' => 'App\Models\Property',
            'subject_id' => $property->id,
            'meta_json' => json_encode(['property_title' => $property->title])
        ]);

        return redirect()->back()->with('success', 'Property verification updated!');
    }

    public function feature(Property $property)
    {
        $this->checkAdmin();

        $property->update(['is_featured' => !$property->is_featured]);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $property->is_featured ? 'feature_property' : 'unfeature_property',
            'subject_type' => 'App\Models\Property',
            'subject_id' => $property->id,
            'meta_json' => json_encode(['property_title' => $property->title])
        ]);

        return redirect()->back()->with('success', 'Featured status updated!');
    }

    /**
     * Display property deletion requests for admin review
     */
    public function deletionRequests(Request $request)
    {
        $this->checkAdmin();

        $query = PropertyDeletionRequest::with(['property', 'landlord'])
            ->latest();

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->status;
            if (in_array($status, ['pending', 'approved', 'rejected'])) {
                $query->where('status', $status);
            }
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('property', function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('location_text', 'like', "%{$searchTerm}%");
            })->orWhereHas('landlord', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $deletionRequests = $query->paginate(15)->withQueryString();

        $statuses = [
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];

        return view('admin.properties.deletion-requests', compact('deletionRequests', 'statuses'));
    }

    /**
     * Approve property deletion request
     */
    public function approveDeletion(Request $request, PropertyDeletionRequest $deletionRequest)
    {
        $this->checkAdmin();

        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::transaction(function () use ($deletionRequest, $request) {
                // Update deletion request status
                $deletionRequest->update([
                    'status' => 'approved',
                    'admin_notes' => $request->admin_notes,
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now()
                ]);

                // Get property for logging before deletion
                $property = $deletionRequest->property;
                $propertyData = [
                    'id' => $property->id,
                    'title' => $property->title,
                    'landlord_id' => $property->user_id,
                    'location_text' => $property->location_text,
                    'price' => $property->price
                ];

                // Delete property and all related data (cascade should handle most)
                $property->delete();

                // Log the deletion approval
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'approve_property_deletion',
                    'subject_type' => 'App\Models\PropertyDeletionRequest',
                    'subject_id' => $deletionRequest->id,
                    'meta_json' => json_encode([
                        'property_data' => $propertyData,
                        'deletion_reason' => $deletionRequest->reason,
                        'admin_notes' => $request->admin_notes,
                        'requested_by' => $deletionRequest->landlord->name ?? 'Unknown'
                    ])
                ]);

                Log::info('Property deletion approved by admin', [
                    'admin_id' => auth()->id(),
                    'admin_name' => auth()->user()->name,
                    'deletion_request_id' => $deletionRequest->id,
                    'property_data' => $propertyData,
                    'landlord_id' => $deletionRequest->landlord_id,
                    'reason' => $deletionRequest->reason,
                    'admin_notes' => $request->admin_notes,
                    'approved_at' => now()
                ]);
            });

            return redirect()->route('admin.properties.deletion-requests')
                ->with('success', 'Property deletion request approved successfully! The property has been permanently deleted.');

        } catch (\Exception $e) {
            Log::error('Failed to approve property deletion', [
                'deletion_request_id' => $deletionRequest->id,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['general' => 'Failed to approve deletion request. Please try again.']);
        }
    }

    /**
     * Reject property deletion request
     */
    public function rejectDeletion(Request $request, PropertyDeletionRequest $deletionRequest)
    {
        $this->checkAdmin();

        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ], [
            'admin_notes.required' => 'Please provide a reason for rejecting this deletion request.'
        ]);

        try {
            $deletionRequest->update([
                'status' => 'rejected',
                'admin_notes' => $request->admin_notes,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now()
            ]);

            // Log the rejection
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'reject_property_deletion',
                'subject_type' => 'App\Models\PropertyDeletionRequest',
                'subject_id' => $deletionRequest->id,
                'meta_json' => json_encode([
                    'property_title' => $deletionRequest->property->title ?? 'Unknown',
                    'property_id' => $deletionRequest->property_id,
                    'deletion_reason' => $deletionRequest->reason,
                    'rejection_reason' => $request->admin_notes,
                    'requested_by' => $deletionRequest->landlord->name ?? 'Unknown'
                ])
            ]);

            Log::info('Property deletion rejected by admin', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'deletion_request_id' => $deletionRequest->id,
                'property_id' => $deletionRequest->property_id,
                'property_title' => $deletionRequest->property->title ?? 'Unknown',
                'landlord_id' => $deletionRequest->landlord_id,
                'landlord_reason' => $deletionRequest->reason,
                'admin_notes' => $request->admin_notes,
                'rejected_at' => now()
            ]);

            return redirect()->route('admin.properties.deletion-requests')
                ->with('success', 'Property deletion request rejected. The landlord will be notified with your feedback.');

        } catch (\Exception $e) {
            Log::error('Failed to reject property deletion', [
                'deletion_request_id' => $deletionRequest->id,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['general' => 'Failed to reject deletion request. Please try again.']);
        }
    }

    /**
     * View detailed information about a specific deletion request
     */
    public function viewDeletionRequest(PropertyDeletionRequest $deletionRequest)
    {
        $this->checkAdmin();

        $deletionRequest->load(['property.images', 'property.rooms', 'landlord', 'reviewer']);

        return view('admin.properties.deletion-request-details', compact('deletionRequest'));
    }
}