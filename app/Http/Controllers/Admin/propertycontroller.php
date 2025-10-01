<?php
// app/Http/Controllers/Admin/PropertyController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyDeletionRequest;
use App\Models\AuditLog;
use App\Models\User;
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
            // Simplest possible query for PostgreSQL
            $properties = DB::table('properties')
                ->join('users', 'properties.user_id', '=', 'users.id')
                ->select(
                    'properties.*',
                    'users.name as landlord_name',
                    'users.email as landlord_email'
                )
                ->where('properties.approval_status', 'pending')
                ->orderBy('properties.created_at', 'desc')
                ->paginate(10);

            return view('admin.properties.pending', compact('properties'));

        } catch (\Exception $e) {
            Log::error('Admin pending properties error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.dashboard')
                ->with('error', 'Unable to load pending properties.');
        }
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
     * Display property deletion requests for admin review
     */
    public function deletionRequests(Request $request)
    {
        $this->checkAdmin();

        try {
            // Simplest possible query for PostgreSQL - using raw DB queries
            $deletionRequests = DB::table('property_deletion_requests')
                ->leftJoin('properties', 'property_deletion_requests.property_id', '=', 'properties.id')
                ->leftJoin('users as landlords', 'property_deletion_requests.landlord_id', '=', 'landlords.id')
                ->leftJoin('users as reviewers', 'property_deletion_requests.reviewed_by', '=', 'reviewers.id')
                ->select(
                    'property_deletion_requests.*',
                    'properties.title as property_title',
                    'properties.location_text as property_location',
                    'properties.city as property_city',
                    'properties.barangay as property_barangay',
                    'properties.price as property_price',
                    'properties.room_count as property_rooms',
                    'landlords.name as landlord_name',
                    'landlords.email as landlord_email',
                    'reviewers.name as reviewer_name'
                )
                ->orderBy('property_deletion_requests.created_at', 'desc')
                ->paginate(15);

            // Add computed attributes for each request
            $deletionRequests->getCollection()->transform(function ($request) {
                $request->status_color = match($request->status) {
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    default => 'bg-gray-100 text-gray-800'
                };
                $request->status_name = ucfirst($request->status);
                return $request;
            });

            $statuses = [
                'pending' => 'Pending Review',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ];

            return view('admin.properties.deletion-requests', compact('deletionRequests', 'statuses'));

        } catch (\Exception $e) {
            Log::error('Admin deletion requests error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.dashboard')
                ->with('error', 'Unable to load deletion requests.');
        }
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