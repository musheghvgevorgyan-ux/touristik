<?php use App\Helpers\View; ?>

<style>
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading, #1a1a2e); margin: 0; }
    .admin-filter-bar { display: flex; flex-wrap: wrap; gap: 0.8rem; align-items: flex-end; background: #fff; padding: 1.2rem 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 1.5rem; }
    .admin-filter-group { display: flex; flex-direction: column; gap: 0.3rem; }
    .admin-filter-group label { font-size: 0.8rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
    .admin-filter-group select,
    .admin-filter-group input { padding: 0.55rem 0.8rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.9rem; background: #fff; color: #333; min-width: 140px; }
    .admin-filter-group select:focus,
    .admin-filter-group input:focus { outline: none; border-color: #FF6B35; box-shadow: 0 0 0 3px rgba(255,107,53,0.15); }
    .btn-filter { padding: 0.55rem 1.2rem; background: #FF6B35; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; align-self: flex-end; }
    .btn-filter:hover { background: #e55a2b; }
    .btn-reset { padding: 0.55rem 1.2rem; background: #6c757d; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; text-decoration: none; align-self: flex-end; display: inline-flex; align-items: center; }
    .btn-reset:hover { background: #5a6268; }
    .admin-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; }
    .admin-table { width: 100%; border-collapse: collapse; min-width: 900px; }
    .admin-table th { text-align: left; padding: 0.8rem 1.2rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .admin-table td { padding: 0.8rem 1.2rem; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; vertical-align: middle; }
    .admin-table tr:hover td { background: #f8f9fa; }
    .admin-table a.view-link { color: #FF6B35; text-decoration: none; font-weight: 600; white-space: nowrap; }
    .admin-table a.view-link:hover { text-decoration: underline; }
    .badge { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 4px; font-size: 0.78rem; font-weight: 600; white-space: nowrap; }
    .badge-confirmed, .badge-completed, .badge-paid, .badge-active { background: #d4edda; color: #155724; }
    .badge-pending, .badge-partial { background: #fff3cd; color: #856404; }
    .badge-cancelled, .badge-failed, .badge-suspended, .badge-rejected { background: #f8d7da; color: #721c24; }
    .badge-refunded { background: #d1ecf1; color: #0c5460; }
    .empty-state { text-align: center; padding: 3rem 1.5rem; color: #6c757d; }
    .empty-state .empty-icon { font-size: 3rem; margin-bottom: 0.8rem; display: block; }
    .empty-state p { font-size: 1.05rem; margin: 0; }
    .ref-code { font-family: 'Courier New', monospace; font-weight: 700; font-size: 0.88rem; }
</style>

<div class="admin-header">
    <h1><?= View::e($title) ?></h1>
</div>

<form method="GET" action="/admin/bookings" class="admin-filter-bar">
    <div class="admin-filter-group">
        <label for="filter-status">Status</label>
        <select name="status" id="filter-status">
            <option value="">All Statuses</option>
            <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>
    </div>
    <div class="admin-filter-group">
        <label for="filter-search">Search</label>
        <input type="text" name="search" id="filter-search" placeholder="Reference or guest name..." value="<?= View::e($filters['search'] ?? '') ?>">
    </div>
    <div class="admin-filter-group">
        <label for="filter-date-from">From</label>
        <input type="date" name="date_from" id="filter-date-from" value="<?= View::e($filters['date_from'] ?? '') ?>">
    </div>
    <div class="admin-filter-group">
        <label for="filter-date-to">To</label>
        <input type="date" name="date_to" id="filter-date-to" value="<?= View::e($filters['date_to'] ?? '') ?>">
    </div>
    <button type="submit" class="btn-filter">Filter</button>
    <a href="/admin/bookings" class="btn-reset">Reset</a>
</form>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Guest Name</th>
                <th>Type</th>
                <th>Hotel / Tour</th>
                <th>Check-in</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Price</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bookings)): ?>
                <tr>
                    <td colspan="10">
                        <div class="empty-state">
                            <span class="empty-icon">&#128203;</span>
                            <p>No bookings found matching your criteria.</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <?php $productData = is_string($booking['product_data'] ?? '') ? json_decode($booking['product_data'], true) : ($booking['product_data'] ?? []); ?>
                    <tr>
                        <td><span class="ref-code"><?= View::e($booking['reference']) ?></span></td>
                        <td><?= View::e(($booking['guest_first_name'] ?? '') . ' ' . ($booking['guest_last_name'] ?? '')) ?></td>
                        <td><?= View::e(ucfirst($booking['product_type'] ?? 'N/A')) ?></td>
                        <td><?= View::e($productData['hotel_name'] ?? $productData['tour_name'] ?? '-') ?></td>
                        <td><?= !empty($booking['check_in']) ? View::date($booking['check_in'], 'M d, Y') : '-' ?></td>
                        <td><span class="badge badge-<?= View::e($booking['status']) ?>"><?= View::e(ucfirst($booking['status'])) ?></span></td>
                        <td><span class="badge badge-<?= View::e($booking['payment_status'] ?? 'pending') ?>"><?= View::e(ucfirst($booking['payment_status'] ?? 'Pending')) ?></span></td>
                        <td><strong><?= View::e($booking['currency'] ?? 'USD') ?> <?= number_format($booking['sell_price'] ?? 0, 2) ?></strong></td>
                        <td><?= View::date($booking['created_at']) ?></td>
                        <td><a href="/admin/bookings/<?= View::e($booking['id']) ?>" class="view-link">View &rarr;</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$baseUrl = '/admin/bookings';
include BASE_PATH . '/app/Views/partials/pagination.php';
?>
