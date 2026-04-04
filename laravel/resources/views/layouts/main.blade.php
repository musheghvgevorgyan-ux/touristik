<!DOCTYPE html>
<html lang="{{ auth()->check() ? auth()->user()->language ?? 'en' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Touristik'))</title>
    <meta name="description" content="@yield('meta_description', 'Touristik Travel - Book flights, hotels, and tour packages from Yerevan to the world. Visa support, incoming tourism programs, and 24/7 service.')">
    <meta property="og:title" content="@yield('title', config('app.name', 'Touristik'))">
    <meta property="og:description" content="@yield('meta_description', 'Explore the world with Touristik. Flights, hotels, Armenia tours, and visa support.')">
    <meta property="og:type" content="website">
    <meta property="og:image" content="@yield('og_image', 'https://touristik.am/img/og-image.jpg')">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="https://touristik.am{{ request()->getRequestUri() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="@yield('og_image', 'https://touristik.am/img/og-image.jpg')">
    @if(config('services.google.analytics_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ config('services.google.analytics_id') }}');</script>
    @endif
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>&#9992;</text></svg>">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f2027">
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
    <link rel="preconnect" href="https://plus.unsplash.com" crossorigin>
    <link rel="preconnect" href="https://photos.hotelbeds.com" crossorigin>
    <link rel="dns-prefetch" href="https://images.unsplash.com">
    <link rel="dns-prefetch" href="https://photos.hotelbeds.com">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Touristik">
    <link rel="apple-touch-icon" href="/img/icon-192.svg">
    <link rel="preload" as="image" href="/img/hero-bg.webp" type="image/webp">
    <link rel="stylesheet" href="/css/styles.min.css">
    @stack('styles')
    @verbatim
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "TravelAgency",
        "name": "Touristik Travel",
        "description": "Book flights, hotels, and tour packages from Yerevan to the world.",
        "url": "https://touristik.am",
        "telephone": ["+37433060609", "+37455060609", "+37444060608", "+37495060608"],
        "email": "touristik.visadepartment@gmail.com",
        "address": [
            {"@type": "PostalAddress", "streetAddress": "Komitas 38", "addressLocality": "Yerevan", "addressCountry": "AM"},
            {"@type": "PostalAddress", "streetAddress": "Mashtots 7/6", "addressLocality": "Yerevan", "addressCountry": "AM"},
            {"@type": "PostalAddress", "streetAddress": "Arshakunyats 34, Yerevan Mall, 2nd floor", "addressLocality": "Yerevan", "addressCountry": "AM"}
        ],
        "openingHoursSpecification": [
            {"@type": "OpeningHoursSpecification", "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"], "opens": "10:00", "closes": "20:00"},
            {"@type": "OpeningHoursSpecification", "dayOfWeek": ["Saturday","Sunday"], "opens": "11:00", "closes": "18:00"}
        ],
        "sameAs": ["https://www.instagram.com/touristik.am/","https://www.facebook.com/touristik.travell","https://t.me/touristikam"]
    }
    </script>
    @endverbatim
</head>
<body>
    <a href="#main-content" class="skip-to-content">Skip to content</a>
    <div class="page-loader" id="pageLoader" style="display:none;">
        <div class="loader-spinner"></div>
    </div>
    <header>
        <nav aria-label="Main navigation">
            <div class="logo"><a href="/"><img src="/img/logo-transparent.png" alt="{{ config('app.name', 'Touristik Travel Club') }}" class="logo-img"></a></div>
            <button class="hamburger" id="hamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
            <ul class="nav-links">
                <li class="nav-dropdown">
                    <a href="javascript:void(0)" {{ request()->is('tours*') ? 'class=active' : '' }} data-t="tours_nav">Tours <span class="nav-arrow">&#9662;</span></a>
                    <ul class="nav-submenu">
                        <li><a href="/tours/ingoing" {{ request()->is('tours/ingoing') ? 'class=active' : '' }} data-t="tour_cat_ingoing">Ingoing Tours</a></li>
                        <li><a href="/tours/outgoing" {{ request()->is('tours/outgoing') ? 'class=active' : '' }} data-t="tour_cat_outgoing">Outgoing Tours</a></li>
                        <li><a href="/tours/transfer" {{ request()->is('tours/transfer') ? 'class=active' : '' }} data-t="tour_cat_transfer">Transfer</a></li>
                    </ul>
                </li>
                <li><a href="/destinations" {{ request()->is('destinations*') ? 'class=active' : '' }} data-t="destinations">Destinations</a></li>
                <li><a href="/about" {{ request()->is('about') ? 'class=active' : '' }} data-t="about">About</a></li>
                <li><a href="/blog" {{ request()->is('blog*') ? 'class=active' : '' }}>Blog</a></li>
                <li><a href="/contact" {{ request()->is('contact') ? 'class=active' : '' }} data-t="contact">Contact</a></li>
                @auth
                    <li>@include('partials.notifications-dropdown')</li>
                    <li><a href="/account" {{ request()->is('account*') ? 'class=active' : '' }} data-t="my_account">My Account</a></li>
                    @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                        <li><a href="/admin" {{ request()->is('admin*') ? 'class=active' : '' }} data-t="admin">Admin</a></li>
                    @endif
                    @if(auth()->user()->role === 'agent')
                        <li><a href="/agent" {{ request()->is('agent*') ? 'class=active' : '' }}>Agent Portal</a></li>
                    @endif
                    <li><a href="/logout" data-t="logout">Logout</a></li>
                @endauth
                @guest
                    <li><a href="/login" {{ request()->is('login') ? 'class=active' : '' }} data-t="login">Login</a></li>
                    <li><a href="/register" class="nav-register-btn" style="background:#FF6B35;color:#fff !important;padding:0.4rem 1rem;border-radius:6px;" data-t="register">Sign Up</a></li>
                @endguest
            </ul>
            <div class="nav-controls">
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">&#9790;</button>
                <div class="currency-switcher">
                    <button class="lang-btn" id="currencyToggle">
                        <span class="lang-current" id="currencyCurrent">$ USD</span>
                        <span class="lang-arrow">&#9662;</span>
                    </button>
                    <div class="lang-dropdown" id="currencyDropdown">
                        <a href="#" class="lang-option currency-opt" data-symbol="$" data-code="USD" data-rate="1">$ USD</a>
                        <a href="#" class="lang-option currency-opt" data-symbol="&euro;" data-code="EUR" data-rate="{{ $rates['EUR'] ?? 1 }}">&euro; EUR</a>
                        <a href="#" class="lang-option currency-opt" data-symbol="&#1423;" data-code="AMD" data-rate="{{ $rates['AMD'] ?? 1 }}">&#1423; AMD</a>
                        <a href="#" class="lang-option currency-opt" data-symbol="&#8381;" data-code="RUB" data-rate="{{ $rates['RUB'] ?? 1 }}">&#8381; RUB</a>
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
        @if(session('success'))
            <script>alert("{{ session('success') }}");</script>
        @endif
        @if(session('error'))
            <div id="flashNotify" style="position:fixed;top:0;left:0;right:0;z-index:9999;padding:1rem;background:#dc3545;color:#fff;text-align:center;font-weight:600;font-size:1.05rem;box-shadow:0 2px 10px rgba(0,0,0,0.2);cursor:pointer;" onclick="this.style.display='none'">
                {{ session('error') }}
            </div>
            <script>setTimeout(function(){var e=document.getElementById('flashNotify');if(e)e.style.display='none';},5000);</script>
        @endif
        @if(session('warning'))
            <div id="flashNotify" style="position:fixed;top:0;left:0;right:0;z-index:9999;padding:1rem;background:#ffc107;color:#333;text-align:center;font-weight:600;font-size:1.05rem;box-shadow:0 2px 10px rgba(0,0,0,0.2);cursor:pointer;" onclick="this.style.display='none'">
                {{ session('warning') }}
            </div>
            <script>setTimeout(function(){var e=document.getElementById('flashNotify');if(e)e.style.display='none';},5000);</script>
        @endif
        @if($errors->any())
            <div id="flashNotify" style="position:fixed;top:0;left:0;right:0;z-index:9999;padding:1rem;background:#dc3545;color:#fff;text-align:center;font-weight:600;font-size:1.05rem;box-shadow:0 2px 10px rgba(0,0,0,0.2);cursor:pointer;" onclick="this.style.display='none'">
                @foreach($errors->all() as $error) {{ $error }} @endforeach
            </div>
            <script>setTimeout(function(){var e=document.getElementById('flashNotify');if(e)e.style.display='none';},8000);</script>
        @endif

        @yield('content')
    </main>

    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <h4 data-t="footer_branches_title">Our Branches</h4>
                <ul class="footer-list">
                    <li><span data-t="branch_1">Komitas 38</span></li>
                    <li><span data-t="branch_2">Mashtots 7/6</span></li>
                    <li><span data-t="branch_3">Arshakunyats 34 (Yerevan Mall, 2nd floor)</span></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4 data-t="footer_hours_title">Working Hours</h4>
                <ul class="footer-list">
                    <li><span data-t="hours_weekday">Mon – Fri: 10:00 – 20:00</span></li>
                    <li><span data-t="hours_weekend">Sat – Sun: 11:00 – 18:00</span></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4 data-t="footer_contact_title">Contact Us</h4>
                <ul class="footer-list">
                    <li><a href="tel:+37433060609">+374 33 060 609</a></li>
                    <li><a href="tel:+37455060609">+374 55 060 609</a></li>
                    <li><a href="tel:+37444060608">+374 44 060 608</a></li>
                    <li><a href="tel:+37495060608">+374 95 060 608</a></li>
                    <li><a href="mailto:info@touristik.am">info@touristik.am</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-social">
            <h4 data-t="footer_follow_title">Follow Us</h4>
            <div class="social-links">
                <a href="https://www.instagram.com/touristik.am/" target="_blank" rel="noopener" aria-label="Instagram">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.054 1.97.24 2.43.403a4.08 4.08 0 011.47.958c.453.453.78.898.958 1.47.163.46.35 1.26.403 2.43.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.054 1.17-.24 1.97-.403 2.43a4.08 4.08 0 01-.958 1.47 4.08 4.08 0 01-1.47.958c-.46.163-1.26.35-2.43.403-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.054-1.97-.24-2.43-.403a4.08 4.08 0 01-1.47-.958 4.08 4.08 0 01-.958-1.47c-.163-.46-.35-1.26-.403-2.43C2.175 15.584 2.163 15.204 2.163 12s.012-3.584.07-4.85c.054-1.17.24-1.97.403-2.43a4.08 4.08 0 01.958-1.47 4.08 4.08 0 011.47-.958c.46-.163 1.26-.35 2.43-.403C8.416 2.175 8.796 2.163 12 2.163M12 0C8.741 0 8.333.014 7.053.072 5.775.13 4.902.333 4.14.63a5.88 5.88 0 00-2.126 1.384A5.88 5.88 0 00.63 4.14C.333 4.902.13 5.775.072 7.053.014 8.333 0 8.741 0 12s.014 3.667.072 4.947c.058 1.278.261 2.151.558 2.913a5.88 5.88 0 001.384 2.126 5.88 5.88 0 002.126 1.384c.762.297 1.635.5 2.913.558C8.333 23.986 8.741 24 12 24s3.667-.014 4.947-.072c1.278-.058 2.151-.261 2.913-.558a5.88 5.88 0 002.126-1.384 5.88 5.88 0 001.384-2.126c.297-.762.5-1.635.558-2.913.058-1.28.072-1.688.072-4.947s-.014-3.667-.072-4.947c-.058-1.278-.261-2.151-.558-2.913a5.88 5.88 0 00-1.384-2.126A5.88 5.88 0 0019.86.63C19.098.333 18.225.13 16.947.072 15.667.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
                <a href="https://www.facebook.com/touristik.travell" target="_blank" rel="noopener" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="https://t.me/touristikam" target="_blank" rel="noopener" aria-label="Telegram">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0h-.056zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                </a>
            </div>
        </div>
        <div class="footer-bottom">
            <p data-t="footer_text">{{ config('settings.footer_text', '&copy; 2026 Touristik. All rights reserved.') }}</p>
        </div>
    </footer>

    <div class="social-float">
        <a href="https://www.instagram.com/touristik.am/" target="_blank" rel="noopener" class="social-float-btn social-instagram" aria-label="Instagram">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="#fff"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.054 1.97.24 2.43.403a4.08 4.08 0 011.47.958c.453.453.78.898.958 1.47.163.46.35 1.26.403 2.43.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.054 1.17-.24 1.97-.403 2.43a4.08 4.08 0 01-.958 1.47 4.08 4.08 0 01-1.47.958c-.46.163-1.26.35-2.43.403-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.054-1.97-.24-2.43-.403a4.08 4.08 0 01-1.47-.958 4.08 4.08 0 01-.958-1.47c-.163-.46-.35-1.26-.403-2.43C2.175 15.584 2.163 15.204 2.163 12s.012-3.584.07-4.85c.054-1.17.24-1.97.403-2.43a4.08 4.08 0 01.958-1.47 4.08 4.08 0 011.47-.958c.46-.163 1.26-.35 2.43-.403C8.416 2.175 8.796 2.163 12 2.163M12 0C8.741 0 8.333.014 7.053.072 5.775.13 4.902.333 4.14.63a5.88 5.88 0 00-2.126 1.384A5.88 5.88 0 00.63 4.14C.333 4.902.13 5.775.072 7.053.014 8.333 0 8.741 0 12s.014 3.667.072 4.947c.058 1.278.261 2.151.558 2.913a5.88 5.88 0 001.384 2.126 5.88 5.88 0 002.126 1.384c.762.297 1.635.5 2.913.558C8.333 23.986 8.741 24 12 24s3.667-.014 4.947-.072c1.278-.058 2.151-.261 2.913-.558a5.88 5.88 0 002.126-1.384 5.88 5.88 0 001.384-2.126c.297-.762.5-1.635.558-2.913.058-1.28.072-1.688.072-4.947s-.014-3.667-.072-4.947c-.058-1.278-.261-2.151-.558-2.913a5.88 5.88 0 00-1.384-2.126A5.88 5.88 0 0019.86.63C19.098.333 18.225.13 16.947.072 15.667.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
        </a>
        <a href="https://www.facebook.com/touristik.travell" target="_blank" rel="noopener" class="social-float-btn social-facebook" aria-label="Facebook">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="#fff"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
        </a>
        <a href="https://t.me/touristikam" target="_blank" rel="noopener" class="social-float-btn social-telegram" aria-label="Telegram">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="#fff"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0h-.056zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
        </a>
        <a href="https://wa.me/37433060609" target="_blank" rel="noopener" class="social-float-btn social-whatsapp" aria-label="WhatsApp">
            <svg viewBox="0 0 32 32" width="20" height="20" fill="#fff"><path d="M16.004 0C7.174 0 .002 7.172.002 16c0 2.82.737 5.572 2.137 7.998L.012 32l8.204-2.094A15.9 15.9 0 0016.004 32C24.834 32 32 24.828 32 16S24.834 0 16.004 0zm0 29.32a13.28 13.28 0 01-7.09-2.04l-.508-.303-4.87 1.244 1.302-4.706-.332-.528A13.27 13.27 0 012.68 16c0-7.348 5.976-13.32 13.324-13.32S29.32 8.652 29.32 16s-5.968 13.32-13.316 13.32zm7.296-9.976c-.4-.2-2.367-1.168-2.734-1.301-.367-.133-.634-.2-.9.2-.268.4-1.034 1.301-1.268 1.568-.234.267-.467.3-.867.1-.4-.2-1.69-.623-3.22-1.987-1.19-1.062-1.993-2.374-2.227-2.774-.233-.4-.025-.616.175-.815.18-.18.4-.467.6-.7.2-.234.267-.4.4-.667.133-.267.067-.5-.033-.7-.1-.2-.9-2.168-1.234-2.968-.325-.78-.655-.674-.9-.686l-.767-.013c-.267 0-.7.1-1.067.5s-1.4 1.368-1.4 3.335c0 1.968 1.434 3.87 1.634 4.137.2.267 2.82 4.306 6.834 6.037.955.412 1.7.658 2.28.842.959.305 1.832.262 2.522.159.77-.115 2.367-.968 2.7-1.902.334-.934.334-1.734.234-1.902-.1-.167-.367-.267-.767-.467z"/></svg>
        </a>
    </div>
    <!-- Request a Call Button -->
    <button class="callback-fab" id="callbackFab" aria-label="Request a Call" onclick="document.getElementById('callbackModal').classList.add('active')">
        <span style="font-size:22px;">&#128222;</span>
    </button>
    <div class="callback-overlay" id="callbackModal">
        <div class="callback-modal">
            <button class="callback-close" onclick="document.getElementById('callbackModal').classList.remove('active')">&times;</button>
            <h3>&#128222; Request a Call</h3>
            <p style="color:#6c757d;font-size:0.9rem;margin-bottom:1.2rem;">Leave your number and we'll call you back shortly</p>
            <div id="callbackResult" style="display:none;padding:0.8rem;border-radius:8px;margin-bottom:1rem;font-weight:600;text-align:center;font-size:0.9rem;"></div>
            <form id="callbackForm" onsubmit="return submitCallback(event)">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div style="margin-bottom:0.8rem;">
                    <input type="text" name="name" placeholder="Your name" required style="width:100%;padding:0.7rem 1rem;border:1px solid #ddd;border-radius:8px;font-size:0.95rem;">
                </div>
                <div style="margin-bottom:0.8rem;">
                    <input type="tel" name="phone" placeholder="+374 XX XXX XXX" required style="width:100%;padding:0.7rem 1rem;border:1px solid #ddd;border-radius:8px;font-size:0.95rem;">
                </div>
                <div style="margin-bottom:1rem;">
                    <input type="text" name="note" placeholder="Brief note (optional)" style="width:100%;padding:0.7rem 1rem;border:1px solid #ddd;border-radius:8px;font-size:0.95rem;">
                </div>
                <button type="submit" style="width:100%;padding:0.8rem;background:#FF6B35;color:#fff;border:none;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer;">Call Me Back</button>
            </form>
        </div>
    </div>
    <style>
        .callback-fab { position: fixed; bottom: 100px; left: 20px; width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg,#28a745,#20c997); color: #fff; border: none; cursor: pointer; box-shadow: 0 4px 20px rgba(40,167,69,0.4); z-index: 999; transition: transform 0.3s; animation: pulse-call 2s infinite; }
        .callback-fab:hover { transform: scale(1.1); }
        @keyframes pulse-call { 0%,100% { box-shadow: 0 4px 20px rgba(40,167,69,0.4); } 50% { box-shadow: 0 4px 30px rgba(40,167,69,0.7); } }
        .callback-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center; }
        .callback-overlay.active { display: flex; }
        .callback-modal { background: #fff; border-radius: 16px; padding: 2rem; max-width: 380px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); position: relative; }
        .callback-modal h3 { margin: 0 0 0.3rem; font-size: 1.3rem; color: #1a1a2e; }
        .callback-close { position: absolute; top: 12px; right: 16px; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #999; }
    </style>
    <script>
    function submitCallback(e) {
        e.preventDefault();
        var form = document.getElementById('callbackForm');
        var result = document.getElementById('callbackResult');
        var btn = form.querySelector('button[type=submit]');
        btn.disabled = true; btn.textContent = 'Sending...';
        fetch('/callback', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(function(r) { return r.json(); }).then(function(data) {
            result.style.display = 'block';
            result.style.background = '#d4edda'; result.style.color = '#155724';
            result.textContent = data.message || 'We will call you shortly!';
            form.reset();
            setTimeout(function() { document.getElementById('callbackModal').classList.remove('active'); result.style.display = 'none'; }, 3000);
        }).catch(function() {
            result.style.display = 'block';
            result.style.background = '#f8d7da'; result.style.color = '#721c24';
            result.textContent = 'Error. Please try again.';
        }).finally(function() { btn.disabled = false; btn.textContent = 'Call Me Back'; });
        return false;
    }
    </script>

    <button class="back-to-top" id="backToTop" aria-label="Back to top">&#8679;</button>

    <div class="cookie-banner" id="cookieBanner">
        <div class="cookie-content">
            <p data-t="cookie_text">We use cookies to improve your experience. By continuing to browse, you agree to our use of cookies.</p>
            <div class="cookie-actions">
                <button class="cookie-btn cookie-accept" id="cookieAccept" data-t="cookie_accept">Accept</button>
                <button class="cookie-btn cookie-decline" id="cookieDecline" data-t="cookie_decline">Decline</button>
            </div>
        </div>
    </div>

    <script src="/js/app.min.js"></script>
    <script src="/js/translations.js"></script>
    <script>
    // Make all .reveal elements visible (fix for opacity:0 blocking interactions)
    document.querySelectorAll('.reveal').forEach(function(el) { el.classList.add('visible'); });
    if ('serviceWorker' in navigator) {
        // Unregister old service workers that cache pages and block flash messages
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            registrations.forEach(function(r) { r.unregister(); });
        });
        caches.keys().then(function(names) {
            names.forEach(function(n) { caches.delete(n); });
        });
    }
    </script>
    @stack('scripts')
</body>
</html>
