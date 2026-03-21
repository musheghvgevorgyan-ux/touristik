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

<section id="destinations" class="destinations">
    <h2 data-t="popular">Popular Destinations</h2>
    <div class="card-grid">
        <?php foreach (array_slice($destinations, 0, 6) as $dest): ?>
        <div class="card">
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
</section>

<section id="about" class="about">
    <h2 data-t="why_travel">Why Travel With Us</h2>
    <div class="features">
        <div class="feature">
            <div class="feature-icon">&#9992;</div>
            <h3 data-t="best_flights">Best Flights</h3>
            <p data-t="best_flights_desc">We partner with top airlines to get you the best deals on flights worldwide.</p>
        </div>
        <div class="feature">
            <div class="feature-icon">&#127960;</div>
            <h3 data-t="top_hotels">Top Hotels</h3>
            <p data-t="top_hotels_desc">Hand-picked accommodations ranging from cozy boutiques to luxury resorts.</p>
        </div>
        <div class="feature">
            <div class="feature-icon">&#128230;</div>
            <h3 data-t="easy_booking">Easy Booking</h3>
            <p data-t="easy_booking_desc">Simple and secure booking process with flexible cancellation policies.</p>
        </div>
    </div>
</section>
