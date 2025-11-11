<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandlordReport;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
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

        $reports = LandlordReport::with(['landlord', 'reporter', 'property', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reports.index', compact('reports'));
    }

    public function resolve(Request $request, $id)
    {
        $this->checkAdmin();

        try {
            $report = LandlordReport::findOrFail($id);

            DB::beginTransaction();

            $report->update([
                'status' => LandlordReport::STATUS_RESOLVED,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_notes' => $request->admin_notes ?? 'Report resolved by admin.'
            ]);

            // Notify the reporter
            Notification::create([
                'user_id' => $report->reporter_id,
                'type' => 'report_resolved',
                'title' => 'Report Resolved',
                'message' => 'Your report against ' . $report->landlord->name . ' has been resolved by our team.',
                'data' => [
                    'report_id' => $report->id,
                    'landlord_name' => $report->landlord->name
                ],
                'action_url' => null
            ]);

            Log::info('Landlord report resolved', [
                'report_id' => $report->id,
                'admin_id' => auth()->id(),
                'landlord_id' => $report->landlord_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Report marked as resolved.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to resolve report', [
                'error' => $e->getMessage(),
                'report_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve report.'
            ], 500);
        }
    }

    public function dismiss(Request $request, $id)
    {
        $this->checkAdmin();

        try {
            $report = LandlordReport::findOrFail($id);

            DB::beginTransaction();

            $report->update([
                'status' => LandlordReport::STATUS_DISMISSED,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_notes' => $request->admin_notes ?? 'Report dismissed after review.'
            ]);

            // Notify the reporter
            Notification::create([
                'user_id' => $report->reporter_id,
                'type' => 'report_dismissed',
                'title' => 'Report Dismissed',
                'message' => 'Your report against ' . $report->landlord->name . ' has been reviewed and dismissed.',
                'data' => [
                    'report_id' => $report->id,
                    'landlord_name' => $report->landlord->name
                ],
                'action_url' => null
            ]);

            Log::info('Landlord report dismissed', [
                'report_id' => $report->id,
                'admin_id' => auth()->id(),
                'landlord_id' => $report->landlord_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Report dismissed.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to dismiss report', [
                'error' => $e->getMessage(),
                'report_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to dismiss report.'
            ], 500);
        }
    }
}
