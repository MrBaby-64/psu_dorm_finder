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

// Mail configuration debug
Route::get('/debug-mail-config', function () {
    return response()->json([
        'environment' => config('app.env'),
        'mail' => [
            'default' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'username' => config('mail.mailers.smtp.username'),
            'password_set' => config('mail.mailers.smtp.password') ? 'YES' : 'NO',
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ],
        'queue' => [
            'default' => config('queue.default'),
        ],
    ], 200, [], JSON_PRETTY_PRINT);
});

// Test SMTP connection
Route::get('/debug-test-smtp-connection', function () {
    try {
        $transport = \Illuminate\Mail\Transport\SmtpTransport::class;
        $swift = new \Swift_SmtpTransport(
            config('mail.mailers.smtp.host'),
            config('mail.mailers.smtp.port'),
            config('mail.mailers.smtp.encryption')
        );
        $swift->setUsername(config('mail.mailers.smtp.username'));
        $swift->setPassword(config('mail.mailers.smtp.password'));

        // Try to start the transport
        $swift->start();

        return response()->json([
            'success' => true,
            'message' => 'SMTP connection successful!',
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'exception_class' => get_class($e),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
        ], 500);
    }
});

// Send test email
Route::get('/debug-send-test-email', function () {
    try {
        \Mail::raw('This is a test email sent at ' . now(), function ($message) {
            $message->to(config('mail.mailers.smtp.username'))
                    ->subject('Debug Test Email - ' . now());
        });

        return response()->json([
            'success' => true,
            'message' => 'Test email sent successfully',
            'to' => config('mail.mailers.smtp.username'),
            'timestamp' => now()->toDateTimeString(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'exception_class' => get_class($e),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});

// Test password reset
Route::get('/debug-password-reset/{email}', function ($email) {
    try {
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found with email: ' . $email,
            ], 404);
        }

        $status = \Illuminate\Support\Facades\Password::sendResetLink(['email' => $email]);

        return response()->json([
            'success' => $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT,
            'status' => $status,
            'message' => __($status),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'timestamp' => now()->toDateTimeString(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});

// View recent logs
Route::get('/debug-logs', function () {
    $logFile = storage_path('logs/laravel.log');

    if (!file_exists($logFile)) {
        return response('<pre>Log file not found</pre>');
    }

    $lines = [];
    $file = new SplFileObject($logFile);
    $file->seek(PHP_INT_MAX);
    $lastLine = $file->key();
    $startLine = max(0, $lastLine - 200);

    $file->seek($startLine);
    while (!$file->eof()) {
        $lines[] = $file->fgets();
    }

    return response('<pre>' . htmlspecialchars(implode('', $lines)) . '</pre>');
});