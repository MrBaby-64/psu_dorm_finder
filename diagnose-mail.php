<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== MAIL DIAGNOSTICS ===\n\n";

echo "Environment Variables:\n";
echo "  APP_ENV: " . env('APP_ENV') . "\n";
echo "  MAIL_MAILER: " . env('MAIL_MAILER') . "\n";
echo "  MAIL_HOST: " . env('MAIL_HOST') . "\n";
echo "  MAIL_PORT: " . env('MAIL_PORT') . "\n";
echo "  MAIL_USERNAME: " . env('MAIL_USERNAME') . "\n";
echo "  MAIL_PASSWORD: " . (env('MAIL_PASSWORD') ? str_repeat('*', strlen(env('MAIL_PASSWORD'))) : 'NOT SET') . "\n";
echo "  MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION') . "\n";
echo "  MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n";
echo "  QUEUE_CONNECTION: " . env('QUEUE_CONNECTION') . "\n\n";

echo "Config Values (cached):\n";
echo "  mail.default: " . config('mail.default') . "\n";
echo "  mail.mailers.smtp.host: " . config('mail.mailers.smtp.host') . "\n";
echo "  mail.mailers.smtp.port: " . config('mail.mailers.smtp.port') . "\n";
echo "  mail.mailers.smtp.username: " . config('mail.mailers.smtp.username') . "\n";
echo "  mail.mailers.smtp.password: " . (config('mail.mailers.smtp.password') ? 'SET' : 'NOT SET') . "\n";
echo "  mail.mailers.smtp.encryption: " . config('mail.mailers.smtp.encryption') . "\n";
echo "  mail.from.address: " . config('mail.from.address') . "\n";
echo "  queue.default: " . config('queue.default') . "\n\n";

echo "Testing SMTP Connection...\n";
try {
    $transport = new \Swift_SmtpTransport(
        config('mail.mailers.smtp.host'),
        config('mail.mailers.smtp.port'),
        config('mail.mailers.smtp.encryption')
    );
    $transport->setUsername(config('mail.mailers.smtp.username'));
    $transport->setPassword(config('mail.mailers.smtp.password'));

    $mailer = new \Swift_Mailer($transport);
    $transport->start();

    echo "✓ SMTP connection successful!\n\n";
} catch (\Exception $e) {
    echo "✗ SMTP connection failed!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
}

echo "Testing Email Send...\n";
try {
    \Illuminate\Support\Facades\Mail::raw('Test email from diagnostics', function ($message) {
        $message->to(config('mail.mailers.smtp.username'))
                ->subject('Diagnostic Test Email');
    });
    echo "✓ Email sent successfully!\n";
} catch (\Exception $e) {
    echo "✗ Email send failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== END DIAGNOSTICS ===\n";
