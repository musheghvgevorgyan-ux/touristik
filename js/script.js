// Forward declaration so currency code can reference it before full definition
var translations = {};

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Navbar background on scroll
window.addEventListener('scroll', () => {
    const header = document.querySelector('header');
    if (window.scrollY > 50) {
        header.style.background = 'rgba(0, 0, 0, 0.95)';
    } else {
        header.style.background = 'rgba(0, 0, 0, 0.85)';
    }
});

// Trip type toggle (One Way / Round Trip / Packages)
document.querySelectorAll('.trip-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.trip-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const type = this.dataset.type;
        const tripInput = document.getElementById('trip-type');
        const returnField = document.querySelector('.return-field');
        const departField = document.querySelector('.depart-field');

        if (tripInput) tripInput.value = type;

        document.querySelectorAll('.persons-field').forEach(f => f.style.display = '');
        const flightFields = document.querySelectorAll('.flight-field');
        const packageFields = document.querySelectorAll('.package-field');

        if (type === 'oneway') {
            if (returnField) { returnField.style.display = 'none'; }
            if (departField) { departField.style.display = ''; }
            flightFields.forEach(f => f.style.display = '');
            packageFields.forEach(f => f.style.display = 'none');
        } else if (type === 'packages') {
            if (returnField) { returnField.style.display = ''; }
            if (departField) { departField.style.display = ''; }
            flightFields.forEach(f => f.style.display = 'none');
            packageFields.forEach(f => f.style.display = '');
        } else {
            if (returnField) { returnField.style.display = ''; }
            if (departField) { departField.style.display = ''; }
            flightFields.forEach(f => f.style.display = '');
            packageFields.forEach(f => f.style.display = 'none');
        }
    });
});

// Live price lookup
(function () {
    const form = document.querySelector('.hero-search');
    if (!form) return;

    const priceBox = document.getElementById('searchPrice');
    const priceValue = document.getElementById('searchPriceValue');
    if (!priceBox || !priceValue) return;

    function fetchPrice() {
        const tripType = document.getElementById('trip-type').value;
        let from, to;

        if (tripType === 'packages') {
            const fromSel = form.querySelector('select[name="pkg_from"]');
            const toSel = form.querySelector('select[name="pkg_to"]');
            from = fromSel ? fromSel.value : '';
            to = toSel ? toSel.value.split(',')[0].trim() : '';
        } else {
            const fromSel = form.querySelector('.flight-field select[name="from"]');
            const toSel = form.querySelector('.flight-field select[name="to"]');
            from = fromSel ? fromSel.value : '';
            to = toSel ? toSel.value : '';
        }

        const type = (tripType === 'oneway') ? 'oneway' : 'roundtrip';

        if (!from || !to || from === to) {
            priceBox.style.display = 'none';
            return;
        }

        const dateInput = form.querySelector('input[name="date"]');
        const depart = dateInput ? dateInput.value : '';

        let url = 'api/get_price.php?from=' + encodeURIComponent(from) + '&to=' + encodeURIComponent(to) + '&type=' + type;
        if (depart) url += '&depart=' + depart;

        priceValue.textContent = '...';
        priceBox.style.display = '';

        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (data.found) {
                    const symbol = localStorage.getItem('currSymbol') || '$';
                    const rate = parseFloat(localStorage.getItem('currRate')) || 1;
                    const converted = Math.round(data.price * rate);
                    priceValue.textContent = symbol + converted.toLocaleString();
                    priceBox.style.display = '';
                } else {
                    priceBox.style.display = 'none';
                }
            })
            .catch(() => { priceBox.style.display = 'none'; });
    }

    // Listen to all selects and date inputs in the form
    form.querySelectorAll('select, input[type="date"]').forEach(el => {
        el.addEventListener('change', fetchPrice);
    });

    // Also refetch when trip type changes
    document.querySelectorAll('.trip-btn').forEach(btn => {
        btn.addEventListener('click', () => setTimeout(fetchPrice, 100));
    });

    // Initial fetch
    fetchPrice();
})();

// Currency switcher
(function () {
    const toggle = document.getElementById('currencyToggle');
    const dropdown = document.getElementById('currencyDropdown');
    const current = document.getElementById('currencyCurrent');
    if (!toggle || !dropdown) return;

    // Load saved currency
    const savedSymbol = localStorage.getItem('currSymbol') || '$';
    const savedCode = localStorage.getItem('currCode') || 'USD';
    const savedRate = parseFloat(localStorage.getItem('currRate')) || 1;
    current.textContent = savedSymbol + ' ' + savedCode;
    highlightCurrency(savedCode);
    if (savedCode !== 'USD') convertPrices(savedSymbol, savedRate);

    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('open');
        // Close language dropdown
        const langDD = document.getElementById('langDropdown');
        if (langDD) langDD.classList.remove('open');
    });

    document.addEventListener('click', function () {
        dropdown.classList.remove('open');
    });

    dropdown.querySelectorAll('.currency-opt').forEach(opt => {
        opt.addEventListener('click', function (e) {
            e.preventDefault();
            const symbol = this.dataset.symbol;
            const code = this.dataset.code;
            const rate = parseFloat(this.dataset.rate);

            localStorage.setItem('currSymbol', symbol);
            localStorage.setItem('currCode', code);
            localStorage.setItem('currRate', rate);

            current.textContent = symbol + ' ' + code;
            dropdown.classList.remove('open');
            highlightCurrency(code);

            // Reconvert from USD base prices
            document.querySelectorAll('.price').forEach(el => {
                const base = el.dataset.basePrice;
                if (base) {
                    const lang = localStorage.getItem('lang') || 'en';
                    const fromLabel = translations[lang] && translations[lang].price_from ? translations[lang].price_from : 'From';
                    const converted = Math.round(parseFloat(base) * rate);
                    el.textContent = fromLabel + ' ' + symbol + converted.toLocaleString();
                }
            });

            // Reconvert flight prices
            document.querySelectorAll('.flight-price-value').forEach(el => {
                const base = el.dataset.basePrice;
                if (base) {
                    const converted = Math.round(parseFloat(base) * rate);
                    el.textContent = symbol + converted.toLocaleString();
                }
            });
            document.querySelectorAll('.flight-price-total').forEach(el => {
                const base = el.dataset.baseTotal;
                if (base) {
                    const converted = Math.round(parseFloat(base) * rate);
                    const lang = localStorage.getItem('lang') || 'en';
                    const totalLabel = translations[lang] && translations[lang].total ? translations[lang].total : 'total';
                    el.textContent = symbol + converted.toLocaleString() + ' ' + totalLabel;
                }
            });
        });
    });

    function highlightCurrency(code) {
        dropdown.querySelectorAll('.currency-opt').forEach(o => {
            o.classList.toggle('selected', o.dataset.code === code);
        });
    }

    function convertPrices(symbol, rate) {
        document.querySelectorAll('.price').forEach(el => {
            const base = el.dataset.basePrice;
            if (base) {
                const lang = localStorage.getItem('lang') || 'en';
                const fromLabel = translations[lang] && translations[lang].price_from ? translations[lang].price_from : 'From';
                const converted = Math.round(parseFloat(base) * rate);
                el.textContent = fromLabel + ' ' + symbol + converted.toLocaleString();
            }
        });
    }
})();

// Store base prices for currency conversion
document.querySelectorAll('.price').forEach(el => {
    if (!el.dataset.basePrice) {
        const match = el.textContent.match(/[\d,]+/);
        if (match) {
            el.dataset.basePrice = match[0].replace(/,/g, '');
        }
    }
    const symbol = localStorage.getItem('currSymbol');
    const rate = parseFloat(localStorage.getItem('currRate'));
    if (symbol && rate && el.dataset.basePrice) {
        const lang = localStorage.getItem('lang') || 'en';
        const fromLabel = translations[lang] && translations[lang].price_from ? translations[lang].price_from : 'From';
        const converted = Math.round(parseFloat(el.dataset.basePrice) * rate);
        el.textContent = fromLabel + ' ' + symbol + converted.toLocaleString();
    }
});

// Convert flight search result prices
document.querySelectorAll('.flight-price-value').forEach(el => {
    const base = el.dataset.basePrice;
    if (base) {
        const symbol = localStorage.getItem('currSymbol') || '$';
        const rate = parseFloat(localStorage.getItem('currRate')) || 1;
        const converted = Math.round(parseFloat(base) * rate);
        el.textContent = symbol + converted.toLocaleString();
    }
});
document.querySelectorAll('.flight-price-total').forEach(el => {
    const match = el.textContent.match(/[\d,]+/);
    if (match) {
        const base = parseFloat(match[0].replace(/,/g, ''));
        el.dataset.baseTotal = base;
        const symbol = localStorage.getItem('currSymbol') || '$';
        const rate = parseFloat(localStorage.getItem('currRate')) || 1;
        const converted = Math.round(base * rate);
        const lang = localStorage.getItem('lang') || 'en';
        const totalLabel = translations[lang] && translations[lang].total ? translations[lang].total : 'total';
        el.textContent = symbol + converted.toLocaleString() + ' ' + totalLabel;
    }
});

// ==================== TRANSLATIONS ====================
translations = {
    en: {
        // Nav
        home: 'Home', destinations: 'Destinations', about: 'About', contact: 'Contact',
        login: 'Login', logout: 'Logout', admin: 'Admin',
        // Hero
        hero_title: 'Explore the World with Touristik',
        hero_subtitle: 'Discover breathtaking destinations and create unforgettable memories',
        roundtrip: 'Round Trip', oneway: 'One Way', packages: 'Packages',
        from: 'From', to: 'To', depart: 'Depart', return_date: 'Return',
        adults: 'Adults', children: 'Children',
        price_from: 'from',
        search: 'Search',
        // Home
        popular: 'Popular Destinations', why_travel: 'Why Travel With Us',
        best_flights: 'Best Flights', top_hotels: 'Top Hotels', easy_booking: 'Easy Booking',
        best_flights_desc: 'We partner with top airlines to get you the best deals on flights worldwide.',
        top_hotels_desc: 'Hand-picked accommodations ranging from cozy boutiques to luxury resorts.',
        easy_booking_desc: 'Simple and secure booking process with flexible cancellation policies.',
        // About
        about_title: 'About Touristik',
        about_p1: 'We are passionate travelers who believe that exploring the world should be accessible, enjoyable, and unforgettable. Our team hand-picks every destination, hotel, and experience to ensure you get the very best.',
        about_p2: 'With years of experience in the travel industry, we\'ve helped thousands of adventurers discover their dream destinations. Whether you\'re looking for a relaxing beach getaway or an action-packed city adventure, we\'ve got you covered.',
        // Destinations
        all_destinations: 'All Destinations',
        no_destinations: 'No destinations available yet.',
        view_dest: 'View Destinations',
        // Destination detail
        book_now: 'Book Now',
        back_dest: 'Back to Destinations',
        // Contact
        get_in_touch: 'Get In Touch',
        your_name: 'Your Name', your_email: 'Your Email',
        dream_trip: 'Tell us about your dream trip...', send: 'Send Message',
        contact_success: 'Thank you! We will get back to you soon.',
        contact_error: 'Please fill in all fields correctly.',
        // Login
        admin_login: 'Admin Login',
        username: 'Username', password: 'Password',
        login_error: 'Invalid username or password.',
        // 404
        page_not_found: 'Oops! The page you\'re looking for doesn\'t exist.',
        go_home: 'Go Home',
        // Search results
        new_search: '\u2190 New Search',
        best_price: 'Best Price',
        per_person: 'per person',
        total: 'total',
        book_now_arrow: 'Book Now \u2192',
        // Destinations content
        dest_name_1: 'Paris, France',
        dest_desc_1: 'The City of Light awaits with iconic landmarks, world-class cuisine, and timeless romance.',
        dest_name_2: 'Tokyo, Japan',
        dest_desc_2: 'Experience the perfect blend of ancient tradition and cutting-edge modernity.',
        dest_name_3: 'Bali, Indonesia',
        dest_desc_3: 'Tropical paradise with stunning temples, lush rice terraces, and pristine beaches.',
        dest_name_4: 'Rome, Italy',
        dest_desc_4: 'Walk through millennia of history among ancient ruins, fountains, and piazzas.',
        dest_name_5: 'New York, USA',
        dest_desc_5: 'The city that never sleeps offers endless entertainment, dining, and culture.',
        dest_name_6: 'Maldives',
        dest_desc_6: 'Crystal-clear waters, overwater villas, and unforgettable sunsets await you.',
        // Admin
        admin_dashboard: 'Admin Dashboard',
        messages: 'Messages', settings: 'Settings',
        add_new_dest: 'Add New Destination', existing_dest: 'Existing Destinations',
        dest_name_ph: 'Destination Name', description_ph: 'Description', price_ph: 'Price',
        add_dest_btn: 'Add Destination', delete_btn: 'Delete',
        dest_added: 'Destination added successfully.',
        dest_deleted: 'Destination deleted.',
        fill_fields: 'Please fill in all fields correctly.',
        settings_saved: 'Settings updated successfully.',
        contact_messages: 'Contact Messages', no_messages: 'No messages yet.',
        site_settings: 'Site Settings', save_settings_btn: 'Save Settings',
        th_name: 'Name', th_price: 'Price', th_actions: 'Actions',
        th_email: 'Email', th_message: 'Message', th_date: 'Date',
        // Booking modal
        modal_title: 'Thank You!',
        modal_text: 'We have received your request. Our team will contact you shortly to finalize your booking.',
        modal_sub: 'Please make sure your contact details are up to date.',
        modal_ok: 'OK',
        // Footer
        footer_text: '\u00a9 2026 Touristik. All rights reserved.'
    },
    ru: {
        // Nav
        home: '\u0413\u043b\u0430\u0432\u043d\u0430\u044f', destinations: '\u041d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f', about: '\u041e \u043d\u0430\u0441', contact: '\u041a\u043e\u043d\u0442\u0430\u043a\u0442\u044b',
        login: '\u0412\u043e\u0439\u0442\u0438', logout: '\u0412\u044b\u0439\u0442\u0438', admin: '\u0410\u0434\u043c\u0438\u043d',
        // Hero
        hero_title: '\u0418\u0441\u0441\u043b\u0435\u0434\u0443\u0439 \u043c\u0438\u0440 \u0441 Touristik',
        hero_subtitle: '\u041e\u0442\u043a\u0440\u043e\u0439\u0442\u0435 \u0434\u043b\u044f \u0441\u0435\u0431\u044f \u0437\u0430\u0445\u0432\u0430\u0442\u044b\u0432\u0430\u044e\u0449\u0438\u0435 \u043d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f \u0438 \u0441\u043e\u0437\u0434\u0430\u0439\u0442\u0435 \u043d\u0435\u0437\u0430\u0431\u044b\u0432\u0430\u0435\u043c\u044b\u0435 \u0432\u043e\u0441\u043f\u043e\u043c\u0438\u043d\u0430\u043d\u0438\u044f',
        roundtrip: '\u0422\u0443\u0434\u0430-\u043e\u0431\u0440\u0430\u0442\u043d\u043e', oneway: '\u0412 \u043e\u0434\u043d\u0443 \u0441\u0442\u043e\u0440\u043e\u043d\u0443', packages: '\u041f\u0430\u043a\u0435\u0442\u044b',
        from: '\u041e\u0442\u043a\u0443\u0434\u0430', to: '\u041a\u0443\u0434\u0430', depart: '\u0412\u044b\u043b\u0435\u0442', return_date: '\u0412\u043e\u0437\u0432\u0440\u0430\u0442',
        adults: '\u0412\u0437\u0440\u043e\u0441\u043b\u044b\u0435', children: '\u0414\u0435\u0442\u0438',
        price_from: '\u043e\u0442',
        search: '\u041f\u043e\u0438\u0441\u043a',
        // Home
        popular: '\u041f\u043e\u043f\u0443\u043b\u044f\u0440\u043d\u044b\u0435 \u043d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f', why_travel: '\u041f\u043e\u0447\u0435\u043c\u0443 \u0432\u044b\u0431\u0438\u0440\u0430\u044e\u0442 \u043d\u0430\u0441',
        best_flights: '\u041b\u0443\u0447\u0448\u0438\u0435 \u0440\u0435\u0439\u0441\u044b', top_hotels: '\u041b\u0443\u0447\u0448\u0438\u0435 \u043e\u0442\u0435\u043b\u0438', easy_booking: '\u041f\u0440\u043e\u0441\u0442\u043e\u0435 \u0431\u0440\u043e\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u0435',
        best_flights_desc: '\u041c\u044b \u0441\u043e\u0442\u0440\u0443\u0434\u043d\u0438\u0447\u0430\u0435\u043c \u0441 \u043b\u0443\u0447\u0448\u0438\u043c\u0438 \u0430\u0432\u0438\u0430\u043a\u043e\u043c\u043f\u0430\u043d\u0438\u044f\u043c\u0438 \u0434\u043b\u044f \u0432\u044b\u0433\u043e\u0434\u043d\u044b\u0445 \u0440\u0435\u0439\u0441\u043e\u0432 \u043f\u043e \u0432\u0441\u0435\u043c\u0443 \u043c\u0438\u0440\u0443.',
        top_hotels_desc: '\u041e\u0442\u043e\u0431\u0440\u0430\u043d\u043d\u044b\u0435 \u0432\u0440\u0443\u0447\u043d\u0443\u044e \u043e\u0442\u0435\u043b\u0438 \u2014 \u043e\u0442 \u0443\u044e\u0442\u043d\u044b\u0445 \u0431\u0443\u0442\u0438\u043a-\u043e\u0442\u0435\u043b\u0435\u0439 \u0434\u043e \u0440\u043e\u0441\u043a\u043e\u0448\u043d\u044b\u0445 \u043a\u0443\u0440\u043e\u0440\u0442\u043e\u0432.',
        easy_booking_desc: '\u041f\u0440\u043e\u0441\u0442\u043e\u0439 \u0438 \u0431\u0435\u0437\u043e\u043f\u0430\u0441\u043d\u044b\u0439 \u043f\u0440\u043e\u0446\u0435\u0441\u0441 \u0431\u0440\u043e\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044f \u0441 \u0433\u0438\u0431\u043a\u0438\u043c\u0438 \u0443\u0441\u043b\u043e\u0432\u0438\u044f\u043c\u0438 \u043e\u0442\u043c\u0435\u043d\u044b.',
        // About
        about_title: '\u041e Touristik',
        about_p1: '\u041c\u044b \u2014 \u0441\u0442\u0440\u0430\u0441\u0442\u043d\u044b\u0435 \u043f\u0443\u0442\u0435\u0448\u0435\u0441\u0442\u0432\u0435\u043d\u043d\u0438\u043a\u0438, \u043a\u043e\u0442\u043e\u0440\u044b\u0435 \u0432\u0435\u0440\u044f\u0442, \u0447\u0442\u043e \u0438\u0441\u0441\u043b\u0435\u0434\u043e\u0432\u0430\u043d\u0438\u0435 \u043c\u0438\u0440\u0430 \u0434\u043e\u043b\u0436\u043d\u043e \u0431\u044b\u0442\u044c \u0434\u043e\u0441\u0442\u0443\u043f\u043d\u044b\u043c, \u043f\u0440\u0438\u044f\u0442\u043d\u044b\u043c \u0438 \u043d\u0435\u0437\u0430\u0431\u044b\u0432\u0430\u0435\u043c\u044b\u043c. \u041d\u0430\u0448\u0430 \u043a\u043e\u043c\u0430\u043d\u0434\u0430 \u0442\u0449\u0430\u0442\u0435\u043b\u044c\u043d\u043e \u043f\u043e\u0434\u0431\u0438\u0440\u0430\u0435\u0442 \u043a\u0430\u0436\u0434\u043e\u0435 \u043d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u0435, \u043e\u0442\u0435\u043b\u044c \u0438 \u0432\u043f\u0435\u0447\u0430\u0442\u043b\u0435\u043d\u0438\u0435.',
        about_p2: '\u0411\u043b\u0430\u0433\u043e\u0434\u0430\u0440\u044f \u043c\u043d\u043e\u0433\u043e\u043b\u0435\u0442\u043d\u0435\u043c\u0443 \u043e\u043f\u044b\u0442\u0443 \u0432 \u0442\u0443\u0440\u0438\u0441\u0442\u0438\u0447\u0435\u0441\u043a\u043e\u0439 \u0438\u043d\u0434\u0443\u0441\u0442\u0440\u0438\u0438, \u043c\u044b \u043f\u043e\u043c\u043e\u0433\u043b\u0438 \u0442\u044b\u0441\u044f\u0447\u0430\u043c \u043f\u0443\u0442\u0435\u0448\u0435\u0441\u0442\u0432\u0435\u043d\u043d\u0438\u043a\u043e\u0432 \u043d\u0430\u0439\u0442\u0438 \u043d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u0435 \u0438\u0445 \u043c\u0435\u0447\u0442\u044b. \u0411\u0443\u0434\u044c \u0442\u043e \u0440\u0430\u0441\u0441\u043b\u0430\u0431\u043b\u044f\u044e\u0449\u0438\u0439 \u043f\u043b\u044f\u0436\u043d\u044b\u0439 \u043e\u0442\u0434\u044b\u0445 \u0438\u043b\u0438 \u043d\u0430\u0441\u044b\u0449\u0435\u043d\u043d\u043e\u0435 \u0433\u043e\u0440\u043e\u0434\u0441\u043a\u043e\u0435 \u043f\u0440\u0438\u043a\u043b\u044e\u0447\u0435\u043d\u0438\u0435 \u2014 \u043c\u044b \u043f\u043e\u043c\u043e\u0436\u0435\u043c.',
        // Destinations
        all_destinations: '\u0412\u0441\u0435 \u043d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f',
        no_destinations: '\u041d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f \u043f\u043e\u043a\u0430 \u043d\u0435\u0434\u043e\u0441\u0442\u0443\u043f\u043d\u044b.',
        view_dest: '\u0421\u043c\u043e\u0442\u0440\u0435\u0442\u044c \u043d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f',
        // Destination detail
        book_now: '\u0417\u0430\u0431\u0440\u043e\u043d\u0438\u0440\u043e\u0432\u0430\u0442\u044c',
        back_dest: '\u041d\u0430\u0437\u0430\u0434 \u043a \u043d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f\u043c',
        // Contact
        get_in_touch: '\u0421\u0432\u044f\u0436\u0438\u0442\u0435\u0441\u044c \u0441 \u043d\u0430\u043c\u0438',
        your_name: '\u0412\u0430\u0448\u0435 \u0438\u043c\u044f', your_email: '\u0412\u0430\u0448 email',
        dream_trip: '\u0420\u0430\u0441\u0441\u043a\u0430\u0436\u0438\u0442\u0435 \u043e \u043f\u0443\u0442\u0435\u0448\u0435\u0441\u0442\u0432\u0438\u0438 \u0432\u0430\u0448\u0435\u0439 \u043c\u0435\u0447\u0442\u044b...', send: '\u041e\u0442\u043f\u0440\u0430\u0432\u0438\u0442\u044c',
        contact_success: '\u0421\u043f\u0430\u0441\u0438\u0431\u043e! \u041c\u044b \u0441\u0432\u044f\u0436\u0435\u043c\u0441\u044f \u0441 \u0432\u0430\u043c\u0438 \u0432 \u0431\u043b\u0438\u0436\u0430\u0439\u0448\u0435\u0435 \u0432\u0440\u0435\u043c\u044f.',
        contact_error: '\u041f\u043e\u0436\u0430\u043b\u0443\u0439\u0441\u0442\u0430, \u0437\u0430\u043f\u043e\u043b\u043d\u0438\u0442\u0435 \u0432\u0441\u0435 \u043f\u043e\u043b\u044f \u043a\u043e\u0440\u0440\u0435\u043a\u0442\u043d\u043e.',
        // Login
        admin_login: '\u0412\u0445\u043e\u0434 \u0434\u043b\u044f \u0430\u0434\u043c\u0438\u043d\u0438\u0441\u0442\u0440\u0430\u0442\u043e\u0440\u0430',
        username: '\u0418\u043c\u044f \u043f\u043e\u043b\u044c\u0437\u043e\u0432\u0430\u0442\u0435\u043b\u044f', password: '\u041f\u0430\u0440\u043e\u043b\u044c',
        login_error: '\u041d\u0435\u0432\u0435\u0440\u043d\u043e\u0435 \u0438\u043c\u044f \u043f\u043e\u043b\u044c\u0437\u043e\u0432\u0430\u0442\u0435\u043b\u044f \u0438\u043b\u0438 \u043f\u0430\u0440\u043e\u043b\u044c.',
        // 404
        page_not_found: '\u0423\u043f\u0441! \u0421\u0442\u0440\u0430\u043d\u0438\u0446\u0430, \u043a\u043e\u0442\u043e\u0440\u0443\u044e \u0432\u044b \u0438\u0449\u0435\u0442\u0435, \u043d\u0435 \u0441\u0443\u0449\u0435\u0441\u0442\u0432\u0443\u0435\u0442.',
        go_home: '\u041d\u0430 \u0433\u043b\u0430\u0432\u043d\u0443\u044e',
        // Search results
        new_search: '\u2190 \u041d\u043e\u0432\u044b\u0439 \u043f\u043e\u0438\u0441\u043a',
        best_price: '\u041b\u0443\u0447\u0448\u0430\u044f \u0446\u0435\u043d\u0430',
        per_person: '\u0437\u0430 \u0447\u0435\u043b\u043e\u0432\u0435\u043a\u0430',
        total: '\u0438\u0442\u043e\u0433\u043e',
        book_now_arrow: '\u0417\u0430\u0431\u0440\u043e\u043d\u0438\u0440\u043e\u0432\u0430\u0442\u044c \u2192',
        // Destinations content
        dest_name_1: '\u041f\u0430\u0440\u0438\u0436, \u0424\u0440\u0430\u043d\u0446\u0438\u044f',
        dest_desc_1: '\u0413\u043e\u0440\u043e\u0434 \u0441\u0432\u0435\u0442\u0430 \u0436\u0434\u0451\u0442 \u0432\u0430\u0441 \u0441 \u043a\u0443\u043b\u044c\u0442\u043e\u0432\u044b\u043c\u0438 \u0434\u043e\u0441\u0442\u043e\u043f\u0440\u0438\u043c\u0435\u0447\u0430\u0442\u0435\u043b\u044c\u043d\u043e\u0441\u0442\u044f\u043c\u0438, \u0438\u0437\u044b\u0441\u043a\u0430\u043d\u043d\u043e\u0439 \u043a\u0443\u0445\u043d\u0435\u0439 \u0438 \u0432\u0435\u0447\u043d\u043e\u0439 \u0440\u043e\u043c\u0430\u043d\u0442\u0438\u043a\u043e\u0439.',
        dest_name_2: '\u0422\u043e\u043a\u0438\u043e, \u042f\u043f\u043e\u043d\u0438\u044f',
        dest_desc_2: '\u041e\u043a\u0443\u043d\u0438\u0442\u0435\u0441\u044c \u0432 \u0438\u0434\u0435\u0430\u043b\u044c\u043d\u043e\u0435 \u0441\u043e\u0447\u0435\u0442\u0430\u043d\u0438\u0435 \u0434\u0440\u0435\u0432\u043d\u0438\u0445 \u0442\u0440\u0430\u0434\u0438\u0446\u0438\u0439 \u0438 \u043f\u0435\u0440\u0435\u0434\u043e\u0432\u044b\u0445 \u0442\u0435\u0445\u043d\u043e\u043b\u043e\u0433\u0438\u0439.',
        dest_name_3: '\u0411\u0430\u043b\u0438, \u0418\u043d\u0434\u043e\u043d\u0435\u0437\u0438\u044f',
        dest_desc_3: '\u0422\u0440\u043e\u043f\u0438\u0447\u0435\u0441\u043a\u0438\u0439 \u0440\u0430\u0439 \u0441 \u0432\u0435\u043b\u0438\u043a\u043e\u043b\u0435\u043f\u043d\u044b\u043c\u0438 \u0445\u0440\u0430\u043c\u0430\u043c\u0438, \u0440\u0438\u0441\u043e\u0432\u044b\u043c\u0438 \u0442\u0435\u0440\u0440\u0430\u0441\u0430\u043c\u0438 \u0438 \u043d\u0435\u0442\u0440\u043e\u043d\u0443\u0442\u044b\u043c\u0438 \u043f\u043b\u044f\u0436\u0430\u043c\u0438.',
        dest_name_4: '\u0420\u0438\u043c, \u0418\u0442\u0430\u043b\u0438\u044f',
        dest_desc_4: '\u041f\u0440\u043e\u0433\u0443\u043b\u044f\u0439\u0442\u0435\u0441\u044c \u0441\u043a\u0432\u043e\u0437\u044c \u0442\u044b\u0441\u044f\u0447\u0435\u043b\u0435\u0442\u0438\u044f \u0438\u0441\u0442\u043e\u0440\u0438\u0438 \u0441\u0440\u0435\u0434\u0438 \u0434\u0440\u0435\u0432\u043d\u0438\u0445 \u0440\u0443\u0438\u043d, \u0444\u043e\u043d\u0442\u0430\u043d\u043e\u0432 \u0438 \u043f\u043b\u043e\u0449\u0430\u0434\u0435\u0439.',
        dest_name_5: '\u041d\u044c\u044e-\u0419\u043e\u0440\u043a, \u0421\u0428\u0410',
        dest_desc_5: '\u0413\u043e\u0440\u043e\u0434, \u043a\u043e\u0442\u043e\u0440\u044b\u0439 \u043d\u0438\u043a\u043e\u0433\u0434\u0430 \u043d\u0435 \u0441\u043f\u0438\u0442, \u043f\u0440\u0435\u0434\u043b\u0430\u0433\u0430\u0435\u0442 \u0431\u0435\u0441\u043a\u043e\u043d\u0435\u0447\u043d\u044b\u0435 \u0440\u0430\u0437\u0432\u043b\u0435\u0447\u0435\u043d\u0438\u044f, \u0440\u0435\u0441\u0442\u043e\u0440\u0430\u043d\u044b \u0438 \u043a\u0443\u043b\u044c\u0442\u0443\u0440\u0443.',
        dest_name_6: '\u041c\u0430\u043b\u044c\u0434\u0438\u0432\u044b',
        dest_desc_6: '\u041a\u0440\u0438\u0441\u0442\u0430\u043b\u044c\u043d\u043e \u0447\u0438\u0441\u0442\u0430\u044f \u0432\u043e\u0434\u0430, \u0432\u0438\u043b\u043b\u044b \u043d\u0430\u0434 \u043e\u043a\u0435\u0430\u043d\u043e\u043c \u0438 \u043d\u0435\u0437\u0430\u0431\u044b\u0432\u0430\u0435\u043c\u044b\u0435 \u0437\u0430\u043a\u0430\u0442\u044b \u0436\u0434\u0443\u0442 \u0432\u0430\u0441.',
        // Admin
        admin_dashboard: '\u041f\u0430\u043d\u0435\u043b\u044c \u0430\u0434\u043c\u0438\u043d\u0438\u0441\u0442\u0440\u0430\u0442\u043e\u0440\u0430',
        messages: '\u0421\u043e\u043e\u0431\u0449\u0435\u043d\u0438\u044f', settings: '\u041d\u0430\u0441\u0442\u0440\u043e\u0439\u043a\u0438',
        add_new_dest: '\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u043d\u043e\u0432\u043e\u0435 \u043d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u0435', existing_dest: '\u0421\u0443\u0449\u0435\u0441\u0442\u0432\u0443\u044e\u0449\u0438\u0435 \u043d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f',
        dest_name_ph: '\u041d\u0430\u0437\u0432\u0430\u043d\u0438\u0435', description_ph: '\u041e\u043f\u0438\u0441\u0430\u043d\u0438\u0435', price_ph: '\u0426\u0435\u043d\u0430',
        add_dest_btn: '\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c', delete_btn: '\u0423\u0434\u0430\u043b\u0438\u0442\u044c',
        dest_added: '\u041d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u0435 \u0443\u0441\u043f\u0435\u0448\u043d\u043e \u0434\u043e\u0431\u0430\u0432\u043b\u0435\u043d\u043e.',
        dest_deleted: '\u041d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u0435 \u0443\u0434\u0430\u043b\u0435\u043d\u043e.',
        fill_fields: '\u041f\u043e\u0436\u0430\u043b\u0443\u0439\u0441\u0442\u0430, \u0437\u0430\u043f\u043e\u043b\u043d\u0438\u0442\u0435 \u0432\u0441\u0435 \u043f\u043e\u043b\u044f \u043a\u043e\u0440\u0440\u0435\u043a\u0442\u043d\u043e.',
        settings_saved: '\u041d\u0430\u0441\u0442\u0440\u043e\u0439\u043a\u0438 \u0443\u0441\u043f\u0435\u0448\u043d\u043e \u0441\u043e\u0445\u0440\u0430\u043d\u0435\u043d\u044b.',
        contact_messages: '\u0421\u043e\u043e\u0431\u0449\u0435\u043d\u0438\u044f \u043e\u0442 \u043f\u043e\u043b\u044c\u0437\u043e\u0432\u0430\u0442\u0435\u043b\u0435\u0439', no_messages: '\u0421\u043e\u043e\u0431\u0449\u0435\u043d\u0438\u0439 \u043f\u043e\u043a\u0430 \u043d\u0435\u0442.',
        site_settings: '\u041d\u0430\u0441\u0442\u0440\u043e\u0439\u043a\u0438 \u0441\u0430\u0439\u0442\u0430', save_settings_btn: '\u0421\u043e\u0445\u0440\u0430\u043d\u0438\u0442\u044c',
        th_name: '\u041d\u0430\u0437\u0432\u0430\u043d\u0438\u0435', th_price: '\u0426\u0435\u043d\u0430', th_actions: '\u0414\u0435\u0439\u0441\u0442\u0432\u0438\u044f',
        th_email: 'Email', th_message: '\u0421\u043e\u043e\u0431\u0449\u0435\u043d\u0438\u0435', th_date: '\u0414\u0430\u0442\u0430',
        // Booking modal
        modal_title: '\u0421\u043f\u0430\u0441\u0438\u0431\u043e!',
        modal_text: '\u041c\u044b \u043f\u043e\u043b\u0443\u0447\u0438\u043b\u0438 \u0432\u0430\u0448 \u0437\u0430\u043f\u0440\u043e\u0441. \u041d\u0430\u0448\u0430 \u043a\u043e\u043c\u0430\u043d\u0434\u0430 \u0441\u0432\u044f\u0436\u0435\u0442\u0441\u044f \u0441 \u0432\u0430\u043c\u0438 \u0432 \u0431\u043b\u0438\u0436\u0430\u0439\u0448\u0435\u0435 \u0432\u0440\u0435\u043c\u044f.',
        modal_sub: '\u0423\u0431\u0435\u0434\u0438\u0442\u0435\u0441\u044c, \u0447\u0442\u043e \u0432\u0430\u0448\u0438 \u043a\u043e\u043d\u0442\u0430\u043a\u0442\u043d\u044b\u0435 \u0434\u0430\u043d\u043d\u044b\u0435 \u0430\u043a\u0442\u0443\u0430\u043b\u044c\u043d\u044b.',
        modal_ok: 'OK',
        // Footer
        footer_text: '\u00a9 2026 Touristik. \u0412\u0441\u0435 \u043f\u0440\u0430\u0432\u0430 \u0437\u0430\u0449\u0438\u0449\u0435\u043d\u044b.'
    },
    hy: {
        // Nav
        home: '\u0533\u056c\u056d\u0561\u057e\u0578\u0580', destinations: '\u0548\u0582\u0572\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580', about: '\u0544\u0565\u0580 \u0574\u0561\u057d\u056b\u0576', contact: '\u053f\u0561\u057a',
        login: '\u0544\u0578\u0582\u057f\u0584', logout: '\u0535\u056c\u0584', admin: '\u0531\u0564\u0574\u056b\u0576',
        // Hero
        hero_title: '\u0532\u0561\u0581\u0561\u0570\u0561\u0575\u057f\u056b\u0580 \u0561\u0577\u056d\u0561\u0580\u0570\u0568 Touristik-\u056b \u0570\u0565\u057f',
        hero_subtitle: '\u0540\u0561\u0575\u057f\u0576\u0561\u0562\u0565\u0580\u0565\u056c \u0570\u056b\u0561\u057d\u0584\u0561\u0576\u0579 \u0578\u0582\u0572\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580 \u0587 \u057d\u057f\u0565\u0572\u056e\u0565\u056c \u0561\u0576\u0574\u0578\u057c\u0561\u0576\u0561\u056c\u056b \u0570\u056b\u0577\u0578\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580',
        roundtrip: '\u0535\u0580\u056f\u0578\u0582\u0572\u056b', oneway: '\u0544\u056b\u0561\u056f\u0578\u0572\u0574\u0561\u0576\u056b', packages: '\u0553\u0561\u0569\u0565\u0569\u0576\u0565\u0580',
        from: '\u0548\u0580\u057f\u0565\u0572\u056b\u0581', to: '\u0548\u0582\u0580', depart: '\u0544\u0565\u056f\u0576\u0578\u0582\u0574', return_date: '\u054e\u0565\u0580\u0561\u0564\u0561\u0580\u0571',
        adults: '\u0544\u0565\u056e\u0561\u0570\u0561\u057d\u0561\u056f\u0576\u0565\u0580', children: '\u0535\u0580\u0565\u056d\u0561\u0576\u0565\u0580',
        price_from: '\u057d\u056f\u057d\u0561\u056e',
        search: '\u0555\u0580\u0578\u0576\u0565\u056c',
        // Home
        popular: '\u0540\u0561\u0575\u057f\u0576\u056b \u0578\u0582\u0572\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580', why_travel: '\u053b\u0576\u0579\u0578\u0582 \u0573\u0561\u0574\u0583\u0578\u0580\u0564\u0565\u056c \u0574\u0565\u0566 \u0570\u0565\u057f',
        best_flights: '\u053c\u0561\u057e\u0561\u0563\u0578\u0582\u0575\u0576 \u0579\u057e\u0565\u0580\u0569\u0576\u0565\u0580', top_hotels: '\u053c\u0561\u057e\u0561\u0563\u0578\u0582\u0575\u0576 \u0570\u0575\u0578\u0582\u0580\u0561\u0576\u0578\u0581\u0576\u0565\u0580', easy_booking: '\u0540\u0565\u0577\u057f \u0561\u0574\u0580\u0561\u0563\u0580\u0578\u0582\u0574',
        best_flights_desc: '\u0544\u0565\u0576\u0584 \u0570\u0561\u0574\u0561\u0563\u0578\u0580\u056e\u0561\u056f\u0581\u0578\u0582\u0574 \u0565\u0576\u0584 \u056c\u0561\u057e\u0561\u0563\u0578\u0582\u0575\u0576 \u0561\u057e\u056b\u0561\u0568\u0576\u056f\u0565\u0580\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580\u056b \u0570\u0565\u057f\u0589',
        top_hotels_desc: '\u0541\u0565\u057c\u0584\u0578\u057e \u0568\u0576\u057f\u0580\u057e\u0561\u056e \u0570\u0575\u0578\u0582\u0580\u0561\u0576\u0578\u0581\u0576\u0565\u0580\u055d \u0570\u0561\u0580\u0574\u0561\u0580\u0561\u057e\u0565\u057f\u056b\u0581 \u0574\u056b\u0576\u0579\u0587 \u0577\u0584\u0565\u0572 \u056f\u0578\u0582\u0580\u0578\u0580\u057f\u0576\u0565\u0580\u056b\u0576\u0589',
        easy_booking_desc: '\u054a\u0561\u0580\u0566 \u0587 \u0561\u0576\u057e\u057f\u0561\u0576\u0563 \u0561\u0574\u0580\u0561\u0563\u0580\u0574\u0561\u0576 \u0563\u0578\u0580\u056e\u0568\u0576\u0569\u0561\u0581\u055d \u0573\u056f\u0578\u0582\u0576 \u0579\u0565\u0572\u0561\u0580\u056f\u0574\u0561\u0576 \u057a\u0561\u0575\u0574\u0561\u0576\u0576\u0565\u0580\u0578\u057e\u0589',
        // About
        about_title: 'Touristik-\u056b \u0574\u0561\u057d\u056b\u0576',
        about_p1: '\u0544\u0565\u0580 \u057f\u0561\u0580\u0565\u0580\u0584\u0568 \u0576\u0578\u0580 \u0570\u0578\u0580\u056b\u0566\u0578\u0576\u0576\u0565\u0580\u0576 \u0565\u0576\u0589 \u0544\u0565\u0576\u0584 \u0570\u0561\u057e\u0561\u057f\u0578\u0582\u0574 \u0565\u0576\u0584, \u0578\u0580 \u0561\u0577\u056d\u0561\u0580\u0570\u056b \u0570\u0565\u057f\u0561\u0566\u0578\u057f\u0578\u0582\u0574\u0568 \u057a\u0565\u057f\u0584 \u0567 \u056c\u056b\u0576\u056b \u0570\u0561\u057d\u0561\u0576\u0565\u056c\u056b, \u0570\u0561\u0573\u0565\u056c\u056b \u0587 \u0561\u0576\u0574\u0578\u057c\u0561\u0576\u0561\u056c\u056b\u0589 \u0544\u0565\u0580 \u0569\u056b\u0574\u0568 \u056d\u0576\u0561\u0574\u0584\u0578\u057e \u0568\u0576\u057f\u0580\u0578\u0582\u0574 \u0567 \u0575\u0578\u0582\u0580\u0561\u0584\u0561\u0576\u0579\u0575\u0578\u0582\u0580 \u0578\u0582\u0572\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576, \u0570\u0575\u0578\u0582\u0580\u0561\u0576\u0578\u0581 \u0587 \u057f\u057a\u0561\u057e\u0578\u0580\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0589',
        about_p2: '\u054f\u0561\u0580\u056b\u0576\u0565\u0580\u056b \u0583\u0578\u0580\u0571\u0578\u057e \u057f\u0578\u0582\u0580\u056b\u057d\u057f\u0561\u056f\u0561\u0576 \u0578\u056c\u043e\u0580\u057f\u0578\u0582\u043c, \u0574\u0565\u0576\u0584 \u0585\u0563\u0576\u0565\u056c \u0565\u0576\u0584 \u0570\u0561\u0566\u0561\u0580\u0561\u057e\u0578\u0580 \u0573\u0561\u0574\u0583\u0578\u0580\u0564\u0576\u0565\u0580\u056b\u0576 \u0563\u057f\u0576\u0565\u056c \u056b\u0580\u0565\u0576\u0581 \u0565\u0580\u0561\u0566\u0561\u0576\u0584\u056b \u0578\u0582\u0572\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0568\u0589 \u053c\u056b\u0576\u056b \u0570\u0561\u0576\u0563\u057d\u057f\u0561\u0581\u0578\u0572 \u056e\u0578\u057e\u0561\u0583\u0576\u0575\u0561 \u0570\u0561\u0576\u0563\u056b\u057d\u057f, \u0569\u0565 \u0561\u056f\u057f\u056b\u057e \u0584\u0561\u0572\u0561\u0584\u0561\u0575\u056b\u0576 \u0561\u0580\u056f\u0561\u056e \u2014 \u0574\u0565\u0576\u0584 \u056f\u0585\u0563\u0576\u0565\u0576\u0584\u0589',
        // Destinations
        all_destinations: '\u0532\u0578\u056c\u0578\u0580 \u0578\u0582\u0572\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580\u0568',
        no_destinations: '\u0548\u0582\u0572\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580 \u0564\u0565\u057c \u0570\u0561\u057d\u0561\u0576\u0565\u056c\u056b \u0579\u0565\u0576\u0589',
        view_dest: '\u054f\u0565\u057d\u0576\u0565\u056c \u0578\u0582\u0572\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580\u0568',
        // Destination detail
        book_now: '\u0531\u0574\u0580\u0561\u0563\u0580\u0565\u056c',
        back_dest: '\u054e\u0565\u0580\u0561\u0564\u0561\u057c\u0576\u0561\u056c',
        // Contact
        get_in_touch: '\u053f\u0561\u057a\u057e\u0565\u056c \u0574\u0565\u0566 \u0570\u0565\u057f',
        your_name: '\u0541\u0565\u0580 \u0561\u0576\u0578\u0582\u0576\u0568', your_email: '\u0541\u0565\u0580 \u0567\u056c. \u0570\u0561\u057d\u0581\u0565\u0568',
        dream_trip: '\u054a\u0561\u057f\u0574\u0565\u0581\u0565\u0584 \u0571\u0565\u0580 \u0565\u0580\u0561\u0566\u0561\u0576\u0584\u0561\u0575\u056b\u0576 \u0573\u0561\u0574\u0583\u0578\u0580\u0564\u0578\u0582\u0569\u0575\u0561\u0576 \u0574\u0561\u057d\u056b\u0576...', send: '\u0548\u0582\u0572\u0561\u0580\u056f\u0565\u056c',
        contact_success: '\u0547\u0576\u0578\u0580\u0570\u0561\u056f\u0561\u056c\u0578\u0582\u0569\u0575\u0578\u0582\u0576! \u0544\u0565\u0576\u0584 \u056f\u056f\u0561\u057a\u057e\u0565\u0576\u0584 \u0571\u0565\u0566 \u0570\u0565\u057f \u0577\u0578\u0582\u057f\u0578\u057e\u0589',
        contact_error: '\u053d\u0576\u0564\u0580\u0578\u0582\u0574 \u0565\u0576\u0584\u055d \u056c\u0580\u0561\u0581\u0580\u0565\u0584 \u0562\u0578\u056c\u0578\u0580 \u0564\u0561\u0577\u057f\u0565\u0580\u0568 \u0573\u056b\u0577\u057f\u0589',
        // Login
        admin_login: '\u0531\u0564\u0574\u056b\u0576\u056b \u0574\u0578\u0582\u057f\u0584',
        username: '\u0555\u0563\u057f\u0561\u0563\u0578\u0580\u056e\u0578\u0572\u056b \u0561\u0576\u0578\u0582\u0576', password: '\u0533\u0561\u0572\u057f\u0576\u0561\u0562\u0561\u057c',
        login_error: '\u054d\u056d\u0561\u056c \u0585\u0563\u057f\u0561\u0563\u0578\u0580\u056e\u0578\u0572\u056b \u0561\u0576\u0578\u0582\u0576 \u056f\u0561\u0574 \u0563\u0561\u0572\u057f\u0576\u0561\u0562\u0561\u057c\u0589',
        // 404
        page_not_found: '\u054e\u0561\u0575! \u0537\u057b\u0568, \u0578\u0580 \u0583\u0576\u057f\u0580\u0578\u0582\u0574 \u0565\u0584, \u0563\u0578\u0575\u0578\u0582\u0569\u0575\u0578\u0582\u0576 \u0579\u0578\u0582\u0576\u056b\u0589',
        go_home: '\u0533\u056c\u056d\u0561\u057e\u0578\u0580 \u0567\u057b',
        // Search results
        new_search: '\u2190 \u0546\u0578\u0580 \u0578\u0580\u0578\u0576\u0578\u0582\u0574',
        best_price: '\u053c\u0561\u057e\u0561\u0563\u0578\u0582\u0575\u0576 \u0563\u056b\u0576',
        per_person: '\u0574\u0565\u056f \u0561\u0576\u0571\u056b \u0570\u0561\u0574\u0561\u0580',
        total: '\u0568\u0576\u0564\u0561\u0574\u0565\u0576\u0568',
        book_now_arrow: '\u0531\u0574\u0580\u0561\u0563\u0580\u0565\u056c \u2192',
        // Destinations content
        dest_name_1: '\u0553\u0561\u0580\u056b\u0566, \u0556\u0580\u0561\u0576\u057d\u056b\u0561',
        dest_desc_1: '\u053c\u0578\u0582\u0575\u057d\u056b \u0584\u0561\u0572\u0561\u0584\u0568 \u057d\u057a\u0561\u057d\u0578\u0582\u0574 \u0567 \u0571\u0565\u0566 \u056b\u0580 \u0570\u0561\u0575\u057f\u0576\u056b \u057f\u0565\u057d\u0561\u0580\u056a\u0561\u0576\u0576\u0565\u0580\u0578\u057e, \u0570\u0561\u0574\u0561\u0577\u056d\u0561\u0580\u0570\u0561\u0575\u056b\u0576 \u056d\u0578\u0570\u0561\u0576\u0578\u0581\u0578\u057e \u0587 \u0570\u0561\u057e\u0565\u0580\u056a\u0561\u056f\u0561\u0576 \u057c\u0578\u0574\u0561\u0576\u057f\u056b\u056f\u0561\u0575\u0578\u057e\u0589',
        dest_name_2: '\u054f\u0578\u056f\u056b\u0578, \u0543\u0561\u057a\u0578\u0576\u056b\u0561',
        dest_desc_2: '\u054f\u0565\u057d\u0565\u0584 \u0570\u056b\u0576 \u0561\u057e\u0561\u0576\u0564\u0578\u0582\u0575\u0569\u0576\u0565\u0580\u056b \u0587 \u0561\u0580\u0564\u056b\u0561\u056f\u0561\u0576 \u057f\u0565\u056d\u0576\u0578\u056c\u0578\u0563\u056b\u0561\u0576\u0565\u0580\u056b \u056f\u0561\u057f\u0561\u0580\u0575\u0561\u056c \u0570\u0561\u0574\u0561\u0564\u0580\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0568\u0589',
        dest_name_3: '\u0532\u0561\u056c\u056b, \u053b\u0576\u0564\u0578\u0576\u0565\u0566\u056b\u0561',
        dest_desc_3: '\u0531\u0580\u0587\u0561\u0564\u0561\u0580\u0571\u0561\u0576\u0561\u0575\u056b\u0576 \u0564\u0580\u0561\u056d\u057f\u055d \u0563\u0565\u0572\u0565\u0581\u056b\u056f \u057f\u0561\u0573\u0561\u0580\u0576\u0565\u0580\u0578\u057e, \u0562\u0580\u0576\u0571\u056b \u057f\u0561\u0580\u0561\u057d\u0576\u0565\u0580\u0578\u057e \u0587 \u0574\u0561\u0584\u0578\u0582\u0580 \u056c\u0578\u0572\u0561\u0583\u0576\u0575\u0561\u0576\u0565\u0580\u0578\u057e\u0589',
        dest_name_4: '\u0540\u057c\u0578\u0574, \u053b\u057f\u0561\u056c\u056b\u0561',
        dest_desc_4: '\u0554\u0561\u0575\u056c\u0565\u0584 \u0570\u0561\u0566\u0561\u0580\u0561\u0574\u0575\u0561\u056f\u0576\u0565\u0580\u056b \u057a\u0561\u057f\u0574\u0578\u0582\u0569\u0575\u0561\u0576 \u0574\u056b\u057b\u0578\u057e\u055d \u0570\u056b\u0576 \u0561\u057e\u0565\u0580\u0561\u056f\u0576\u0565\u0580\u056b, \u0577\u0561\u057f\u0580\u057e\u0561\u0576\u0576\u0565\u0580\u056b \u0587 \u0570\u0580\u0561\u057a\u0561\u0580\u0561\u056f\u0576\u0565\u0580\u056b \u0574\u0565\u057b\u0589',
        dest_name_5: '\u0546\u0575\u0578\u0582 \u0545\u0578\u0580\u0584, \u0531\u0544\u0546',
        dest_desc_5: '\u0554\u0561\u0572\u0561\u0584\u0568, \u0578\u0580\u0568 \u0565\u0580\u0562\u0565\u0584 \u0579\u056b \u0584\u0576\u0578\u0582\u0574, \u0561\u057c\u0561\u057b\u0561\u0580\u056f\u0578\u0582\u0574 \u0567 \u0561\u0576\u057e\u0565\u0580\u057b \u0566\u0562\u0561\u0572\u0561\u0576\u0584\u0576\u0565\u0580, \u057c\u0565\u057d\u057f\u0578\u0580\u0561\u0576\u0576\u0565\u0580 \u0587 \u0574\u0577\u0561\u056f\u0578\u0582\u0575\u0569\u0589',
        dest_name_6: '\u0544\u0561\u056c\u0564\u056b\u057e\u0576\u0565\u0580',
        dest_desc_6: '\u054a\u0561\u0580\u0566 \u057b\u0580\u0565\u0580, \u057b\u0580\u056b \u057e\u0580\u0561 \u057e\u056b\u056c\u056c\u0561\u0576\u0565\u0580 \u0587 \u0561\u0576\u0574\u0578\u057c\u0561\u0576\u0561\u056c\u056b \u0574\u0561\u0575\u0580\u0561\u0574\u0578\u0582\u057f\u0576\u0565\u0580 \u057d\u057a\u0561\u057d\u0578\u0582\u0574 \u0565\u0576 \u0571\u0565\u0566\u0589',
        // Admin
        admin_dashboard: '\u0531\u0564\u0574\u056b\u0576\u056b \u057e\u0561\u0570\u0561\u0576\u0561\u056f',
        messages: '\u0540\u0561\u0572\u0578\u0580\u0564\u0561\u0563\u0580\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580', settings: '\u053f\u0561\u0580\u0563\u0561\u057e\u0578\u0580\u0578\u0582\u0574\u0576\u0565\u0580',
        add_new_dest: '\u0531\u057e\u0565\u056c\u0561\u0581\u0576\u0565\u056c \u0576\u0578\u0580 \u0578\u0582\u0572\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576', existing_dest: '\u0531\u057c\u056f\u0561 \u0578\u0582\u0572\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580',
        dest_name_ph: '\u0531\u0576\u057e\u0561\u0576\u0578\u0582\u0574', description_ph: '\u0546\u056f\u0561\u0580\u0561\u0563\u0580\u0578\u0582\u0569\u0575\u0578\u0582\u0576', price_ph: '\u0533\u056b\u0576',
        add_dest_btn: '\u0531\u057e\u0565\u056c\u0561\u0581\u0576\u0565\u056c', delete_btn: '\u054b\u0576\u057b\u0565\u056c',
        dest_added: '\u0548\u0582\u0572\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0568 \u0570\u0561\u057b\u0578\u0572\u0578\u0582\u0569\u0575\u0561\u0574\u0562 \u0561\u057e\u0565\u056c\u0561\u0581\u057e\u0565\u0581\u0589',
        dest_deleted: '\u0548\u0582\u0572\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0568 \u057b\u0576\u057b\u057e\u0565\u0581\u0589',
        fill_fields: '\u053d\u0576\u0564\u0580\u0578\u0582\u0574 \u0565\u0576\u0584\u055d \u056c\u0580\u0561\u0581\u0580\u0565\u0584 \u0562\u0578\u056c\u0578\u0580 \u0564\u0561\u0577\u057f\u0565\u0580\u0568 \u0573\u056b\u0577\u057f\u0589',
        settings_saved: '\u053f\u0561\u0580\u0563\u0561\u057e\u0578\u0580\u0578\u0582\u0574\u0576\u0565\u0580\u0568 \u0570\u0561\u057b\u0578\u0572\u0578\u0582\u0569\u0575\u0561\u0574\u0562 \u057a\u0561\u0570\u057a\u0561\u0576\u057e\u0565\u0581\u0589',
        contact_messages: '\u053f\u0561\u057a\u056b \u0570\u0561\u0572\u0578\u0580\u0564\u0561\u0563\u0580\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580', no_messages: '\u0540\u0561\u0572\u0578\u0580\u0564\u0561\u0563\u0580\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580 \u0564\u0565\u057c \u0579\u056f\u0561\u0576\u0589',
        site_settings: '\u053f\u0561\u0575\u0584\u056b \u056f\u0561\u0580\u0563\u0561\u057e\u0578\u0580\u0578\u0582\u0574\u0576\u0565\u0580', save_settings_btn: '\u054a\u0561\u0570\u057a\u0561\u0576\u0565\u056c',
        th_name: '\u0531\u0576\u057e\u0561\u0576\u0578\u0582\u0574', th_price: '\u0533\u056b\u0576', th_actions: '\u0533\u0578\u0580\u056e\u0578\u0572\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580',
        th_email: '\u0537\u056c. \u0570\u0561\u057d\u0581\u0565', th_message: '\u0540\u0561\u0572\u0578\u0580\u0564\u0561\u0563\u0580\u0578\u0582\u0569\u0575\u0578\u0582\u0576', th_date: '\u0531\u0574\u057d\u0561\u0569\u056b\u057e',
        // Booking modal
        modal_title: '\u0547\u0576\u0578\u0580\u0570\u0561\u056f\u0561\u056c\u0578\u0582\u0569\u0575\u0578\u0582\u0576!',
        modal_text: '\u0544\u0565\u0576\u0584 \u057d\u057f\u0561\u0581\u0565\u056c \u0565\u0576\u0584 \u0571\u0565\u0580 \u0570\u0561\u0575\u057f\u0568\u0589 \u0544\u0565\u0580 \u0569\u056b\u0574\u0568 \u0577\u0578\u0582\u057f\u0578\u057e \\u056f\u056f\u0561\u057a\u057e\u056b \u0571\u0565\u0566\u0570\u0565\u057f \u0561\u0574\u0580\u0561\u0563\u0580\u0578\u0582\u0574\u0568 \u0568\u0576\u0569\u0561\u0581\u0584\u056b \u0576\u057a\u0561\u057f\u0561\u056f\u0578\u057e\u0589',
        modal_sub: '\u053d\u0576\u0564\u0580\u0578\u0582\u0574 \u0565\u0576\u0584 \u0570\u0561\u0574\u0578\u0566\u057e\u0565\u0584, \u0578\u0580 \u0571\u0565\u0580 \u056f\u0578\u0576\u057f\u0561\u056f\u057f\u0561\u0575\u056b\u0576 \u057f\u057e\u0575\u0561\u056c\u0576\u0565\u0580\u0568 \u0569\u0561\u0580\u0574\u0561\u0581\u057e\u0561\u056e \u0565\u0576\u0589',
        modal_ok: 'OK',
        // Footer
        footer_text: '\u00a9 2026 Touristik. \u0532\u0578\u056c\u0578\u0580 \u056b\u0580\u0561\u057e\u0578\u0582\u0576\u0584\u0576\u0565\u0580\u0568 \u057a\u0561\u0577\u057f\u057a\u0561\u0576\u057e\u0561\u056e \u0565\u0576\u0589'
    }
};

// Language switcher
(function () {
    const toggle = document.getElementById('langToggle');
    const dropdown = document.getElementById('langDropdown');
    const current = document.getElementById('langCurrent');
    if (!toggle || !dropdown) return;

    const labels = { en: 'EN', ru: 'RU', hy: 'HY' };

    // Load saved language
    const saved = localStorage.getItem('lang') || 'en';
    if (current) current.textContent = labels[saved] || 'EN';
    highlightSelected(saved);
    if (saved !== 'en') {
        applyTranslations(saved);
        // Also update price labels with translated "from" text
        const symbol = localStorage.getItem('currSymbol') || '$';
        const rate = parseFloat(localStorage.getItem('currRate')) || 1;
        const fromLabel = translations[saved] && translations[saved].price_from ? translations[saved].price_from : 'From';
        document.querySelectorAll('.price').forEach(el => {
            const base = el.dataset.basePrice;
            if (base) {
                const converted = Math.round(parseFloat(base) * rate);
                el.textContent = fromLabel + ' ' + symbol + converted.toLocaleString();
            }
        });
    }

    // Toggle dropdown
    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('open');
        // Close currency dropdown
        const currDD = document.getElementById('currencyDropdown');
        if (currDD) currDD.classList.remove('open');
    });

    document.addEventListener('click', function () {
        dropdown.classList.remove('open');
    });

    // Language selection
    dropdown.querySelectorAll('.lang-option').forEach(opt => {
        opt.addEventListener('click', function (e) {
            e.preventDefault();
            const lang = this.dataset.lang;
            localStorage.setItem('lang', lang);
            current.textContent = labels[lang];
            dropdown.classList.remove('open');
            highlightSelected(lang);
            applyTranslations(lang);

            // Also reconvert prices with correct "From" label
            const symbol = localStorage.getItem('currSymbol') || '$';
            const rate = parseFloat(localStorage.getItem('currRate')) || 1;
            const fromLabel = translations[lang] && translations[lang].price_from ? translations[lang].price_from : 'From';
            document.querySelectorAll('.price').forEach(el => {
                const base = el.dataset.basePrice;
                if (base) {
                    const converted = Math.round(parseFloat(base) * rate);
                    el.textContent = fromLabel + ' ' + symbol + converted.toLocaleString();
                }
            });
        });
    });

    function highlightSelected(lang) {
        dropdown.querySelectorAll('.lang-option').forEach(o => {
            o.classList.toggle('selected', o.dataset.lang === lang);
        });
    }

    function applyTranslations(lang) {
        const t = translations[lang];
        if (!t) return;

        // Text content
        document.querySelectorAll('[data-t]').forEach(el => {
            const key = el.dataset.t;
            if (t[key]) el.textContent = t[key];
        });

        // Placeholders
        document.querySelectorAll('[data-tp]').forEach(el => {
            const key = el.dataset.tp;
            if (t[key]) el.placeholder = t[key];
        });
    }
})();

// Fade-in animation on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.card, .feature').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});

// Booking modal
(function () {
    const modal = document.getElementById('bookingModal');
    if (!modal) return;
    const closeBtn = modal.querySelector('.booking-modal-close');
    const okBtn = modal.querySelector('.booking-modal-ok');

    function openModal() {
        modal.classList.add('active');
        // Apply translations to modal
        const lang = localStorage.getItem('selectedLang') || 'en';
        const t = translations[lang] || translations['en'] || {};
        modal.querySelectorAll('[data-t]').forEach(el => {
            const key = el.getAttribute('data-t');
            if (t[key]) el.textContent = t[key];
        });
    }
    function closeModal() {
        modal.classList.remove('active');
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('.book-trigger')) {
            e.preventDefault();
            openModal();
        }
    });
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (okBtn) okBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
})();
