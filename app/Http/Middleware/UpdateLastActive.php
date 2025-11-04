<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            try {
                $user = auth()->user();

                // Check if column exists before updating
                if (\Schema::hasColumn('users', 'last_active_at')) {
                    // Only update if last_active_at is null or older than 5 minutes
                    if (!$user->last_active_at || $user->last_active_at->lt(now()->subMinutes(5))) {
                        $user->update(['last_active_at' => now()]);
                    }
                }
            } catch (\Exception $e) {
                // Silently fail if there's an error - don't break the app
                \Log::warning('UpdateLastActive middleware error: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}
