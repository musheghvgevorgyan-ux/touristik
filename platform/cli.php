<?php

/**
 * Touristik Platform — CLI Tool
 *
 * Usage:
 *   php cli.php migrate          Run all pending migrations
 *   php cli.php migrate:status   Show migration status
 *   php cli.php migrate:rollback Rollback last migration
 *   php cli.php seed             Run all seeders
 *   php cli.php cache:clear      Clear cache files
 */

define('BASE_PATH', __DIR__);

// Load autoloader from App
require_once BASE_PATH . '/core/App.php';

// Register autoloader without booting HTTP services
spl_autoload_register(function (string $class) {
    $map = [
        'Core\\'              => BASE_PATH . '/core/',
        'App\\Controllers\\'  => BASE_PATH . '/app/Controllers/',
        'App\\Models\\'       => BASE_PATH . '/app/Models/',
        'App\\Services\\'     => BASE_PATH . '/app/Services/',
        'App\\Suppliers\\'    => BASE_PATH . '/app/Suppliers/',
        'App\\Middleware\\'   => BASE_PATH . '/app/Middleware/',
        'App\\Helpers\\'      => BASE_PATH . '/app/Helpers/',
        'Database\\Seeds\\'   => BASE_PATH . '/database/seeds/',
    ];

    foreach ($map as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $relativeClass = substr($class, strlen($prefix));
            $file = $dir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// Parse command
$command = $argv[1] ?? 'help';

echo "\n  Touristik Platform CLI\n";
echo "  ─────────────────────\n\n";

try {
    match ($command) {
        'migrate' => runMigrations(),
        'migrate:status' => migrationStatus(),
        'migrate:rollback' => rollbackMigration(),
        'seed' => runSeeders(),
        'cache:clear' => clearCache(),
        'help', '--help', '-h' => showHelp(),
        default => die("  Unknown command: {$command}\n  Run 'php cli.php help' for available commands.\n\n"),
    };
} catch (\Throwable $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
    if (str_contains($e->getMessage(), 'Database connection failed')) {
        echo "  Make sure MySQL is running and the database exists.\n";
        echo "  Create it with: CREATE DATABASE touristik_platform;\n";
    }
    echo "\n";
    exit(1);
}

// ─── Commands ────────────────────────────────────────────

function runMigrations(): void
{
    $migration = new \Core\Migration();
    $results = $migration->migrate();

    if (empty($results)) {
        echo "  Nothing to migrate. All migrations are up to date.\n\n";
        return;
    }

    foreach ($results as $file) {
        echo "  ✓ Migrated: {$file}\n";
    }
    echo "\n  Done! " . count($results) . " migration(s) ran.\n\n";
}

function migrationStatus(): void
{
    $migration = new \Core\Migration();
    $status = $migration->status();

    if (empty($status)) {
        echo "  No migration files found.\n\n";
        return;
    }

    echo "  Migration                              Status\n";
    echo "  ───────────────────────────────────────────────\n";
    foreach ($status as $item) {
        $icon = $item['status'] === 'ran' ? '✓' : '○';
        $label = $item['status'] === 'ran' ? 'Ran' : 'Pending';
        printf("  %s %-38s %s\n", $icon, $item['migration'], $label);
    }
    echo "\n";
}

function rollbackMigration(): void
{
    global $argv;
    $steps = (int) ($argv[2] ?? 1);

    $migration = new \Core\Migration();
    $results = $migration->rollback($steps);

    if (empty($results)) {
        echo "  Nothing to rollback.\n\n";
        return;
    }

    foreach ($results as $file) {
        echo "  ✓ Rolled back: {$file}\n";
    }
    echo "\n  Done! " . count($results) . " migration(s) rolled back.\n\n";
}

function runSeeders(): void
{
    $seeders = [
        \Database\Seeds\AdminSeeder::class,
        \Database\Seeds\DestinationSeeder::class,
        \Database\Seeds\SettingsSeeder::class,
    ];

    echo "  Running seeders...\n\n";

    foreach ($seeders as $seederClass) {
        $name = basename(str_replace('\\', '/', $seederClass));
        echo "  → {$name}\n";
        $seeder = new $seederClass();
        $seeder->run();
    }

    echo "\n  Done! All seeders completed.\n\n";
}

function clearCache(): void
{
    $cacheDir = BASE_PATH . '/cache';
    $files = glob($cacheDir . '/*.json');
    $count = 0;

    foreach ($files as $file) {
        if (unlink($file)) {
            $count++;
        }
    }

    echo "  Cleared {$count} cache file(s).\n\n";
}

function showHelp(): void
{
    echo "  Available commands:\n\n";
    echo "    migrate            Run all pending database migrations\n";
    echo "    migrate:status     Show status of all migrations\n";
    echo "    migrate:rollback   Rollback the last migration (add N for steps)\n";
    echo "    seed               Run all database seeders\n";
    echo "    cache:clear        Clear all cached files\n";
    echo "    help               Show this help message\n";
    echo "\n";
}
