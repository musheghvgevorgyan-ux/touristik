# Touristik - Travel Booking Platform

A full-featured tourism and travel booking website built with PHP, MySQL, and JavaScript. Search flights, explore destinations, and book trips — all in one place.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?logo=javascript&logoColor=black)
![License](https://img.shields.io/badge/License-MIT-green)

## Features

### Flight Search
- Real-time flight search powered by **Travelpayouts API**
- Multiple API endpoints with fallback chain (cheap, direct, latest)
- Flight duration and stops display with visual indicators
- Color-coded stops (green = direct, orange = layovers)

### Destinations
- Browse destinations with live flight prices
- Detailed destination pages with hero images
- Price caching (24h) for optimal performance

### Multi-Language Support
- Full translation in **English**, **Russian**, and **Armenian**
- Language preference persists across pages via localStorage
- Covers all UI elements including admin dashboard

### Multi-Currency Support
- Real-time exchange rates from open.er-api.com
- Supports **USD**, **EUR**, **AMD**, **RUB**
- Rates cached for 6 hours
- All prices convert instantly on currency switch

### Booking System
- "Book Now" opens a confirmation modal
- Translated booking confirmations in all 3 languages
- Contact form with email notification support

### Admin Dashboard
- Manage destinations (add/delete)
- View contact messages
- Update site settings
- Secure login authentication
- Fully translated admin interface

## Tech Stack

| Component | Technology |
|-----------|-----------|
| Backend | PHP 8.x |
| Database | MySQL 5.7+ |
| Frontend | HTML5, CSS3, JavaScript (ES6) |
| Flight Data | Travelpayouts API |
| Currency Rates | open.er-api.com |
| Server | Apache (XAMPP) |

## Project Structure

```
touristik/
├── api/                  # API endpoints
│   ├── get_price.php     # Flight price API
│   └── get_rates.php     # Currency rates API
├── config/
│   ├── routes.json       # URL routing config
│   ├── database.json     # DB credentials (gitignored)
│   └── travelpayouts.json # API key (gitignored)
├── css/
│   └── styles.css        # All styles, responsive design
├── includes/
│   ├── db.php            # Database connection
│   ├── functions.php     # Core functions (CRUD, auth, settings)
│   ├── router.php        # URL routing engine
│   ├── currency.php      # Live currency rate fetching & caching
│   └── flight_prices.php # Travelpayouts API integration
├── js/
│   └── script.js         # Frontend logic, translations, currency conversion
├── pages/
│   ├── home.php          # Homepage with featured destinations
│   ├── destinations.php  # All destinations grid
│   ├── destination.php   # Single destination detail
│   ├── search.php        # Flight search & results
│   ├── about.php         # About page
│   ├── contact.php       # Contact form
│   ├── admin.php         # Admin dashboard
│   ├── login.php         # Admin login
│   ├── logout.php        # Logout handler
│   └── 404.php           # Not found page
├── templates/
│   ├── header.php        # Header with nav, language & currency switchers
│   └── footer.php        # Footer with booking modal
├── index.php             # Entry point & router
└── .gitignore
```

## Setup

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Travelpayouts API token

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/musheghvgevorgyan-ux/touristik.git
   ```

2. Move to your XAMPP htdocs:
   ```bash
   cp -r touristik /xampp/htdocs/tourism
   ```

3. Create the database config (`config/database.json`):
   ```json
   {
     "host": "localhost",
     "name": "tourism",
     "user": "root",
     "pass": ""
   }
   ```

4. Create the API config (`config/travelpayouts.json`):
   ```json
   {
     "token": "YOUR_TRAVELPAYOUTS_TOKEN"
   }
   ```

5. Import the database schema and start Apache + MySQL in XAMPP.

6. Open `http://localhost/tourism` in your browser.

### Admin Access
- URL: `http://localhost/tourism/?page=login`
- Default credentials are configured in the database

## Screenshots

> Add screenshots of your running application here

## License

MIT License - feel free to use this project for learning and development.

## Author

**Mushegh Gevorgyan** - [GitHub](https://github.com/musheghvgevorgyan-ux)
