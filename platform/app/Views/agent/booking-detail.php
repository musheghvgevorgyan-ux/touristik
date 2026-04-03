<?php use App\Helpers\View; ?>

<?php
$productData = $booking['product_data'] ?? [];
if (is_string($productData)) {
    $productData = json_decode($productData, true) ?: [];
}
?>

<style>
    .agent-portal { max-width: 900px; margin: 0 auto; padding: 1.5rem; }
    .agent-page-header { margin-bottom: 1.5rem; }
    .agent-page-header h1 { font-size: 1.5rem; color: #1a2332; margin: 0.3rem 0 0; }
    .agent-breadcrumb { font-size: 0.85rem; color: #6c757d; }
    .agent-breadcrumb a { color: #2c5364; text-decoration: none; }

    .booking-status-bar { display: flex; justify-content: space-between; align-items: center; background: #fff; border-radius: 10px; padding: 1.2rem 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem; }
    .status-left { display: flex; align-items: center; gap: 1rem; }

    .badge-lg { display: inline-block; padding: 0.3rem 0.8rem; border-radius: 6px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; }
    .badge-lg.badge-confirmed { background: #d4edda; color: #155724; }
    .badge-lg.badge-pending { background: #fff3cd; color: #856404; }
    .badge-lg.badge-cancelled { background: #f8d7da; color: #721c24; }
    .badge-lg.badge-completed { background: #cce5ff; color: #004085; }
    .badge-lg.badge-failed { background: #f8d7da; color: #721c24; }

    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
    .detail-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 1.5rem; }
    .detail-card h3 { font-size: 1rem; color: #1a2332; margin: 0 0 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #eee; }
    .detail-row { display: flex; justify-content: space-between; padding: 0.4rem 0; font-size: 0.9rem; }
    .detail-row .label { color: #6c757d; }
    .detail-row .value { font-weight: 600; color: #1a2332; }
    .detail-row .value.commission { color: #28a745; }

    .financial-card { border-left: 4px solid #2c5364; }
    .financial-card .total-row { border-top: 2px solid #eee; margin-top: 0.5rem; padding-top: 0.8rem; }
    .financial-card .total-row .value { font-size: 1.1rem; color: #2c5364; }

    .back-link { display: inline-block; margin-top: 1rem; color: #2c5364; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
    .back-link:hover { text-decoration: underline; }

    @media (max-width: 768px) {
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="agent-portal">
    <div class="agent-page-header">
        <div class="agent-breadcrumb"><a href="/agent">Dashboard</a> &rsaquo; <a href="/agent/bookings">Bookings</a> &rsaquo; <?= View::e($booking['reference'] ?? '') ?></div>
        <h1>Booking #<?= View::e($booking['reference'] ?? '') ?></h1>
    </div>

    <div class="booking-status-bar">
        <div class="status-left">
            <span class="badge-lg badge-<?= View::e($booking['status'] ?? 'pending') ?>"><?= View::e(ucfirst($booking['status'] ?? 'pending')) ?></span>
            <span style="font-size:0.85rem;color:#6c757d;">Created <?= View::date($booking['created_at'] ?? date('Y-m-d'), 'M d, Y H:i') ?></span>
        </div>
        <div>
            <span style="font-size:0.85rem;color:#6c757d;">Supplier Ref: <strong><?= View::e($booking['supplier_ref'] ?? 'N/A') ?></strong></span>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-card">
            <h3>Guest Information</h3>
            <div class="detail-row">
                <span class="label">Name</span>
                <span class="value"><?= View::e($booking['guest_first_name'] . ' ' . $booking['guest_last_name']) ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Email</span>
                <span class="value"><?= View::e($booking['guest_email'] ?? '-') ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Phone</span>
                <span class="value"><?= View::e($booking['guest_phone'] ?? '-') ?></span>
            </div>
        </div>

        <div class="detail-card">
            <h3>Booking Details</h3>
            <div class="detail-row">
                <span class="label">Type</span>
                <span class="value"><?= View::e(ucfirst($booking['product_type'] ?? 'hotel')) ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Supplier</span>
                <span class="value"><?= View::e(ucfirst($booking['supplier'] ?? '-')) ?></span>
            </div>
            <?php if (!empty($productData['hotel'])): ?>
            <div class="detail-row">
                <span class="label">Hotel</span>
                <span class="value"><?= View::e($productData['hotel'] ?? '-') ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($productData['check_in'])): ?>
            <div class="detail-row">
                <span class="label">Check-in</span>
                <span class="value"><?= View::date($productData['check_in']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($productData['check_out'])): ?>
            <div class="detail-row">
                <span class="label">Check-out</span>
                <span class="value"><?= View::date($productData['check_out']) ?></span>
            </div>
            <?php endif; ?>
            <div class="detail-row">
                <span class="label">Payment Status</span>
                <span class="value"><?= View::e(ucfirst($booking['payment_status'] ?? 'unpaid')) ?></span>
            </div>
        </div>

        <div class="detail-card financial-card" style="grid-column: 1 / -1;">
            <h3>Financial Summary</h3>
            <div class="detail-row">
                <span class="label">Currency</span>
                <span class="value"><?= View::e($booking['currency'] ?? 'USD') ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Net Price (Supplier Cost)</span>
                <span class="value"><?= View::e($booking['currency'] ?? 'USD') ?> <?= number_format((float)($booking['net_price'] ?? 0), 2) ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Sell Price (Client Charged)</span>
                <span class="value"><?= View::e($booking['currency'] ?? 'USD') ?> <?= number_format((float)($booking['sell_price'] ?? 0), 2) ?></span>
            </div>
            <?php if ((float)($booking['discount_amount'] ?? 0) > 0): ?>
            <div class="detail-row">
                <span class="label">Discount</span>
                <span class="value">-<?= View::e($booking['currency'] ?? 'USD') ?> <?= number_format((float)$booking['discount_amount'], 2) ?></span>
            </div>
            <?php endif; ?>
            <div class="detail-row total-row">
                <span class="label">Commission</span>
                <span class="value commission"><?= View::e($booking['currency'] ?? 'USD') ?> <?= number_format((float)($booking['commission'] ?? 0), 2) ?></span>
            </div>
        </div>
    </div>

    <a href="/agent/bookings" class="back-link">&larr; Back to Bookings</a>
</div>
