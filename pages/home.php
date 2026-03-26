<?php
$destinations = getDestinationsWithLivePrices($pdo);
$heroTitle = getSetting($pdo, 'hero_title', 'Explore the World with Touristik');
$heroSubtitle = getSetting($pdo, 'hero_subtitle', 'Discover breathtaking destinations and create unforgettable memories');
?>

<section id="home" class="hero">
    <div class="hero-content">
        <h1 data-t="hero_title"><?= htmlspecialchars($heroTitle) ?></h1>
        <p data-t="hero_subtitle"><?= htmlspecialchars($heroSubtitle) ?></p>
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
                <select name="from">
                    <optgroup label="Armenia">
                        <option value="Yerevan" selected>Yerevan</option>
                    </optgroup>
                    <optgroup label="Egypt">
                        <option value="Cairo">Cairo</option>
                        <option value="El Alamein">El Alamein</option>
                        <option value="Sharm El Sheikh">Sharm El Sheikh</option>
                        <option value="Hurghada">Hurghada</option>
                    </optgroup>
                    <optgroup label="France">
                        <option value="Paris">Paris</option>
                    </optgroup>
                    <optgroup label="Georgia">
                        <option value="Tbilisi">Tbilisi</option>
                    </optgroup>
                    <optgroup label="Germany">
                        <option value="Berlin">Berlin</option>
                        <option value="Frankfurt">Frankfurt</option>
                        <option value="Munich">Munich</option>
                    </optgroup>
                    <optgroup label="Greece">
                        <option value="Athens">Athens</option>
                        <option value="Halkidiki">Halkidiki</option>
                        <option value="Crete">Crete</option>
                    </optgroup>
                    <optgroup label="Italy">
                        <option value="Rome">Rome</option>
                        <option value="Milan">Milan</option>
                    </optgroup>
                    <optgroup label="Montenegro">
                        <option value="Tivat">Tivat</option>
                    </optgroup>
                    <optgroup label="Russia">
                        <option value="Moscow">Moscow</option>
                        <option value="Sochi">Sochi</option>
                    </optgroup>
                    <optgroup label="Spain">
                        <option value="Barcelona">Barcelona</option>
                        <option value="Madrid">Madrid</option>
                    </optgroup>
                    <optgroup label="Thailand">
                        <option value="Bangkok">Bangkok</option>
                        <option value="Phuket">Phuket</option>
                    </optgroup>
                    <optgroup label="Turkey">
                        <option value="Istanbul">Istanbul</option>
                        <option value="Antalya">Antalya</option>
                    </optgroup>
                    <optgroup label="UAE">
                        <option value="Dubai">Dubai</option>
                    </optgroup>
                    <optgroup label="UK">
                        <option value="London">London</option>
                    </optgroup>
                    <optgroup label="USA">
                        <option value="New York">New York</option>
                        <option value="Los Angeles">Los Angeles</option>
                        <option value="Miami">Miami</option>
                    </optgroup>
                </select>
            </div>
            <div class="search-field flight-field">
                <label data-t="to">To</label>
                <select name="to">
                    <optgroup label="Armenia">
                        <option value="Yerevan">Yerevan</option>
                    </optgroup>
                    <optgroup label="Egypt">
                        <option value="Cairo">Cairo</option>
                        <option value="El Alamein">El Alamein</option>
                        <option value="Sharm El Sheikh">Sharm El Sheikh</option>
                        <option value="Hurghada">Hurghada</option>
                    </optgroup>
                    <optgroup label="France">
                        <option value="Paris">Paris</option>
                    </optgroup>
                    <optgroup label="Georgia">
                        <option value="Tbilisi">Tbilisi</option>
                    </optgroup>
                    <optgroup label="Germany">
                        <option value="Berlin">Berlin</option>
                        <option value="Frankfurt">Frankfurt</option>
                        <option value="Munich">Munich</option>
                    </optgroup>
                    <optgroup label="Greece">
                        <option value="Athens">Athens</option>
                        <option value="Halkidiki">Halkidiki</option>
                        <option value="Crete">Crete</option>
                    </optgroup>
                    <optgroup label="Italy">
                        <option value="Rome">Rome</option>
                        <option value="Milan">Milan</option>
                    </optgroup>
                    <optgroup label="Montenegro">
                        <option value="Tivat">Tivat</option>
                    </optgroup>
                    <optgroup label="Russia">
                        <option value="Moscow" selected>Moscow</option>
                        <option value="Sochi">Sochi</option>
                    </optgroup>
                    <optgroup label="Spain">
                        <option value="Barcelona">Barcelona</option>
                        <option value="Madrid">Madrid</option>
                    </optgroup>
                    <optgroup label="Thailand">
                        <option value="Bangkok">Bangkok</option>
                        <option value="Phuket">Phuket</option>
                    </optgroup>
                    <optgroup label="Turkey">
                        <option value="Istanbul">Istanbul</option>
                        <option value="Antalya">Antalya</option>
                    </optgroup>
                    <optgroup label="UAE">
                        <option value="Dubai">Dubai</option>
                    </optgroup>
                    <optgroup label="UK">
                        <option value="London">London</option>
                    </optgroup>
                    <optgroup label="USA">
                        <option value="New York">New York</option>
                        <option value="Los Angeles">Los Angeles</option>
                        <option value="Miami">Miami</option>
                    </optgroup>
                </select>
            </div>
            <div class="search-field package-field" style="display:none;">
                <label data-t="from">From</label>
                <select name="pkg_from">
                    <option value="Yerevan">Yerevan</option>
                </select>
            </div>
            <div class="search-field package-field" style="display:none;">
                <label data-t="to">To</label>
                <select name="pkg_to" id="pkgCountry">
                    <optgroup label="Egypt">
                        <option value="El Alamein, Egypt">El Alamein</option>
                        <option value="Sharm El Sheikh, Egypt">Sharm El Sheikh</option>
                        <option value="Hurghada, Egypt">Hurghada</option>
                    </optgroup>
                    <optgroup label="Greece">
                        <option value="Halkidiki, Greece">Halkidiki</option>
                        <option value="Crete, Greece">Crete</option>
                    </optgroup>
                    <optgroup label="Montenegro">
                        <option value="Tivat, Montenegro">Tivat</option>
                    </optgroup>
                    <optgroup label="Turkey">
                        <option value="Antalya, Turkey">Antalya</option>
                    </optgroup>
                </select>
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
                        <div class="tour-card-img" style="background-image: url('https://images.unsplash.com/photo-1668717096562-5a14dbf9454e?w=1200&q=80');">
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
                        <div class="tour-card-img" style="background-image: url('https://images.unsplash.com/photo-1695571803214-9e6820bffce7?w=1200&q=80');">
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
                        <div class="tour-card-img" style="background-image: url('https://plus.unsplash.com/premium_photo-1670552850982-abe0e83c4391?w=1200&q=80');">
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
                        <div class="tour-card-img" style="background-image: url('https://images.unsplash.com/photo-1624357485917-fbb18b951125?w=1200&q=80');">
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
                        <div class="tour-card-img" style="background-image: url('https://images.unsplash.com/photo-1743366500405-6689bb916fd2?w=1200&q=80');">
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

<section id="visa-support" class="visa-support reveal">
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

<section id="destinations" class="destinations reveal">
    <h2 data-t="popular">Popular Destinations</h2>
    <p class="section-subtitle" data-t="popular_subtitle">Handpicked travel experiences from Yerevan to the world</p>
    <div class="card-grid">
        <?php foreach (array_slice($destinations, 0, 6) as $dest): ?>
        <div class="card reveal-scale">
            <a href="<?= url('destination', ['id' => $dest['id']]) ?>">
                <?php if (!empty($dest['image_url'])): ?>
                <div class="card-image" style="background-image: url('<?= htmlspecialchars($dest['image_url']) ?>');"></div>
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

<section id="about" class="about reveal">
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
