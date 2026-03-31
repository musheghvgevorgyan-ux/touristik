<?php
require_once __DIR__ . '/../includes/hotelbeds.php';

// Handle hotel selection from search page (POST with hotel_data)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_hotel'])) {
    $hotelJson = json_decode($_POST['hotel_data'] ?? '{}', true);
    if ($hotelJson) {
        $_SESSION['booking_search_hotel'] = $hotelJson;
    }
    // Redirect to GET to avoid resubmit issues
    $redirectUrl = url('booking', [
        'rate_key' => $_GET['rate_key'] ?? '',
        'hotel_name' => $_GET['hotel_name'] ?? '',
        'rate_type' => $_GET['rate_type'] ?? 'BOOKABLE',
    ]);
    header('Location: ' . $redirectUrl);
    exit;
}

$step = 'checkrate'; // checkrate → confirm → voucher
$rateKey = isset($_GET['rate_key']) ? urldecode($_GET['rate_key']) : '';
$hotelName = isset($_GET['hotel_name']) ? urldecode($_GET['hotel_name']) : '';
$allowedRateTypes = ['RECHECK', 'BOOKABLE'];
$rateType = (isset($_GET['rate_type']) && in_array($_GET['rate_type'], $allowedRateTypes)) ? $_GET['rate_type'] : 'BOOKABLE';
$error = '';
$checkData = null;
$bookingData = null;

// Step 1: CheckRate — only when rateType is RECHECK, otherwise skip to confirm
if ($rateKey && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($rateType === 'RECHECK') {
        // Rate needs revalidation — call CheckRate
        $checkData = hbCheckRate($rateKey);
        if (!$checkData['success']) {
            $error = $checkData['error'];
        } else {
            $step = 'confirm';
            $_SESSION['booking_rate'] = $checkData;
            $_SESSION['booking_rate_key'] = $checkData['rate']['key'];
        }
    } else {
        // BOOKABLE rate — skip CheckRate, go straight to confirm
        $step = 'confirm';
        $checkData = [
            'success' => true,
            'hotel' => [
                'name' => $hotelName,
                'code' => '',
                'category' => '',
                'destination' => '',
                'image' => '',
            ],
            'room' => ['name' => '', 'code' => ''],
            'rate' => [
                'key' => $rateKey,
                'net' => 0,
                'selling_rate' => 0,
                'currency' => 'EUR',
                'board' => '',
                'board_code' => '',
                'cancellation_policies' => [],
                'check_in' => '',
                'check_out' => '',
                'rooms' => 1,
                'adults' => 1,
                'children' => 0,
                'rateComments' => '',
            ],
        ];
        // For BOOKABLE rates, try to get details from session search cache
        if (!empty($_SESSION['booking_search_hotel'])) {
            $cached = $_SESSION['booking_search_hotel'];
            $checkData['hotel']['image'] = $cached['image'] ?? '';
            $checkData['hotel']['category'] = $cached['stars'] ?? '';
            $checkData['rate']['net'] = $cached['price'] ?? 0;
            $checkData['rate']['currency'] = $cached['currency'] ?? 'EUR';
            $checkData['rate']['board'] = $cached['board'] ?? '';
            $checkData['room']['name'] = $cached['room'] ?? '';
        }
        $_SESSION['booking_rate'] = $checkData;
        $_SESSION['booking_rate_key'] = $rateKey;
    }
}

// Step 2: Booking — process the reservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking']) && verifyCsrf()) {
    $step = 'processing';
    $storedRate = $_SESSION['booking_rate'] ?? null;
    $storedRateKey = $_SESSION['booking_rate_key'] ?? '';

    if (!$storedRate || !$storedRateKey) {
        $error = 'Session expired. Please search again.';
        $step = 'error';
    } else {
        $holder = [
            'name' => trim($_POST['holder_name'] ?? ''),
            'surname' => trim($_POST['holder_surname'] ?? ''),
            'remark' => trim($_POST['remark'] ?? ''),
        ];

        if (empty($holder['name']) || empty($holder['surname'])) {
            $error = 'Please enter guest name and surname.';
            $step = 'confirm';
            $checkData = $storedRate;
        } else {
            // Build paxes
            $rooms = [[
                'paxes' => [[
                    'roomId' => 1,
                    'type' => 'AD',
                    'name' => $holder['name'],
                    'surname' => $holder['surname'],
                ]]
            ]];

            $bookResult = hbBook($storedRateKey, $holder, $rooms);

            if ($bookResult['success']) {
                $step = 'voucher';
                $bookingData = $bookResult['booking'];
                // Save to session for voucher page
                $_SESSION['last_booking'] = $bookingData;

                // Save booking to database
                $guestEmail = filter_var(trim($_POST['holder_email'] ?? ''), FILTER_VALIDATE_EMAIL);
                $guestPhone = trim($_POST['holder_phone'] ?? '');
                saveBooking($pdo, [
                    'reference' => $bookingData['reference'] ?? '',
                    'client_reference' => $bookingData['client_reference'] ?? '',
                    'hotel_name' => $bookingData['hotel'] ?? $hotelName,
                    'guest_name' => $holder['name'] . ' ' . $holder['surname'],
                    'guest_email' => $guestEmail ?: '',
                    'guest_phone' => $guestPhone,
                    'check_in' => $bookingData['check_in'] ?? null,
                    'check_out' => $bookingData['check_out'] ?? null,
                    'rooms' => count($bookingData['rooms'] ?? [1]),
                    'currency' => $bookingData['currency'] ?? 'EUR',
                    'total_price' => $bookingData['total_net'] ?? $bookingData['total_selling'] ?? 0,
                    'status' => $bookingData['status'] ?? 'CONFIRMED',
                    'raw_response' => json_encode($bookingData),
                ]);

                // Send booking confirmation emails
                $adminEmail = getSetting($pdo, 'contact_email', '');
                $hotelNameEmail = htmlspecialchars($bookingData['hotel'] ?? $hotelName);
                $ref = htmlspecialchars($bookingData['reference'] ?? '');
                $checkIn = $bookingData['check_in'] ?? '';
                $checkOut = $bookingData['check_out'] ?? '';
                $guestName = htmlspecialchars($holder['name'] . ' ' . $holder['surname']);
                $status = $bookingData['status'] ?? 'CONFIRMED';

                $infoRow = function($label, $value, $alt = false) {
                    $bg = $alt ? 'background:#f8f9fa;' : '';
                    return '<tr style="' . $bg . '"><td style="border-bottom:1px solid #e0e0e0;font-weight:600;width:140px;color:#203a43;">' . $label . '</td><td style="border-bottom:1px solid #e0e0e0;">' . $value . '</td></tr>';
                };
                $table = '<table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e0e0e0;border-radius:6px;border-collapse:collapse;margin-bottom:20px;">'
                    . $infoRow('Reference', $ref, true) . $infoRow('Hotel', $hotelNameEmail)
                    . $infoRow('Check-in', $checkIn, true) . $infoRow('Check-out', $checkOut)
                    . '<tr style="background:#f8f9fa;"><td style="font-weight:600;color:#203a43;">Status</td><td><span style="background:#28a745;color:#fff;padding:3px 10px;border-radius:4px;font-size:13px;">' . $status . '</span></td></tr></table>';

                // Email to guest
                if ($guestEmail) {
                    $guestHtml = '<p style="margin:0 0 15px;">Dear <strong>' . $guestName . '</strong>,</p>'
                        . '<p style="margin:0 0 20px;">Your booking has been confirmed. Here are your reservation details:</p>'
                        . $table . '<p style="margin:0;">Thank you for booking with Touristik Travel Club!</p>';
                    $voucherUrl = 'https://touristik.am/index.php?page=booking&ref=' . urlencode($bookingData['reference'] ?? '');
                    $cta = '<a href="' . $voucherUrl . '" style="display:inline-block;background:#f18f01;color:#ffffff;padding:12px 30px;border-radius:6px;text-decoration:none;font-weight:600;font-size:15px;">View Your Voucher</a>';
                    sendHtmlEmail($guestEmail, "Booking Confirmation - $ref | Touristik", emailTemplate('Booking Confirmation', $guestHtml, $cta));
                }

                // Email to admin
                if ($adminEmail && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                    $guestPhone = htmlspecialchars(trim($_POST['holder_phone'] ?? 'Not provided'));
                    $adminHtml = '<p style="margin:0 0 15px;font-size:16px;font-weight:600;color:#203a43;">A new booking has been received.</p>'
                        . '<table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e0e0e0;border-radius:6px;border-collapse:collapse;margin-bottom:15px;">'
                        . $infoRow('Reference', $ref, true) . $infoRow('Hotel', $hotelNameEmail)
                        . $infoRow('Guest', $guestName, true) . $infoRow('Email', $guestEmail ?: 'Not provided')
                        . $infoRow('Phone', $guestPhone, true) . $infoRow('Check-in', $checkIn)
                        . $infoRow('Check-out', $checkOut, true)
                        . '<tr><td style="font-weight:600;color:#203a43;">Status</td><td>' . $status . '</td></tr></table>';
                    sendHtmlEmail($adminEmail, "New Booking: $ref - $hotelNameEmail", emailTemplate('New Booking Received', $adminHtml));
                }

                // Clear rate data
                unset($_SESSION['booking_rate'], $_SESSION['booking_rate_key']);
            } else {
                $error = $bookResult['error'];
                $step = 'confirm';
                $checkData = $storedRate;
            }
        }
    }
}

// If accessing voucher page directly with reference
if (isset($_GET['ref']) && !$bookingData) {
    $step = 'voucher';
    if (!empty($_SESSION['last_booking']) && $_SESSION['last_booking']['reference'] === $_GET['ref']) {
        $bookingData = $_SESSION['last_booking'];
    } else {
        $refResult = hbGetBooking($_GET['ref']);
        if ($refResult['success']) {
            $bookingData = $refResult['booking'];
        } else {
            $error = 'Booking not found.';
            $step = 'error';
        }
    }
}
?>

<nav class="breadcrumbs" aria-label="Breadcrumb">
    <a href="<?= url('home') ?>" data-t="breadcrumb_home">Home</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <a href="<?= url('search') ?>">Search</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <span class="breadcrumb-current">Booking</span>
</nav>

<section class="booking-section">

<?php if ($error && $step === 'error'): ?>
    <!-- Error State -->
    <div class="booking-error-page">
        <div class="booking-icon">&#9888;</div>
        <h2>Something went wrong</h2>
        <p class="alert error"><?= htmlspecialchars($error) ?></p>
        <a href="<?= url('home') ?>" class="btn">&#8592; New Search</a>
    </div>

<?php elseif (!$rateKey && $step === 'checkrate'): ?>
    <!-- No rate key -->
    <div class="booking-error-page">
        <div class="booking-icon">&#128269;</div>
        <h2>No hotel selected</h2>
        <p>Please search for hotels first and select one to book.</p>
        <a href="<?= url('home') ?>" class="btn">&#8592; Search Hotels</a>
    </div>

<?php elseif ($step === 'checkrate' && $error): ?>
    <!-- CheckRate failed -->
    <div class="booking-error-page">
        <div class="booking-icon">&#128683;</div>
        <h2>Rate No Longer Available</h2>
        <p class="alert error"><?= htmlspecialchars($error) ?></p>
        <p>The selected rate has expired or is no longer available. Please search again.</p>
        <a href="<?= url('home') ?>" class="btn">&#8592; Search Again</a>
    </div>

<?php elseif ($step === 'confirm' && $checkData): ?>
    <!-- Step 2: Confirmation Form -->
    <div class="booking-flow">
        <div class="booking-steps">
            <div class="booking-step done">&#10003; Rate Verified</div>
            <div class="booking-step active">2. Guest Details</div>
            <div class="booking-step">3. Confirmation</div>
        </div>

        <div class="booking-layout">
            <div class="booking-summary">
                <h3>&#127960; Booking Summary</h3>
                <?php if ($checkData['hotel']['image']): ?>
                    <div class="booking-hotel-image">
                        <img src="<?= htmlspecialchars($checkData['hotel']['image']) ?>" alt="<?= htmlspecialchars($checkData['hotel']['name']) ?>">
                    </div>
                <?php endif; ?>
                <div class="booking-detail">
                    <h4><?= htmlspecialchars($checkData['hotel']['name']) ?></h4>
                    <p class="booking-category"><?= htmlspecialchars($checkData['hotel']['category']) ?></p>
                    <?php if ($checkData['hotel']['destination']): ?>
                        <p class="booking-dest">&#128205; <?= htmlspecialchars($checkData['hotel']['destination']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="booking-info-grid">
                    <div class="booking-info-item">
                        <span class="booking-label">Check-in</span>
                        <span class="booking-value"><?= $checkData['rate']['check_in'] ? date('D, M d Y', strtotime($checkData['rate']['check_in'])) : 'N/A' ?></span>
                    </div>
                    <div class="booking-info-item">
                        <span class="booking-label">Check-out</span>
                        <span class="booking-value"><?= $checkData['rate']['check_out'] ? date('D, M d Y', strtotime($checkData['rate']['check_out'])) : 'N/A' ?></span>
                    </div>
                    <div class="booking-info-item">
                        <span class="booking-label">Room</span>
                        <span class="booking-value"><?= htmlspecialchars($checkData['room']['name']) ?></span>
                    </div>
                    <div class="booking-info-item">
                        <span class="booking-label">Board</span>
                        <span class="booking-value"><?= htmlspecialchars($checkData['rate']['board']) ?></span>
                    </div>
                </div>

                <?php if (!empty($checkData['rate']['cancellation_policies'])): ?>
                    <div class="booking-cancellation">
                        <h5>&#128196; Cancellation Policy</h5>
                        <?php foreach ($checkData['rate']['cancellation_policies'] as $policy): ?>
                            <p>From <?= date('M d, Y', strtotime($policy['from'])) ?>: <?= htmlspecialchars($policy['amount'] ?? '') ?> <?= htmlspecialchars($checkData['rate']['currency']) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($checkData['rate']['rateComments'])): ?>
                    <div class="booking-rate-comments">
                        <h5>&#128196; Important Information</h5>
                        <p><?= nl2br(htmlspecialchars($checkData['rate']['rateComments'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="booking-total">
                    <span>Total Price</span>
                    <span class="booking-total-price"><?= htmlspecialchars($checkData['rate']['currency']) ?> <?= number_format($checkData['rate']['net'], 2) ?></span>
                </div>
            </div>

            <div class="booking-form-panel">
                <h3>&#128100; Guest Details</h3>
                <?php if ($error): ?>
                    <div class="alert error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST" action="<?= url('booking') ?>" class="booking-form">
                    <?= csrfField() ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="holder_name">First Name *</label>
                            <input type="text" id="holder_name" name="holder_name" value="<?= htmlspecialchars($_POST['holder_name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="holder_surname">Last Name *</label>
                            <input type="text" id="holder_surname" name="holder_surname" value="<?= htmlspecialchars($_POST['holder_surname'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="holder_email">Email</label>
                        <input type="email" id="holder_email" name="holder_email" value="<?= htmlspecialchars($_POST['holder_email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="holder_phone">Phone</label>
                        <input type="tel" id="holder_phone" name="holder_phone" value="<?= htmlspecialchars($_POST['holder_phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="remark">Special Requests</label>
                        <textarea id="remark" name="remark" rows="3" placeholder="Any special requests..."><?= htmlspecialchars($_POST['remark'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" name="confirm_booking" class="btn btn-book-confirm">&#128274; Confirm Booking</button>
                    <p class="booking-disclaimer">By confirming, you agree to the cancellation policy shown above.</p>
                </form>
            </div>
        </div>
    </div>

<?php elseif ($step === 'voucher' && $bookingData): ?>
    <!-- Step 3: Voucher / Confirmation -->
    <div class="booking-flow">
        <div class="booking-steps">
            <div class="booking-step done">&#10003; Rate Verified</div>
            <div class="booking-step done">&#10003; Booked</div>
            <div class="booking-step active">3. Confirmation</div>
        </div>

        <div class="voucher">
            <div class="voucher-header">
                <div class="voucher-icon">&#10003;</div>
                <h2>Booking Confirmed!</h2>
                <p class="voucher-subtitle">Your reservation has been successfully completed.</p>
            </div>

            <div class="voucher-ref">
                <span class="voucher-ref-label">Booking Reference</span>
                <span class="voucher-ref-code"><?= htmlspecialchars($bookingData['reference']) ?></span>
                <?php if (!empty($bookingData['client_reference'])): ?>
                    <span class="voucher-ref-label" style="margin-top:0.5rem;">Agency Reference</span>
                    <span class="voucher-ref-agency"><?= htmlspecialchars($bookingData['client_reference']) ?></span>
                <?php endif; ?>
            </div>

            <div class="voucher-details">
                <!-- Hotel Information (mandatory: name, address; recommended: category, destination, phone) -->
                <div class="voucher-section">
                    <h4>&#127960; Hotel Information</h4>
                    <p class="voucher-hotel-name"><?= htmlspecialchars($bookingData['hotel']) ?></p>
                    <?php if (!empty($bookingData['hotel_category'])): ?>
                        <p>&#11088; <?= htmlspecialchars($bookingData['hotel_category']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($bookingData['hotel_address'])): ?>
                        <p>&#128205; <?= htmlspecialchars($bookingData['hotel_address']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($bookingData['hotel_destination'])): ?>
                        <p>&#127758; <?= htmlspecialchars($bookingData['hotel_destination']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($bookingData['hotel_phone'])): ?>
                        <p>&#128222; <?= htmlspecialchars($bookingData['hotel_phone']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Booking Information (mandatory: reference, dates, room type, board type) -->
                <div class="voucher-grid">
                    <div class="voucher-item">
                        <span class="voucher-label">Check-in</span>
                        <span class="voucher-value"><?= $bookingData['check_in'] ? date('D, M d Y', strtotime($bookingData['check_in'])) : 'N/A' ?></span>
                    </div>
                    <div class="voucher-item">
                        <span class="voucher-label">Check-out</span>
                        <span class="voucher-value"><?= $bookingData['check_out'] ? date('D, M d Y', strtotime($bookingData['check_out'])) : 'N/A' ?></span>
                    </div>
                    <div class="voucher-item">
                        <span class="voucher-label">Status</span>
                        <span class="voucher-value voucher-status-<?= strtolower($bookingData['status'] ?? 'confirmed') ?>"><?= htmlspecialchars($bookingData['status'] ?? 'CONFIRMED') ?></span>
                    </div>
                    <div class="voucher-item">
                        <span class="voucher-label">Booking Date</span>
                        <span class="voucher-value"><?= htmlspecialchars($bookingData['created_at'] ?? date('Y-m-d')) ?></span>
                    </div>
                </div>

                <!-- Passenger Information (mandatory: holder name, pax per room) -->
                <div class="voucher-section">
                    <h4>&#128100; Guest Information</h4>
                    <p><strong>Lead Guest:</strong> <?= htmlspecialchars($bookingData['holder']) ?></p>
                </div>

                <!-- Room Details (mandatory: room type, board type, rate comments) -->
                <?php if (!empty($bookingData['rooms'])): ?>
                    <div class="voucher-section">
                        <h4>&#128719; Room Details</h4>
                        <?php foreach ($bookingData['rooms'] as $ri => $room): ?>
                            <div class="voucher-room-item">
                                <p><strong>Room <?= $ri + 1 ?>:</strong> <?= htmlspecialchars($room['name'] ?? 'Standard Room') ?> (<?= htmlspecialchars($room['code'] ?? '') ?>)</p>
                                <?php if (!empty($room['rates'])): ?>
                                    <?php foreach ($room['rates'] as $rate): ?>
                                        <?php if (!empty($rate['boardName'])): ?>
                                            <p>&#127860; Board: <strong><?= htmlspecialchars($rate['boardName']) ?></strong> (<?= htmlspecialchars($rate['boardCode'] ?? '') ?>)</p>
                                        <?php endif; ?>
                                        <?php if (!empty($rate['rateComments'])): ?>
                                            <div class="voucher-rate-comments">
                                                <p><strong>&#128196; Important Information:</strong></p>
                                                <p><?= nl2br(htmlspecialchars($rate['rateComments'])) ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($rate['cancellationPolicies'])): ?>
                                            <div class="voucher-cancel-policy">
                                                <p><strong>Cancellation Policy:</strong></p>
                                                <?php foreach ($rate['cancellationPolicies'] as $cp): ?>
                                                    <p>From <?= date('M d, Y H:i', strtotime($cp['from'])) ?>: <?= htmlspecialchars($cp['amount'] ?? '') ?> <?= htmlspecialchars($bookingData['currency']) ?></p>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($rate['paxes'])): ?>
                                            <p><strong>Guests:</strong>
                                            <?php foreach ($rate['paxes'] as $pax): ?>
                                                <?= htmlspecialchars(($pax['name'] ?? '') . ' ' . ($pax['surname'] ?? '')) ?> (<?= $pax['type'] === 'AD' ? 'Adult' : 'Child' . (!empty($pax['age']) ? ', age ' . $pax['age'] : '') ?>)<?php if (!end($rate['paxes'])) echo ', '; ?>
                                            <?php endforeach; ?>
                                            </p>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Payment Information (mandatory per 4.5) -->
                <div class="voucher-section voucher-payment">
                    <h4>&#128179; Payment Information</h4>
                    <p>Payable through <strong><?= htmlspecialchars($bookingData['supplier_name'] ?? 'Hotelbeds') ?></strong>, acting as agent for the service operating company, details of which can be provided upon request.<?php if (!empty($bookingData['supplier_vat'])): ?> VAT: <?= htmlspecialchars($bookingData['supplier_vat']) ?><?php endif; ?> Reference: <?= htmlspecialchars($bookingData['reference']) ?></p>
                </div>
            </div>

            <div class="voucher-actions">
                <button onclick="window.print()" class="btn btn-outline-dark">&#128424; Print Voucher</button>
                <a href="<?= url('home') ?>" class="btn">&#8592; Back to Home</a>
            </div>
        </div>
    </div>

<?php endif; ?>

</section>
