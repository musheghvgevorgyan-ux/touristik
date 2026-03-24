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
        // Incoming tours
        incoming_title: 'Incoming Tourism Programs',
        incoming_subtitle: 'Discover the beauty of Armenia with our curated tour packages',
        tour_name_1: 'Classic Yerevan',
        tour_desc_1: 'Explore the Pink City: Republic Square, Cascade Complex, Matenadaran, and the vibrant nightlife of Northern Avenue.',
        tour_name_2: 'Ancient Temples & Monasteries',
        tour_desc_2: 'Visit Garni Temple, Geghard Monastery, Tatev, Noravank, and Khor Virap with breathtaking views of Mount Ararat.',
        tour_name_3: 'Grand Armenia Tour',
        tour_desc_3: 'The ultimate Armenian experience: Lake Sevan, Dilijan, Jermuk, wine tasting in Areni, and the Silk Road trails.',
        tour_name_4: 'Adventure & Hiking',
        tour_desc_4: 'Trek through the stunning landscapes of Dilijan National Park, Aragats summit, and the Lastiver caves.',
        tour_name_5: 'Wine & Gastronomy Trail',
        tour_desc_5: 'Taste the world\'s oldest wine tradition in Areni, savor Armenian BBQ, lavash baking, and brandy tasting at Ararat factory.',
        // Visa support
        visa_title: 'Visa Support for Armenia',
        visa_subtitle: 'We handle all the paperwork so you can focus on your trip',
        visa_feat1_title: 'Invitation Letter',
        visa_feat1_desc: 'Official invitation letters for visa applications to Armenian consulates worldwide.',
        visa_feat2_title: 'Fast Processing',
        visa_feat2_desc: 'Standard processing in 5-7 business days, express option available in 2-3 days.',
        visa_feat3_title: 'E-Visa Assistance',
        visa_feat3_desc: 'Full guidance through the Armenian e-visa application process for eligible countries.',
        visa_feat4_title: '24/7 Consultation',
        visa_feat4_desc: 'Our visa specialists are available around the clock to answer your questions.',
        visa_info: '\ud83d\udec8 Citizens of 60+ countries can enter Armenia visa-free for up to 180 days. Not sure about your country? Contact us for a free consultation.',
        visa_cta: 'Request Visa Support \u2192',
        // Footer & Contact info
        footer_branches_title: 'Our Branches',
        footer_hours_title: 'Working Hours',
        footer_contact_title: 'Contact Us',
        contact_branches_title: 'Our Branches',
        contact_hours_title: 'Working Hours',
        contact_phone_title: 'Phone & Email',
        branch_1: 'Komitas 38',
        branch_2: 'Mashtots 7/6',
        branch_3: 'Arshakunyats 34 (Yerevan Mall, 2nd floor)',
        hours_weekday: 'Mon \u2013 Fri: 10:00 \u2013 20:00',
        hours_weekend: 'Sat \u2013 Sun: 11:00 \u2013 18:00',
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
        // Incoming tours
        incoming_title: '\u041f\u0440\u043e\u0433\u0440\u0430\u043c\u043c\u044b \u0432\u044a\u0435\u0437\u0434\u043d\u043e\u0433\u043e \u0442\u0443\u0440\u0438\u0437\u043c\u0430',
        incoming_subtitle: '\u041e\u0442\u043a\u0440\u043e\u0439\u0442\u0435 \u043a\u0440\u0430\u0441\u043e\u0442\u0443 \u0410\u0440\u043c\u0435\u043d\u0438\u0438 \u0441 \u043d\u0430\u0448\u0438\u043c\u0438 \u0442\u0443\u0440\u043f\u0430\u043a\u0435\u0442\u0430\u043c\u0438',
        tour_name_1: '\u041a\u043b\u0430\u0441\u0441\u0438\u0447\u0435\u0441\u043a\u0438\u0439 \u0415\u0440\u0435\u0432\u0430\u043d',
        tour_desc_1: '\u0418\u0441\u0441\u043b\u0435\u0434\u0443\u0439\u0442\u0435 \u0420\u043e\u0437\u043e\u0432\u044b\u0439 \u0433\u043e\u0440\u043e\u0434: \u043f\u043b\u043e\u0449\u0430\u0434\u044c \u0420\u0435\u0441\u043f\u0443\u0431\u043b\u0438\u043a\u0438, \u041a\u0430\u0441\u043a\u0430\u0434, \u041c\u0430\u0442\u0435\u043d\u0430\u0434\u0430\u0440\u0430\u043d \u0438 \u044f\u0440\u043a\u0443\u044e \u043d\u043e\u0447\u043d\u0443\u044e \u0436\u0438\u0437\u043d\u044c \u0421\u0435\u0432\u0435\u0440\u043d\u043e\u0433\u043e \u043f\u0440\u043e\u0441\u043f\u0435\u043a\u0442\u0430.',
        tour_name_2: '\u0414\u0440\u0435\u0432\u043d\u0438\u0435 \u0445\u0440\u0430\u043c\u044b \u0438 \u043c\u043e\u043d\u0430\u0441\u0442\u044b\u0440\u0438',
        tour_desc_2: '\u041f\u043e\u0441\u0435\u0442\u0438\u0442\u0435 \u0445\u0440\u0430\u043c \u0413\u0430\u0440\u043d\u0438, \u043c\u043e\u043d\u0430\u0441\u0442\u044b\u0440\u044c \u0413\u0435\u0433\u0430\u0440\u0434, \u0422\u0430\u0442\u0435\u0432, \u041d\u043e\u0440\u0430\u0432\u0430\u043d\u043a \u0438 \u0425\u043e\u0440 \u0412\u0438\u0440\u0430\u043f \u0441 \u0437\u0430\u0445\u0432\u0430\u0442\u044b\u0432\u0430\u044e\u0449\u0438\u043c\u0438 \u0432\u0438\u0434\u0430\u043c\u0438 \u043d\u0430 \u0410\u0440\u0430\u0440\u0430\u0442.',
        tour_name_3: '\u0411\u043e\u043b\u044c\u0448\u043e\u0439 \u0442\u0443\u0440 \u043f\u043e \u0410\u0440\u043c\u0435\u043d\u0438\u0438',
        tour_desc_3: '\u041d\u0435\u0437\u0430\u0431\u044b\u0432\u0430\u0435\u043c\u044b\u0439 \u0430\u0440\u043c\u044f\u043d\u0441\u043a\u0438\u0439 \u043e\u043f\u044b\u0442: \u043e\u0437\u0435\u0440\u043e \u0421\u0435\u0432\u0430\u043d, \u0414\u0438\u043b\u0438\u0436\u0430\u043d, \u0414\u0436\u0435\u0440\u043c\u0443\u043a, \u0434\u0435\u0433\u0443\u0441\u0442\u0430\u0446\u0438\u044f \u0432\u0438\u043d \u0432 \u0410\u0440\u0435\u043d\u0438 \u0438 \u0442\u0440\u043e\u043f\u044b \u0428\u0451\u043b\u043a\u043e\u0432\u043e\u0433\u043e \u043f\u0443\u0442\u0438.',
        tour_name_4: '\u041f\u0440\u0438\u043a\u043b\u044e\u0447\u0435\u043d\u0438\u044f \u0438 \u043f\u0435\u0448\u0438\u0435 \u043f\u043e\u0445\u043e\u0434\u044b',
        tour_desc_4: '\u041f\u0440\u043e\u0439\u0434\u0438\u0442\u0435 \u043f\u043e \u043f\u043e\u0442\u0440\u044f\u0441\u0430\u044e\u0449\u0438\u043c \u043b\u0430\u043d\u0434\u0448\u0430\u0444\u0442\u0430\u043c \u0414\u0438\u043b\u0438\u0436\u0430\u043d\u0441\u043a\u043e\u0433\u043e \u043d\u0430\u0446\u0438\u043e\u043d\u0430\u043b\u044c\u043d\u043e\u0433\u043e \u043f\u0430\u0440\u043a\u0430, \u0432\u0435\u0440\u0448\u0438\u043d\u0435 \u0410\u0440\u0430\u0433\u0430\u0446\u0430 \u0438 \u043f\u0435\u0449\u0435\u0440\u0430\u043c \u041b\u0430\u0441\u0442\u0438\u0432\u0435\u0440.',
        tour_name_5: '\u0412\u0438\u043d\u043d\u043e-\u0433\u0430\u0441\u0442\u0440\u043e\u043d\u043e\u043c\u0438\u0447\u0435\u0441\u043a\u0438\u0439 \u0442\u0443\u0440',
        tour_desc_5: '\u041f\u043e\u043f\u0440\u043e\u0431\u0443\u0439\u0442\u0435 \u0434\u0440\u0435\u0432\u043d\u0435\u0439\u0448\u0438\u0435 \u0432\u0438\u043d\u043e\u0434\u0435\u043b\u044c\u0447\u0435\u0441\u043a\u0438\u0435 \u0442\u0440\u0430\u0434\u0438\u0446\u0438\u0438 \u0432 \u0410\u0440\u0435\u043d\u0438, \u0430\u0440\u043c\u044f\u043d\u0441\u043a\u0438\u0439 \u0448\u0430\u0448\u043b\u044b\u043a, \u043b\u0430\u0432\u0430\u0448 \u0438 \u0434\u0435\u0433\u0443\u0441\u0442\u0430\u0446\u0438\u044e \u043a\u043e\u043d\u044c\u044f\u043a\u0430 \u043d\u0430 \u0437\u0430\u0432\u043e\u0434\u0435 \u0410\u0440\u0430\u0440\u0430\u0442.',
        // Visa support
        visa_title: '\u0412\u0438\u0437\u043e\u0432\u0430\u044f \u043f\u043e\u0434\u0434\u0435\u0440\u0436\u043a\u0430 \u0434\u043b\u044f \u0410\u0440\u043c\u0435\u043d\u0438\u0438',
        visa_subtitle: '\u041c\u044b \u0431\u0435\u0440\u0451\u043c \u043d\u0430 \u0441\u0435\u0431\u044f \u0432\u0441\u0435 \u0434\u043e\u043a\u0443\u043c\u0435\u043d\u0442\u044b, \u0447\u0442\u043e\u0431\u044b \u0432\u044b \u043c\u043e\u0433\u043b\u0438 \u0441\u043e\u0441\u0440\u0435\u0434\u043e\u0442\u043e\u0447\u0438\u0442\u044c\u0441\u044f \u043d\u0430 \u043f\u043e\u0435\u0437\u0434\u043a\u0435',
        visa_feat1_title: '\u041f\u0440\u0438\u0433\u043b\u0430\u0441\u0438\u0442\u0435\u043b\u044c\u043d\u043e\u0435 \u043f\u0438\u0441\u044c\u043c\u043e',
        visa_feat1_desc: '\u041e\u0444\u0438\u0446\u0438\u0430\u043b\u044c\u043d\u044b\u0435 \u043f\u0440\u0438\u0433\u043b\u0430\u0441\u0438\u0442\u0435\u043b\u044c\u043d\u044b\u0435 \u043f\u0438\u0441\u044c\u043c\u0430 \u0434\u043b\u044f \u0432\u0438\u0437\u043e\u0432\u044b\u0445 \u0437\u0430\u044f\u0432\u043e\u043a \u0432 \u043a\u043e\u043d\u0441\u0443\u043b\u044c\u0441\u0442\u0432\u0430 \u0410\u0440\u043c\u0435\u043d\u0438\u0438 \u043f\u043e \u0432\u0441\u0435\u043c\u0443 \u043c\u0438\u0440\u0443.',
        visa_feat2_title: '\u0411\u044b\u0441\u0442\u0440\u043e\u0435 \u043e\u0444\u043e\u0440\u043c\u043b\u0435\u043d\u0438\u0435',
        visa_feat2_desc: '\u0421\u0442\u0430\u043d\u0434\u0430\u0440\u0442\u043d\u043e\u0435 \u043e\u0444\u043e\u0440\u043c\u043b\u0435\u043d\u0438\u0435 \u0437\u0430 5\u20137 \u0440\u0430\u0431\u043e\u0447\u0438\u0445 \u0434\u043d\u0435\u0439, \u044d\u043a\u0441\u043f\u0440\u0435\u0441\u0441-\u0432\u0430\u0440\u0438\u0430\u043d\u0442 \u0437\u0430 2\u20133 \u0434\u043d\u044f.',
        visa_feat3_title: '\u041f\u043e\u043c\u043e\u0449\u044c \u0441 \u044d\u043b\u0435\u043a\u0442\u0440\u043e\u043d\u043d\u043e\u0439 \u0432\u0438\u0437\u043e\u0439',
        visa_feat3_desc: '\u041f\u043e\u043b\u043d\u043e\u0435 \u0441\u043e\u043f\u0440\u043e\u0432\u043e\u0436\u0434\u0435\u043d\u0438\u0435 \u043f\u0440\u043e\u0446\u0435\u0441\u0441\u0430 \u043f\u043e\u0434\u0430\u0447\u0438 \u044d\u043b\u0435\u043a\u0442\u0440\u043e\u043d\u043d\u043e\u0439 \u0432\u0438\u0437\u044b \u0434\u043b\u044f \u0433\u0440\u0430\u0436\u0434\u0430\u043d \u0441\u043e\u043e\u0442\u0432\u0435\u0442\u0441\u0442\u0432\u0443\u044e\u0449\u0438\u0445 \u0441\u0442\u0440\u0430\u043d.',
        visa_feat4_title: '\u041a\u043e\u043d\u0441\u0443\u043b\u044c\u0442\u0430\u0446\u0438\u044f 24/7',
        visa_feat4_desc: '\u041d\u0430\u0448\u0438 \u0432\u0438\u0437\u043e\u0432\u044b\u0435 \u0441\u043f\u0435\u0446\u0438\u0430\u043b\u0438\u0441\u0442\u044b \u0434\u043e\u0441\u0442\u0443\u043f\u043d\u044b \u043a\u0440\u0443\u0433\u043b\u043e\u0441\u0443\u0442\u043e\u0447\u043d\u043e \u0434\u043b\u044f \u043e\u0442\u0432\u0435\u0442\u043e\u0432 \u043d\u0430 \u0432\u0430\u0448\u0438 \u0432\u043e\u043f\u0440\u043e\u0441\u044b.',
        visa_info: '\u2139\ufe0f \u0413\u0440\u0430\u0436\u0434\u0430\u043d\u0435 60+ \u0441\u0442\u0440\u0430\u043d \u043c\u043e\u0433\u0443\u0442 \u0432\u044a\u0435\u0445\u0430\u0442\u044c \u0432 \u0410\u0440\u043c\u0435\u043d\u0438\u044e \u0431\u0435\u0437 \u0432\u0438\u0437\u044b \u043d\u0430 \u0441\u0440\u043e\u043a \u0434\u043e 180 \u0434\u043d\u0435\u0439. \u041d\u0435 \u0443\u0432\u0435\u0440\u0435\u043d\u044b \u043d\u0430\u0441\u0447\u0451\u0442 \u0432\u0430\u0448\u0435\u0439 \u0441\u0442\u0440\u0430\u043d\u044b? \u0421\u0432\u044f\u0436\u0438\u0442\u0435\u0441\u044c \u0441 \u043d\u0430\u043c\u0438 \u0434\u043b\u044f \u0431\u0435\u0441\u043f\u043b\u0430\u0442\u043d\u043e\u0439 \u043a\u043e\u043d\u0441\u0443\u043b\u044c\u0442\u0430\u0446\u0438\u0438.',
        visa_cta: '\u0417\u0430\u043f\u0440\u043e\u0441\u0438\u0442\u044c \u0432\u0438\u0437\u043e\u0432\u0443\u044e \u043f\u043e\u0434\u0434\u0435\u0440\u0436\u043a\u0443 \u2192',
        // Footer & Contact info
        footer_branches_title: '\u041d\u0430\u0448\u0438 \u0444\u0438\u043b\u0438\u0430\u043b\u044b',
        footer_hours_title: '\u0420\u0435\u0436\u0438\u043c \u0440\u0430\u0431\u043e\u0442\u044b',
        footer_contact_title: '\u0421\u0432\u044f\u0436\u0438\u0442\u0435\u0441\u044c \u0441 \u043d\u0430\u043c\u0438',
        contact_branches_title: '\u041d\u0430\u0448\u0438 \u0444\u0438\u043b\u0438\u0430\u043b\u044b',
        contact_hours_title: '\u0420\u0435\u0436\u0438\u043c \u0440\u0430\u0431\u043e\u0442\u044b',
        contact_phone_title: '\u0422\u0435\u043b\u0435\u0444\u043e\u043d \u0438 \u044d\u043b. \u043f\u043e\u0447\u0442\u0430',
        branch_1: '\u041a\u043e\u043c\u0438\u0442\u0430\u0441 38',
        branch_2: '\u041c\u0430\u0448\u0442\u043e\u0446 7/6',
        branch_3: '\u0410\u0440\u0448\u0430\u043a\u0443\u043d\u044f\u0446 34 (\u0415\u0440\u0435\u0432\u0430\u043d \u041c\u043e\u043b\u043b, 2-\u0439 \u044d\u0442\u0430\u0436)',
        hours_weekday: '\u041f\u043d \u2013 \u041f\u0442: 10:00 \u2013 20:00',
        hours_weekend: '\u0421\u0431 \u2013 \u0412\u0441: 11:00 \u2013 18:00',
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
        // Incoming tours
        incoming_title: '\u0546\u0565\u0580\u0563\u0576\u0561 \u057f\u0578\u0582\u0580\u056b\u0566\u0574\u056b \u056e\u0580\u0561\u0563\u0580\u0565\u0580',
        incoming_subtitle: '\u0532\u0561\u0581\u0561\u0570\u0561\u0575\u057f\u0565\u0584 \u0540\u0561\u0575\u0561\u057d\u057f\u0561\u0576\u056b \u0563\u0565\u0572\u0565\u0581\u056f\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0568 \u0574\u0565\u0580 \u057f\u0578\u0582\u0580\u0583\u0561\u0569\u0565\u0569\u0576\u0565\u0580\u0578\u057e',
        tour_name_1: '\u0534\u0561\u057d\u0561\u056f\u0561\u0576 \u0535\u0580\u0587\u0561\u0576',
        tour_desc_1: '\u0548\u0582\u057d\u0578\u0582\u0574\u0576\u0561\u057d\u056b\u0580\u0565\u0584 \u054e\u0561\u0580\u0564\u0561\u0563\u0578\u0582\u0575\u0576 \u0584\u0561\u0572\u0561\u0584\u0568\u055d \u0540\u0561\u0576\u0580\u0561\u057a\u0565\u057f\u0578\u0582\u0569\u0575\u0561\u0576 \u0570\u0580\u0561\u057a\u0561\u0580\u0561\u056f, \u053f\u0561\u057d\u056f\u0561\u0564, \u0544\u0561\u057f\u0565\u0576\u0561\u0564\u0561\u0580\u0561\u0576 \u0587 \u0540\u0575\u0578\u0582\u057d\u056b\u057d\u0561\u0575\u056b\u0576 \u057a\u0578\u0572\u0578\u057f\u0561\u0575\u056b \u0563\u056b\u0577\u0565\u0580\u0561\u0575\u056b\u0576 \u056f\u0575\u0561\u0576\u0584\u0568\u0589',
        tour_name_2: '\u0540\u056b\u0576 \u057f\u0561\u0573\u0561\u0580\u0576\u0565\u0580 \u0587 \u057e\u0561\u0576\u0584\u0565\u0580',
        tour_desc_2: '\u0531\u0575\u0581\u0565\u056c\u0565\u0584 \u0533\u0561\u057c\u0576\u056b\u056b \u057f\u0561\u0573\u0561\u0580\u0568, \u0533\u0565\u0572\u0561\u0580\u0564\u056b \u057e\u0561\u0576\u0584\u0568, \u054f\u0561\u0569\u0587, \u0546\u0578\u0580\u0561\u057e\u0561\u0576\u0584 \u0587 \u053d\u0578\u0580 \u054e\u056b\u0580\u0561\u057a\u0568\u055d \u0531\u0580\u0561\u0580\u0561\u057f \u056c\u0565\u057c\u0561\u0576 \u0570\u056b\u0561\u0576\u0561\u056c\u056b \u057f\u0565\u057d\u0561\u0580\u0561\u0576\u0576\u0565\u0580\u0578\u057e\u0589',
        tour_name_3: '\u0544\u0565\u056e \u0570\u0561\u0575\u0561\u057d\u057f\u0561\u0576\u0575\u0561\u0576 \u057f\u0578\u0582\u0580',
        tour_desc_3: '\u0531\u0576\u0574\u0578\u057c\u0561\u0576\u0561\u056c\u056b \u0570\u0561\u0575\u056f\u0561\u056f\u0561\u0576 \u0583\u0578\u0580\u0571\u055d \u054d\u0587\u0561\u0576\u0561 \u056c\u056b\u0573, \u0534\u056b\u056c\u056b\u0573\u0561\u0576, \u054b\u0565\u0580\u0574\u0578\u0582\u056f, \u0563\u056b\u0576\u0565\u0563\u0578\u0580\u056e\u0578\u0582\u0569\u0575\u0578\u0582\u0576 \u0531\u0580\u0565\u0576\u056b\u0578\u0582\u0574 \u0587 \u0544\u0565\u057f\u0561\u0584\u057d\u056b \u0573\u0561\u0576\u0561\u057a\u0561\u0580\u0570\u0576\u0565\u0580\u0568\u0589',
        tour_name_4: '\u0531\u0580\u056f\u0561\u056e\u0561\u056d\u0576\u0564\u0578\u0582\u0569\u0575\u0578\u0582\u0576 \u0587 \u0561\u0580\u0577\u0561\u057e\u0576\u0565\u0580',
        tour_desc_4: '\u0531\u0576\u0581\u0565\u0584 \u0534\u056b\u056c\u056b\u0573\u0561\u0576\u056b \u0561\u0566\u0563\u0561\u0575\u056b\u0576 \u057a\u0561\u0580\u056f\u056b, \u0531\u0580\u0561\u0563\u0561\u056e\u056b \u0563\u0561\u0563\u0561\u0569\u056b \u0587 \u053c\u0561\u057d\u057f\u056b\u057e\u0565\u0580\u056b \u0584\u0561\u0580\u0561\u0576\u0571\u0561\u057e\u0576\u0565\u0580\u056b \u0566\u0561\u0580\u0574\u0561\u0576\u0561\u056c\u056b \u0562\u0576\u0561\u057a\u0561\u057f\u056f\u0565\u0580\u0576\u0565\u0580\u0578\u057e\u0589',
        tour_name_5: '\u0533\u056b\u0576\u0565\u0563\u0578\u0580\u056e\u0561\u056f\u0561\u0576-\u0563\u0561\u057d\u057f\u0580\u0578\u0576\u0578\u0574\u056b\u0561\u056f\u0561\u0576 \u057f\u0578\u0582\u0580',
        tour_desc_5: '\u0540\u0561\u0574\u057f\u0565\u0584 \u0531\u0580\u0565\u0576\u056b\u056b \u0570\u0576\u0561\u0563\u0578\u0582\u0575\u0576 \u0563\u056b\u0576\u0565\u0563\u0578\u0580\u056e\u0561\u056f\u0561\u0576 \u0561\u057e\u0561\u0576\u0564\u0578\u0582\u0575\u0569\u0576\u0565\u0580\u0568, \u0570\u0561\u0575\u056f\u0561\u056f\u0561\u0576 \u056d\u0578\u0580\u0578\u057e\u0561\u056e, \u056c\u0561\u057e\u0561\u0577 \u0587 \u056f\u0578\u0576\u0575\u0561\u056f\u056b \u0570\u0561\u0574\u057f\u0565\u0563\u0578\u0582\u0574 \u0531\u0580\u0561\u0580\u0561\u057f \u0563\u0578\u0580\u056e\u0561\u0580\u0561\u0576\u0578\u0582\u0574\u0589',
        // Visa support
        visa_title: '\u054e\u056b\u0566\u0561\u0575\u056b\u0576 \u0561\u057b\u0561\u056f\u0581\u0578\u0582\u0569\u0575\u0578\u0582\u0576 \u0540\u0561\u0575\u0561\u057d\u057f\u0561\u0576\u056b \u0570\u0561\u0574\u0561\u0580',
        visa_subtitle: '\u0544\u0565\u0576\u0584 \u056f\u0561\u057f\u0561\u0580\u0578\u0582\u0574 \u0565\u0576\u0584 \u0562\u0578\u056c\u0578\u0580 \u0583\u0561\u057d\u057f\u0561\u0569\u0572\u0569\u0565\u0580\u0568, \u0578\u0580\u057a\u0565\u057d\u0566\u056b \u0564\u0578\u0582\u0584 \u056f\u0565\u0576\u057f\u0580\u0578\u0576\u0561\u0576\u0561\u0584 \u0571\u0565\u0580 \u0573\u0561\u0574\u0583\u0578\u0580\u0564\u056b\u0576',
        visa_feat1_title: '\u0540\u0580\u0561\u057e\u0565\u0580\u056b \u0576\u0561\u0574\u0561\u056f',
        visa_feat1_desc: '\u054a\u0561\u0577\u057f\u0578\u0576\u0561\u056f\u0561\u0576 \u0570\u0580\u0561\u057e\u0565\u0580\u056b \u0576\u0561\u0574\u0561\u056f\u0576\u0565\u0580 \u057e\u056b\u0566\u0561\u0575\u056b\u0576 \u0564\u056b\u0574\u0578\u0582\u0574\u0576\u0565\u0580\u056b \u0570\u0561\u0574\u0561\u0580 \u0540\u0561\u0575\u0561\u057d\u057f\u0561\u0576\u056b \u0570\u0575\u0578\u0582\u057a\u0561\u057f\u0578\u057d\u0561\u056f\u0561\u0576 \u0570\u056b\u0574\u0576\u0561\u0580\u056f\u0576\u0565\u0580\u0578\u0582\u0574\u0589',
        visa_feat2_title: '\u0531\u0580\u0561\u0563 \u0571\u0587\u0561\u056f\u0565\u0580\u057a\u0578\u0582\u0574',
        visa_feat2_desc: '\u054d\u057f\u0561\u0576\u0564\u0561\u0580\u057f \u0571\u0587\u0561\u056f\u0565\u0580\u057a\u0578\u0582\u0574\u0568 5\u20137 \u0561\u0577\u056d\u0561\u057f\u0561\u0576\u0584\u0561\u0575\u056b\u0576 \u0585\u0580\u0578\u0582\u0574, \u0561\u0580\u0561\u0563 \u057f\u0561\u0580\u0562\u0565\u0580\u0561\u056f\u0568\u055d 2\u20133 \u0585\u0580\u0578\u0582\u0574\u0589',
        visa_feat3_title: '\u0537\u056c\u0565\u056f\u057f\u0580\u0578\u0576\u0561\u0575\u056b\u0576 \u057e\u056b\u0566\u0561\u0575\u056b \u0585\u0563\u0576\u0578\u0582\u0569\u0575\u0578\u0582\u0576',
        visa_feat3_desc: '\u053c\u056b\u0561\u0580\u056a\u0565\u0584 \u0578\u0582\u0572\u0565\u056f\u0581\u0578\u0582\u0569\u0575\u0578\u0582\u0576 \u0540\u0561\u0575\u0561\u057d\u057f\u0561\u0576\u056b \u0537\u056c\u0565\u056f\u057f\u0580\u0578\u0576\u0561\u0575\u056b\u0576 \u057e\u056b\u0566\u0561\u0575\u056b \u0564\u056b\u0574\u0578\u0582\u0574\u056b \u0563\u0578\u0580\u056e\u0568\u0576\u0569\u0561\u0581\u0578\u0582\u0574 \u0570\u0561\u0574\u0561\u057a\u0561\u057f\u0561\u057d\u056d\u0561\u0576 \u0565\u0580\u056f\u0580\u0576\u0565\u0580\u056b \u0570\u0561\u0574\u0561\u0580\u0589',
        visa_feat4_title: '\u053d\u0578\u0580\u0570\u0580\u0564\u0561\u057f\u057e\u0578\u0582\u0569\u0575\u0578\u0582\u0576 24/7',
        visa_feat4_desc: '\u0544\u0565\u0580 \u057e\u056b\u0566\u0561\u0575\u056b\u0576 \u0574\u0561\u057d\u0576\u0561\u0563\u0565\u057f\u0576\u0565\u0580\u0568 \u0570\u0561\u057d\u0561\u0576\u0565\u056c\u056b \u0565\u0576 \u0577\u0578\u0582\u0580\u057b\u0585\u0580\u0575\u0561 \u0571\u0565\u0580 \u0570\u0561\u0580\u0581\u0565\u0580\u056b\u0576 \u057a\u0561\u057f\u0561\u057d\u056d\u0561\u0576\u0565\u056c\u0578\u0582 \u0570\u0561\u0574\u0561\u0580\u0589',
        visa_info: '\u2139\ufe0f 60+ \u0565\u0580\u056f\u0580\u0576\u0565\u0580\u056b \u0584\u0561\u0572\u0561\u0584\u0561\u0581\u056b\u0576\u0565\u0580\u0568 \u056f\u0561\u0580\u0578\u0572 \u0565\u0576 \u0574\u0578\u0582\u057f\u0584 \u0563\u0578\u0580\u056e\u0565\u056c \u0540\u0561\u0575\u0561\u057d\u057f\u0561\u0576 \u0561\u057c\u0561\u0576\u0581 \u057e\u056b\u0566\u0561\u0575\u056b \u0574\u056b\u0576\u0579\u0587 180 \u0585\u0580\u0589 \u054e\u057d\u057f\u0561\u0570 \u0579\u0565\u055e\u0584 \u0571\u0565\u0580 \u0565\u0580\u056f\u0580\u056b \u057e\u0565\u0580\u0561\u0562\u0565\u0580\u0575\u0561\u056c\u0589 \u053f\u0561\u057a\u057e\u0565\u0584 \u0574\u0565\u0566 \u0561\u0576\u057e\u0573\u0561\u0580 \u056d\u0578\u0580\u0570\u0580\u0564\u0561\u057f\u057e\u0578\u0582\u0569\u0575\u0561\u0576 \u0570\u0561\u0574\u0561\u0580\u0589',
        visa_cta: '\u054a\u0561\u0570\u0561\u0576\u057b\u0565\u056c \u057e\u056b\u0566\u0561\u0575\u056b\u0576 \u0561\u057b\u0561\u056f\u0581\u0578\u0582\u0569\u0575\u0578\u0582\u0576 \u2192',
        // Footer & Contact info
        footer_branches_title: '\u0544\u0565\u0580 \u0574\u0561\u057d\u0576\u0561\u0573\u0575\u0578\u0582\u0572\u0565\u0580\u0568',
        footer_hours_title: '\u0531\u0577\u056d\u0561\u057f\u0561\u0576\u0584\u0561\u0575\u056b\u0576 \u056a\u0561\u0574\u0565\u0580',
        footer_contact_title: '\u053f\u0561\u057a\u057e\u0565\u0584 \u0574\u0565\u0566',
        contact_branches_title: '\u0544\u0565\u0580 \u0574\u0561\u057d\u0576\u0561\u0573\u0575\u0578\u0582\u0572\u0565\u0580\u0568',
        contact_hours_title: '\u0531\u0577\u056d\u0561\u057f\u0561\u0576\u0584\u0561\u0575\u056b\u0576 \u056a\u0561\u0574\u0565\u0580',
        contact_phone_title: '\u0540\u0565\u057c. \u0587 \u0537\u056c. \u0570\u0561\u057d\u0581\u0565',
        branch_1: '\u053f\u0578\u0574\u056b\u057f\u0561\u057d 38',
        branch_2: '\u0544\u0561\u0577\u057f\u0578\u0581 7/6',
        branch_3: '\u0531\u0580\u0577\u0561\u056f\u0578\u0582\u0576\u0575\u0561\u0581 34 (\u0535\u0580\u0587\u0561\u0576 \u0544\u0578\u056c\u056c, 2-\u0580\u0564 \u0570\u0561\u0580\u056f)',
        hours_weekday: '\u0535\u0580\u056f \u2013 \u0548\u0582\u0580\u0562: 10:00 \u2013 20:00',
        hours_weekend: '\u0547\u0561\u0562 \u2013 \u053f\u056b\u0580: 11:00 \u2013 18:00',
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

// Tour slider
(function () {
    const track = document.querySelector('.tour-slider-track');
    if (!track) return;
    const slides = track.querySelectorAll('.tour-slide');
    const prevBtn = document.querySelector('.tour-prev');
    const nextBtn = document.querySelector('.tour-next');
    const dotsContainer = document.querySelector('.tour-dots');
    let current = 0;
    const total = slides.length;
    let autoPlay;

    // Create dots
    slides.forEach(function (_, i) {
        const dot = document.createElement('button');
        dot.className = 'tour-dot' + (i === 0 ? ' active' : '');
        dot.addEventListener('click', function () { goTo(i); });
        dotsContainer.appendChild(dot);
    });

    function goTo(index) {
        current = (index + total) % total;
        track.style.transform = 'translateX(-' + (current * 100) + '%)';
        document.querySelectorAll('.tour-dot').forEach(function (d, i) {
            d.classList.toggle('active', i === current);
        });
        resetAuto();
    }

    function resetAuto() {
        clearInterval(autoPlay);
        autoPlay = setInterval(function () { goTo(current + 1); }, 5000);
    }

    prevBtn.addEventListener('click', function () { goTo(current - 1); });
    nextBtn.addEventListener('click', function () { goTo(current + 1); });
    resetAuto();

    // Swipe support
    let startX = 0;
    track.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; });
    track.addEventListener('touchend', function (e) {
        var diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) goTo(current + (diff > 0 ? 1 : -1));
    });
})();
