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
$flights = [];
$flightError = '';

if ($fromCode && $toCode) {
    $flightError = 'Flight search is being updated. Please check back soon.';
} else {
    $flightError = 'Please select valid departure and destination cities.';
}

$totalPassengers = $adults + $children;

// ─── Hotelbeds Hotel Search ───
$hotels = [];
$hotelError = '';

// Map cities to coordinates for geolocation-based hotel search
$cityCoordinates = [
    'Yerevan' => [40.1872, 44.5152],
    'Moscow' => [55.7558, 37.6173],
    'Sochi' => [43.6028, 39.7342],
    'Dubai' => [25.2048, 55.2708],
    'Istanbul' => [41.0082, 28.9784],
    'Antalya' => [36.8969, 30.7133],
    'Paris' => [48.8566, 2.3522],
    'London' => [51.5074, -0.1278],
    'Berlin' => [52.5200, 13.4050],
    'Frankfurt' => [50.1109, 8.6821],
    'Munich' => [48.1351, 11.5820],
    'Rome' => [41.9028, 12.4964],
    'Milan' => [45.4642, 9.1900],
    'Athens' => [37.9838, 23.7275],
    'Halkidiki' => [40.2932, 23.4418],
    'Crete' => [35.2401, 24.4709],
    'Tivat' => [42.4366, 18.6964],
    'Tbilisi' => [41.7151, 44.8271],
    'Cairo' => [30.0444, 31.2357],
    'El Alamein' => [30.8291, 28.9543],
    'Sharm El Sheikh' => [27.9158, 34.3300],
    'Hurghada' => [27.2579, 33.8116],
    'Barcelona' => [41.3874, 2.1686],
    'Madrid' => [40.4168, -3.7038],
    'Bangkok' => [13.7563, 100.5018],
    'Phuket' => [7.8804, 98.3923],
    'New York' => [40.7128, -74.0060],
    'Los Angeles' => [34.0522, -118.2437],
    'Miami' => [25.7617, -80.1918],
];

$destCity = $toCity;
$coords = $cityCoordinates[$destCity] ?? null;

if ($coords && $depart && $returnDate && $returnDate > $depart) {
    $configFile = __DIR__ . '/../config/hotelbeds.json';
    if (file_exists($configFile)) {
        $hbConfig = json_decode(file_get_contents($configFile), true);
        $apiKey = $hbConfig['api_key'] ?? '';
        $apiSecret = $hbConfig['api_secret'] ?? '';

        if ($apiKey && $apiSecret) {
            // Cache: 1 hour per unique search
            $cacheDir = __DIR__ . '/../cache';
            if (!is_dir($cacheDir)) mkdir($cacheDir, 0755, true);
            $cacheKey = md5($destCity . $depart . $returnDate . $adults . $children);
            $cacheFile = $cacheDir . '/hotels_' . $cacheKey . '.json';

            if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 3600) {
                $hotels = json_decode(file_get_contents($cacheFile), true);
            } else {

            $signature = hash('sha256', $apiKey . $apiSecret . time());

            // Build occupancy
            $occupancies = [];
            $occ = ['rooms' => 1, 'adults' => $adults, 'children' => $children];
            if ($children > 0) {
                $childAges = [];
                for ($c = 0; $c < $children; $c++) {
                    $childAges[] = 8; // default age
                }
                $occ['childrenAges'] = implode(',', $childAges);
            }
            $occupancies[] = $occ;

            $nights = (int)((strtotime($returnDate) - strtotime($depart)) / 86400);
            if ($nights < 1) $nights = 1;

            $requestBody = [
                'stay' => [
                    'checkIn' => $depart,
                    'checkOut' => $returnDate,
                ],
                'occupancies' => [
                    [
                        'rooms' => 1,
                        'adults' => $adults,
                        'children' => $children,
                    ]
                ],
                'geolocation' => [
                    'latitude' => $coords[0],
                    'longitude' => $coords[1],
                    'radius' => 15,
                    'unit' => 'km'
                ],
                'filter' => [
                    'maxHotels' => 15
                ]
            ];

            // Add children ages if needed
            if ($children > 0) {
                $childAges = [];
                for ($c = 0; $c < $children; $c++) {
                    $childAges[] = 8;
                }
                $requestBody['occupancies'][0]['childrenAges'] = implode(',', $childAges);
            }

            $ch = curl_init('https://api.test.hotelbeds.com/hotel-api/1.0/hotels');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($requestBody),
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Api-key: ' . $apiKey,
                    'X-Signature: ' . $signature,
                ],
                CURLOPT_TIMEOUT => 15,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (!empty($data['hotels']['hotels'])) {
                    foreach ($data['hotels']['hotels'] as $h) {
                        $minRate = null;
                        $boardName = '';
                        $roomName = '';
                        $rateKey = '';
                        if (!empty($h['rooms'])) {
                            foreach ($h['rooms'] as $room) {
                                if (!empty($room['rates'])) {
                                    foreach ($room['rates'] as $rate) {
                                        $net = (float)($rate['net'] ?? 0);
                                        if ($minRate === null || $net < $minRate) {
                                            $minRate = $net;
                                            $boardName = $rate['boardName'] ?? '';
                                            $roomName = $room['name'] ?? '';
                                            $rateKey = $rate['rateKey'] ?? '';
                                        }
                                    }
                                }
                            }
                        }

                        if ($minRate !== null) {
                            $hotels[] = [
                                'name' => $h['name'] ?? 'Hotel',
                                'code' => $h['code'] ?? '',
                                'stars' => $h['categoryName'] ?? '',
                                'stars_num' => (int)($h['categoryCode'] ?? 0),
                                'price' => $minRate,
                                'price_per_night' => round($minRate / max($nights, 1), 2),
                                'nights' => $nights,
                                'board' => $boardName,
                                'room' => $roomName,
                                'currency' => $data['hotels']['currency'] ?? 'EUR',
                                'image' => !empty($h['images'][0]['path']) ? 'https://photos.hotelbeds.com/giata/bigger/' . $h['images'][0]['path'] : '',
                                'rate_key' => $rateKey,
                            ];
                        }
                    }
                    // Fetch images from Content API
                    $hotelCodes = array_column($hotels, 'code');
                    if (!empty($hotelCodes)) {
                        $sigContent = hash('sha256', $apiKey . $apiSecret . time());
                        $codesParam = implode(',', $hotelCodes);
                        $contentUrl = "https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?codes={$codesParam}&fields=images&language=ENG";
                        $chImg = curl_init($contentUrl);
                        curl_setopt_array($chImg, [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_HTTPHEADER => [
                                'Accept: application/json',
                                'Api-key: ' . $apiKey,
                                'X-Signature: ' . $sigContent,
                            ],
                            CURLOPT_TIMEOUT => 10,
                        ]);
                        $imgResp = curl_exec($chImg);
                        $imgCode = curl_getinfo($chImg, CURLINFO_HTTP_CODE);
                        curl_close($chImg);

                        if ($imgCode === 200 && $imgResp) {
                            $imgData = json_decode($imgResp, true);
                            $imageMap = [];
                            if (!empty($imgData['hotels'])) {
                                foreach ($imgData['hotels'] as $imgHotel) {
                                    $hCode = $imgHotel['code'] ?? '';
                                    if ($hCode && !empty($imgHotel['images'])) {
                                        // Find first general/exterior image, fallback to first image
                                        $bestImg = '';
                                        foreach ($imgHotel['images'] as $img) {
                                            $type = $img['type']['code'] ?? '';
                                            if ($type === 'GEN' || $type === 'EXT') {
                                                $bestImg = $img['path'];
                                                break;
                                            }
                                        }
                                        if (!$bestImg) {
                                            $bestImg = $imgHotel['images'][0]['path'] ?? '';
                                        }
                                        if ($bestImg) {
                                            $imageMap[$hCode] = 'https://photos.hotelbeds.com/giata/bigger/' . $bestImg;
                                        }
                                    }
                                }
                            }
                            // Apply images to hotels
                            foreach ($hotels as &$htl) {
                                if (isset($imageMap[$htl['code']])) {
                                    $htl['image'] = $imageMap[$htl['code']];
                                }
                            }
                            unset($htl);
                        }
                    }

                    // Sort by price
                    usort($hotels, function($a, $b) {
                        return $a['price'] <=> $b['price'];
                    });
                }
            } else {
                $hotelError = 'Could not fetch hotel results. Please try again.';
            }

            // Save to cache
            if (!empty($hotels)) {
                file_put_contents($cacheFile, json_encode($hotels));
            }

            } // end cache else
        }
    }
} elseif (!$depart || !$returnDate) {
    $hotelError = 'Please select check-in and check-out dates to see hotels.';
} elseif ($returnDate <= $depart) {
    $hotelError = 'Return date must be after departure date to search hotels.';
}
?>

<section class="search-results">
    <div class="search-hero-banner">
        <div class="search-hero-overlay">
            <h2>&#9992; <?= htmlspecialchars($from) ?> &rarr; <?= htmlspecialchars($toCity) ?></h2>
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

    <div class="search-body">
        <!-- Flight Results -->
        <div class="search-section">
            <h3 class="section-heading">&#9992; Flights</h3>
            <?php if ($flightError): ?>
                <div class="alert info"><?= htmlspecialchars($flightError) ?></div>
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
                                <?php endif; ?>
                            </div>
                            <?php $bookingUrl = $flight['booking_url'] ?? '#'; ?>
                            <a href="<?= htmlspecialchars($bookingUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-book">Book Now &#8594;</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Hotel Results -->
        <div class="search-section">
            <h3 class="section-heading">&#127960; Hotels in <?= htmlspecialchars($toCity) ?></h3>
            <?php if ($hotelError): ?>
                <div class="alert info"><?= htmlspecialchars($hotelError) ?></div>
            <?php elseif (empty($hotels)): ?>
                <div class="alert info">No hotels found for this destination and dates.</div>
            <?php else: ?>
                <div class="results-bar">
                    <span class="results-count"><?= count($hotels) ?> hotel<?= count($hotels) !== 1 ? 's' : '' ?> found</span>
                    <span class="results-sort">Sorted by: <strong>Lowest price</strong></span>
                </div>
                <div class="hotel-list">
                    <?php foreach ($hotels as $i => $hotel): ?>
                    <div class="hotel-card <?= $i === 0 ? 'hotel-card-best' : '' ?>">
                        <?php if ($i === 0): ?>
                            <div class="hotel-badge">Best Deal</div>
                        <?php endif; ?>
                        <div class="hotel-card-inner">
                            <div class="hotel-image">
                                <?php if ($hotel['image']): ?>
                                    <img src="<?= htmlspecialchars($hotel['image']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>" loading="lazy"
                                         onerror="this.parentElement.innerHTML='<div class=\'hotel-image-placeholder\'>&#127960;</div>'">
                                <?php else: ?>
                                    <div class="hotel-image-placeholder">&#127960;</div>
                                <?php endif; ?>
                            </div>
                            <div class="hotel-info">
                                <div class="hotel-info-top">
                                    <h4 class="hotel-name"><?= htmlspecialchars($hotel['name']) ?></h4>
                                    <div class="hotel-stars">
                                        <?php
                                            $starCount = min((int)substr($hotel['stars_num'], 0, 1), 5);
                                            for ($s = 0; $s < $starCount; $s++) echo '&#11088;';
                                        ?>
                                        <span class="star-label"><?= htmlspecialchars($hotel['stars']) ?></span>
                                    </div>
                                    <div class="hotel-details">
                                        <?php if ($hotel['room']): ?>
                                            <span class="hotel-detail">&#128719; <?= htmlspecialchars($hotel['room']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($hotel['board']): ?>
                                            <span class="hotel-detail">&#127860; <?= htmlspecialchars($hotel['board']) ?></span>
                                        <?php endif; ?>
                                        <span class="hotel-detail">&#127769; <?= $hotel['nights'] ?> night<?= $hotel['nights'] > 1 ? 's' : '' ?></span>
                                    </div>
                                </div>
                                <div class="hotel-price-section">
                                    <div class="hotel-price">
                                        <span class="hotel-price-value"><?= htmlspecialchars($hotel['currency']) ?> <?= number_format($hotel['price'], 2) ?></span>
                                        <span class="hotel-price-detail">total for <?= $hotel['nights'] ?> night<?= $hotel['nights'] > 1 ? 's' : '' ?></span>
                                        <span class="hotel-price-pernight"><?= htmlspecialchars($hotel['currency']) ?> <?= number_format($hotel['price_per_night'], 2) ?> / night</span>
                                    </div>
                                    <a href="<?= url('contact') ?>" class="btn btn-sm btn-book">Book Now &#8594;</a>
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
