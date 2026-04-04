var TRANSLATIONS = {
    en: {},
    ru: {
        home: "Главная", tours_nav: "Туры", tour_cat_ingoing: "Входящие туры", tour_cat_outgoing: "Выездные туры", tour_cat_transfer: "Трансфер",
        destinations: "Направления", about: "О нас", contact: "Контакты", my_account: "Мой аккаунт", admin: "Админ", logout: "Выйти", login: "Войти", register: "Регистрация",
        hero_title: "Откройте мир с Touristik", hero_subtitle: "Авиабилеты, отели, туры и визовая поддержка из Еревана",
        roundtrip: "Туда-обратно", oneway: "В одну сторону", packages: "Пакеты",
        from: "Откуда", to: "Куда", depart: "Вылет", return_date: "Возврат", adults: "Взрослые", children: "Дети", search: "Поиск", price_from: "от",
        tours_title: "Туры", tours_subtitle: "Изучите наши туристические услуги",
        tour_cat_ingoing_desc: "Туры", tour_cat_outgoing_desc: "Туры", tour_cat_transfer_desc: "Трансферы",
        visa_title: "Визовая поддержка", visa_subtitle: "Мы берем на себя все документы",
        visa_feat1_title: "Приглашение", visa_feat1_desc: "Официальные приглашения для визы.",
        visa_feat2_title: "Быстрое оформление", visa_feat2_desc: "Стандарт 5-7 дней, экспресс 2-3 дня.",
        visa_feat3_title: "Помощь с E-Visa", visa_feat3_desc: "Полное сопровождение при оформлении e-visa.",
        visa_feat4_title: "Консультация 24/7", visa_feat4_desc: "Наши специалисты доступны круглосуточно.",
        visa_cta: "Запросить визовую поддержку",
        stat_travelers: "Довольных путешественников", stat_destinations: "Направлений", stat_branches: "Филиалов", stat_years: "Лет опыта",
        popular: "Популярные направления", popular_subtitle: "Лучшие путешествия из Еревана",
        view_all_dest: "Все направления", from_price: "От",
        why_travel: "Почему путешествовать с нами",
        best_flights: "Лучшие рейсы", best_flights_desc: "Партнерство с лучшими авиакомпаниями.",
        top_hotels: "Лучшие отели", top_hotels_desc: "Тщательно отобранные отели.",
        easy_booking: "Простое бронирование", easy_booking_desc: "Простой и безопасный процесс.",
        support_247: "Поддержка 24/7", support_247_desc: "Наша команда всегда на связи.",
        partners_title: "Наши партнеры", partners_subtitle: "Нам доверяют ведущие авиакомпании",
        testimonials_title: "Отзывы путешественников", testimonials_subtitle: "Реальные отзывы наших клиентов",
        faq_title: "Часто задаваемые вопросы", faq_subtitle: "Все, что нужно знать перед поездкой",
        faq_q1: "Как забронировать рейс или тур?", faq_a1: "Используйте форму поиска или свяжитесь с нами.",
        faq_q2: "Вы предоставляете визовую поддержку?", faq_a2: "Да! Полная визовая поддержка.",
        faq_q3: "Какие способы оплаты?", faq_a3: "Наличные, перевод, карты Visa/MasterCard.",
        faq_q4: "Можно отменить бронирование?", faq_a4: "Большинство можно изменить за 48 часов.",
        faq_q5: "Групповые поездки?", faq_a5: "Да! Группы и корпоративные поездки.",
        footer_branches_title: "Наши филиалы", branch_1: "Комитас 38", branch_2: "Маштоц 7/6", branch_3: "Арцахуняц 34 (Ереван Молл)",
        footer_hours_title: "Время работы", hours_weekday: "Пн-Пт: 10:00-20:00", hours_weekend: "Сб-Вс: 11:00-18:00",
        footer_contact_title: "Контакты", footer_follow_title: "Подпишитесь",
        cookie_text: "Мы используем cookies.", cookie_accept: "Принять", cookie_decline: "Отклонить",
        contact_title: "Связаться с нами", contact_subtitle: "Мы рады помочь спланировать поездку.",
        send_message: "Написать нам", form_name: "Имя", form_email: "Email", form_subject: "Тема", form_message: "Сообщение", send_btn: "Отправить",
        about_title: "О Touristik", about_subtitle: "Ваш надежный партнер в Армении",
        destinations_title: "Популярные направления", destinations_subtitle: "Лучшие места мира с Touristik",
        explore: "Подробнее",
    },
    hy: {
        home: "Գլխավոր", tours_nav: "Տուdelays", tour_cat_ingoing: " Delays delays", tour_cat_outgoing: "Delays delays", tour_cat_transfer: "Delays",
        destinations: "Delays", about: "Delays delays", contact: "Delays delays", login: "Delays", register: "Delays",
        search: "Delays", cookie_accept: "Delays", cookie_decline: "Delays",
    }
};
// Armenian translations are partial - English will be used as fallback for missing keys

(function() {
    var currentLang = localStorage.getItem('touristik_lang') || 'en';
    var originals = {};

    function setLang(lang) {
        currentLang = lang;
        localStorage.setItem('touristik_lang', lang);
        var dict = TRANSLATIONS[lang] || {};
        document.querySelectorAll('[data-t]').forEach(function(el) {
            var key = el.getAttribute('data-t');
            if (!originals[key]) originals[key] = el.innerHTML;
            if (lang === 'en') {
                if (originals[key]) el.innerHTML = originals[key];
            } else if (dict[key]) {
                el.innerHTML = dict[key];
            }
        });
        var langCurrent = document.getElementById('langCurrent');
        if (langCurrent) langCurrent.textContent = lang.toUpperCase();
        document.documentElement.lang = lang;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Save originals
        document.querySelectorAll('[data-t]').forEach(function(el) {
            originals[el.getAttribute('data-t')] = el.innerHTML;
        });
        // Apply saved lang
        if (currentLang !== 'en') setLang(currentLang);
        // Click handlers
        document.querySelectorAll('.lang-option[data-lang]').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                setLang(this.getAttribute('data-lang'));
            });
        });
    });

    window.setTouristikLang = setLang;
})();
