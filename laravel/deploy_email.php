<?php
$envPath = __DIR__ . '/.env';
$env = file_get_contents($envPath);

// Update mail settings
$updates = [
    'MAIL_MAILER' => 'smtp',
    'MAIL_HOST' => 'mail.touristik.am',
    'MAIL_PORT' => '465',
    'MAIL_USERNAME' => 'info@touristik.am',
    'MAIL_PASSWORD' => 'Touristik055060609',
    'MAIL_SCHEME' => 'ssl',
    'MAIL_FROM_ADDRESS' => 'info@touristik.am',
    'MAIL_FROM_NAME' => '"Touristik Travel"',
];

foreach ($updates as $key => $value) {
    if (preg_match("/^{$key}=.*/m", $env)) {
        $env = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $env);
    } else {
        $env .= "\n{$key}={$value}";
    }
}

file_put_contents($envPath, $env);
echo "Email config updated\n";

echo shell_exec('php ' . __DIR__ . '/artisan config:cache 2>&1');
echo "\nDone! Testing email...\n";

// Quick test
echo shell_exec('php ' . __DIR__ . '/artisan tinker --execute="Mail::raw(\"Touristik email test - SMTP is working!\", function(\$m) { \$m->to(\"info@touristik.am\")->subject(\"SMTP Test\"); }); echo \"Email sent!\";" 2>&1');
