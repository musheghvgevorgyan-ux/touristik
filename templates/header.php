<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo"><a href="<?= url('home') ?>"><?= htmlspecialchars(getSetting($pdo, 'site_name', 'Touristik Travel
            ')) ?></a></div>
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
