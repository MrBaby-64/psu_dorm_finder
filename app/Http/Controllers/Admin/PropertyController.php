<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyDeletionRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PropertyController
 *
 * Manages property approval workflow including reviewing, approving, rejecting,
 * and verifying property listings. Also handles deletion request processing.
 */
class PropertyController extends Controller
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
     * Display properties pending admin approval
     */
    public function pending()
    {
        $this->checkAdmin();

        // PostgreSQL compatible - use Eloquent with eager loading
        $properties = Property::with('landlord:id,name,email')
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.properties.pending', compact('properties'));
    }

    public function approve($id)
    {
        $this->checkAdmin();

        try {
            $property = Property::findOrFail($id);

            DB::beginTransaction();

            // Store title before update for logging
            $propertyTitle = $property->title;

            // Update property status
            $property->approval_status = 'approved';
            $property->rejection_reason = null;
            $property->save();

            // Log the action
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'approve_property',
                    'subject_type' => 'App\Models\Property',
                    'subject_id' => $property->id,
                    'meta_json' => json_encode(['property_title' => $propertyTitle])
                ]);
            } catch (\Exception $e) {
                Log::warning('Audit log failed: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->back()->with('success', 'Property approved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Property approval error', [
                'error' => $e->getMessage(),
                'property_id' => $id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to approve property: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $this->checkAdmin();

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        try {
            $property = Property::findOrFail($id);

            DB::beginTransaction();

            // Store title before update for logging
            $propertyTitle = $property->title;

            // Update property status
            $property->approval_status = 'rejected';
            $property->rejection_reason = $request->rejection_reason;
            $property->save();

            // Log the action
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'reject_property',
                    'subject_type' => 'App\Models\Property',
                    'subject_id' => $property->id,
                    'meta_json' => json_encode([
                        'property_title' => $propertyTitle,
                        'reason' => $request->rejection_reason
                    ])
                ]);
            } catch (\Exception $e) {
                Log::warning('Audit log failed: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->back()->with('success', 'Property rejected.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Property rejection error', [
                'error' => $e->getMessage(),
                'property_id' => $id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to reject property: ' . $e->getMessage());
        }
    }

    public function verify($id)
    {
        $this->checkAdmin();

        try {
            $property = Property::findOrFail($id);
            $property->is_verified = !$property->is_verified;
            $property->save();

            // Log the action
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => $property->is_verified ? 'verify_property' : 'unverify_property',
                    'subject_type' => 'App\Models\Property',
                    'subject_id' => $property->id,
                    'meta_json' => json_encode(['property_title' => $property->title])
                ]);
            } catch (\Exception $e) {
                Log::warning('Audit log failed: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Property verification updated!');
        } catch (\Exception $e) {
            Log::error('Property verify error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update verification.');
        }
    }

    public function feature($id)
    {
        $this->checkAdmin();

        try {
            $property = Property::findOrFail($id);
            $property->is_featured = !$property->is_featured;
            $property->save();

            // Log the action
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => $property->is_featured ? 'feature_property' : 'unfeature_property',
                    'subject_type' => 'App\Models\Property',
                    'subject_id' => $property->id,
                    'meta_json' => json_encode(['property_title' => $property->title])
                ]);
            } catch (\Exception $e) {
                Log::warning('Audit log failed: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Featured status updated!');
        } catch (\Exception $e) {
            Log::error('Property feature error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update featured status.');
        }
    }

    /**
     * Display property deletion requests and landlord reports for admin review
     */
    public function deletionRequests(Request $request)
    {
        $this->checkAdmin();

        // PostgreSQL compatible - use Eloquent with eager loading
        $deletionRequests = PropertyDeletionRequest::with(['property:id,title,location_text,city,barangay,price,room_count', 'landlord:id,name,email', 'reviewer:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Load landlord reports
        $landlordReports = \App\Models\LandlordReport::with(['landlord:id,name,email', 'reporter:id,name,email', 'property:id,title,location_text', 'reviewer:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'reports_page');

        $statuses = [
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];

        $reportStatuses = \App\Models\LandlordReport::getStatuses();

        return view('admin.properties.deletion-requests', compact('deletionRequests', 'landlordReports', 'statuses', 'reportStatuses'));
    }

    /**
     * Approve property deletion request
     */
    public function approveDeletion(Request $request, $id)
    {
        $this->checkAdmin();

        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $deletionRequest = PropertyDeletionRequest::findOrFail($id);

            DB::beginTransaction();

            // Load property first
            $property = $deletionRequest->property;

            if (!$property) {
                throw new \Exception('Property not found for this deletion request');
            }

            // Store property data before deletion
            $propertyData = [
                'id' => $property->id,
                'title' => $property->title,
                'landlord_id' => $property->user_id,
                'location_text' => $property->location_text,
                'price' => $property->price
            ];

            // Load landlord name separately to avoid relationship issues
            $landlordName = 'Unknown';
            try {
                $landlord = User::find($deletionRequest->landlord_id);
                $landlordName = $landlord ? $landlord->name : 'Unknown';
            } catch (\Exception $e) {
                Log::warning('Could not load landlord: ' . $e->getMessage());
            }

            // Update deletion request status
            $deletionRequest->status = 'approved';
            $deletionRequest->admin_notes = $request->admin_notes;
            $deletionRequest->reviewed_by = auth()->id();
            $deletionRequest->reviewed_at = now();
            $deletionRequest->save();

            // Delete property and all related data (cascade should handle most)
            $property->delete();

            // Log the deletion approval
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'approve_property_deletion',
                    'subject_type' => 'App\Models\PropertyDeletionRequest',
                    'subject_id' => $deletionRequest->id,
                    'meta_json' => json_encode([
                        'property_data' => $propertyData,
                        'deletion_reason' => $deletionRequest->reason,
                        'admin_notes' => $request->admin_notes,
                        'requested_by' => $landlordName
                    ])
                ]);
            } catch (\Exception $e) {
                Log::warning('Audit log failed: ' . $e->getMessage());
            }

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

            DB::commit();

            return redirect()->route('admin.properties.deletion-requests')
                ->with('success', 'Property deletion request approved successfully! The property has been permanently deleted.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to approve property deletion', [
                'deletion_request_id' => $id ?? 'unknown',
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to approve deletion request: ' . $e->getMessage());
        }
    }

    /**
     * Reject property deletion request
     */
    public function rejectDeletion(Request $request, $id)
    {
        $this->checkAdmin();

        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ], [
            'admin_notes.required' => 'Please provide a reason for rejecting this deletion request.'
        ]);

        try {
            $deletionRequest = PropertyDeletionRequest::findOrFail($id);

            DB::beginTransaction();

            // Get property title and landlord name separately
            $propertyTitle = 'Unknown';
            $landlordName = 'Unknown';

            try {
                if ($deletionRequest->property) {
                    $propertyTitle = $deletionRequest->property->title;
                }
            } catch (\Exception $e) {
                Log::warning('Could not load property: ' . $e->getMessage());
            }

            try {
                $landlord = User::find($deletionRequest->landlord_id);
                $landlordName = $landlord ? $landlord->name : 'Unknown';
            } catch (\Exception $e) {
                Log::warning('Could not load landlord: ' . $e->getMessage());
            }

            // Update deletion request
            $deletionRequest->status = 'rejected';
            $deletionRequest->admin_notes = $request->admin_notes;
            $deletionRequest->reviewed_by = auth()->id();
            $deletionRequest->reviewed_at = now();
            $deletionRequest->save();

            // Create notification for landlord
            \App\Models\Notification::create([
                'user_id' => $deletionRequest->landlord_id,
                'type' => \App\Models\Notification::TYPE_DELETION_REJECTED,
                'title' => 'Property Deletion Request Rejected',
                'message' => 'Your deletion request for "' . $propertyTitle . '" has been rejected. Admin feedback: ' . $request->admin_notes,
                'data' => [
                    'property_id' => $deletionRequest->property_id,
                    'property_title' => $propertyTitle,
                    'deletion_request_id' => $deletionRequest->id,
                    'admin_notes' => $request->admin_notes
                ],
                'action_url' => route('landlord.properties.index')
            ]);

            // Log the rejection
            try {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'reject_property_deletion',
                    'subject_type' => 'App\Models\PropertyDeletionRequest',
                    'subject_id' => $deletionRequest->id,
                    'meta_json' => json_encode([
                        'property_title' => $propertyTitle,
                        'property_id' => $deletionRequest->property_id,
                        'deletion_reason' => $deletionRequest->reason,
                        'rejection_reason' => $request->admin_notes,
                        'requested_by' => $landlordName
                    ])
                ]);
            } catch (\Exception $e) {
                Log::warning('Audit log failed: ' . $e->getMessage());
            }

            Log::info('Property deletion rejected by admin', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'deletion_request_id' => $deletionRequest->id,
                'property_id' => $deletionRequest->property_id,
                'property_title' => $propertyTitle,
                'landlord_id' => $deletionRequest->landlord_id,
                'landlord_reason' => $deletionRequest->reason,
                'admin_notes' => $request->admin_notes,
                'rejected_at' => now()
            ]);

            DB::commit();

            return redirect()->route('admin.properties.deletion-requests')
                ->with('success', 'Property deletion request rejected. The landlord will be notified with your feedback.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to reject property deletion', [
                'deletion_request_id' => $id ?? 'unknown',
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to reject deletion request: ' . $e->getMessage());
        }
    }

    /**
     * View detailed information about a specific deletion request
     */
    public function viewDeletionRequest($id)
    {
        $this->checkAdmin();

        try {
            $deletionRequest = PropertyDeletionRequest::findOrFail($id);
            $deletionRequest->load(['property.images', 'property.rooms', 'landlord', 'reviewer']);

            return view('admin.properties.deletion-request-details', compact('deletionRequest'));
        } catch (\Exception $e) {
            Log::error('View deletion request error: ' . $e->getMessage());
            return redirect()->route('admin.properties.deletion-requests')
                ->with('error', 'Unable to load deletion request details.');
        }
    }
}