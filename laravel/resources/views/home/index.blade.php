@extends('layouts.main')

@section('title', 'Touristik - Travel Club')

@push('styles')
<style>
    .testimonials { text-align: center; padding: 4rem 2rem; }
    .testimonials h2 { font-size: 2rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .testimonials-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; max-width: 1200px; margin: 2rem auto 0; }
    .testimonial-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 2rem; text-align: left; transition: transform 0.3s; }
    .testimonial-card:hover { transform: translateY(-4px); }
    .testimonial-stars { color: #f7b731; font-size: 1.2rem; margin-bottom: 0.8rem; letter-spacing: 2px; }
    .testimonial-text { color: var(--text-secondary); font-size: 0.95rem; line-height: 1.7; font-style: italic; margin-bottom: 1.2rem; }
    .testimonial-author { display: flex; align-items: center; gap: 0.8rem; }
    .testimonial-avatar { width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #FF6B35, #f7a072); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 1.1rem; flex-shrink: 0; }
    .testimonial-author strong { display: block; font-size: 0.95rem; color: var(--text-heading); }
    .testimonial-author span { font-size: 0.8rem; color: var(--text-secondary); }
</style>
@endpush

@section('content')
<section id="home" class="hero">
    <div class="hero-floating" aria-hidden="true">
        <span class="hero-float hero-cloud hero-cloud-1">&#9729;</span>
        <span class="hero-float hero-cloud hero-cloud-2">&#9729;</span>
        <span class="hero-float hero-cloud hero-cloud-3">&#9729;</span>
        <span class="hero-float hero-plane hero-plane-1">&#9992;</span>
        <span class="hero-float hero-plane hero-plane-2">&#9992;</span>
        <span class="hero-float hero-globe">&#127758;</span>
    </div>
    <div class="hero-content">
        <h1 class="hero-typed" data-t="hero_title">{{ app()->getLocale() === 'en' ? $heroTitle : __('site.hero_title') }}</h1>
        <p class="hero-subtitle-fade" data-t="hero_subtitle">{{ app()->getLocale() === 'en' ? $heroSubtitle : __('site.hero_subtitle') }}</p>
        <div class="trip-toggle">
            <button type="button" class="trip-btn active" data-type="roundtrip" data-t="roundtrip">{{ __('site.roundtrip') }}</button>
            <button type="button" class="trip-btn" data-type="oneway" data-t="oneway">{{ __('site.oneway') }}</button>
            <button type="button" class="trip-btn" data-type="packages" data-t="packages">{{ __('site.packages') }}</button>
        </div>
        <form class="hero-search" method="GET" action="/hotels/search">
            <input type="hidden" name="trip" id="trip-type" value="roundtrip">
            <div class="search-field flight-field">
                <label data-t="from">{{ __('site.from') }}</label>
                <div class="city-autocomplete">
                    <input type="text" name="from" class="city-input" id="cityFrom" value="Yerevan" autocomplete="off" data-tp="type_city">
                    <div class="city-dropdown" id="dropdownFrom"></div>
                </div>
            </div>
            <div class="search-field flight-field">
                <label data-t="to">{{ __('site.to') }}</label>
                <div class="city-autocomplete">
                    <input type="text" name="to" class="city-input" id="cityTo" value="Moscow" autocomplete="off" data-tp="type_city">
                    <div class="city-dropdown" id="dropdownTo"></div>
                </div>
            </div>
            <div class="search-field package-field" style="display:none;">
                <label data-t="from">{{ __('site.from') }}</label>
                <div class="city-autocomplete">
                    <input type="text" name="pkg_from" class="city-input" value="Yerevan" autocomplete="off" readonly>
                </div>
            </div>
            <div class="search-field package-field" style="display:none;">
                <label data-t="to">{{ __('site.to') }}</label>
                <div class="city-autocomplete">
                    <input type="text" name="pkg_to" id="pkgCountry" class="city-input pkg-city-input" value="" autocomplete="off" data-tp="type_city">
                    <div class="city-dropdown" id="dropdownPkg"></div>
                </div>
            </div>
            <div class="search-field depart-field">
                <label data-t="depart">{{ __('site.depart') }}</label>
                <input type="date" name="date" value="{!! date('Y-m-d', strtotime('+7 days')) !!}">
            </div>
            <div class="search-field return-field">
                <label data-t="return_date">{{ __('site.return_date') }}</label>
                <input type="date" name="return_date" value="{!! date('Y-m-d', strtotime('+14 days')) !!}">
            </div>
            <div class="persons-row">
                <div class="search-field persons-field">
                    <label data-t="adults">{{ __('site.adults') }}</label>
                    <select name="adults">
                        <option value="1">1</option>
                        <option value="2" selected>2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6+</option>
                    </select>
                </div>
                <div class="search-field persons-field">
                    <label data-t="children">{{ __('site.children') }}</label>
                    <select name="children" id="childrenSelect">
                        <option value="0" selected>0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </div>
            </div>
            <div class="children-ages" id="childrenAges" style="display:none;">
                <label>Ages</label>
                <div class="children-ages-inputs" id="childrenAgesInputs"></div>
            </div>
            <div class="search-price" id="searchPrice" style="display:none;">
                <span class="search-price-label" data-t="price_from">from</span>
                <span class="search-price-value" id="searchPriceValue"></span>
                <span class="search-price-pp">/pp</span>
            </div>
            <button type="submit" class="btn search-btn" data-t="search">{{ __('site.search') }}</button>
        </form>
    </div>
</section>

<section id="tours" class="tours-section reveal">
    <h2 data-t="tours_title">{{ __('site.tours_title') }}</h2>
    <p class="section-subtitle tours-subtitle" data-t="tours_subtitle">{{ __('site.tours_subtitle') }}</p>
    <div class="tours-grid">

        <a href="{{ lurl('/tours/ingoing') }}" class="tour-category-card reveal-scale">
            <div class="tour-category-img lazy-bg" data-bg="https://images.unsplash.com/photo-1668717096562-5a14dbf9454e?w=600&q=80&fm=webp"></div>
            <div class="tour-category-body">
                <h3 data-t="tour_cat_ingoing">{{ __('site.ingoing_tours') }}</h3>
                <p data-t="tour_cat_ingoing_desc">{{ __('site.ingoing_desc') }}</p>
            </div>
        </a>

        <a href="{{ lurl('/tours/outgoing') }}" class="tour-category-card reveal-scale">
            <div class="tour-category-img lazy-bg" data-bg="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80&fm=webp"></div>
            <div class="tour-category-body">
                <h3 data-t="tour_cat_outgoing">{{ __('site.outgoing_tours') }}</h3>
                <p data-t="tour_cat_outgoing_desc">{{ __('site.outgoing_desc') }}</p>
            </div>
        </a>

        <a href="{{ lurl('/tours/transfer') }}" class="tour-category-card reveal-scale">
            <div class="tour-category-img lazy-bg" data-bg="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=600&q=80&fm=webp"></div>
            <div class="tour-category-body">
                <h3 data-t="tour_cat_transfer">{{ __('site.transfer') }}</h3>
                <p data-t="tour_cat_transfer_desc">{{ __('site.transfer_desc') }}</p>
            </div>
        </a>

    </div>
</section>

<section id="visa-support" class="visa-support reveal has-blobs">
    <div class="blob blob-2" aria-hidden="true"></div>
    <div class="blob blob-3" aria-hidden="true"></div>
    <div class="visa-container">
        <div class="visa-content">
            <h2 data-t="visa_title">{{ __('site.visa_title') }}</h2>
            <p class="visa-subtitle" data-t="visa_subtitle">{{ __('site.visa_subtitle') }}</p>
            <div class="visa-features">
                <div class="visa-feature reveal-left">
                    <span class="visa-icon">&#128196;</span>
                    <div>
                        <h4 data-t="visa_feat1_title">{{ __('site.visa_feat1_title') }}</h4>
                        <p data-t="visa_feat1_desc">{{ __('site.visa_feat1_desc') }}</p>
                    </div>
                </div>
                <div class="visa-feature reveal-left">
                    <span class="visa-icon">&#9201;</span>
                    <div>
                        <h4 data-t="visa_feat2_title">{{ __('site.visa_feat2_title') }}</h4>
                        <p data-t="visa_feat2_desc">{{ __('site.visa_feat2_desc') }}</p>
                    </div>
                </div>
                <div class="visa-feature reveal-left">
                    <span class="visa-icon">&#127758;</span>
                    <div>
                        <h4 data-t="visa_feat3_title">{{ __('site.visa_feat3_title') }}</h4>
                        <p data-t="visa_feat3_desc">{{ __('site.visa_feat3_desc') }}</p>
                    </div>
                </div>
                <div class="visa-feature reveal-left">
                    <span class="visa-icon">&#128222;</span>
                    <div>
                        <h4 data-t="visa_feat4_title">{{ __('site.visa_feat4_title') }}</h4>
                        <p data-t="visa_feat4_desc">{{ __('site.visa_feat4_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="visa-info-box">
                <p data-t="visa_info">{{ __('site.visa_info') }}</p>
            </div>
            <a href="{{ lurl('/contact') }}" class="btn btn-visa" data-t="visa_cta">{{ __('site.visa_cta') }} &#8594;</a>
        </div>
    </div>
</section>

<section class="stats-bar">
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-number" data-target="5000">0</span><span class="stat-plus">+</span>
            <span class="stat-label" data-t="stat_travelers">{{ __('site.stat_travelers') }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-number" data-target="29">0</span>
            <span class="stat-label" data-t="stat_destinations">{{ __('site.stat_destinations') }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-number" data-target="3">0</span>
            <span class="stat-label" data-t="stat_branches">{{ __('site.stat_branches') }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-number" data-target="10">0</span><span class="stat-plus">+</span>
            <span class="stat-label" data-t="stat_years">{{ __('site.stat_years') }}</span>
        </div>
    </div>
</section>

<section id="destinations" class="destinations reveal has-blobs">
    <div class="blob blob-1" aria-hidden="true"></div>
    <div class="blob blob-2" aria-hidden="true"></div>
    <h2 data-t="popular">{{ __('site.popular') }}</h2>
    <p class="section-subtitle" data-t="popular_subtitle">{{ __('site.popular_subtitle') }}</p>
    <div class="card-grid">
        @foreach($destinations->take(6) as $dest)
        <div class="card reveal-scale">
            <a href="{{ lurl('/destinations/' . $dest['slug']) }}">
                @if(!empty($dest['image_url']))
                <div class="card-image lazy-bg" data-bg="{{ $dest['image_url'] }}"></div>
                @else
                <div class="card-image" style="background-color: {{ $dest['color'] }};">
                    <span class="card-emoji">{{ $dest['emoji'] }}</span>
                </div>
                @endif
                <div class="card-body">
                    <h3 data-t="dest_name_{!! $dest['id'] !!}">{{ $dest['name'] }}</h3>
                    <p data-t="dest_desc_{!! $dest['id'] !!}">{{ $dest['description'] }}</p>
                    <span class="price" data-base-price="{!! $dest['price_from'] !!}">From ${!! number_format($dest['price_from'], 0) !!}</span>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    <div class="section-cta">
        <a href="{{ lurl('/destinations') }}" class="btn btn-outline-dark" data-t="view_all_dest">{{ __('site.view_all_dest') }} &#8594;</a>
    </div>
</section>

<section id="about" class="about reveal has-blobs">
    <div class="blob blob-4" aria-hidden="true"></div>
    <div class="blob blob-5" aria-hidden="true"></div>
    <h2 data-t="why_travel">{{ __('site.why_travel') }}</h2>
    <div class="features">
        <div class="feature reveal" style="transition-delay: 0s;">
            <div class="feature-icon">&#9992;</div>
            <h3 data-t="best_flights">{{ __('site.best_flights') }}</h3>
            <p data-t="best_flights_desc">{{ __('site.best_flights_desc') }}</p>
        </div>
        <div class="feature reveal" style="transition-delay: 0.15s;">
            <div class="feature-icon">&#127960;</div>
            <h3 data-t="top_hotels">{{ __('site.top_hotels') }}</h3>
            <p data-t="top_hotels_desc">{{ __('site.top_hotels_desc') }}</p>
        </div>
        <div class="feature reveal" style="transition-delay: 0.3s;">
            <div class="feature-icon">&#128230;</div>
            <h3 data-t="easy_booking">{{ __('site.easy_booking') }}</h3>
            <p data-t="easy_booking_desc">{{ __('site.easy_booking_desc') }}</p>
        </div>
        <div class="feature reveal" style="transition-delay: 0.45s;">
            <div class="feature-icon">&#128222;</div>
            <h3 data-t="support_247">{{ __('site.support_247') }}</h3>
            <p data-t="support_247_desc">{{ __('site.support_247_desc') }}</p>
        </div>
    </div>
</section>

<section class="testimonials reveal has-blobs">
    <div class="blob blob-3" aria-hidden="true"></div>
    <h2 data-t="testimonials_title">{{ __('site.testimonials_title') }}</h2>
    <p class="section-subtitle" data-t="testimonials_subtitle">{{ __('site.testimonials_subtitle') }}</p>
    <div class="testimonials-grid">
        <div class="testimonial-card reveal-scale">
            <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <p class="testimonial-text">"Touristik made our honeymoon to Greece absolutely perfect. Every detail was planned, from the flights to the seaside hotel. Highly recommend!"</p>
            <div class="testimonial-author">
                <div class="testimonial-avatar">A</div>
                <div>
                    <strong>Anna K.</strong>
                    <span>Greece Tour, 2025</span>
                </div>
            </div>
        </div>
        <div class="testimonial-card reveal-scale">
            <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <p class="testimonial-text">"Best visa support service in Yerevan. They handled everything for our family's Schengen visa in just 5 days. Professional and friendly team."</p>
            <div class="testimonial-author">
                <div class="testimonial-avatar">D</div>
                <div>
                    <strong>David M.</strong>
                    <span>Visa Support, 2025</span>
                </div>
            </div>
        </div>
        <div class="testimonial-card reveal-scale">
            <div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <p class="testimonial-text">"We used Touristik for our corporate retreat — 30 people, flights, hotels, and transfers. Everything was seamless. Will use again for sure."</p>
            <div class="testimonial-author">
                <div class="testimonial-avatar">S</div>
                <div>
                    <strong>Sargis T.</strong>
                    <span>Corporate Travel, 2026</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="partners reveal">
    <h2 data-t="partners_title">{{ __('site.partners_title') }}</h2>
    <p class="section-subtitle" data-t="partners_subtitle">{{ __('site.partners_subtitle') }}</p>
    <div class="partners-track">
        <img class="partner-logo" src="https://logo.clearbit.com/turkishairlines.com" alt="Turkish Airlines" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/flydubai.com" alt="FlyDubai" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/qatarairways.com" alt="Qatar Airways" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/emirates.com" alt="Emirates" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/lufthansa.com" alt="Lufthansa" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/booking.com" alt="Booking.com" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/hotelbeds.com" alt="Hotelbeds" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/airbnb.com" alt="Airbnb" loading="lazy">
        <!-- Duplicated for seamless infinite scroll -->
        <img class="partner-logo" src="https://logo.clearbit.com/turkishairlines.com" alt="Turkish Airlines" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/flydubai.com" alt="FlyDubai" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/qatarairways.com" alt="Qatar Airways" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/emirates.com" alt="Emirates" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/lufthansa.com" alt="Lufthansa" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/booking.com" alt="Booking.com" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/hotelbeds.com" alt="Hotelbeds" loading="lazy">
        <img class="partner-logo" src="https://logo.clearbit.com/airbnb.com" alt="Airbnb" loading="lazy">
    </div>
</section>

<div class="recently-viewed" id="recentlyViewed" style="display:none;">
    <h3 data-t="recently_viewed">&#128065; {{ __('site.recently_viewed') }}</h3>
    <div class="rv-strip" id="rvStrip"></div>
</div>

<section id="faq" class="faq-section reveal has-blobs">
    <div class="blob blob-1" aria-hidden="true"></div>
    <div class="blob blob-5" aria-hidden="true"></div>
    <h2 data-t="faq_title">{{ __('site.faq_title') }}</h2>
    <p class="section-subtitle" data-t="faq_subtitle">{{ __('site.faq_subtitle') }}</p>
    <div class="faq-list">
        <div class="faq-item">
            <button class="faq-question" aria-expanded="false">
                <span data-t="faq_q1">{{ __('site.faq_q1') }}</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer" role="region">
                <p data-t="faq_a1">{{ __('site.faq_a1') }}</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" aria-expanded="false">
                <span data-t="faq_q2">{{ __('site.faq_q2') }}</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer" role="region">
                <p data-t="faq_a2">{{ __('site.faq_a2') }}</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" aria-expanded="false">
                <span data-t="faq_q3">{{ __('site.faq_q3') }}</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer" role="region">
                <p data-t="faq_a3">{{ __('site.faq_a3') }}</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" aria-expanded="false">
                <span data-t="faq_q4">{{ __('site.faq_q4') }}</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer" role="region">
                <p data-t="faq_a4">{{ __('site.faq_a4') }}</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" aria-expanded="false">
                <span data-t="faq_q5">{{ __('site.faq_q5') }}</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer" role="region">
                <p data-t="faq_a5">{{ __('site.faq_a5') }}</p>
            </div>
        </div>
    </div>
</section>

@verbatim
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "How do I book a flight or tour package?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Simply use our search form on the homepage to find flights or packages. Select your dates, passengers, and destination, then click Search. You can also contact us directly by phone or email for personalized assistance."
            }
        },
        {
            "@type": "Question",
            "name": "Do you provide visa support?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Yes! We offer full visa support including invitation letters, e-visa assistance, and consultation. Standard processing takes 5-7 business days, with an express option in 2-3 days. Citizens of 60+ countries can enter Armenia visa-free."
            }
        },
        {
            "@type": "Question",
            "name": "What payment methods do you accept?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "We accept cash (AMD, USD, EUR, RUB), bank transfers, and major credit/debit cards (Visa, MasterCard). Payment can be made at any of our three branches in Yerevan or online via bank transfer."
            }
        },
        {
            "@type": "Question",
            "name": "Can I cancel or modify my booking?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Cancellation and modification policies depend on the airline and hotel. Most bookings can be modified up to 48 hours before departure. Contact our 24/7 support team for assistance with changes to your reservation."
            }
        },
        {
            "@type": "Question",
            "name": "Do you offer group or corporate travel?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Absolutely! We specialize in group tours, corporate travel, and MICE (Meetings, Incentives, Conferences, Events). Contact us for customized group rates and tailored itineraries for your team or organization."
            }
        }
    ]
}
</script>
@endverbatim
@endsection
