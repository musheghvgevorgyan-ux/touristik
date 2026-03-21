<?php
$config = json_decode(file_get_contents(__DIR__ . '/../config/travelpayouts.json'), true);
$token = $config['api_token'];

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
    // Clean "City, Country" format
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

// Fetch cheapest prices from Travelpayouts (multiple endpoints)
$flights = [];
$seen = [];
$error = '';

function addFlight(&$flights, &$seen, $data, $tripType) {
    $key = $data['price'] . '|' . substr($data['departure_at'], 0, 10);
    if (isset($seen[$key])) return;
    $seen[$key] = true;

    if ($tripType === 'oneway') {
        $data['price'] = round($data['price'] / 2);
    }
    $flights[] = $data;
}

if ($fromCode && $toCode) {

    // 1. Cheapest prices endpoint (best for lowest fares)
    $url1 = "https://api.travelpayouts.com/v1/prices/cheap?origin={$fromCode}&destination={$toCode}&currency=usd&token={$token}";
    if ($depart) {
        $url1 .= "&depart_date=" . substr($depart, 0, 7);
    }
    if ($returnDate) {
        $url1 .= "&return_date=" . substr($returnDate, 0, 7);
    }
    $resp = @file_get_contents($url1);
    if ($resp) {
        $data = json_decode($resp, true);
        if (!empty($data['success']) && isset($data['data'][$toCode])) {
            foreach ($data['data'][$toCode] as $f) {
                addFlight($flights, $seen, [
                    'price' => $f['price'],
                    'airline' => $f['airline'] ?? '',
                    'departure_at' => $f['departure_at'] ?? '',
                    'return_at' => $f['return_at'] ?? '',
                    'flight_number' => $f['flight_number'] ?? '',
                    'transfers' => $f['number_of_changes'] ?? 0,
                    'duration' => $f['duration'] ?? 0,
                ], $tripType);
            }
        }
    }

    // 2. Direct flights endpoint (non-stop flights only)
    $url2 = "https://api.travelpayouts.com/v1/prices/direct?origin={$fromCode}&destination={$toCode}&currency=usd&token={$token}";
    if ($depart) {
        $url2 .= "&depart_date=" . substr($depart, 0, 7);
    }
    if ($returnDate) {
        $url2 .= "&return_date=" . substr($returnDate, 0, 7);
    }
    $resp2 = @file_get_contents($url2);
    if ($resp2) {
        $data2 = json_decode($resp2, true);
        if (!empty($data2['success']) && isset($data2['data'][$toCode])) {
            $f = $data2['data'][$toCode];
            addFlight($flights, $seen, [
                'price' => $f['price'],
                'airline' => $f['airline'] ?? '',
                'departure_at' => $f['departure_at'] ?? '',
                'return_at' => $f['return_at'] ?? '',
                'flight_number' => $f['flight_number'] ?? '',
                'transfers' => 0,
                'duration' => $f['duration'] ?? 0,
            ], $tripType);
        }
    }

    // 3. Latest prices (more results, sorted by price)
    $url3 = "https://api.travelpayouts.com/v2/prices/latest?origin={$fromCode}&destination={$toCode}&currency=usd&token={$token}&limit=30&sorting=price&trip_class=0";
    if ($depart) {
        $url3 .= "&beginning_of_period=" . substr($depart, 0, 10);
        $url3 .= "&period_type=month";
    }
    $resp3 = @file_get_contents($url3);
    if ($resp3) {
        $data3 = json_decode($resp3, true);
        if (!empty($data3['success']) && !empty($data3['data'])) {
            foreach ($data3['data'] as $f) {
                addFlight($flights, $seen, [
                    'price' => $f['value'],
                    'airline' => $f['gate'] ?? '',
                    'departure_at' => ($f['depart_date'] ?? '') . 'T00:00:00',
                    'return_at' => ($f['return_date'] ?? '') . 'T00:00:00',
                    'flight_number' => '',
                    'transfers' => $f['number_of_changes'] ?? 0,
                    'duration' => $f['duration'] ?? 0,
                ], $tripType);
            }
        }
    }

    // 4. Monthly prices calendar (finds cheapest day)
    if ($depart) {
        $url4 = "https://api.travelpayouts.com/v2/prices/month-matrix?origin={$fromCode}&destination={$toCode}&currency=usd&token={$token}&month=" . substr($depart, 0, 7) . "-01";
        $resp4 = @file_get_contents($url4);
        if ($resp4) {
            $data4 = json_decode($resp4, true);
            if (!empty($data4['success']) && !empty($data4['data'])) {
                foreach (array_slice($data4['data'], 0, 10) as $f) {
                    addFlight($flights, $seen, [
                        'price' => $f['value'],
                        'airline' => $f['gate'] ?? '',
                        'departure_at' => ($f['depart_date'] ?? '') . 'T00:00:00',
                        'return_at' => ($f['return_date'] ?? '') . 'T00:00:00',
                        'flight_number' => '',
                        'transfers' => $f['number_of_changes'] ?? 0,
                        'duration' => $f['duration'] ?? 0,
                    ], $tripType);
                }
            }
        }
    }

    // Sort all by price (cheapest first)
    usort($flights, function($a, $b) { return $a['price'] - $b['price']; });

    if (empty($flights)) {
        $error = 'No flights found for this route. Try different dates or destinations.';
    }
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
                                <img src="https://pics.avs.io/80/80/<?= htmlspecialchars($flight['airline']) ?>.png"
                                     alt="<?= htmlspecialchars($flight['airline']) ?>" class="airline-logo"
                                     onerror="this.parentElement.innerHTML='<span class=\'airline-placeholder\'>&#9992;</span>'">
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
                        <button class="btn btn-sm btn-book book-trigger" data-t="book_now_arrow">Book Now &#8594;</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
