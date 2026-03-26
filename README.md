# Touristik Travel

Tourism booking website for **Touristik LLC** — a travel agency based in Yerevan, Armenia with 3 branches.

## Features

- **Flight & Hotel Search** — Autocomplete city inputs, Hotelbeds API integration with geolocation-based hotel results
- **Incoming Tourism** — Curated Armenia tour packages slider (Classic Yerevan, Monasteries, Grand Tour, Hiking, Gastronomy)
- **Visa Support** — Invitation letters, e-visa assistance, fast processing info
- **Multi-language** — English, Russian, Armenian (data-t attribute translation system)
- **Currency Switcher** — USD, EUR, AMD, RUB with live conversion rates
- **Dark Mode** — Toggle with localStorage persistence, CSS custom properties theming
- **PWA** — Installable on mobile, service worker with offline fallback page
- **Search Filters** — Price range slider, star rating toggle, sort by price/stars
- **FAQ Accordion** — Common questions about booking, visas, payments
- **Breadcrumbs** — Navigation trail on all inner pages

## Animations

- 3D card tilt effect with shine overlay on destination cards
- Hero typing effect with floating clouds and planes
- Morphing blob backgrounds on key sections
- Page transition animations (fade in/out)
- Scroll reveal, stats counter, image zoom overlay
- Partners infinite scroll carousel

## SEO & Performance

- JSON-LD structured data (TravelAgency schema)
- Dynamic XML sitemap (`sitemap.php`)
- Preconnect hints for external resources
- WebP image format for Unsplash images
- CSS/JS minification (auto-detected via `file_exists`)
- Google Analytics ready (set `ga_measurement_id` in admin settings)
- robots.txt configured

## Accessibility

- Skip-to-content link
- `focus-visible` outlines on all interactive elements
- ARIA labels on navigation, FAQ, breadcrumbs
- `prefers-reduced-motion` support — disables all animations
- Semantic HTML with `<main>`, `<nav>`, `<header>`, `<footer>`

## Tech Stack

- **Backend:** PHP 8+ (vanilla, no framework)
- **Frontend:** Vanilla CSS & JavaScript (no dependencies)
- **Database:** MySQL via PDO
- **Server:** Apache (XAMPP)
- **APIs:** Hotelbeds Hotel API (test environment)
- **Build:** `clean-css-cli` + `terser` for minification

## Project Structure

```
touristik/
├── api/              # API endpoints
├── cache/            # Hotel search cache (gitignored)
├── config/           # DB & API credentials (gitignored)
│   ├── database.json
│   ├── hotelbeds.json
│   └── routes.json
├── css/
│   ├── styles.css
│   └── styles.min.css
├── img/              # PWA icons
├── includes/
│   ├── currency.php
│   ├── db.php
│   ├── flight_prices.php
│   ├── functions.php
│   └── router.php
├── js/
│   ├── script.js
│   └── script.min.js
├── pages/
│   ├── home.php
│   ├── destinations.php
│   ├── destination.php
│   ├── search.php
│   ├── about.php
│   ├── contact.php
│   ├── admin.php
│   ├── login.php
│   └── 404.php
├── templates/
│   ├── header.php
│   └── footer.php
├── index.php         # Entry point / router
├── manifest.json     # PWA manifest
├── sw.js             # Service worker
├── offline.html      # Offline fallback
├── sitemap.php       # Dynamic XML sitemap
└── robots.txt
```

## Setup

1. Clone into your web server directory (e.g., `htdocs/tourism/`)
2. Create MySQL database and import the schema
3. Copy config templates and fill in credentials:
   - `config/database.json` — MySQL connection
   - `config/hotelbeds.json` — Hotelbeds API key & secret
4. Visit `http://localhost/tourism/`

## Minification

```bash
npx clean-css-cli -o css/styles.min.css css/styles.css
npx terser js/script.js -o js/script.min.js -c -m
```

The site auto-detects and serves minified files when they exist.

## Contact

- **Website:** [touristik.am](https://touristik.am)
- **Email:** touristik.visadepartment@gmail.com
- **Phone:** +374 33 060 609
- **Branches:** Komitas 38 | Mashtots 7/6 | Yerevan Mall (2nd floor)
