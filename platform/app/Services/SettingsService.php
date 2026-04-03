<?php

namespace App\Services;

use Core\Database;

class SettingsService
{
    private static ?array $cache = null;

    /**
     * Get a setting value by key
     */
    public static function get(string $key, string $default = ''): string
    {
        self::loadAll();
        return self::$cache[$key] ?? $default;
    }

    /**
     * Update a setting value
     */
    public static function set(string $key, string $value): void
    {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)",
            [$key, $value]
        );
        // Invalidate cache
        self::$cache = null;
    }

    /**
     * Get all settings as key => value array
     */
    public static function all(): array
    {
        self::loadAll();
        return self::$cache;
    }

    private static function loadAll(): void
    {
        if (self::$cache !== null) return;

        $db = Database::getInstance();
        $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
        self::$cache = [];
        foreach ($stmt->fetchAll() as $row) {
            self::$cache[$row['setting_key']] = $row['setting_value'];
        }
    }
}
