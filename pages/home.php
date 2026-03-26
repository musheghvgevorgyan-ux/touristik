<?php
$destinations = getDestinationsWithLivePrices($pdo);
$heroTitle = getSetting($pdo, 'hero_title', 'Explore the World with Touristik');
$heroSubtitle = getSetting($pdo, 'hero_subtitle', 'Discover breathtaking destinations and create unforgettable memories');
?>

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
        <h1 class="hero-typed" data-t="hero_title"><?= htmlspecialchars($heroTitle) ?></h1>
        <p class="hero-subtitle-fade" data-t="hero_subtitle"><?= htmlspecialchars($heroSubtitle) ?></p>
        <div class="trip-toggle">
            <button type="button" class="trip-btn active" data-type="roundtrip" data-t="roundtrip">Round Trip</button>
            <button type="button" class="trip-btn" data-type="oneway" data-t="oneway">One Way</button>
            <button type="button" class="trip-btn" data-type="packages" data-t="packages">Packages</button>
        </div>
        <form class="hero-search" method="GET" action="index.php">
            <input type="hidden" name="page" value="search">
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
                <input type="date" name="date" value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
            </div>
            <div class="search-field return-field">
                <label data-t="return_date">Return</label>
                <input type="date" name="return_date" value="<?= date('Y-m-d', strtotime('+14 days')) ?>">
            </div>
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
                <select name="children">
                    <option value="0" selected>0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4+</option>
                </select>
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

<section id="incoming-tours" class="incoming-tours reveal">
    <h2 data-t="incoming_title">Incoming Tourism Programs</h2>
    <p class="section-subtitle" data-t="incoming_subtitle">Discover the beauty of Armenia with our curated tour packages</p>
    <div class="tour-slider-wrapper">
        <button class="tour-slider-btn tour-prev">&#10094;</button>
        <div class="tour-slider">
            <div class="tour-slider-track">

                <div class="tour-slide">
                    <div class="tour-card-fancy">
                        <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1668717096562-5a14dbf9454e?w=1200&q=80&fm=webp">
                            <div class="tour-card-overlay">
                                <span class="tour-duration-badge">3 Days</span>
                                <div class="tour-card-info">
                                    <h3 data-t="tour_name_1">Classic Yerevan</h3>
                                    <p data-t="tour_desc_1">Explore the Pink City: Republic Square, Cascade Complex, Matenadaran, and the vibrant nightlife of Northern Avenue.</p>
                                    <div class="tour-tags">
                                        <span>&#127963; City Tour</span>
                                        <span>&#127860; Food & Wine</span>
                                        <span>&#127751; Nightlife</span>
                                    </div>
                                    <div class="tour-card-bottom">
                                        <span class="tour-price-tag">From <strong>$199</strong> /person</span>
                                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour">Inquire &#8594;</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tour-slide">
                    <div class="tour-card-fancy">
                        <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1695571803214-9e6820bffce7?w=1200&q=80&fm=webp">
                            <div class="tour-card-overlay">
                                <span class="tour-duration-badge">5 Days</span>
                                <div class="tour-card-info">
                                    <h3 data-t="tour_name_2">Ancient Temples & Monasteries</h3>
                                    <p data-t="tour_desc_2">Visit Garni Temple, Geghard Monastery, Tatev, Noravank, and Khor Virap with breathtaking views of Mount Ararat.</p>
                                    <div class="tour-tags">
                                        <span>&#9968; Cultural</span>
                                        <span>&#127956; UNESCO Sites</span>
                                        <span>&#9968; Historical</span>
                                    </div>
                                    <div class="tour-card-bottom">
                                        <span class="tour-price-tag">From <strong>$349</strong> /person</span>
                                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour">Inquire &#8594;</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tour-slide">
                    <div class="tour-card-fancy">
                        <div class="tour-card-img lazy-bg" data-bg="https://plus.unsplash.com/premium_photo-1670552850982-abe0e83c4391?w=1200&q=80&fm=webp">
                            <div class="tour-card-overlay">
                                <span class="tour-duration-badge">7 Days</span>
                                <div class="tour-card-info">
                                    <h3 data-t="tour_name_3">Grand Armenia Tour</h3>
                                    <p data-t="tour_desc_3">The ultimate Armenian experience: Lake Sevan, Dilijan, Jermuk, wine tasting in Areni, and the Silk Road trails.</p>
                                    <div class="tour-tags">
                                        <span>&#127863; Wine Tasting</span>
                                        <span>&#127956; Nature</span>
                                        <span>&#128507; Scenic</span>
                                    </div>
                                    <div class="tour-card-bottom">
                                        <span class="tour-price-tag">From <strong>$599</strong> /person</span>
                                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour">Inquire &#8594;</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tour-slide">
                    <div class="tour-card-fancy">
                        <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1624357485917-fbb18b951125?w=1200&q=80&fm=webp">
                            <div class="tour-card-overlay">
                                <span class="tour-duration-badge">4 Days</span>
                                <div class="tour-card-info">
                                    <h3 data-t="tour_name_4">Adventure & Hiking</h3>
                                    <p data-t="tour_desc_4">Trek through the stunning landscapes of Dilijan National Park, Aragats summit, and the Lastiver caves.</p>
                                    <div class="tour-tags">
                                        <span>&#127699; Hiking</span>
                                        <span>&#9968; Adventure</span>
                                        <span>&#127956; Mountains</span>
                                    </div>
                                    <div class="tour-card-bottom">
                                        <span class="tour-price-tag">From <strong>$279</strong> /person</span>
                                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour">Inquire &#8594;</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tour-slide">
                    <div class="tour-card-fancy">
                        <div class="tour-card-img lazy-bg" data-bg="https://images.unsplash.com/photo-1743366500405-6689bb916fd2?w=1200&q=80&fm=webp">
                            <div class="tour-card-overlay">
                                <span class="tour-duration-badge">6 Days</span>
                                <div class="tour-card-info">
                                    <h3 data-t="tour_name_5">Wine & Gastronomy Trail</h3>
                                    <p data-t="tour_desc_5">Taste the world's oldest wine tradition in Areni, savor Armenian BBQ, lavash baking, and brandy tasting at Ararat factory.</p>
                                    <div class="tour-tags">
                                        <span>&#127863; Wine</span>
                                        <span>&#127860; Gastronomy</span>
                                        <span>&#127943; Brandy</span>
                                    </div>
                                    <div class="tour-card-bottom">
                                        <span class="tour-price-tag">From <strong>$449</strong> /person</span>
                                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour">Inquire &#8594;</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <button class="tour-slider-btn tour-next">&#10095;</button>
    </div>
    <div class="tour-dots"></div>
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
            <a href="<?= url('contact') ?>" class="btn btn-visa" data-t="visa_cta">Request Visa Support &#8594;</a>
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
        <?php foreach (array_slice($destinations, 0, 6) as $dest): ?>
        <div class="card reveal-scale">
            <a href="<?= url('destination', ['id' => $dest['id']]) ?>">
                <?php if (!empty($dest['image_url'])): ?>
                <div class="card-image lazy-bg" data-bg="<?= htmlspecialchars($dest['image_url']) ?>"></div>
                <?php else: ?>
                <div class="card-image" style="background-color: <?= htmlspecialchars($dest['color']) ?>;">
                    <span class="card-emoji"><?= htmlspecialchars($dest['emoji']) ?></span>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <h3 data-t="dest_name_<?= $dest['id'] ?>"><?= htmlspecialchars($dest['name']) ?></h3>
                    <p data-t="dest_desc_<?= $dest['id'] ?>"><?= htmlspecialchars($dest['description']) ?></p>
                    <span class="price" data-base-price="<?= $dest['price'] ?>">From $<?= number_format($dest['price'], 0) ?></span>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="section-cta">
        <a href="<?= url('destinations') ?>" class="btn btn-outline-dark" data-t="view_all_dest">View All Destinations &#8594;</a>
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
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2023/01/Turkish-Airlines-Logo.png" alt="Turkish Airlines" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2023/01/FlyDubai-Logo.png" alt="FlyDubai" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2023/01/Qatar-Airways-Logo.png" alt="Qatar Airways" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2021/09/Emirates-Logo.png" alt="Emirates" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2023/01/Lufthansa-Logo.png" alt="Lufthansa" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2022/05/Booking.com-Logo.png" alt="Booking.com" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2023/01/Hotelbeds-Logo.png" alt="Hotelbeds" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2023/02/Airbnb-Logo.png" alt="Airbnb" loading="lazy">
        <!-- Duplicated for seamless infinite scroll -->
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2023/01/Turkish-Airlines-Logo.png" alt="Turkish Airlines" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2023/01/FlyDubai-Logo.png" alt="FlyDubai" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2023/01/Qatar-Airways-Logo.png" alt="Qatar Airways" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2021/09/Emirates-Logo.png" alt="Emirates" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2023/01/Lufthansa-Logo.png" alt="Lufthansa" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2022/05/Booking.com-Logo.png" alt="Booking.com" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2023/01/Hotelbeds-Logo.png" alt="Hotelbeds" loading="lazy">
        <img class="partner-logo" src="https://logos-world.net/wp-content/uploads/2023/02/Airbnb-Logo.png" alt="Airbnb" loading="lazy">
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
