<?php
// Get search params
$tripType = isset($_GET['trip']) ? $_GET['trip'] : 'roundtrip';
$depart = isset($_GET['date']) ? $_GET['date'] : '';
$returnDate = isset($_GET['return_date']) ? $_GET['return_date'] : '';
$adults = isset($_GET['adults']) ? (int)$_GET['adults'] : 1;
$children = isset($_GET['children']) ? (int)$_GET['children'] : 0;

// Determine from/to based on trip type
if ($tripType === 'packages') {
    $from = isset($_GET['pkg_from']) ? trim($_GET['pkg_from']) : 'Yerevan';
    $to = isset($_GET['pkg_to']) ? trim($_GET['pkg_to']) : '';
    $toParts = explode(',', $to);
    $toCity = trim($toParts[0]);
} else {
    $from = isset($_GET['from']) ? trim($_GET['from']) : '';
    $to = isset($_GET['to']) ? trim($_GET['to']) : '';
    $toCity = $to;
}

// IATA codes
$iataCodes = [
    'Yerevan' => 'EVN', 'Moscow' => 'SVO', 'Sochi' => 'AER', 'Dubai' => 'DXB',
    'Istanbul' => 'IST', 'Antalya' => 'AYT', 'Paris' => 'CDG', 'London' => 'LHR',
    'Berlin' => 'BER', 'Frankfurt' => 'FRA', 'Munich' => 'MUC', 'Rome' => 'FCO',
    'Milan' => 'MXP', 'Athens' => 'ATH', 'Halkidiki' => 'SKG', 'Crete' => 'HER',
    'Tivat' => 'TIV', 'Tbilisi' => 'TBS', 'Cairo' => 'CAI', 'El Alamein' => 'DBB',
    'Sharm El Sheikh' => 'SSH', 'Hurghada' => 'HRG', 'Barcelona' => 'BCN',
    'Madrid' => 'MAD', 'Bangkok' => 'BKK', 'Phuket' => 'HKT', 'New York' => 'JFK',
    'Los Angeles' => 'LAX', 'Miami' => 'MIA'
];

$fromCode = $iataCodes[$from] ?? '';
$toCode = $iataCodes[$toCity] ?? '';

$tripLabels = [
    'roundtrip' => 'Round Trip',
    'oneway' => 'One Way',
    'packages' => 'Package'
];

// TODO: Integrate new flight API here
// The search form collects: $fromCode, $toCode, $depart, $returnDate, $adults, $children, $tripType
// Populate $flights array with results in this format:
// ['price' => int, 'airline' => string, 'departure_at' => string, 'return_at' => string,
//  'flight_number' => string, 'transfers' => int, 'duration' => int, 'booking_url' => string]
$flights = [];
$error = '';

if ($fromCode && $toCode) {
    $error = 'Flight search is being updated. Please check back soon.';
} else {
    $error = 'Please select valid departure and destination cities.';
}

$totalPassengers = $adults + $children;
?>

<section class="search-results">
    <div class="search-hero-banner">
        <div class="search-hero-overlay">
            <h2>&#9992; <?= htmlspecialchars($from) ?> → <?= htmlspecialchars($toCity) ?></h2>
            <div class="search-info">
                <span class="search-tag"><span class="tag-icon">&#128203;</span> <?= $tripLabels[$tripType] ?? 'Round Trip' ?></span>
                <?php if ($depart): ?>
                    <span class="search-tag"><span class="tag-icon">&#128197;</span> <?= date('M d, Y', strtotime($depart)) ?></span>
                <?php endif; ?>
                <?php if ($returnDate && $tripType !== 'oneway'): ?>
                    <span class="search-tag"><span class="tag-icon">&#128260;</span> <?= date('M d, Y', strtotime($returnDate)) ?></span>
                <?php endif; ?>
                <span class="search-tag"><span class="tag-icon">&#128101;</span> <?= $adults ?> Adult<?= $adults > 1 ? 's' : '' ?><?= $children > 0 ? ", {$children} Child" . ($children > 1 ? 'ren' : '') : '' ?></span>
            </div>
            <a href="<?= url('home') ?>" class="btn btn-outline btn-sm" data-t="new_search">&#8592; New Search</a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="search-body">
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        </div>
    <?php else: ?>
        <div class="search-body">
            <div class="results-bar">
                <span class="results-count"><?= count($flights) ?> flight<?= count($flights) !== 1 ? 's' : '' ?> found</span>
                <span class="results-sort">Sorted by: <strong>Lowest price</strong></span>
            </div>
            <div class="flight-list">
                <?php foreach ($flights as $i => $flight): ?>
                <div class="flight-card <?= $i === 0 ? 'flight-card-best' : '' ?>">
                    <?php if ($i === 0): ?>
                        <div class="flight-badge" data-t="best_price">Best Price</div>
                    <?php endif; ?>
                    <div class="flight-card-top">
                        <div class="flight-airline">
                            <?php if ($flight['airline']): ?>
                                <span class="airline-placeholder">&#9992;</span>
                                <span class="airline-code"><?= htmlspecialchars($flight['airline']) ?></span>
                            <?php else: ?>
                                <span class="airline-placeholder">&#9992;</span>
                            <?php endif; ?>
                            <?php if ($flight['flight_number']): ?>
                                <span class="flight-num">Flight <?= htmlspecialchars($flight['flight_number']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flight-route">
                            <div class="flight-city">
                                <strong><?= htmlspecialchars($fromCode) ?></strong>
                                <span class="city-name"><?= htmlspecialchars($from) ?></span>
                            </div>
                            <div class="flight-arrow">
                                <?php if ($flight['duration'] > 0): ?>
                                    <div class="flight-duration">
                                        &#128336; <?= floor($flight['duration'] / 60) ?>h <?= $flight['duration'] % 60 ?>m
                                    </div>
                                <?php endif; ?>
                                <div class="arrow-visual">
                                    <span class="arrow-dot"></span>
                                    <span class="arrow-line"></span>
                                    <?php if ($flight['transfers'] > 0): ?>
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
                                    <span class="transfers <?= $flight['transfers'] == 0 ? 'direct' : 'has-stops' ?>">
                                        <?= $flight['transfers'] == 0 ? '&#10003; Direct' : $flight['transfers'] . ' stop' . ($flight['transfers'] > 1 ? 's' : '') ?>
                                    </span>
                                    <?php if ($tripType !== 'oneway'): ?>
                                        <span class="trip-label"><?= $tripLabels[$tripType] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flight-city">
                                <strong><?= htmlspecialchars($toCode) ?></strong>
                                <span class="city-name"><?= htmlspecialchars($toCity) ?></span>
                            </div>
                        </div>
                        <div class="flight-price">
                            <span class="flight-price-value" data-base-price="<?= $flight['price'] ?>">$<?= number_format($flight['price']) ?></span>
                            <span class="flight-price-pp" data-t="per_person">per person</span>
                            <?php if ($totalPassengers > 1): ?>
                                <span class="flight-price-total">$<?= number_format($flight['price'] * $totalPassengers) ?> total</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flight-card-bottom">
                        <div class="flight-dates">
                            <?php if ($flight['departure_at']): ?>
                                <span>&#128197; Depart: <strong><?= date('D, M d Y', strtotime($flight['departure_at'])) ?></strong></span>
                            <?php endif; ?>
                            <?php if ($flight['return_at'] && $tripType !== 'oneway'): ?>
                                <span>&#128260; Return: <strong><?= date('D, M d Y', strtotime($flight['return_at'])) ?></strong></span>
                                <?php
                                    $depDate = strtotime($flight['departure_at']);
                                    $retDate = strtotime($flight['return_at']);
                                    if ($depDate && $retDate) {
                                        $tripDays = (int)round(($retDate - $depDate) / 86400);
                                        if ($tripDays > 0) {
                                            echo '<span class="trip-duration-days">&#9200; ' . $tripDays . ' day' . ($tripDays > 1 ? 's' : '') . ' trip</span>';
                                        }
                                    }
                                ?>
                            <?php endif; ?>
                        </div>
                        <?php $bookingUrl = $flight['booking_url'] ?? '#'; ?>
                        <a href="<?= htmlspecialchars($bookingUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-book">Book Now &#8594;</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
