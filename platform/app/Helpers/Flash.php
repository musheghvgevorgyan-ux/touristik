<?php

namespace App\Helpers;

use Core\App;

class Flash
{
    public static function success(string $message): void
    {
        App::get('session')->flash('success', $message);
    }

    public static function error(string $message): void
    {
        App::get('session')->flash('error', $message);
    }

    public static function warning(string $message): void
    {
        App::get('session')->flash('warning', $message);
    }

    public static function info(string $message): void
    {
        App::get('session')->flash('info', $message);
    }

    public static function errors(array $errors): void
    {
        App::get('session')->flash('errors', $errors);
    }

    public static function old(array $data): void
    {
        App::get('session')->flash('old_input', $data);
    }

    public static function getOld(string $key, string $default = ''): string
    {
        $old = App::get('session')->getFlash('old_input');
        if ($old === null) {
            // Put it back if other fields still need it
            return $default;
        }
        $value = $old[$key] ?? $default;
        // Re-flash for other fields
        App::get('session')->flash('old_input', $old);
        return htmlspecialchars($value);
    }
}
