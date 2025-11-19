<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Suspension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuspensionController extends Controller
{
    private function checkAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can access this area');
        }
    }

    /**
     * Suspend a user (tenant or landlord)
     */
    public function suspend(Request $request, User $user)
    {
        $this->checkAdmin();

        if (!in_array($user->role, ['tenant', 'landlord'])) {
            return redirect()->back()->with('error', 'Only tenant and landlord accounts can be suspended');
        }

        $validated = $request->validate([
            'duration_type' => 'required|in:1_day,3_days,permanent',
            'reason' => 'required|string|max:1000',
            'admin_notes' => 'nullable|string|max:2000'
        ]);

        try {
            DB::beginTransaction();

            // Determine warning number based on suspension history
            $warningNumber = $user->suspension_count + 1;

            // Calculate expiration date
            $expiresAt = null;
            if ($validated['duration_type'] === '1_day') {
                $expiresAt = now()->addDay();
            } elseif ($validated['duration_type'] === '3_days') {
                $expiresAt = now()->addDays(3);
            }

            // Create suspension record
            $suspension = Suspension::create([
                'user_id' => $user->id,
                'suspended_by' => auth()->id(),
                'duration_type' => $validated['duration_type'],
                'suspended_at' => now(),
                'expires_at' => $expiresAt,
                'reason' => $validated['reason'],
                'admin_notes' => $validated['admin_notes'] ?? null,
                'warning_number' => $warningNumber,
                'is_active' => true
            ]);

            // Update user suspension status
            $user->update([
                'is_suspended' => true,
                'suspended_until' => $expiresAt,
                'suspension_count' => $warningNumber
            ]);

            DB::commit();

            Log::info('User suspended', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'suspended_by' => auth()->user()->name,
                'duration' => $validated['duration_type'],
                'warning_number' => $warningNumber,
                'reason' => $validated['reason']
            ]);

            $warningText = match($warningNumber) {
                1 => '1st Warning',
                2 => '2nd Warning',
                3 => '3rd Warning (Final)',
                default => $warningNumber . 'th Warning'
            };

            return redirect()->back()->with('success', ucfirst($user->role) . " {$user->name} has been suspended ({$warningText} - {$suspension->duration_text}). They will not be able to login until the suspension is lifted.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User suspension failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? 'unknown',
                'user_role' => $user->role ?? 'unknown'
            ]);
            return redirect()->back()->with('error', 'Failed to suspend user: ' . $e->getMessage());
        }
    }

    /**
     * Lift a suspension
     */
    public function lift(Request $request, User $user)
    {
        $this->checkAdmin();

        try {
            DB::beginTransaction();

            // Find active suspension
            $suspension = $user->activeSuspension;

            if (!$suspension) {
                return redirect()->back()->with('error', 'No active suspension found for this user');
            }

            // Deactivate the suspension
            $suspension->update([
                'is_active' => false,
                'lifted_at' => now(),
                'lifted_by' => auth()->id()
            ]);

            // Update user status
            $user->update([
                'is_suspended' => false,
                'suspended_until' => null
            ]);

            DB::commit();

            Log::info('Suspension lifted', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'lifted_by' => auth()->user()->name
            ]);

            return redirect()->back()->with('success', "Suspension lifted for {$user->name}. They can now login again.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lift suspension failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? 'unknown',
                'user_role' => $user->role ?? 'unknown'
            ]);
            return redirect()->back()->with('error', 'Failed to lift suspension: ' . $e->getMessage());
        }
    }
}
