<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class HandleProduction500Errors
{
    /**
     * Handle an incoming request and catch 500 errors gracefully.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Check database connectivity
            if (!$this->isDatabaseConnected()) {
                Log::error('Database connection failed');
                return $this->handleDatabaseError($request);
            }

            return $next($request);
        } catch (\Throwable $e) {
            // Log the error with full context
            Log::error('Production 500 Error', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
                'user_role' => auth()->user()->role ?? 'guest',
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->handleProductionError($request, $e);
        }
    }

    /**
     * Check if database is connected
     */
    private function isDatabaseConnected(): bool
    {
        try {
            DB::connection()->getPdo();
            DB::connection()->reconnect();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Handle database connectivity errors
     */
    private function handleDatabaseError(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Service temporarily unavailable. Please try again in a moment.',
                'code' => 503
            ], 503);
        }

        return response()->view('errors.503', [
            'message' => 'Database connection failed. Our team has been notified and is working to resolve this.'
        ], 503);
    }

    /**
     * Handle production errors gracefully
     */
    private function handleProductionError(Request $request, \Throwable $e)
    {
        // Don't show detailed errors in production
        if (app()->environment('production')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'An unexpected error occurred. Please try again.',
                    'code' => 500
                ], 500);
            }

            return response()->view('errors.500', [
                'message' => 'Something went wrong on our end. Our team has been notified.'
            ], 500);
        }

        // In development, show the original error
        throw $e;
    }
}