<?php

if (!function_exists('lurl')) {
    /**
     * Generate a locale-aware URL.
     * English (default) returns /path, others return /{locale}/path.
     */
    function lurl(string $path = '/'): string
    {
        $locale = app()->getLocale();
        $path   = '/' . ltrim($path, '/');
        return $locale === 'en' ? $path : "/{$locale}{$path}";
    }
}
