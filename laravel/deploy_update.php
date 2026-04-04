<?php
// Update .env with all production settings
$envPath = __DIR__ . '/.env';
$env = file_get_contents($envPath);

// Add missing env variables if not present
$additions = [
    'GOOGLE_ANALYTICS_ID' => 'G-JHCDZH0E3T',
    'MAIL_MAILER' => 'smtp',
    'MAIL_HOST' => 'mail.touristik.am',
    'MAIL_PORT' => '465',
    'MAIL_USERNAME' => 'info@touristik.am',
    'MAIL_PASSWORD' => '',
    'MAIL_SCHEME' => 'tls',
    'MAIL_FROM_ADDRESS' => 'info@touristik.am',
    'MAIL_FROM_NAME' => 'Touristik Travel',
];

foreach ($additions as $key => $value) {
    if (strpos($env, "$key=") === false) {
        $env .= "\n$key=$value";
    }
}

file_put_contents($envPath, $env);
echo "ENV updated\n";

// Clear and rebuild caches
echo shell_exec('php ' . __DIR__ . '/artisan config:cache 2>&1');
echo shell_exec('php ' . __DIR__ . '/artisan route:cache 2>&1');
echo shell_exec('php ' . __DIR__ . '/artisan view:cache 2>&1');

echo "\nDONE!\n";
