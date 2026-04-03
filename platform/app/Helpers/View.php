<?php

namespace App\Helpers;

use Core\App;

class View
{
    /**
     * Escape output for HTML
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate CSRF hidden input field
     */
    public static function csrf(): string
    {
        $token = App::get('session')->csrfToken();
        return '<input type="hidden" name="_csrf_token" value="' . self::e($token) . '">';
    }

    /**
     * Get old form input value
     */
    public static function old(string $key, string $default = ''): string
    {
        return Flash::getOld($key, $default);
    }

    /**
     * Get translation string
     */
    public static function t(string $key, string $default = ''): string
    {
        static $translations = null;

        if ($translations === null) {
            $lang = App::get('session')->get('language', 'en');
            $file = BASE_PATH . '/app/translations/' . $lang . '.php';
            $translations = file_exists($file) ? require $file : [];
        }

        return $translations[$key] ?? $default ?: $key;
    }

    /**
     * Get current URL path
     */
    public static function currentUrl(): string
    {
        return App::get('request')->uri();
    }

    /**
     * Check if given path matches current URL
     */
    public static function isActive(string $path): bool
    {
        $current = self::currentUrl();
        return $current === $path || str_starts_with($current, $path . '/');
    }

    /**
     * Format price with currency
     */
    public static function price(float $amount, string $currency = 'USD'): string
    {
        return match ($currency) {
            'USD' => '$' . number_format($amount, 2),
            'EUR' => '€' . number_format($amount, 2),
            'AMD' => number_format($amount, 0) . ' AMD',
            'RUB' => number_format($amount, 0) . ' ₽',
            default => number_format($amount, 2) . ' ' . $currency,
        };
    }

    /**
     * Format date
     */
    public static function date(string $date, string $format = 'M d, Y'): string
    {
        return date($format, strtotime($date));
    }
}
