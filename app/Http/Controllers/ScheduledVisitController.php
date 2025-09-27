<?php

namespace App\Http\Controllers;

use App\Models\ScheduledVisit;
use App\Models\Property;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduledVisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'property_id' => 'required|exists:properties,id',
                'preferred_date' => 'required|date|after:today|before:' . now()->addMonths(3)->format('Y-m-d'),
                'preferred_time' => 'required|date_format:H:i',
                'notes' => 'nullable|string|max:1000'
            ], [
                'preferred_date.after' => 'Preferred date must be in the future.',
                'preferred_date.before' => 'Preferred date cannot be more than 3 months from now.',
                'preferred_time.date_format' => 'Preferred time must be in HH:MM format.',
                'notes.max' => 'Notes cannot exceed 1000 characters.'
            ]);

            // Additional validation for business hours
            $preferredTime = strtotime($request->preferred_time);
            $businessStart = strtotime('08:00');
            $businessEnd = strtotime('20:00');

            if ($preferredTime < $businessStart || $preferredTime > $businessEnd) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Preferred time must be between 8:00 AM and 8:00 PM.',
                        'errors' => ['preferred_time' => ['Preferred time must be between 8:00 AM and 8:00 PM.']]
                    ], 422);
                }
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['preferred_time' => 'Preferred time must be between 8:00 AM and 8:00 PM.']);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $property = Property::findOrFail($request->property_id);

        // Check if visit scheduling is enabled for this property
        if (!$property->isVisitSchedulingEnabled()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Visit scheduling is not available for this property.'], 400);
            }
            return redirect()->back()
                ->with('error', 'Visit scheduling is not available for this property.');
        }

        // Check if user already has a pending/confirmed visit for this property
        $existingVisit = ScheduledVisit::where('user_id', Auth::id())
            ->where('property_id', $property->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingVisit) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'You already have a scheduled visit for this property.'], 400);
            }
            return redirect()->back()
                ->with('error', 'You already have a scheduled visit for this property.');
        }

        // Create the visit with transaction for data consistency
        try {
            DB::transaction(function () use ($property, $request, &$visit) {
                $visit = ScheduledVisit::create([
                    'user_id' => Auth::id(),
                    'property_id' => $property->id,
                    'preferred_date' => $request->preferred_date,
                    'preferred_time' => $request->preferred_time,
                    'notes' => trim($request->notes),
                    'status' => ScheduledVisit::STATUS_PENDING
                ]);

                // Log the visit creation for audit purposes
                Log::info('Visit scheduled', [
                    'visit_id' => $visit->id,
                    'tenant_id' => Auth::id(),
                    'property_id' => $property->id,
                    'preferred_date' => $request->preferred_date,
                    'preferred_time' => $request->preferred_time
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to create scheduled visit', [
                'user_id' => Auth::id(),
                'property_id' => $property->id,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to schedule visit. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to schedule visit. Please try again.');
        }

        // Create notification for landlord
        Notification::create([
            'user_id' => $property->user_id, // Property owner
            'type' => Notification::TYPE_VISIT_SCHEDULED,
            'title' => 'New Visit Request',
            'message' => Auth::user()->name . ' has requested to visit your property: ' . $property->title,
            'data' => [
                'visit_id' => $visit->id,
                'property_id' => $property->id,
                'tenant_name' => Auth::user()->name,
                'preferred_date' => $request->preferred_date,
                'preferred_time' => $request->preferred_time
            ],
            'action_url' => route('landlord.scheduled-visits', ['visit_id' => $visit->id])
        ]);

        // Create notification for tenant (confirmation)
        Notification::create([
            'user_id' => Auth::id(), // Tenant
            'type' => Notification::TYPE_VISIT_SCHEDULED,
            'title' => 'Visit Request Sent',
            'message' => 'Your visit request for "' . $property->title . '" has been sent to the landlord. You will be notified when they confirm.',
            'data' => [
                'visit_id' => $visit->id,
                'property_id' => $property->id,
                'landlord_name' => $property->user->name,
                'preferred_date' => $request->preferred_date,
                'preferred_time' => $request->preferred_time
            ],
            'action_url' => route('tenant.scheduled-visits')
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Visit request sent successfully! The landlord will confirm your appointment.',
                'visit_id' => $visit->id
            ]);
        }

        return redirect()->back()
            ->with('success', 'Visit request sent successfully! The landlord will confirm your appointment.');
    }

    public function update(Request $request, ScheduledVisit $visit)
    {
        // Only the tenant who created the visit can update it
        if ($visit->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow updates if visit is still pending
        if ($visit->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This visit cannot be updated.');
        }

        $request->validate([
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'required|string',
            'notes' => 'nullable|string|max:1000'
        ]);

        $visit->update([
            'preferred_date' => $request->preferred_date,
            'preferred_time' => $request->preferred_time,
            'notes' => $request->notes
        ]);

        // Notify landlord of the update
        Notification::create([
            'user_id' => $visit->property->landlord->id,
            'type' => Notification::TYPE_VISIT_SCHEDULED,
            'title' => 'Visit Request Updated',
            'message' => $visit->tenant->name . ' has updated their visit request for ' . $visit->property->title,
            'data' => [
                'visit_id' => $visit->id,
                'property_id' => $visit->property_id,
                'tenant_name' => $visit->tenant->name,
                'preferred_date' => $request->preferred_date,
                'preferred_time' => $request->preferred_time
            ],
            'action_url' => route('landlord.scheduled-visits', ['visit_id' => $visit->id])
        ]);

        return redirect()->back()
            ->with('success', 'Visit request updated successfully!');
    }

    public function destroy(ScheduledVisit $visit)
    {
        // Only the tenant who created the visit can cancel it
        if ($visit->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$visit->canBeCancelled()) {
            return redirect()->back()
                ->with('error', 'This visit cannot be cancelled.');
        }

        $visit->cancel(Auth::id(), 'Cancelled by tenant');

        // Notify landlord of cancellation
        Notification::create([
            'user_id' => $visit->property->landlord->id,
            'type' => Notification::TYPE_VISIT_SCHEDULED,
            'title' => 'Visit Cancelled',
            'message' => $visit->tenant->name . ' has cancelled their visit to ' . $visit->property->title,
            'data' => [
                'visit_id' => $visit->id,
                'property_id' => $visit->property_id,
                'tenant_name' => $visit->tenant->name
            ]
        ]);

        return redirect()->back()
            ->with('success', 'Visit cancelled successfully.');
    }

    // For landlords to confirm visits
    public function confirm(Request $request, ScheduledVisit $visit)
    {
        // Only the property owner can confirm
        if ($visit->property->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$visit->canBeConfirmed()) {
            return redirect()->back()
                ->with('error', 'This visit cannot be confirmed.');
        }

        $request->validate([
            'confirmed_date' => 'required|date|after_or_equal:today|before:' . now()->addMonths(2)->format('Y-m-d'),
            'confirmed_time' => 'required|date_format:H:i',
            'landlord_response' => 'nullable|string|max:500'
        ], [
            'confirmed_date.after_or_equal' => 'Confirmed date cannot be in the past.',
            'confirmed_date.before' => 'Confirmed date cannot be more than 2 months from now.',
            'confirmed_time.date_format' => 'Confirmed time must be in HH:MM format.',
            'landlord_response.max' => 'Response message cannot exceed 500 characters.'
        ]);

        // Additional validation for business hours
        $confirmedTime = strtotime($request->confirmed_time);
        $businessStart = strtotime('08:00');
        $businessEnd = strtotime('20:00');

        if ($confirmedTime < $businessStart || $confirmedTime > $businessEnd) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['confirmed_time' => 'Confirmed time must be between 8:00 AM and 8:00 PM.']);
        }

        try {
            DB::transaction(function () use ($visit, $request) {
                $visit->confirm(
                    $request->confirmed_date,
                    $request->confirmed_time,
                    trim($request->landlord_response)
                );

                Log::info('Visit confirmed by landlord', [
                    'visit_id' => $visit->id,
                    'landlord_id' => Auth::id(),
                    'confirmed_date' => $request->confirmed_date,
                    'confirmed_time' => $request->confirmed_time
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to confirm visit', [
                'visit_id' => $visit->id,
                'landlord_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to confirm visit. Please try again.');
        }

        // Notify tenant of confirmation
        Notification::create([
            'user_id' => $visit->user_id,
            'type' => Notification::TYPE_VISIT_CONFIRMED,
            'title' => 'Visit Confirmed',
            'message' => 'Your visit to ' . $visit->property->title . ' has been confirmed for ' . 
                        \Carbon\Carbon::parse($request->confirmed_date)->format('M j, Y') . 
                        ' at ' . $request->confirmed_time,
            'data' => [
                'visit_id' => $visit->id,
                'property_id' => $visit->property_id,
                'confirmed_date' => $request->confirmed_date,
                'confirmed_time' => $request->confirmed_time
            ],
            'action_url' => route('tenant.scheduled-visits')
        ]);

        return redirect()->back()
            ->with('success', 'Visit confirmed successfully!');
    }

    // Mark visit as completed
    public function markCompleted(ScheduledVisit $visit)
    {
        // Only the property owner can mark as completed
        if ($visit->property->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$visit->canBeCompleted()) {
            return redirect()->back()
                ->with('error', 'This visit cannot be marked as completed.');
        }

        $visit->markCompleted();

        return redirect()->back()
            ->with('success', 'Visit marked as completed.');
    }

    // Cancel visit by landlord
    public function cancelByLandlord(Request $request, ScheduledVisit $visit)
    {
        // Only the property owner can cancel
        if ($visit->property->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$visit->canBeCancelled()) {
            return redirect()->back()
                ->with('error', 'This visit cannot be cancelled.');
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $visit->cancel(Auth::id(), $request->reason);

        // Notify tenant of cancellation
        Notification::create([
            'user_id' => $visit->user_id,
            'type' => Notification::TYPE_VISIT_SCHEDULED,
            'title' => 'Visit Cancelled by Landlord',
            'message' => 'Your visit to ' . $visit->property->title . ' has been cancelled by the landlord.',
            'data' => [
                'visit_id' => $visit->id,
                'property_id' => $visit->property_id,
                'cancellation_reason' => $request->reason
            ],
            'action_url' => route('tenant.scheduled-visits')
        ]);

        return redirect()->back()
            ->with('success', 'Visit cancelled successfully.');
    }
}