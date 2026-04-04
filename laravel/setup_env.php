<?php
$env = <<<'ENV'
APP_NAME=Touristik
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://touristik.am
APP_TIMEZONE=Asia/Yerevan

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=touristi_laravel
DB_USERNAME=touristi_TouristikLLC
DB_PASSWORD=Mushjan1993!!

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
ENV;

file_put_contents(__DIR__ . '/.env', $env);
echo file_exists(__DIR__ . '/.env') ? "ENV FILE CREATED OK\n" : "FAILED\n";

// Generate app key
echo shell_exec('php ' . __DIR__ . '/artisan key:generate --force 2>&1');

// Run migrations
echo shell_exec('php ' . __DIR__ . '/artisan migrate --force 2>&1');

// Run seeders
echo shell_exec('php ' . __DIR__ . '/artisan db:seed --force 2>&1');

// Cache config
echo shell_exec('php ' . __DIR__ . '/artisan config:cache 2>&1');
echo shell_exec('php ' . __DIR__ . '/artisan route:cache 2>&1');
echo shell_exec('php ' . __DIR__ . '/artisan view:cache 2>&1');

echo "\nDONE! Delete this file now.\n";
