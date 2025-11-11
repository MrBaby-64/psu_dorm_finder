<?php

namespace App\Http\Controllers;

use App\Models\LandlordReport;
use App\Models\User;
use App\Models\Property;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Submit a report against a landlord
     */
    public function reportLandlord(Request $request, $landlordId)
    {
        // Only tenants can report
        if (!auth()->check() || auth()->user()->role !== 'tenant') {
            return response()->json([
                'success' => false,
                'message' => 'Only tenants can submit reports.'
            ], 403);
        }

        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'reason' => 'required|in:fraud,harassment,misleading_info,unprofessional,safety_concern,other',
            'description' => 'required|string|min:10|max:2000'
        ]);

        try {
            // Verify landlord exists and is actually a landlord
            $landlord = User::findOrFail($landlordId);
            if ($landlord->role !== 'landlord') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid landlord ID.'
                ], 400);
            }

            // Check if property belongs to this landlord
            $property = Property::findOrFail($request->property_id);
            if ($property->user_id != $landlordId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property does not belong to this landlord.'
                ], 400);
            }

            // Check for duplicate reports (within 30 days)
            $existingReport = LandlordReport::where('reporter_id', auth()->id())
                ->where('landlord_id', $landlordId)
                ->where('property_id', $request->property_id)
                ->where('created_at', '>=', now()->subDays(30))
                ->first();

            if ($existingReport) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reported this landlord for this property within the last 30 days.'
                ], 400);
            }

            DB::beginTransaction();

            // Create the report
            $report = LandlordReport::create([
                'reporter_id' => auth()->id(),
                'landlord_id' => $landlordId,
                'property_id' => $request->property_id,
                'reason' => $request->reason,
                'description' => $request->description,
                'status' => LandlordReport::STATUS_PENDING
            ]);

            // Create notification for admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'landlord_reported',
                    'title' => 'New Landlord Report',
                    'message' => 'A tenant has reported landlord "' . $landlord->name . '" for: ' . $report->reason_name,
                    'data' => [
                        'report_id' => $report->id,
                        'landlord_id' => $landlordId,
                        'landlord_name' => $landlord->name,
                        'reporter_name' => auth()->user()->name,
                        'property_title' => $property->title,
                        'reason' => $report->reason_name
                    ],
                    'action_url' => route('admin.reports.index')
                ]);
            }

            Log::info('Landlord reported', [
                'report_id' => $report->id,
                'reporter_id' => auth()->id(),
                'landlord_id' => $landlordId,
                'property_id' => $request->property_id,
                'reason' => $request->reason
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully. Our team will review it shortly.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to submit landlord report', [
                'error' => $e->getMessage(),
                'reporter_id' => auth()->id(),
                'landlord_id' => $landlordId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report. Please try again.'
            ], 500);
        }
    }
}
