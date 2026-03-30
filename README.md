# Touristik Travel

**Live site: [touristik.am](https://touristik.am)**

Tourism booking website for **Touristik LLC** — a travel agency based in Yerevan, Armenia with 3 branches.

## Features

- **Tour Pages** — Dedicated pages for Ingoing Tours, Outgoing Tours, and Transfer services with filters (search, date, price range, region)
- **Hotel Booking Flow** — Full Hotelbeds integration: Availability → CheckRate → Booking → Voucher (certification-ready)
- **Flight Search** — Autocomplete city inputs with IATA codes, local price database
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
- **Email Notifications** — Booking confirmation to guest (with voucher link) + admin alert; contact form auto-reply to customer
- **Google Analytics 4** — Enhanced measurement (page views, scrolls, outbound clicks), configurable via admin settings

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

## Animations

- Card hover: lift up + shadow + image zoom with brightness/saturation boost
- Hero typing effect with floating clouds and planes
- Tours section with Armenian church background and glassmorphism cards
- Lazy loading with shimmer placeholder animation
- Morphing blob backgrounds on key sections
- 3D card tilt with shine overlay
- Page transitions with back-button fix
- Scroll reveal, stats counter, partners infinite scroll

## Security

- `.htaccess` — HTTPS redirect, directory protection (config/, cache/, includes/, templates/), security headers (X-Frame-Options, CSP, HSTS), no directory listing
- Session hardening — httponly, SameSite=Lax, strict mode, session regeneration on login
- Rate limiting — 5 login attempts max, 15-minute lockout
- CSRF tokens on all POST forms
- Admin settings whitelist — only allowed keys can be updated
- Password hashing (PASSWORD_DEFAULT)
- Prepared statements (PDO) for all database queries
- SSL verification on all external API calls
- Output escaping with htmlspecialchars

## SEO & Performance

- JSON-LD structured data (TravelAgency schema)
- Dynamic XML sitemap (`sitemap.php`)
- Preconnect & dns-prefetch hints for external resources
- WebP image format for Unsplash images
- CSS/JS minification (auto-detected via `file_exists`)
- Lazy background images with IntersectionObserver
- Google Analytics 4 integrated (GA4 with enhanced measurement — page views, scrolls, outbound clicks)
- Google Search Console verified, sitemap submitted
- GZIP compression (mod_deflate) for all text-based assets
- Browser caching headers (CSS/JS: 1 month, images: 1 year)
- Hero image optimized (405KB → 252KB) with WebP alternative (184KB)
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
- **Database:** MySQL via PDO (auto-creates tables on first run)
- **Server:** Apache (XAMPP locally, Internet.am hosting for production)
- **APIs:** Hotelbeds Hotel API, open.er-api.com (currency rates)
- **Build:** `terser` for JS minification

## Project Structure

```
touristik/
├── api/                  # API endpoints (get_price, get_rates)
├── cache/                # Hotel search & currency cache (gitignored)
├── config/               # Credentials (gitignored except routes.json)
│   ├── .htaccess           # Deny all access
│   ├── database.json
│   ├── hotelbeds.json
│   └── routes.json
├── css/
│   ├── styles.css
│   └── styles.min.css
├── img/                  # Hero image, PWA icons
├── includes/
│   ├── .htaccess           # Deny all access
│   ├── currency.php        # Exchange rate fetching + caching
│   ├── db.php              # Database setup + schema
│   ├── flight_prices.php   # Local flight price lookup
│   ├── functions.php       # Helpers, CSRF, rate limiting
│   ├── hotelbeds.php       # Hotelbeds API (CheckRate, Book, Cancel)
│   └── router.php          # JSON-based route resolver
├── js/
│   ├── script.js
│   └── script.min.js
├── pages/
│   ├── home.php            # Hero, search, tours, visa, stats, destinations
│   ├── ingoing-tours.php   # Armenia tours with filters
│   ├── outgoing-tours.php  # International tours with filters
│   ├── transfer.php        # Transfer services with filters
│   ├── destinations.php    # All destinations grid
│   ├── destination.php     # Single destination detail
│   ├── search.php          # Flight + hotel search results
│   ├── booking.php         # Hotel booking flow
│   ├── about.php           # Why travel with us
│   ├── contact.php         # Contact form + branch info
│   ├── admin.php           # Dashboard (destinations, messages, settings)
│   ├── login.php           # Admin authentication
│   ├── logout.php          # Session termination
│   └── 404.php             # Error page
├── templates/
│   ├── header.php          # Nav, dark mode, currency, language, SEO meta
│   └── footer.php          # Footer, WhatsApp, back-to-top, cookies
├── .htaccess               # HTTPS, security headers, directory protection
├── index.php               # Entry point / router
├── manifest.json           # PWA manifest
├── sw.js                   # Service worker
├── offline.html            # Offline fallback
├── sitemap.php             # Dynamic XML sitemap
└── robots.txt
```

## Deployment

The site is deployed to **touristik.am** via Internet.am hosting (cPanel).

**Local development:**
1. Clone into `htdocs/tourism/`
2. Create `config/database.json` with MySQL credentials
3. Create `config/hotelbeds.json` with API keys
4. Visit `http://localhost/tourism/`

**Production deployment:**
1. ZIP the project files
2. Upload to `public_html` via cPanel File Manager
3. Extract and configure `config/database.json` for the server
4. Tables auto-create on first visit

## Admin Panel

Access via `/index.php?page=admin` after logging in:

- **Destinations** — Add/delete travel destinations with images
- **Bookings** — View all hotel bookings (reference, guest, hotel, dates, price, status) with stats
- **Messages** — View contact form submissions
- **Settings** — Edit site name, tagline, hero text, footer, GA tracking ID
- **Performance** — Health score, server info, file sizes, database stats, cache status, SEO/PWA checks

## Contact

- **Website:** [touristik.am](https://touristik.am)
- **Email:** info@touristik.am | touristik.visadepartment@gmail.com
- **Phone:** +374 33 060 609
- **Branches:** Komitas 38 | Mashtots 7/6 | Yerevan Mall (2nd floor)
