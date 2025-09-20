<?php
// app/Http/Controllers/Admin/PropertyController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\AuditLog;
use Illuminate\Http\Request;

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

        $properties = Property::where('approval_status', 'pending')
            ->with(['landlord'])
            ->latest()
            ->paginate(10);

        return view('admin.properties.pending', compact('properties'));
    }

    public function approve(Property $property)
    {
        $this->checkAdmin();

        $property->update([
            'approval_status' => 'approved',
            'rejection_reason' => null
        ]);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'approve_property',
            'subject_type' => 'App\Models\Property',
            'subject_id' => $property->id,
            'meta_json' => json_encode(['property_title' => $property->title])
        ]);

        return redirect()->back()->with('success', 'Property approved successfully!');
    }

    public function reject(Request $request, Property $property)
    {
        $this->checkAdmin();

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        $property->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'reject_property',
            'subject_type' => 'App\Models\Property',
            'subject_id' => $property->id,
            'meta_json' => json_encode([
                'property_title' => $property->title,
                'reason' => $request->rejection_reason
            ])
        ]);

        return redirect()->back()->with('success', 'Property rejected.');
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
}