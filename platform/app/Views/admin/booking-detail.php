<?php use App\Helpers\View; ?>

<?php $productData = is_string($booking['product_data'] ?? '') ? json_decode($booking['product_data'], true) : ($booking['product_data'] ?? []); ?>

<style>
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading, #1a1a2e); margin: 0; }
    .admin-header .btn-back { display: inline-flex; align-items: center; gap: 0.4rem; color: #6c757d; text-decoration: none; font-size: 0.9rem; transition: color 0.2s; }
    .admin-header .btn-back:hover { color: #FF6B35; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
    .detail-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 1.5rem; }
    .detail-card h3 { margin: 0 0 1rem; font-size: 1.05rem; color: var(--text-heading, #1a1a2e); padding-bottom: 0.6rem; border-bottom: 2px solid #f0f0f0; }
    .detail-row { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f8f8f8; }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { color: #6c757d; font-size: 0.88rem; font-weight: 600; }
    .detail-value { color: #333; font-size: 0.92rem; text-align: right; max-width: 60%; word-break: break-word; }
    .badge { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 4px; font-size: 0.78rem; font-weight: 600; white-space: nowrap; }
    .badge-confirmed, .badge-completed, .badge-paid, .badge-active { background: #d4edda; color: #155724; }
    .badge-pending, .badge-partial { background: #fff3cd; color: #856404; }
    .badge-cancelled, .badge-failed, .badge-suspended, .badge-rejected { background: #f8d7da; color: #721c24; }
    .badge-refunded { background: #d1ecf1; color: #0c5460; }
    .ref-code { font-family: 'Courier New', monospace; font-weight: 700; }
    .full-width { grid-column: 1 / -1; }
    .admin-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; margin-bottom: 1.5rem; }
    .admin-table-wrap h3 { padding: 1.2rem 1.5rem 0; color: var(--text-heading, #1a1a2e); margin: 0 0 0.8rem; font-size: 1.05rem; }
    .admin-table { width: 100%; border-collapse: collapse; min-width: 600px; }
    .admin-table th { text-align: left; padding: 0.7rem 1.2rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .admin-table td { padding: 0.7rem 1.2rem; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
    .admin-table tr:hover td { background: #f8f9fa; }
    .actions-bar { display: flex; align-items: center; gap: 1rem; background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 1.2rem 1.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .actions-bar h3 { margin: 0; font-size: 1.05rem; color: var(--text-heading, #1a1a2e); margin-right: auto; }
    .btn-action { padding: 0.5rem 1rem; border: none; border-radius: 6px; font-size: 0.88rem; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem; }
    .btn-cancel { background: #f8d7da; color: #721c24; }
    .btn-cancel:hover { background: #f1b0b7; }
    .btn-status { background: #FF6B35; color: #fff; }
    .btn-status:hover { background: #e55a2b; }
    .status-form { display: inline-flex; align-items: center; gap: 0.5rem; }
    .status-form select { padding: 0.45rem 0.7rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.88rem; }
    .activity-item { display: flex; align-items: flex-start; gap: 1rem; padding: 0.7rem 0; border-bottom: 1px solid #f0f0f0; }
    .activity-item:last-child { border-bottom: none; }
    .activity-dot { width: 8px; height: 8px; border-radius: 50%; background: #FF6B35; margin-top: 0.4rem; flex-shrink: 0; }
    .activity-text { font-size: 0.9rem; color: #333; }
    .activity-meta { font-size: 0.8rem; color: #6c757d; margin-top: 0.15rem; }
    .empty-state { text-align: center; padding: 2rem 1rem; color: #6c757d; font-size: 0.95rem; }
    @media (max-width: 768px) {
        .detail-grid { grid-template-columns: 1fr; }
        .detail-row { flex-direction: column; gap: 0.2rem; }
        .detail-value { text-align: left; max-width: 100%; }
    }
</style>

<div class="admin-header">
    <div>
        <a href="/admin/bookings" class="btn-back">&larr; Back to Bookings</a>
        <h1><?= View::e($title) ?></h1>
    </div>
</div>

<!-- Actions Bar -->
<div class="actions-bar">
    <h3>Actions</h3>
    <form method="POST" action="/admin/bookings/<?= View::e($booking['id']) ?>/status" class="status-form">
        <?= View::csrf() ?>
        <select name="status">
            <option value="pending" <?= $booking['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $booking['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="completed" <?= $booking['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="cancelled" <?= $booking['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <button type="submit" class="btn-action btn-status">Update Status</button>
    </form>
    <?php if ($booking['status'] !== 'cancelled'): ?>
        <form method="POST" action="/admin/bookings/<?= View::e($booking['id']) ?>/cancel" onsubmit="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')">
            <?= View::csrf() ?>
            <button type="submit" class="btn-action btn-cancel">Cancel Booking</button>
        </form>
    <?php endif; ?>
</div>

<div class="detail-grid">
    <!-- Booking Info -->
    <div class="detail-card">
        <h3>Booking Information</h3>
        <div class="detail-row">
            <span class="detail-label">Reference</span>
            <span class="detail-value ref-code"><?= View::e($booking['reference']) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Supplier Reference</span>
            <span class="detail-value"><?= View::e($booking['supplier_reference'] ?? '-') ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-value"><span class="badge badge-<?= View::e($booking['status']) ?>"><?= View::e(ucfirst($booking['status'])) ?></span></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Payment Status</span>
            <span class="detail-value"><span class="badge badge-<?= View::e($booking['payment_status'] ?? 'pending') ?>"><?= View::e(ucfirst($booking['payment_status'] ?? 'Pending')) ?></span></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Product Type</span>
            <span class="detail-value"><?= View::e(ucfirst($booking['product_type'] ?? 'N/A')) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Supplier</span>
            <span class="detail-value"><?= View::e(ucfirst($booking['supplier'] ?? '-')) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Price</span>
            <span class="detail-value"><strong><?= View::e($booking['currency'] ?? 'USD') ?> <?= number_format($booking['sell_price'] ?? 0, 2) ?></strong></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Cost Price</span>
            <span class="detail-value"><?= View::e($booking['currency'] ?? 'USD') ?> <?= number_format($booking['cost_price'] ?? 0, 2) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Created</span>
            <span class="detail-value"><?= View::date($booking['created_at']) ?></span>
        </div>
    </div>

    <!-- Guest Info -->
    <div class="detail-card">
        <h3>Guest Information</h3>
        <div class="detail-row">
            <span class="detail-label">Full Name</span>
            <span class="detail-value"><?= View::e(($booking['guest_first_name'] ?? '') . ' ' . ($booking['guest_last_name'] ?? '')) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Email</span>
            <span class="detail-value"><?= View::e($booking['guest_email'] ?? '-') ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Phone</span>
            <span class="detail-value"><?= View::e($booking['guest_phone'] ?? '-') ?></span>
        </div>
    </div>

    <!-- Product Details -->
    <div class="detail-card full-width">
        <h3>Product Details</h3>
        <?php if (!empty($productData)): ?>
            <div class="detail-row">
                <span class="detail-label">Hotel / Tour Name</span>
                <span class="detail-value"><?= View::e($productData['hotel_name'] ?? $productData['tour_name'] ?? '-') ?></span>
            </div>
            <?php if (!empty($productData['check_in'])): ?>
            <div class="detail-row">
                <span class="detail-label">Check-in</span>
                <span class="detail-value"><?= View::date($productData['check_in'], 'l, M d, Y') ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($productData['check_out'])): ?>
            <div class="detail-row">
                <span class="detail-label">Check-out</span>
                <span class="detail-value"><?= View::date($productData['check_out'], 'l, M d, Y') ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($productData['room_name'])): ?>
            <div class="detail-row">
                <span class="detail-label">Room</span>
                <span class="detail-value"><?= View::e($productData['room_name']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($productData['board_name'])): ?>
            <div class="detail-row">
                <span class="detail-label">Board</span>
                <span class="detail-value"><?= View::e($productData['board_name']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($productData['adults'])): ?>
            <div class="detail-row">
                <span class="detail-label">Adults</span>
                <span class="detail-value"><?= View::e($productData['adults']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($productData['children'])): ?>
            <div class="detail-row">
                <span class="detail-label">Children</span>
                <span class="detail-value"><?= View::e($productData['children']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($productData['destination'])): ?>
            <div class="detail-row">
                <span class="detail-label">Destination</span>
                <span class="detail-value"><?= View::e($productData['destination']) ?></span>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">No product details available.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Payment History -->
<div class="admin-table-wrap">
    <h3>Payment History</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Gateway</th>
                <th>Method</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($payments)): ?>
                <tr><td colspan="6" class="empty-state">No payment records found.</td></tr>
            <?php else: ?>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><span class="ref-code"><?= View::e($payment['transaction_id'] ?? '-') ?></span></td>
                    <td><?= View::e(ucfirst($payment['gateway'] ?? '-')) ?></td>
                    <td><?= View::e(ucfirst($payment['payment_method'] ?? '-')) ?></td>
                    <td><strong><?= View::e($payment['currency'] ?? 'USD') ?> <?= number_format($payment['amount'] ?? 0, 2) ?></strong></td>
                    <td><span class="badge badge-<?= View::e($payment['status'] ?? 'pending') ?>"><?= View::e(ucfirst($payment['status'] ?? 'Pending')) ?></span></td>
                    <td><?= !empty($payment['created_at']) ? View::date($payment['created_at']) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Activity Log -->
<div class="detail-card full-width" style="grid-column: unset;">
    <h3>Activity Log</h3>
    <?php if (empty($activities)): ?>
        <div class="empty-state">No activity recorded yet.</div>
    <?php else: ?>
        <?php foreach ($activities as $activity): ?>
        <div class="activity-item">
            <div class="activity-dot"></div>
            <div>
                <div class="activity-text"><?= View::e($activity['action']) ?></div>
                <div class="activity-meta">
                    <?= View::e($activity['user_name'] ?? 'System') ?>
                    &middot;
                    <?= View::date($activity['created_at'], 'M d, Y H:i') ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
