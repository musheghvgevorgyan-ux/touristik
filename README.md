# Touristik Travel

Tourism booking website for **Touristik LLC** — a travel agency based in Yerevan, Armenia with 3 branches.

## Features

- **Hotel Booking Flow** — Full Hotelbeds integration: Availability → CheckRate → Booking → Voucher (certification-ready)
- **Flight Search** — Autocomplete city inputs with IATA codes, local price database
- **Incoming Tourism** — Curated Armenia tour packages with continuous marquee scroll (Classic Yerevan, Monasteries, Grand Tour, Hiking, Gastronomy)
- **Visa Support** — Invitation letters, e-visa assistance, fast processing info
- **Multi-language** — English, Russian, Armenian (data-t attribute translation system)
- **Currency Switcher** — USD, EUR, AMD, RUB with live exchange rates (open.er-api.com, 6h cache)
- **Dark Mode** — Toggle with localStorage persistence, CSS custom properties theming
- **PWA** — Installable on mobile, service worker with offline fallback page
- **Search Filters** — Price range slider, star rating toggle, sort by price/stars
- **FAQ Accordion** — Common questions about booking, visas, payments
- **Breadcrumbs** — Navigation trail on all inner pages
- **CSRF Protection** — Token-based protection on all POST forms
- **Children Ages** — Dynamic age selection (1-17) for hotel search

## Hotel Booking (Hotelbeds API)

The booking flow follows Hotelbeds certification requirements:

1. **Availability** (`/hotels`) — Geolocation search with GZIP, filters, children ages as paxes
2. **CheckRate** (`/checkrates`) — Only called when `rateType = RECHECK`
3. **Booking** (`/bookings`) — 60s timeout, holder + pax details, client reference
4. **Voucher** — Printable confirmation with all mandatory fields:
   - Hotel name, category, address, destination, phone
   - Booking reference + agency reference
   - Check-in/out dates, room type, board type
   - Guest names, cancellation policies, rate comments
   - Payment text: "Payable through [Supplier], acting as agent..."

Search results display promotions, cancellation policies, and all available room/board options.

## Animations

- Card hover: lift up + shadow + image zoom with brightness/saturation boost
- Hero typing effect with floating clouds and planes
- Incoming tours continuous marquee scroll (pauses on hover)
- Lazy loading with shimmer placeholder animation
- Morphing blob backgrounds on key sections
- 3D card tilt with shine overlay
- Scroll reveal, stats counter, partners infinite scroll

## SEO & Performance

- JSON-LD structured data (TravelAgency schema)
- Dynamic XML sitemap (`sitemap.php`)
- Preconnect & dns-prefetch hints for external resources
- WebP image format for Unsplash images
- CSS/JS minification (auto-detected via `file_exists`)
- JS minified via terser (106KB → 50KB)
- Lazy background images with IntersectionObserver (400px preload)
- Google Analytics ready (set `ga_measurement_id` in admin settings)
- robots.txt configured

## Accessibility

- Skip-to-content link
- `focus-visible` outlines on all interactive elements
- ARIA labels on navigation, FAQ, breadcrumbs
- `prefers-reduced-motion` support — disables all animations
- Semantic HTML with `<main>`, `<nav>`, `<header>`, `<footer>`

## Security

- CSRF tokens on all POST forms (login, admin, contact)
- Password hashing (PASSWORD_DEFAULT)
- Input sanitization with htmlspecialchars and FILTER_VALIDATE_EMAIL
- Prepared statements (PDO) for all database queries
- Admin session authentication

## Tech Stack

- **Backend:** PHP 8+ (vanilla, no framework)
- **Frontend:** Vanilla CSS & JavaScript (no dependencies)
- **Database:** MySQL via PDO (auto-creates tables on first run)
- **Server:** Apache (XAMPP)
- **APIs:** Hotelbeds Hotel API, open.er-api.com (currency rates)
- **Build:** `terser` for JS minification

## Project Structure

```
touristik/
├── api/                # API endpoints (get_price, get_rates)
├── cache/              # Hotel search & currency cache (gitignored)
├── config/             # Credentials (gitignored except routes.json)
│   ├── database.json
│   ├── hotelbeds.json
│   └── routes.json
├── css/
│   ├── styles.css
│   └── styles.min.css
├── img/                # Hero image, PWA icons
├── includes/
│   ├── currency.php      # Exchange rate fetching + caching
│   ├── db.php            # Database setup + schema
│   ├── flight_prices.php # Local flight price lookup
│   ├── functions.php     # Helpers + CSRF protection
│   ├── hotelbeds.php     # Hotelbeds API (CheckRate, Book, Cancel)
│   └── router.php        # JSON-based route resolver
├── js/
│   ├── script.js
│   └── script.min.js
├── pages/
│   ├── home.php          # Hero, search, tours, visa, stats, destinations
│   ├── destinations.php  # All destinations grid
│   ├── destination.php   # Single destination detail
│   ├── search.php        # Flight + hotel search results
│   ├── booking.php       # Hotel booking flow (CheckRate → Book → Voucher)
│   ├── about.php         # Why travel with us
│   ├── contact.php       # Contact form + branch info
│   ├── admin.php         # Dashboard (destinations, messages, settings)
│   ├── login.php         # Admin authentication
│   ├── logout.php        # Session termination
│   └── 404.php           # Error page
├── templates/
│   ├── header.php        # Nav, dark mode, currency, language, SEO meta
│   └── footer.php        # Footer, WhatsApp, back-to-top, cookies
├── index.php             # Entry point / router
├── manifest.json         # PWA manifest
├── sw.js                 # Service worker
├── offline.html          # Offline fallback
├── sitemap.php           # Dynamic XML sitemap
└── robots.txt
```

## Setup

1. Clone into your web server directory (e.g., `htdocs/tourism/`)
2. Create a MySQL database (tables auto-create on first visit)
3. Copy config templates and fill in credentials:
   - `config/database.json` — MySQL host, database, username, password
   - `config/hotelbeds.json` — Hotelbeds API key & secret
4. Visit `http://localhost/tourism/`
5. Default admin login: `admin` / `admin123` (change after first login)

## Minification

```bash
npx terser js/script.js -o js/script.min.js --compress --mangle
```

The site auto-detects and serves minified files when they exist.

## Admin Panel

Access via `/index.php?page=admin` after logging in:

- **Destinations** — Add/delete travel destinations
- **Messages** — View contact form submissions
- **Settings** — Edit site name, tagline, hero text, footer, GA tracking ID

## Contact

- **Website:** [touristik.am](https://touristik.am)
- **Email:** touristik.visadepartment@gmail.com
- **Phone:** +374 33 060 609
- **Branches:** Komitas 38 | Mashtots 7/6 | Yerevan Mall (2nd floor)
