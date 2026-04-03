<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Settings service.
 *
 * Provides get/set access to the settings table with 1-hour caching
 * via Laravel's Cache facade. Cache is invalidated on set().
 */
class SettingsService
{
    private const CACHE_KEY = 'app_settings_all';
    private const CACHE_TTL = 3600; // 1 hour in seconds

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, string $default = ''): string
    {
        $all = self::all();
        return $all[$key] ?? $default;
    }

    /**
     * Update a setting value. Invalidates the cache.
     */
    public static function set(string $key, string $value): void
    {
        DB::table('settings')->updateOrInsert(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );

        // Invalidate cache so the next read fetches fresh data
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get all settings as a key => value array (cached for 1 hour).
     */
    public static function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $rows = DB::table('settings')
                ->select('setting_key', 'setting_value')
                ->get();

            $settings = [];
            foreach ($rows as $row) {
                $settings[$row->setting_key] = $row->setting_value;
            }
            return $settings;
        });
    }
}
