<?php use App\Helpers\View; ?>

<?php
// Extract search params
$from       = $searchParams['from'] ?? '';
$to         = $searchParams['to'] ?? '';
$toCity     = $searchParams['toCity'] ?? '';
$date       = $searchParams['date'] ?? '';
$returnDate = $searchParams['return_date'] ?? '';
$adults     = $searchParams['adults'] ?? 1;
$children   = $searchParams['children'] ?? 0;
$tripType   = $searchParams['tripType'] ?? 'roundtrip';
$fromCode   = $searchParams['fromCode'] ?? '';
$toCode     = $searchParams['toCode'] ?? '';

$tripLabels = [
    'roundtrip' => 'Round Trip',
    'oneway'    => 'One Way',
    'packages'  => 'Package',
];

$totalPassengers = $adults + $children;
?>

<nav class="breadcrumbs" aria-label="Breadcrumb">
    <a href="/" data-t="breadcrumb_home">Home</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <span class="breadcrumb-current" data-t="search">Search</span>
</nav>

<section class="search-results">
    <div class="search-hero-banner">
        <div class="search-hero-overlay">
            <h2>&#9992; <?= View::e($from) ?> &rarr; <?= View::e($toCity) ?></h2>
            <div class="search-info">
                <span class="search-tag"><span class="tag-icon">&#128203;</span> <?= $tripLabels[$tripType] ?? 'Round Trip' ?></span>
                <?php if ($date): ?>
                    <span class="search-tag"><span class="tag-icon">&#128197;</span> <?= View::date($date) ?></span>
                <?php endif; ?>
                <?php if ($returnDate && $tripType !== 'oneway'): ?>
                    <span class="search-tag"><span class="tag-icon">&#128260;</span> <?= View::date($returnDate) ?></span>
                <?php endif; ?>
                <span class="search-tag"><span class="tag-icon">&#128101;</span> <?= $adults ?> Adult<?= $adults > 1 ? 's' : '' ?><?= $children > 0 ? ", {$children} Child" . ($children > 1 ? 'ren' : '') : '' ?></span>
            </div>
            <a href="/" class="btn btn-outline btn-sm" data-t="new_search">&#8592; New Search</a>
        </div>
    </div>

    <div class="search-body">
        <!-- Flight Results -->
        <div class="search-section">
            <h3 class="section-heading">&#9992; Flights</h3>
            <?php if ($flightError): ?>
                <div class="alert info"><?= View::e($flightError) ?></div>
            <?php else: ?>
                <div class="results-bar">
                    <span class="results-count"><?= count($flights) ?> flight<?= count($flights) !== 1 ? 's' : '' ?> found</span>
                    <span class="results-sort">Sorted by: <strong>Shortest duration</strong></span>
                </div>
                <div class="flight-list">
                    <?php foreach ($flights as $i => $flight): ?>
                    <div class="flight-card <?= $i === 0 ? 'flight-card-best' : '' ?>">
                        <?php if ($i === 0): ?>
                            <div class="flight-badge" data-t="best_price">Best Price</div>
                        <?php endif; ?>
                        <div class="flight-card-top">
                            <div class="flight-airline">
                                <?php if (!empty($flight['airline'])): ?>
                                    <span class="airline-placeholder">&#9992;</span>
                                    <span class="airline-code"><?= View::e($flight['airline']) ?></span>
                                <?php else: ?>
                                    <span class="airline-placeholder">&#9992;</span>
                                <?php endif; ?>
                                <?php if (!empty($flight['flight_number'])): ?>
                                    <span class="flight-num">Flight <?= View::e($flight['flight_number']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flight-route">
                                <div class="flight-city">
                                    <strong><?= View::e($fromCode) ?></strong>
                                    <span class="city-name"><?= View::e($from) ?></span>
                                </div>
                                <div class="flight-arrow">
                                    <?php if (!empty($flight['duration']) && $flight['duration'] > 0): ?>
                                        <div class="flight-duration">
                                            &#128336; <?= floor($flight['duration'] / 60) ?>h <?= $flight['duration'] % 60 ?>m
                                        </div>
                                    <?php endif; ?>
                                    <div class="arrow-visual">
                                        <span class="arrow-dot"></span>
                                        <span class="arrow-line"></span>
                                        <?php if (!empty($flight['transfers']) && $flight['transfers'] > 0): ?>
                                            <?php for ($s = 0; $s < min($flight['transfers'], 3); $s++): ?>
                                                <span class="arrow-stop-dot"></span>
                                                <span class="arrow-line"></span>
                                            <?php endfor; ?>
                                        <?php endif; ?>
                                        <span class="arrow-plane">&#9992;</span>
                                        <span class="arrow-line"></span>
                                        <span class="arrow-dot"></span>
                                    </div>
                                    <div class="arrow-bottom-info">
                                        <span class="transfers <?= empty($flight['transfers']) || $flight['transfers'] == 0 ? 'direct' : 'has-stops' ?>">
                                            <?= empty($flight['transfers']) || $flight['transfers'] == 0 ? '&#10003; Direct' : $flight['transfers'] . ' stop' . ($flight['transfers'] > 1 ? 's' : '') ?>
                                        </span>
                                        <?php if ($tripType !== 'oneway'): ?>
                                            <span class="trip-label"><?= $tripLabels[$tripType] ?? '' ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flight-city">
                                    <strong><?= View::e($toCode) ?></strong>
                                    <span class="city-name"><?= View::e($toCity) ?></span>
                                </div>
                            </div>
                            <div class="flight-price">
                                <span class="flight-price-value" data-base-price="<?= $flight['price'] ?? 0 ?>">$<?= number_format($flight['price'] ?? 0) ?></span>
                                <span class="flight-price-pp" data-t="per_person">per person</span>
                                <?php if ($totalPassengers > 1): ?>
                                    <span class="flight-price-total">$<?= number_format(($flight['price'] ?? 0) * $totalPassengers) ?> total</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flight-card-bottom">
                            <div class="flight-dates">
                                <?php if (!empty($flight['departure_at'])): ?>
                                    <span>&#128197; Depart: <strong><?= View::date($flight['departure_at'], 'D, M d Y') ?></strong></span>
                                <?php endif; ?>
                                <?php if (!empty($flight['return_at']) && $tripType !== 'oneway'): ?>
                                    <span>&#128260; Return: <strong><?= View::date($flight['return_at'], 'D, M d Y') ?></strong></span>
                                <?php endif; ?>
                            </div>
                            <?php $bookingUrl = $flight['booking_url'] ?? '#'; ?>
                            <a href="<?= View::e($bookingUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-book">Book Now &#8594;</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Hotel Results -->
        <div class="search-section">
            <h3 class="section-heading">&#127960; Hotels in <?= View::e($toCity) ?></h3>
            <?php if ($hotelError): ?>
                <div class="alert info"><?= View::e($hotelError) ?></div>
            <?php elseif (empty($hotels)): ?>
                <div class="alert info">No hotels found for this destination and dates.</div>
            <?php else: ?>
                <?php
                    $maxHotelPrice = 0;
                    foreach ($hotels as $h) {
                        if (($h['price'] ?? 0) > $maxHotelPrice) {
                            $maxHotelPrice = $h['price'];
                        }
                    }
                    $maxHotelPrice = ceil($maxHotelPrice / 100) * 100;
                ?>
                <div class="search-filters">
                    <div class="filter-group">
                        <label>Max Price</label>
                        <div style="display:flex;align-items:center;gap:0.5rem;">
                            <input type="range" id="filterPrice" min="0" max="<?= $maxHotelPrice ?>" value="<?= $maxHotelPrice ?>" step="50">
                            <span class="filter-price-label" id="filterPriceLabel">$<?= number_format($maxHotelPrice) ?></span>
                        </div>
                    </div>
                    <div class="filter-group">
                        <label>Stars</label>
                        <div class="star-filter">
                            <button type="button" class="star-filter-btn" data-star="3">3&#11088;</button>
                            <button type="button" class="star-filter-btn" data-star="4">4&#11088;</button>
                            <button type="button" class="star-filter-btn" data-star="5">5&#11088;</button>
                        </div>
                    </div>
                    <div class="filter-group">
                        <label>Sort By</label>
                        <select id="filterSort">
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                            <option value="stars">Star Rating</option>
                        </select>
                    </div>
                </div>
                <div class="results-bar">
                    <span class="results-count"><?= count($hotels) ?> hotel<?= count($hotels) !== 1 ? 's' : '' ?> found</span>
                </div>
                <div class="hotel-list">
                    <?php foreach ($hotels as $i => $hotel): ?>
                    <div class="hotel-card <?= $i === 0 ? 'hotel-card-best' : '' ?>"
                         data-price="<?= $hotel['price'] ?? 0 ?>"
                         data-stars="<?= (int)substr($hotel['stars_num'] ?? '0', 0, 1) ?>">
                        <?php if ($i === 0): ?>
                            <div class="hotel-badge">Best Deal</div>
                        <?php endif; ?>
                        <div class="hotel-card-inner">
                            <div class="hotel-image">
                                <?php if (!empty($hotel['image'])): ?>
                                    <img src="<?= View::e($hotel['image']) ?>" alt="<?= View::e($hotel['name'] ?? 'Hotel') ?>" loading="lazy"
                                         onerror="this.parentElement.innerHTML='<div class=\'hotel-image-placeholder\'>&#127960;</div>'">
                                <?php else: ?>
                                    <div class="hotel-image-placeholder">&#127960;</div>
                                <?php endif; ?>
                            </div>
                            <div class="hotel-info">
                                <div class="hotel-info-top">
                                    <h4 class="hotel-name"><?= View::e($hotel['name'] ?? 'Hotel') ?></h4>
                                    <div class="hotel-stars">
                                        <?php
                                            $starCount = min((int)substr($hotel['stars_num'] ?? '0', 0, 1), 5);
                                            for ($s = 0; $s < $starCount; $s++) echo '&#11088;';
                                        ?>
                                        <span class="star-label"><?= View::e($hotel['stars'] ?? '') ?></span>
                                    </div>
                                    <div class="hotel-details">
                                        <?php if (!empty($hotel['room'])): ?>
                                            <span class="hotel-detail">&#128719; <?= View::e($hotel['room']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($hotel['board'])): ?>
                                            <span class="hotel-detail">&#127860; <?= View::e($hotel['board']) ?></span>
                                        <?php endif; ?>
                                        <span class="hotel-detail">&#127769; <?= $hotel['nights'] ?? 1 ?> night<?= ($hotel['nights'] ?? 1) > 1 ? 's' : '' ?></span>
                                    </div>
                                    <?php if (!empty($hotel['promotions'])): ?>
                                        <div class="hotel-promotions">
                                            <?php foreach ($hotel['promotions'] as $promo): ?>
                                                <span class="hotel-promo-tag">&#127873; <?= View::e($promo['name'] ?? '') ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($hotel['cancellation_policies'])): ?>
                                        <div class="hotel-cancellation-info">
                                            <?php
                                                $firstPolicy = $hotel['cancellation_policies'][0];
                                                $cancelFrom = date('M d, Y', strtotime($firstPolicy['from'] ?? 'now'));
                                            ?>
                                            <span class="cancel-policy">&#128196; Free cancellation before <?= $cancelFrom ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="hotel-price-section">
                                    <div class="hotel-price">
                                        <?php
                                            $displayPrice = (!empty($hotel['hotel_mandatory']) && ($hotel['selling_rate'] ?? 0) > 0)
                                                ? $hotel['selling_rate']
                                                : ($hotel['price'] ?? 0);
                                            $nights = max($hotel['nights'] ?? 1, 1);
                                        ?>
                                        <span class="hotel-price-value"><?= View::e($hotel['currency'] ?? 'EUR') ?> <?= number_format($displayPrice, 2) ?></span>
                                        <span class="hotel-price-detail">total for <?= $nights ?> night<?= $nights > 1 ? 's' : '' ?></span>
                                        <span class="hotel-price-pernight"><?= View::e($hotel['currency'] ?? 'EUR') ?> <?= number_format(round($displayPrice / $nights, 2), 2) ?> / night</span>
                                    </div>
                                    <form method="POST" action="/booking/create/hotel/0?rate_key=<?= urlencode($hotel['rate_key'] ?? '') ?>&rate_type=<?= View::e($hotel['rate_type'] ?? 'BOOKABLE') ?>" style="display:inline">
                                        <?= View::csrf() ?>
                                        <input type="hidden" name="hotel_data" value="<?= View::e(json_encode($hotel)) ?>">
                                        <button type="submit" class="btn btn-sm btn-book">Book Now &#8594;</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
