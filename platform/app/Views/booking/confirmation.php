<?php use App\Helpers\View; ?>

<nav class="breadcrumbs" aria-label="Breadcrumb">
    <a href="/" data-t="breadcrumb_home">Home</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <a href="/hotels/search">Search</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <span class="breadcrumb-current">Booking Confirmation</span>
</nav>

<section class="booking-section">

<?php if (!$booking): ?>
    <!-- Booking Not Found -->
    <div class="booking-error-page">
        <div class="booking-icon">&#9888;</div>
        <h2>Booking Not Found</h2>
        <p>The requested booking could not be found. Please check the reference number and try again.</p>
        <a href="/" class="btn">&#8592; Back to Home</a>
    </div>

<?php else: ?>
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
                <span class="voucher-ref-code"><?= View::e($booking['reference'] ?? '') ?></span>
                <?php if (!empty($booking['client_reference'])): ?>
                    <span class="voucher-ref-label" style="margin-top:0.5rem;">Agency Reference</span>
                    <span class="voucher-ref-agency"><?= View::e($booking['client_reference']) ?></span>
                <?php endif; ?>
            </div>

            <div class="voucher-details">
                <!-- Hotel Information -->
                <div class="voucher-section">
                    <h4>&#127960; Hotel Information</h4>
                    <p class="voucher-hotel-name"><?= View::e($booking['hotel'] ?? '') ?></p>
                    <?php if (!empty($booking['hotel_category'])): ?>
                        <p>&#11088; <?= View::e($booking['hotel_category']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($booking['hotel_address'])): ?>
                        <p>&#128205; <?= View::e($booking['hotel_address']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($booking['hotel_destination'])): ?>
                        <p>&#127758; <?= View::e($booking['hotel_destination']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($booking['hotel_phone'])): ?>
                        <p>&#128222; <?= View::e($booking['hotel_phone']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Booking Grid -->
                <div class="voucher-grid">
                    <div class="voucher-item">
                        <span class="voucher-label">Check-in</span>
                        <span class="voucher-value"><?= !empty($booking['check_in']) ? View::date($booking['check_in'], 'D, M d Y') : 'N/A' ?></span>
                    </div>
                    <div class="voucher-item">
                        <span class="voucher-label">Check-out</span>
                        <span class="voucher-value"><?= !empty($booking['check_out']) ? View::date($booking['check_out'], 'D, M d Y') : 'N/A' ?></span>
                    </div>
                    <div class="voucher-item">
                        <span class="voucher-label">Status</span>
                        <span class="voucher-value voucher-status-<?= strtolower($booking['status'] ?? 'confirmed') ?>"><?= View::e($booking['status'] ?? 'CONFIRMED') ?></span>
                    </div>
                    <div class="voucher-item">
                        <span class="voucher-label">Booking Date</span>
                        <span class="voucher-value"><?= View::e($booking['created_at'] ?? date('Y-m-d')) ?></span>
                    </div>
                </div>

                <!-- Guest Information -->
                <div class="voucher-section">
                    <h4>&#128100; Guest Information</h4>
                    <p><strong>Lead Guest:</strong> <?= View::e($booking['holder'] ?? '') ?></p>
                </div>

                <!-- Room Details -->
                <?php if (!empty($booking['rooms'])): ?>
                    <div class="voucher-section">
                        <h4>&#128719; Room Details</h4>
                        <?php foreach ($booking['rooms'] as $ri => $room): ?>
                            <div class="voucher-room-item">
                                <p><strong>Room <?= $ri + 1 ?>:</strong> <?= View::e($room['name'] ?? 'Standard Room') ?> (<?= View::e($room['code'] ?? '') ?>)</p>
                                <?php if (!empty($room['rates'])): ?>
                                    <?php foreach ($room['rates'] as $rate): ?>
                                        <?php if (!empty($rate['boardName'])): ?>
                                            <p>&#127860; Board: <strong><?= View::e($rate['boardName']) ?></strong> (<?= View::e($rate['boardCode'] ?? '') ?>)</p>
                                        <?php endif; ?>
                                        <?php if (!empty($rate['rateComments'])): ?>
                                            <div class="voucher-rate-comments">
                                                <p><strong>&#128196; Important Information:</strong></p>
                                                <p><?= nl2br(View::e($rate['rateComments'])) ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($rate['cancellationPolicies'])): ?>
                                            <div class="voucher-cancel-policy">
                                                <p><strong>Cancellation Policy:</strong></p>
                                                <?php foreach ($rate['cancellationPolicies'] as $cp): ?>
                                                    <p>From <?= View::date($cp['from'] ?? '', 'M d, Y H:i') ?>: <?= View::e($cp['amount'] ?? '') ?> <?= View::e($booking['currency'] ?? '') ?></p>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($rate['paxes'])): ?>
                                            <p><strong>Guests:</strong>
                                            <?php foreach ($rate['paxes'] as $pi => $pax): ?>
                                                <?= View::e(($pax['name'] ?? '') . ' ' . ($pax['surname'] ?? '')) ?> (<?= ($pax['type'] ?? 'AD') === 'AD' ? 'Adult' : 'Child' . (!empty($pax['age']) ? ', age ' . (int) $pax['age'] : '') ?>)<?= $pi < count($rate['paxes']) - 1 ? ', ' : '' ?>
                                            <?php endforeach; ?>
                                            </p>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Payment Information -->
                <div class="voucher-section voucher-payment">
                    <h4>&#128179; Payment Information</h4>
                    <p>Payable through <strong><?= View::e($booking['supplier_name'] ?? 'Hotelbeds') ?></strong>, acting as agent for the service operating company, details of which can be provided upon request.<?php if (!empty($booking['supplier_vat'])): ?> VAT: <?= View::e($booking['supplier_vat']) ?><?php endif; ?> Reference: <?= View::e($booking['reference'] ?? '') ?></p>
                </div>
            </div>

            <div class="voucher-actions">
                <button onclick="window.print()" class="btn btn-outline-dark">&#128424; Print Voucher</button>
                <a href="/" class="btn">&#8592; Back to Home</a>
            </div>
        </div>
    </div>

<?php endif; ?>

</section>
