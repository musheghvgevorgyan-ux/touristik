# Touristik Travel Club

**Live site: [touristik.am](https://touristik.am)**

Tourism booking website for **Touristik LLC** — a travel agency based in Yerevan, Armenia with 3 branches.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?logo=javascript&logoColor=black)

## Features

- **Hotel Booking** — Full Hotelbeds API integration: Availability, CheckRate, Booking, Cancellation, Voucher
- **Tour Pages** — Ingoing Tours (Armenia), Outgoing Tours (international), Transfer services with sidebar filters
- **Tours Dropdown Nav** — Hover submenu for quick access to tour categories
- **Flight Search** — Autocomplete city inputs with IATA codes, local price database
- **Multi-language** — Full site translation in English, Russian, Armenian (including admin panel, confirm dialogs, validation messages)
- **Currency Switcher** — USD, EUR, AMD, RUB with live exchange rates (6h cache)
- **Dark Mode** — Toggle with localStorage persistence
- **Contact Form** — Client-side validation with translated error messages + server-side validation
- **HTML Email Templates** — Branded emails for booking confirmation, cancellation, contact form (admin + auto-reply)
- **Visa Support** — Invitation letters, e-visa assistance, fast processing info
- **PWA** — Installable on mobile, service worker with offline fallback
- **FAQ Accordion** — Common questions about booking, visas, payments
- **Google Analytics 4** — Enhanced measurement, configurable via admin
- **Google Search Console** — Verified, sitemap submitted

## Hotel Booking (Hotelbeds API)

The booking flow follows Hotelbeds certification requirements:

1. **Availability** (`/hotels`) — Geolocation search with GZIP, filters, children ages
2. **CheckRate** (`/checkrates`) — Called when `rateType = RECHECK`
3. **Booking** (`/bookings`) — 60s timeout, holder + pax details, client reference
4. **Cancellation** (`DELETE /bookings`) — Cancel from admin panel with email notification
5. **Voucher** — Printable confirmation with all mandatory fields

## Security

- HTTPS redirect, security headers (X-Frame-Options, CSP, HSTS)
- Session hardening (httponly, SameSite=Lax, regeneration on login)
- Rate limiting (5 login attempts, 15-min lockout)
- CSRF tokens on all POST forms
- Prepared statements (PDO), output escaping
- Directory protection for config/, includes/, cache/

## SEO & Performance

- JSON-LD structured data (TravelAgency schema)
- Dynamic XML sitemap (`sitemap.php`)
- GZIP compression, browser caching headers
- CSS/JS minification (auto-detected)
- Lazy background images with IntersectionObserver
- Hero image optimized with WebP alternative
- Preconnect & dns-prefetch hints

## Accessibility

- Skip-to-content link, `focus-visible` outlines
- ARIA labels on navigation, FAQ, breadcrumbs
- `prefers-reduced-motion` support
- Semantic HTML

## Tech Stack

| Component | Technology |
|-----------|-----------|
| Backend | PHP 8+ (vanilla, no framework) |
| Database | MySQL via PDO (auto-creates tables) |
| Frontend | Vanilla CSS & JavaScript (no dependencies) |
| Server | Apache (XAMPP locally, Internet.am production) |
| Hotel API | Hotelbeds Hotel API |
| Currency | open.er-api.com |
| Build | `terser` (JS), `clean-css-cli` (CSS) |

## Project Structure

```
touristik/
├── api/                  # API endpoints (get_price, get_rates)
├── cache/                # Hotel search & currency cache (gitignored)
├── config/               # Credentials (gitignored except routes.json)
│   ├── .htaccess           # Deny all access
│   ├── database.json
│   ├── hotelbeds.json
│   ├── travelpayouts.json
│   └── routes.json
├── css/
│   ├── styles.css
│   └── styles.min.css
├── img/                  # Hero image, logo, PWA icons, partner logos
├── includes/
│   ├── .htaccess           # Deny all access
│   ├── currency.php        # Exchange rate fetching + caching
│   ├── db.php              # Database setup + schema
│   ├── flight_prices.php   # Local flight price lookup
│   ├── functions.php       # Helpers, CSRF, rate limiting, email templates
│   ├── hotelbeds.php       # Hotelbeds API (CheckRate, Book, Cancel)
│   └── router.php          # JSON-based route resolver
├── js/
│   ├── script.js
│   └── script.min.js
├── pages/
│   ├── home.php            # Hero, search, tours, visa, stats, destinations
│   ├── tours.php           # Tours overview (3 categories + featured)
│   ├── ingoing-tours.php   # Armenia tours with filters
│   ├── outgoing-tours.php  # International tours with filters
│   ├── transfer.php        # Transfer services with filters
│   ├── destinations.php    # All destinations grid
│   ├── destination.php     # Single destination detail
│   ├── search.php          # Flight + hotel search results
│   ├── booking.php         # Hotel booking flow + voucher
│   ├── about.php           # About page
│   ├── contact.php         # Contact form with validation
│   ├── admin.php           # Dashboard (destinations, bookings, messages, settings, performance)
│   ├── login.php           # Admin authentication
│   ├── logout.php          # Session termination
│   └── 404.php             # Error page
├── templates/
│   ├── header.php          # Nav with Tours dropdown, dark mode, currency, language
│   └── footer.php          # Footer, social buttons, back-to-top, cookies
├── .htaccess               # HTTPS, security headers, GZIP, caching
├── index.php               # Entry point / router
├── manifest.json           # PWA manifest
├── sw.js                   # Service worker
├── offline.html            # Offline fallback
├── sitemap.php             # Dynamic XML sitemap
└── robots.txt
```

## Setup

### Local Development

1. Clone into XAMPP htdocs:
   ```bash
   git clone https://github.com/musheghvgevorgyan-ux/touristik.git
   cp -r touristik /xampp/htdocs/tourism
   ```

2. Create `config/database.json`:
   ```json
   {
       "host": "localhost",
       "dbname": "tourism_db",
       "username": "root",
       "password": "",
       "charset": "utf8mb4"
   }
   ```

3. Create `config/hotelbeds.json`:
   ```json
   {
       "api_key": "YOUR_KEY",
       "api_secret": "YOUR_SECRET"
   }
   ```

4. Start Apache + MySQL in XAMPP, visit `http://localhost/tourism/`
   - Tables auto-create on first visit
   - Default admin: `admin` / `admin123`

### Production Deployment

Upload code files to `public_html/` via cPanel File Manager.

**Never overwrite these on production** — they have different credentials:
- `config/database.json`
- `config/hotelbeds.json`
- `config/travelpayouts.json`

## Admin Panel

Access via `/index.php?page=admin` after logging in:

- **Destinations** — Add/delete travel destinations with images
- **Bookings** — View hotel bookings with stats (total, confirmed, upcoming), cancel with email notification
- **Messages** — View contact form submissions
- **Settings** — Site name, tagline, hero text, footer, GA tracking ID
- **Performance** — Health score, server info, file sizes, database stats

## Contact

- **Website:** [touristik.am](https://touristik.am)
- **Email:** info@touristik.am
- **Phone:** +374 33 060 609
- **Branches:** Komitas 38 | Mashtots 7/6 | Yerevan Mall (2nd floor)

## Author

**Mushegh Gevorgyan** — [GitHub](https://github.com/musheghvgevorgyan-ux)
