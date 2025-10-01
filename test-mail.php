<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Mail Configuration\n";
echo "==========================\n\n";

echo "Environment: " . config('app.env') . "\n";
echo "Mail Driver: " . config('mail.default') . "\n";
echo "Mail Host: " . config('mail.mailers.smtp.host') . "\n";
echo "Mail Port: " . config('mail.mailers.smtp.port') . "\n";
echo "Mail Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
echo "Mail Username: " . config('mail.mailers.smtp.username') . "\n";
echo "Mail From: " . config('mail.from.address') . "\n";
echo "Mail From Name: " . config('mail.from.name') . "\n\n";

echo "Attempting to send test email...\n";

try {
    \Illuminate\Support\Facades\Mail::raw('This is a test email from PSU Dorm Finder', function ($message) {
        $message->to(config('mail.mailers.smtp.username'))
                ->subject('Test Email - Password Reset Debug');
    });

    echo "✓ Email sent successfully!\n";
    echo "Check your inbox at: " . config('mail.mailers.smtp.username') . "\n";
} catch (\Exception $e) {
    echo "✗ Failed to send email\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
