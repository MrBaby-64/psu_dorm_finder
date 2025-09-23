<?php

namespace App\Http\Controllers;

use App\Models\ScheduledVisit;
use App\Models\Property;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduledVisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'required|string',
            'notes' => 'nullable|string|max:1000'
        ]);

        $property = Property::findOrFail($request->property_id);

        // Check if visit scheduling is enabled for this property
        if (!$property->isVisitSchedulingEnabled()) {
            return redirect()->back()
                ->with('error', 'Visit scheduling is not available for this property.');
        }

        // Check if user already has a pending/confirmed visit for this property
        $existingVisit = ScheduledVisit::where('user_id', Auth::id())
            ->where('property_id', $property->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingVisit) {
            return redirect()->back()
                ->with('error', 'You already have a scheduled visit for this property.');
        }

        // Create the visit
        $visit = ScheduledVisit::create([
            'user_id' => Auth::id(),
            'property_id' => $property->id,
            'preferred_date' => $request->preferred_date,
            'preferred_time' => $request->preferred_time,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        // Create notification for landlord
        Notification::create([
            'user_id' => $property->landlord->id,
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
            'confirmed_date' => 'required|date|after_or_equal:today',
            'confirmed_time' => 'required|string',
            'landlord_response' => 'nullable|string|max:500'
        ]);

        $visit->confirm(
            $request->confirmed_date,
            $request->confirmed_time,
            $request->landlord_response
        );

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