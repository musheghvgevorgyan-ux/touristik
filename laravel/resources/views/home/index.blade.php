@extends('layouts.main')

@section('title', 'Touristik - Travel Club')

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
        <h1 class="hero-typed" data-t="hero_title">{{ $heroTitle }}</h1>
        <p class="hero-subtitle-fade" data-t="hero_subtitle">{{ $heroSubtitle }}</p>
        <div class="trip-toggle">
            <button type="button" class="trip-btn active" data-type="roundtrip" data-t="roundtrip">Round Trip</button>
            <button type="button" class="trip-btn" data-type="oneway" data-t="oneway">One Way</button>
            <button type="button" class="trip-btn" data-type="packages" data-t="packages">Packages</button>
        </div>
        <form class="hero-search" method="GET" action="/hotels/search">
            <input type="hidden" name="trip" id="trip-type" value="roundtrip">
            <div class="search-field flight-field">
                <label data-t="from">From</label>
                <div class="city-autocomplete">
                    <input type="text" name="from" class="city-input" id="cityFrom" value="Yerevan" autocomplete="off" data-tp="type_city">
                    <div class="city-dropdown" id="dropdownFrom"></div>
                </div>
            </div>
            <div class="search-field flight-field">
                <label data-t="to">To</label>
                <div class="city-autocomplete">
                    <input type="text" name="to" class="city-input" id="cityTo" value="Moscow" autocomplete="off" data-tp="type_city">
                    <div class="city-dropdown" id="dropdownTo"></div>
                </div>
            </div>
            <div class="search-field package-field" style="display:none;">
                <label data-t="from">From</label>
                <div class="city-autocomplete">
                    <input type="text" name="pkg_from" class="city-input" value="Yerevan" autocomplete="off" readonly>
                </div>
            </div>
            <div class="search-field package-field" style="display:none;">
                <label data-t="to">To</label>
                <div class="city-autocomplete">
                    <input type="text" name="pkg_to" id="pkgCountry" class="city-input pkg-city-input" value="" autocomplete="off" data-tp="type_city">
                    <div class="city-dropdown" id="dropdownPkg"></div>
                </div>
            </div>
            <div class="search-field depart-field">
                <label data-t="depart">Depart</label>
                <input type="date" name="date" value="{!! date('Y-m-d', strtotime('+7 days')) !!}">
            </div>
            <div class="search-field return-field">
                <label data-t="return_date">Return</label>
                <input type="date" name="return_date" value="{!! date('Y-m-d', strtotime('+14 days')) !!}">
            </div>
            <div class="persons-row">
                <div class="search-field persons-field">
                    <label data-t="adults">Adults</label>
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
                    <label data-t="children">Children</label>
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
            <button type="submit" class="btn search-btn" data-t="search">Search</button>
        </form>
    </div>
</section>

<section id="tours" class="tours-section reveal">
    <h2 data-t="tours_title">Tours</h2>
    <p class="section-subtitle tours-subtitle" data-t="tours_subtitle">Explore our travel services</p>
    <div class="tours-grid">

        <a href="/tours/ingoing" class="tour-category-card reveal-scale">
            <div class="tour-category-img lazy-bg" data-bg="https://images.unsplash.com/photo-1668717096562-5a14dbf9454e?w=600&q=80&fm=webp"></div>
            <div class="tour-category-body">
                <h3 data-t="tour_cat_ingoing">Ingoing Tours</h3>
                <p data-t="tour_cat_ingoing_desc">Tours</p>
            </div>
        </a>

        <a href="/tours/outgoing" class="tour-category-card reveal-scale">
            <div class="tour-category-img lazy-bg" data-bg="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80&fm=webp"></div>
            <div class="tour-category-body">
                <h3 data-t="tour_cat_outgoing">Outgoing Tours</h3>
                <p data-t="tour_cat_outgoing_desc">Tours</p>
            </div>
        </a>

        <a href="/tours/transfer" class="tour-category-card reveal-scale">
            <div class="tour-category-img lazy-bg" data-bg="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=600&q=80&fm=webp"></div>
            <div class="tour-category-body">
                <h3 data-t="tour_cat_transfer">Transfer</h3>
                <p data-t="tour_cat_transfer_desc">Transfers</p>
            </div>
        </a>

    </div>
</section>

<section id="visa-support" class="visa-support reveal has-blobs">
    <div class="blob blob-2" aria-hidden="true"></div>
    <div class="blob blob-3" aria-hidden="true"></div>
    <div class="visa-container">
        <div class="visa-content">
            <h2 data-t="visa_title">Visa Support for Armenia</h2>
            <p class="visa-subtitle" data-t="visa_subtitle">We handle all the paperwork so you can focus on your trip</p>
            <div class="visa-features">
                <div class="visa-feature reveal-left">
                    <span class="visa-icon">&#128196;</span>
                    <div>
                        <h4 data-t="visa_feat1_title">Invitation Letter</h4>
                        <p data-t="visa_feat1_desc">Official invitation letters for visa applications to Armenian consulates worldwide.</p>
                    </div>
                </div>
                <div class="visa-feature reveal-left">
                    <span class="visa-icon">&#9201;</span>
                    <div>
                        <h4 data-t="visa_feat2_title">Fast Processing</h4>
                        <p data-t="visa_feat2_desc">Standard processing in 5-7 business days, express option available in 2-3 days.</p>
                    </div>
                </div>
                <div class="visa-feature reveal-left">
                    <span class="visa-icon">&#127758;</span>
                    <div>
                        <h4 data-t="visa_feat3_title">E-Visa Assistance</h4>
                        <p data-t="visa_feat3_desc">Full guidance through the Armenian e-visa application process for eligible countries.</p>
                    </div>
                </div>
                <div class="visa-feature reveal-left">
                    <span class="visa-icon">&#128222;</span>
                    <div>
                        <h4 data-t="visa_feat4_title">24/7 Consultation</h4>
                        <p data-t="visa_feat4_desc">Our visa specialists are available around the clock to answer your questions.</p>
                    </div>
                </div>
            </div>
            <div class="visa-info-box">
                <p data-t="visa_info">&#128712; Citizens of 60+ countries can enter Armenia visa-free for up to 180 days. Not sure about your country? Contact us for a free consultation.</p>
            </div>
            <a href="/contact" class="btn btn-visa" data-t="visa_cta">Request Visa Support &#8594;</a>
        </div>
    </div>
</section>

<section class="stats-bar">
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-number" data-target="5000">0</span><span class="stat-plus">+</span>
            <span class="stat-label" data-t="stat_travelers">Happy Travelers</span>
        </div>
        <div class="stat-item">
            <span class="stat-number" data-target="29">0</span>
            <span class="stat-label" data-t="stat_destinations">Destinations</span>
        </div>
        <div class="stat-item">
            <span class="stat-number" data-target="3">0</span>
            <span class="stat-label" data-t="stat_branches">Branches</span>
        </div>
        <div class="stat-item">
            <span class="stat-number" data-target="10">0</span><span class="stat-plus">+</span>
            <span class="stat-label" data-t="stat_years">Years Experience</span>
        </div>
    </div>
</section>

<section id="destinations" class="destinations reveal has-blobs">
    <div class="blob blob-1" aria-hidden="true"></div>
    <div class="blob blob-2" aria-hidden="true"></div>
    <h2 data-t="popular">Popular Destinations</h2>
    <p class="section-subtitle" data-t="popular_subtitle">Handpicked travel experiences from Yerevan to the world</p>
    <div class="card-grid">
        @foreach($destinations->take(6) as $dest)
        <div class="card reveal-scale">
            <a href="/destinations/{{ $dest['slug'] }}">
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
        <a href="/destinations" class="btn btn-outline-dark" data-t="view_all_dest">View All Destinations &#8594;</a>
    </div>
</section>

<section id="about" class="about reveal has-blobs">
    <div class="blob blob-4" aria-hidden="true"></div>
    <div class="blob blob-5" aria-hidden="true"></div>
    <h2 data-t="why_travel">Why Travel With Us</h2>
    <div class="features">
        <div class="feature reveal" style="transition-delay: 0s;">
            <div class="feature-icon">&#9992;</div>
            <h3 data-t="best_flights">Best Flights</h3>
            <p data-t="best_flights_desc">We partner with top airlines to get you the best deals on flights worldwide.</p>
        </div>
        <div class="feature reveal" style="transition-delay: 0.15s;">
            <div class="feature-icon">&#127960;</div>
            <h3 data-t="top_hotels">Top Hotels</h3>
            <p data-t="top_hotels_desc">Hand-picked accommodations ranging from cozy boutiques to luxury resorts.</p>
        </div>
        <div class="feature reveal" style="transition-delay: 0.3s;">
            <div class="feature-icon">&#128230;</div>
            <h3 data-t="easy_booking">Easy Booking</h3>
            <p data-t="easy_booking_desc">Simple and secure booking process with flexible cancellation policies.</p>
        </div>
        <div class="feature reveal" style="transition-delay: 0.45s;">
            <div class="feature-icon">&#128222;</div>
            <h3 data-t="support_247">24/7 Support</h3>
            <p data-t="support_247_desc">Our team is available around the clock to assist you before, during, and after your trip.</p>
        </div>
    </div>
</section>

<section class="partners reveal">
    <h2 data-t="partners_title">Our Partners</h2>
    <p class="section-subtitle" data-t="partners_subtitle">Trusted by leading airlines and hotel chains worldwide</p>
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
    <h3 data-t="recently_viewed">&#128065; Recently Viewed</h3>
    <div class="rv-strip" id="rvStrip"></div>
</div>

<section id="faq" class="faq-section reveal has-blobs">
    <div class="blob blob-1" aria-hidden="true"></div>
    <div class="blob blob-5" aria-hidden="true"></div>
    <h2 data-t="faq_title">Frequently Asked Questions</h2>
    <p class="section-subtitle" data-t="faq_subtitle">Everything you need to know before your trip</p>
    <div class="faq-list">
        <div class="faq-item">
            <button class="faq-question" aria-expanded="false">
                <span data-t="faq_q1">How do I book a flight or tour package?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer" role="region">
                <p data-t="faq_a1">Simply use our search form on the homepage to find flights or packages. Select your dates, passengers, and destination, then click Search. You can also contact us directly by phone or email for personalized assistance.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" aria-expanded="false">
                <span data-t="faq_q2">Do you provide visa support?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer" role="region">
                <p data-t="faq_a2">Yes! We offer full visa support including invitation letters, e-visa assistance, and consultation. Standard processing takes 5-7 business days, with an express option in 2-3 days. Citizens of 60+ countries can enter Armenia visa-free.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" aria-expanded="false">
                <span data-t="faq_q3">What payment methods do you accept?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer" role="region">
                <p data-t="faq_a3">We accept cash (AMD, USD, EUR, RUB), bank transfers, and major credit/debit cards (Visa, MasterCard). Payment can be made at any of our three branches in Yerevan or online via bank transfer.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" aria-expanded="false">
                <span data-t="faq_q4">Can I cancel or modify my booking?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer" role="region">
                <p data-t="faq_a4">Cancellation and modification policies depend on the airline and hotel. Most bookings can be modified up to 48 hours before departure. Contact our 24/7 support team for assistance with changes to your reservation.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" aria-expanded="false">
                <span data-t="faq_q5">Do you offer group or corporate travel?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer" role="region">
                <p data-t="faq_a5">Absolutely! We specialize in group tours, corporate travel, and MICE (Meetings, Incentives, Conferences, Events). Contact us for customized group rates and tailored itineraries for your team or organization.</p>
            </div>
        </div>
    </div>
</section>
@endsection
