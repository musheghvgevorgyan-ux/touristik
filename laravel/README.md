# Touristik - Travel Agency Platform

Official website for **Touristik LLC**, a travel agency based in Yerevan, Armenia with 3 branches.

**Live:** [https://touristik.am](https://touristik.am)

## Tech Stack

- **Backend:** Laravel 12, PHP 8.3
- **Database:** MySQL
- **Frontend:** Blade templates, vanilla JS, CSS3
- **Maps:** Leaflet.js (world map), custom SVG (Armenia map)
- **Hosting:** internet.am (cPanel), deployed via Git

## Features

### Public Pages
- **Homepage** — Hero with flight search, destinations, FAQ, testimonials, partners
- **Ingoing Tours** — Interactive SVG map of Armenia (11 provinces), click region to filter tours
- **Outgoing Tours** — Interactive Leaflet world map with destination markers
- **Tour Detail Pages** — Photo gallery, day-by-day itinerary, includes/excludes, inline booking form, related tours
- **Destinations** — World map with markers, destination cards with prices
- **Blog** — Travel articles with admin CRUD
- **Contact** — Form with styled HTML emails, branch maps
- **About** — Company story, team, branch locations
- **Transfer** — Airport pickup, city transfers, intercity routes

### Admin Panel (`/admin`)
- Dashboard with charts and recent messages
- Tour management (full CRUD: gallery, itinerary, includes/excludes, region)
- Blog post management
- Contact messages
- Destination management
- Settings (site name, GA tracking, etc.)
- User management, profile/password change

### Integrations
- Google Analytics 4 (G-JHCDZH0E3T)
- Google Search Console (`/sitemap.xml`)
- SMTP email (mail.touristik.am)
- Request-a-Call floating button

### Internationalisation (i18n)

Server-side, URL-prefix-based i18n supporting **3 languages: English, Russian, Armenian**.

| Locale | URL prefix | Example |
|--------|-----------|---------|
| English (default) | none | `touristik.am/tours` |
| Russian | `/ru` | `touristik.am/ru/tours` |
| Armenian | `/hy` | `touristik.am/hy/tours` |

**How it works:**

- `SetLocale` middleware reads the first URL segment (`ru`/`hy`) and calls `app()->setLocale()` on every request.
- A second route group with `Route::prefix('{locale}')->where(['locale' => 'ru|hy'])` mirrors all public routes.
- `app/helpers.php` provides a global `lurl(string $path)` helper that auto-prefixes paths based on the active locale.
- All views use `{{ __('site.key') }}` for translated strings and `lurl('/path')` for internal links.
- Translation files live in `lang/en/site.php`, `lang/ru/site.php`, `lang/hy/site.php`.
- The main layout computes `$enUrl / $ruUrl / $hyUrl` and injects `hreflang` alternate tags in `<head>`.
- The language switcher navigates directly to the locale-prefixed canonical URL.
- `data-t` attributes are retained on elements as a JS fallback (no-op in current builds).
- `/sitemap.xml` emits all three locale variants per URL with `xhtml:link` hreflang alternates.

**Adding a new translation key:**

1. Add the key to all three files: `lang/en/site.php`, `lang/ru/site.php`, `lang/hy/site.php`.
2. Use `{{ __('site.your_key') }}` in the Blade view.

## Setup (Local Development)

```bash
git clone https://github.com/musheghvgevorgyan-ux/touristik.git
cd touristik/laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

## Deployment (Production)

SSH into the server (or use cPanel Terminal) and run:

```bash
cd ~/touristik_laravel/laravel
git pull origin laravel
composer dump-autoload --no-dev
php deploy.php
```

`deploy.php` runs: `migrate --force`, `config:cache`, `route:cache`, `view:cache`.

> **Note:** `composer dump-autoload` is required whenever `autoload.files` changes (e.g. `app/helpers.php`).

To seed tours:
```bash
php artisan db:seed --class=IngoingToursSeeder --force
php artisan db:seed --class=OutgoingToursSeeder --force
```

## Server Details

- **Host:** internet.am — cPanel at `ext48.host.am:2083` (auto-login via gear icon)
- **cPanel user:** TouristikLLC
- **PHP:** 8.3 (via `.htaccess` AddHandler ea-php83)
- **Database:** `touristi_laravel` / user `touristi_TouristikLLC`
- **Laravel path:** `/home/touristi/touristik_laravel/laravel/`
- **public_html:** symlink → `laravel/public`

## Project Structure

```
laravel/
  app/
    helpers.php                   # Global lurl() helper for locale-aware URLs
    Http/
      Controllers/                # Public + Admin controllers
      Middleware/
        SetLocale.php             # Reads /{locale}/ prefix, sets app locale
    Models/                       # Tour, Destination, User, Post, Setting, etc.
  lang/
    en/site.php                   # English translations
    ru/site.php                   # Russian translations
    hy/site.php                   # Armenian translations
  database/
    migrations/                   # All table schemas
    seeders/                      # IngoingToursSeeder, OutgoingToursSeeder
  resources/views/
    layouts/main.blade.php        # Main layout: nav, footer, hreflang, language switcher
    home/index.blade.php          # Homepage
    home/about.blade.php          # About page
    contact/index.blade.php       # Contact page
    tours/
      ingoing.blade.php           # Armenia SVG map + tours
      outgoing.blade.php          # World map + tours
      transfer.blade.php          # Transfer services
      show.blade.php              # Tour detail page
    destinations/
      index.blade.php             # Destinations listing + map
      show.blade.php              # Destination detail page
    blog/
      index.blade.php             # Blog listing
      show.blade.php              # Blog post
    admin/                        # Admin panel views
  routes/web.php                  # Routes: public, localized prefix group, admin, auth
  public/
    css/styles.css                # Main stylesheet
    js/app.js                     # Main JS (animations, menu, lazy-load, etc.)
    img/                          # Logo, hero, favicons
```

## Known Issues

- Mobile nav Tours dropdown has intermittent toggle issues on some devices
- Tour images are Unsplash placeholders — need real photos

## License

Proprietary — Touristik LLC. All rights reserved.
