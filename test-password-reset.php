<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Password;
use App\Models\User;

echo "Testing Password Reset Flow\n";
echo "============================\n\n";

// Check mail configuration
echo "Mail Configuration:\n";
echo "  Mailer: " . config('mail.default') . "\n";
echo "  Host: " . config('mail.mailers.smtp.host') . "\n";
echo "  Port: " . config('mail.mailers.smtp.port') . "\n";
echo "  Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
echo "  From: " . config('mail.from.address') . "\n";
echo "  Queue: " . config('queue.default') . "\n\n";

// Find a user or create test user
$email = 'tohkayatogam@gmail.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "No user found with email: $email\n";
    echo "Please provide an existing email address.\n";
    exit(1);
}

echo "Found user: {$user->name} ({$user->email})\n\n";

echo "Sending password reset link...\n";

try {
    $status = Password::sendResetLink(['email' => $email]);

    echo "Status: $status\n";
    echo "Message: " . __($status) . "\n\n";

    if ($status === Password::RESET_LINK_SENT) {
        echo "✓ Password reset link sent successfully!\n";
        echo "Check your email at: $email\n";
    } else {
        echo "✗ Failed to send password reset link\n";
        echo "Reason: " . __($status) . "\n";
    }
} catch (\Exception $e) {
    echo "✗ Exception occurred:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
