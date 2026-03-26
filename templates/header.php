<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="Touristik Travel - Book flights, hotels, and tour packages from Yerevan to the world. Visa support, incoming tourism programs, and 24/7 service.">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="Explore the world with Touristik. Flights, hotels, Armenia tours, and visa support.">
    <meta property="og:type" content="website">
    <?php $gaId = getSetting($pdo, 'ga_measurement_id', ''); if ($gaId): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($gaId) ?>"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= htmlspecialchars($gaId) ?>');</script>
    <?php endif; ?>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>&#9992;</text></svg>">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0f2027">
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
    <link rel="preconnect" href="https://plus.unsplash.com" crossorigin>
    <link rel="preconnect" href="https://photos.hotelbeds.com" crossorigin>
    <link rel="preconnect" href="https://logos-world.net" crossorigin>
    <link rel="dns-prefetch" href="https://images.unsplash.com">
    <link rel="dns-prefetch" href="https://photos.hotelbeds.com">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Touristik">
    <link rel="apple-touch-icon" href="img/icon-192.svg">
    <link rel="stylesheet" href="css/styles<?= file_exists(__DIR__ . '/../css/styles.min.css') ? '.min' : '' ?>.css">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "TravelAgency",
        "name": "Touristik Travel",
        "description": "Book flights, hotels, and tour packages from Yerevan to the world. Visa support, incoming tourism programs, and 24/7 service.",
        "url": "https://touristik.am",
        "telephone": ["+37433060609", "+37455060609", "+37444060608", "+37495060608"],
        "email": "touristik.visadepartment@gmail.com",
        "address": [
            {
                "@type": "PostalAddress",
                "streetAddress": "Komitas 38",
                "addressLocality": "Yerevan",
                "addressCountry": "AM"
            },
            {
                "@type": "PostalAddress",
                "streetAddress": "Mashtots 7/6",
                "addressLocality": "Yerevan",
                "addressCountry": "AM"
            },
            {
                "@type": "PostalAddress",
                "streetAddress": "Arshakunyats 34, Yerevan Mall, 2nd floor",
                "addressLocality": "Yerevan",
                "addressCountry": "AM"
            }
        ],
        "openingHoursSpecification": [
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                "opens": "10:00",
                "closes": "20:00"
            },
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Saturday", "Sunday"],
                "opens": "11:00",
                "closes": "18:00"
            }
        ],
        "sameAs": [
            "https://www.instagram.com/touristik.am/",
            "https://www.facebook.com/touristik.am",
            "https://t.me/touristikam"
        ],
        "areaServed": "Worldwide",
        "priceRange": "$$"
    }
    </script>
</head>
<body>
    <a href="#main-content" class="skip-to-content">Skip to content</a>
    <div class="page-loader" id="pageLoader">
        <div class="loader-spinner"></div>
    </div>
    <header>
        <nav aria-label="Main navigation">
            <div class="logo"><a href="<?= url('home') ?>"><?= htmlspecialchars(getSetting($pdo, 'site_name', 'Touristik Travel
            ')) ?></a></div>
            <button class="hamburger" id="hamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
            <ul class="nav-links">
                <li><a href="<?= url('home') ?>" <?= $currentPage === 'home' ? 'class="active"' : '' ?> data-t="home">Home</a></li>
                <li><a href="<?= url('destinations') ?>" <?= $currentPage === 'destinations' ? 'class="active"' : '' ?> data-t="destinations">Destinations</a></li>
                <li><a href="<?= url('about') ?>" <?= $currentPage === 'about' ? 'class="active"' : '' ?> data-t="about">About</a></li>
                <li><a href="<?= url('contact') ?>" <?= $currentPage === 'contact' ? 'class="active"' : '' ?> data-t="contact">Contact</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="<?= url('admin') ?>" <?= $currentPage === 'admin' ? 'class="active"' : '' ?> data-t="admin">Admin</a></li>
                    <li><a href="<?= url('logout') ?>" data-t="logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?= url('login') ?>" <?= $currentPage === 'login' ? 'class="active"' : '' ?> data-t="login">Login</a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-controls">
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">&#9790;</button>
                <div class="currency-switcher">
                    <button class="lang-btn" id="currencyToggle">
                        <span class="lang-current" id="currencyCurrent">$ USD</span>
                        <span class="lang-arrow">&#9662;</span>
                    </button>
                    <?php $rates = getCurrencyRates(); ?>
                    <div class="lang-dropdown" id="currencyDropdown">
                        <a href="#" class="lang-option currency-opt" data-symbol="$" data-code="USD" data-rate="1">$ USD</a>
                        <a href="#" class="lang-option currency-opt" data-symbol="€" data-code="EUR" data-rate="<?= $rates['EUR'] ?>">€ EUR</a>
                        <a href="#" class="lang-option currency-opt" data-symbol="֏" data-code="AMD" data-rate="<?= $rates['AMD'] ?>">֏ AMD</a>
                        <a href="#" class="lang-option currency-opt" data-symbol="₽" data-code="RUB" data-rate="<?= $rates['RUB'] ?>">₽ RUB</a>
                    </div>
                </div>
                <div class="lang-switcher">
                    <button class="lang-btn" id="langToggle">
                        <span class="lang-current" id="langCurrent">EN</span>
                        <span class="lang-arrow">&#9662;</span>
                    </button>
                    <div class="lang-dropdown" id="langDropdown">
                        <a href="#" class="lang-option" data-lang="en">&#127468;&#127463; English</a>
                        <a href="#" class="lang-option" data-lang="ru">&#127479;&#127482; Русский</a>
                        <a href="#" class="lang-option" data-lang="hy">&#127462;&#127474; Հայերեն</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main id="main-content" role="main">
