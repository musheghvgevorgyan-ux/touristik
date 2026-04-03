<?php

namespace App\Helpers;

use Core\App;

class Redirect
{
    public static function to(string $url): void
    {
        App::get('response')->redirect($url);
    }

    public static function back(): void
    {
        App::get('response')->back();
    }

    public static function withErrors(array $errors, string $url = null): void
    {
        Flash::errors($errors);
        Flash::old($_POST);
        if ($url) {
            self::to($url);
        } else {
            self::back();
        }
    }
}
