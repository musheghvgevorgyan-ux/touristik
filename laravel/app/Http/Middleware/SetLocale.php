<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class SetLocale
{
    public const LOCALES = ['en', 'ru', 'hy'];
    public const DEFAULT_LOCALE = 'en';

    public function handle(Request $request, Closure $next)
    {
        $locale = $request->segment(1);

        if (in_array($locale, self::LOCALES) && $locale !== self::DEFAULT_LOCALE) {
            App::setLocale($locale);
            URL::defaults(['locale' => $locale]);
        } else {
            App::setLocale(self::DEFAULT_LOCALE);
            URL::defaults(['locale' => null]);
        }

        return $next($request);
    }
}
