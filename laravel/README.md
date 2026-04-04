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
- **Homepage** - Hero with flight search, destinations, FAQ, testimonials, partners
- **Ingoing Tours** - Interactive SVG map of Armenia (11 provinces + Lake Sevan), click region to filter tours, region info panel with cities/sights/stats
- **Outgoing Tours** - Interactive Leaflet world map with destination markers, flight lines from Yerevan, 8 destinations (Greece, Egypt, UAE, Georgia, Turkey, Thailand, Italy, Maldives)
- **Tour Detail Pages** - Photo gallery, day-by-day itinerary, what's included/excluded, sticky sidebar with inline booking form, related tours
- **Destinations** - World map with markers, destination cards
- **Blog** - Posts with admin CRUD
- **Contact** - Form with styled HTML emails, branch maps (Leaflet)
- **About** - Company story, team, branch locations with maps
- **Transfer** - Airport pickup, city transfers, intercity routes

### Admin Panel (/admin)
- Dashboard with charts and recent messages
- Tour management (full CRUD: create, edit, delete with gallery, itinerary, includes/excludes, region)
- Blog post management
- Contact messages
- User management
- Destination management
- Settings (site name, GA tracking, etc.)
- Profile/password change

### Integrations
- Google Analytics 4 (G-JHCDZH0E3T)
- Google Search Console (sitemap.xml)
- SMTP email (mail.touristik.am)
- Request-a-Call floating button

### i18n
- 3 languages: English, Russian, Armenian
- Client-side translation system via `translations.js`
- Language switcher in navbar

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

SSH into server and run:
```bash
cd ~/touristik_laravel/laravel
git pull
php artisan migrate --force
php deploy.php
```

`deploy.php` runs: migrate, config:cache, route:cache, view:cache.

To seed tours:
```bash
php artisan db:seed --class=IngoingToursSeeder --force
php artisan db:seed --class=OutgoingToursSeeder --force
```

## Server Details

- **Host:** internet.am, cPanel at ext48.host.am:2083
- **Username:** TouristikLLC
- **PHP:** 8.3 (via .htaccess AddHandler)
- **Database:** touristi_laravel
- **Domain:** touristik.am

## Project Structure

```
laravel/
  app/
    Http/Controllers/       # Public + Admin controllers
    Models/                 # Tour, Destination, User, Post, Setting, etc.
  database/
    migrations/             # All table schemas
    seeders/                # IngoingToursSeeder, OutgoingToursSeeder, DatabaseSeeder
  resources/views/
    layouts/main.blade.php  # Main layout (nav, footer, floating buttons)
    home/index.blade.php    # Homepage
    tours/
      ingoing.blade.php     # Armenia map + tours
      outgoing.blade.php    # World map + tours
      show.blade.php        # Tour detail page
    admin/                  # Admin panel views
  public/
    css/styles.css          # Main stylesheet
    js/app.js               # Main JS (animations, menu, search, etc.)
    js/translations.js      # i18n translations (EN/RU/HY)
    img/                    # Logo, hero, favicons
```

## Known Issues

- Mobile nav submenu (Tours dropdown) has intermittent toggle issues on some devices - needs CSS/JS refactor for touch events
- Tour images are Unsplash placeholders - need real Armenian landmark photos

## License

Proprietary - Touristik LLC. All rights reserved.
