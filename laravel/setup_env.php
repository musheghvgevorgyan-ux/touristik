<?php
// Reset migrations and re-run
echo shell_exec('php ' . __DIR__ . '/artisan migrate:fresh --force 2>&1');
echo shell_exec('php ' . __DIR__ . '/artisan db:seed --force 2>&1');
echo shell_exec('php ' . __DIR__ . '/artisan config:cache 2>&1');
echo shell_exec('php ' . __DIR__ . '/artisan route:cache 2>&1');
echo shell_exec('php ' . __DIR__ . '/artisan view:cache 2>&1');
echo "\nDONE!\n";
