<?php
// Fix .env - rewrite clean version
$envPath = __DIR__ . '/.env';
$env = file_get_contents($envPath);

// Fix MAIL_FROM_NAME to have quotes
$env = preg_replace('/MAIL_FROM_NAME=(.+)/', 'MAIL_FROM_NAME="${1}"', $env);

// If MAIL_FROM_NAME not present, it was added without quotes - remove bad lines and re-add
$lines = explode("\n", $env);
$clean = [];
foreach ($lines as $line) {
    $trimmed = trim($line);
    if ($trimmed === '' && end($clean) === '') continue; // skip double blanks
    if (strpos($trimmed, 'MAIL_FROM_NAME=') === 0) {
        $clean[] = 'MAIL_FROM_NAME="Touristik Travel"';
    } else {
        $clean[] = $line;
    }
}
$env = implode("\n", $clean);

file_put_contents($envPath, $env);
echo "ENV fixed\n";

// Rebuild caches
echo shell_exec('php ' . __DIR__ . '/artisan config:cache 2>&1');
echo shell_exec('php ' . __DIR__ . '/artisan route:cache 2>&1');
echo shell_exec('php ' . __DIR__ . '/artisan view:cache 2>&1');

echo "\nDONE!\n";
