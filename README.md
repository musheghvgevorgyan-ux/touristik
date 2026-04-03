# Touristik Travel Platform

**Live site: [touristik.am](https://touristik.am)**

Full-featured travel booking platform for **Touristik LLC** — a travel agency based in Yerevan, Armenia with 3 branches.

## Tech Stack

- **Framework:** Laravel 12 (PHP 8.2+)
- **Database:** MySQL 8 with 17+ tables
- **Frontend:** Blade templates + custom CSS (4,400+ lines) + vanilla JS
- **API Integration:** Hotelbeds (hotel search & booking)
- **Architecture:** MVC with Service Layer + Supplier Abstraction

## Features

### Public Site
- Hotel search with real-time Hotelbeds API integration
- Tour listings (Ingoing Armenia, Outgoing, Transfer)
- Destination pages with images and pricing
- Contact form with database storage
- Multi-language support (EN, RU, HY)
- Multi-currency display (USD, EUR, AMD, RUB)
- Dark mode toggle
- PWA support (offline page, manifest)
- SEO optimized (Schema.org, Open Graph, sitemap)
- Responsive design (mobile-first)

### Booking System
- Full booking pipeline: Search → Select → Guest Details → Confirm → Voucher
- Hotelbeds CheckRate + Book API integration
- Payment gateway abstraction (sandbox + office + future real gateways)
- Printable HTML vouchers (Hotelbeds certification compliant)
- Booking reference generation (TK-YYMMDD-XXX format)
- Email notifications (booking confirmation, cancellation, admin alerts)

### Customer Accounts
- Registration & login with password reset
- Customer dashboard with booking history
- Profile management (name, phone, language, currency preferences)
- Wishlist (save hotels, tours, destinations)
- Reviews & ratings (with admin moderation)
- In-app notifications (bell icon with unread count)

### Admin Panel (`/admin`)
- Dashboard with stats (users, bookings, revenue)
- Booking management (view, filter, cancel)
- User management (roles: customer/agent/admin/superadmin, suspend/activate)
- Destination CRUD (name, slug, description, image, featured)
- Tour management (ingoing/outgoing/transfer, itinerary editor)
- Site settings (hero text, contact info, GA4, maintenance mode)
- Promo code management (percentage/fixed, usage limits, date ranges)
- Review moderation (approve/reject/reply)
- Reports (revenue, top destinations, user growth)

### B2B Agent Portal (`/agent`)
- Agency registration with admin approval
- Agent dashboard (bookings, commission, balance)
- Hotel search with NET pricing (agent sees wholesale prices)
- Commission tracking (markup/percentage/prepaid models)
- Monthly commission breakdown
- Per-booking commission reports

## Database Schema (17 tables)

| Table | Purpose |
|-------|---------|
| users | Customers, agents, admins (role-based) |
| agencies | B2B travel agency accounts |
| bookings | Unified bookings (hotel, flight, tour, transfer) |
| payments | Payment transactions with gateway abstraction |
| destinations | Travel destinations with images |
| tours | Tour packages (ingoing/outgoing/transfer) |
| contacts | Contact form submissions |
| flight_prices | Flight pricing data |
| settings | Site configuration key-value store |
| activity_log | Audit trail for all actions |
| notifications | In-app user notifications |
| invoices | B2B agency invoices |
| promo_codes | Discount/promo codes |
| promo_usage | Promo code usage tracking |
| wishlists | User saved items |
| reviews | Product reviews with moderation |
| password_reset_tokens | Password reset flow |

## Supplier Abstraction

The platform uses a supplier interface pattern allowing any travel API to be plugged in:

```php
interface SupplierInterface {
    public function search(array $params): array;
    public function checkRate(string $rateKey): array;
    public function book(array $details): array;
    public function cancel(string $reference): array;
    public function getBooking(string $reference): array;
}
```

Currently implemented: **Hotelbeds** (800,000+ hotels worldwide)
Ready for: Amadeus (flights), local tour operators, transfer services

## Setup

### Requirements
- PHP 8.2+
- MySQL 8+
- Composer
- Apache with mod_rewrite

### Installation

```bash
# Clone the repository
git clone https://github.com/musheghvgeorgyan/touristik.git
cd touristik/laravel

# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Edit .env with your database and API credentials
# DB_DATABASE=touristik_laravel
# HOTELBEDS_API_KEY=your_key
# HOTELBEDS_API_SECRET=your_secret

# Run migrations and seed
php artisan migrate
php artisan db:seed

# Default admin: admin@touristik.am / admin123
```

### Local Development

```bash
php artisan serve
# Visit http://localhost:8000
```

### Apache Virtual Host

```apache
<VirtualHost *:80>
    DocumentRoot "/path/to/touristik/laravel/public"
    ServerName touristik.local
    <Directory "/path/to/touristik/laravel/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Project Structure

```
laravel/
├── app/
│   ├── Http/Controllers/       # 29 controllers (Public, Admin, Agent, API)
│   ├── Models/                 # 16 Eloquent models with relationships
│   └── Services/               # 12 services (Booking, Payment, Email, etc.)
│       └── Suppliers/          # Supplier abstraction (Hotelbeds adapter)
├── config/
│   ├── suppliers.php           # API credentials
│   └── payment.php             # Payment gateway config
├── database/
│   ├── migrations/             # 18 migration files
│   └── seeders/                # Admin, destinations, tours, settings
├── resources/views/            # 45+ Blade templates
│   ├── layouts/main.blade.php  # Main layout with header/footer
│   ├── admin/                  # 11 admin views
│   ├── agent/                  # 4 agent portal views
│   ├── auth/                   # 5 auth views
│   └── ...                     # Public page views
└── public/
    ├── css/styles.css          # 4,400+ lines of custom CSS
    ├── js/app.js               # 1,600+ lines of vanilla JS
    └── img/                    # Site images and icons
```

## Branches

| Branch | Description |
|--------|-------------|
| `master` | Original vanilla PHP site (live at touristik.am) |
| `platform-v2` | Custom MVC platform (140 PHP files, intermediate step) |
| `laravel` | **Laravel 12 platform (production-ready)** |

## Company

**Touristik LLC** — Travel Agency, Yerevan, Armenia

- **Branches:** Komitas 38 | Mashtots 7/6 | Yerevan Mall (2nd floor)
- **Phone:** +374 33 060 609, +374 55 060 609, +374 44 060 608
- **Email:** info@touristik.am
- **Social:** [Instagram](https://instagram.com/touristik.am) | [Facebook](https://facebook.com/touristik.travell) | [Telegram](https://t.me/touristikam)
