<?php
// Debug routes for production diagnostics
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Production diagnostic endpoint
Route::get('/system-check', function () {
    try {
        $diagnostics = [];

        // Basic PHP info
        $diagnostics['php_version'] = PHP_VERSION;
        $diagnostics['laravel_version'] = app()->version();
        $diagnostics['environment'] = app()->environment();

        // Database connection test
        try {
            \DB::connection()->getPdo();
            $diagnostics['database'] = 'Connected (' . config('database.default') . ')';

            // Test basic query
            $userCount = \DB::table('users')->count();
            $diagnostics['database_query'] = "Users table accessible: {$userCount} users";
        } catch (\Exception $e) {
            $diagnostics['database'] = 'ERROR: ' . $e->getMessage();
        }

        // Storage permissions
        $storagePath = storage_path();
        $diagnostics['storage_writable'] = is_writable($storagePath) ? 'YES' : 'NO';
        $diagnostics['storage_path'] = $storagePath;

        // Key configuration
        $diagnostics['app_key_set'] = config('app.key') ? 'YES' : 'NO';
        $diagnostics['app_debug'] = config('app.debug') ? 'YES' : 'NO';

        // Memory and extensions
        $diagnostics['memory_limit'] = ini_get('memory_limit');
        $diagnostics['required_extensions'] = [
            'pdo' => extension_loaded('pdo') ? 'YES' : 'NO',
            'pdo_pgsql' => extension_loaded('pdo_pgsql') ? 'YES' : 'NO',
            'pdo_mysql' => extension_loaded('pdo_mysql') ? 'YES' : 'NO',
            'mbstring' => extension_loaded('mbstring') ? 'YES' : 'NO',
            'json' => extension_loaded('json') ? 'YES' : 'NO',
        ];

        // Test Property model
        try {
            $propertyCount = \App\Models\Property::count();
            $diagnostics['property_model'] = "Working: {$propertyCount} properties";
        } catch (\Exception $e) {
            $diagnostics['property_model'] = 'ERROR: ' . $e->getMessage();
        }

        return response()->json($diagnostics, 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
});

// Test landlord properties endpoint
Route::get('/test-landlord-properties', function () {
    try {
        // Simulate what landlord properties page does
        $user = \App\Models\User::where('role', 'landlord')->first();

        if (!$user) {
            return response()->json(['error' => 'No landlord user found'], 404);
        }

        // Test basic property retrieval
        $properties = \App\Models\Property::where('user_id', $user->id)->get();

        return response()->json([
            'landlord_id' => $user->id,
            'properties_count' => $properties->count(),
            'properties' => $properties->map(function($property) {
                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'status' => $property->approval_status
                ];
            })
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500, [], JSON_PRETTY_PRINT);
    }
});