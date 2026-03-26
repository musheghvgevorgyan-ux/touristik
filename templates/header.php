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
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>&#9992;</text></svg>">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="page-loader" id="pageLoader">
        <div class="loader-spinner"></div>
    </div>
    <header>
        <nav>
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
