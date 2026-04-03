<?php use App\Helpers\View; ?>

<style>
    .agent-portal { max-width: 1200px; margin: 0 auto; padding: 1.5rem; }
    .agent-page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .agent-page-header h1 { font-size: 1.5rem; color: #1a2332; margin: 0; }
    .agent-breadcrumb { font-size: 0.85rem; color: #6c757d; }
    .agent-breadcrumb a { color: #2c5364; text-decoration: none; }

    .agent-filter-bar { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 1.2rem 1.5rem; margin-bottom: 1.5rem; }
    .filter-grid { display: flex; gap: 1rem; flex-wrap: wrap; align-items: end; }
    .filter-field { flex: 1; min-width: 150px; }
    .filter-field label { display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; margin-bottom: 0.3rem; }
    .filter-field input, .filter-field select { width: 100%; padding: 0.5rem 0.7rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.9rem; }
    .filter-field input:focus, .filter-field select:focus { outline: none; border-color: #2c5364; box-shadow: 0 0 0 3px rgba(44,83,100,0.1); }
    .btn-filter { padding: 0.5rem 1.2rem; background: #2c5364; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-filter:hover { background: #1e3a47; }
    .btn-reset { padding: 0.5rem 1rem; background: transparent; color: #6c757d; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.9rem; cursor: pointer; text-decoration: none; transition: all 0.2s; }
    .btn-reset:hover { background: #f8f9fa; color: #1a2332; }

    .agent-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; }
    .agent-table { width: 100%; border-collapse: collapse; min-width: 900px; }
    .agent-table th { text-align: left; padding: 0.8rem 1rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .agent-table td { padding: 0.8rem 1rem; border-bottom: 1px solid #f0f0f0; font-size: 0.85rem; }
    .agent-table tr:hover td { background: #f8f9fa; }
    .agent-table .text-right { text-align: right; }
    .agent-table .commission-col { color: #28a745; font-weight: 600; }

    .badge { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
    .badge-confirmed { background: #d4edda; color: #155724; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }
    .badge-completed { background: #cce5ff; color: #004085; }
    .badge-failed { background: #f8d7da; color: #721c24; }
    .badge-paid { background: #d4edda; color: #155724; }
    .badge-unpaid { background: #f8d7da; color: #721c24; }

    .agent-pagination { display: flex; justify-content: center; gap: 0.3rem; margin-top: 1.5rem; }
    .agent-pagination a, .agent-pagination span { display: inline-block; padding: 0.4rem 0.8rem; border-radius: 4px; font-size: 0.85rem; text-decoration: none; color: #1a2332; background: #fff; border: 1px solid #dee2e6; transition: all 0.2s; }
    .agent-pagination a:hover { background: #2c5364; color: #fff; border-color: #2c5364; }
    .agent-pagination .active { background: #2c5364; color: #fff; border-color: #2c5364; font-weight: 600; }
    .agent-pagination .disabled { opacity: 0.5; pointer-events: none; }

    .export-hint { text-align: right; margin-top: 1rem; font-size: 0.8rem; color: #6c757d; }

    @media (max-width: 768px) {
        .agent-portal { padding: 1rem; }
        .filter-grid { flex-direction: column; }
        .filter-field { min-width: 100%; }
    }
</style>

<div class="agent-portal">
    <div class="agent-page-header">
        <div>
            <div class="agent-breadcrumb"><a href="/agent">Dashboard</a> &rsaquo; Bookings</div>
            <h1>My Bookings</h1>
        </div>
        <a href="/agent/search" class="btn-filter">+ New Booking</a>
    </div>

    <form method="GET" action="/agent/bookings" class="agent-filter-bar">
        <div class="filter-grid">
            <div class="filter-field">
                <label for="search">Search</label>
                <input type="text" id="search" name="search" value="<?= View::e($filters['search'] ?? '') ?>" placeholder="Reference or guest name">
            </div>
            <div class="filter-field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All Statuses</option>
                    <?php foreach (['pending', 'confirmed', 'completed', 'cancelled', 'failed'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($filters['status'] ?? '') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-field">
                <label for="date_from">From Date</label>
                <input type="date" id="date_from" name="date_from" value="<?= View::e($filters['date_from'] ?? '') ?>">
            </div>
            <div class="filter-field">
                <label for="date_to">To Date</label>
                <input type="date" id="date_to" name="date_to" value="<?= View::e($filters['date_to'] ?? '') ?>">
            </div>
            <div class="filter-field" style="flex:0;display:flex;gap:0.5rem;align-items:end;">
                <button type="submit" class="btn-filter">Filter</button>
                <a href="/agent/bookings" class="btn-reset">Reset</a>
            </div>
        </div>
    </form>

    <div class="agent-table-wrap">
        <table class="agent-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Client Name</th>
                    <th>Type</th>
                    <th>Check-in</th>
                    <th>Net Price</th>
                    <th>Sell Price</th>
                    <th>Commission</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr><td colspan="10" style="text-align:center;color:#999;padding:2rem;">No bookings match your filters.</td></tr>
                <?php else: ?>
                    <?php foreach ($bookings as $b): ?>
                    <?php
                        $productData = is_string($b['product_data'] ?? '') ? json_decode($b['product_data'] ?? '{}', true) : ($b['product_data'] ?? []);
                        $checkIn = $productData['check_in'] ?? '';
                    ?>
                    <tr>
                        <td><strong><a href="/agent/bookings/<?= (int)$b['id'] ?>" style="color:#2c5364;text-decoration:none;"><?= View::e($b['reference']) ?></a></strong></td>
                        <td><?= View::e($b['guest_first_name'] . ' ' . $b['guest_last_name']) ?></td>
                        <td><?= View::e(ucfirst($b['product_type'] ?? 'hotel')) ?></td>
                        <td><?= $checkIn ? View::date($checkIn) : '-' ?></td>
                        <td><?= View::e($b['currency'] ?? 'USD') ?> <?= number_format((float)($b['net_price'] ?? 0), 2) ?></td>
                        <td><?= View::e($b['currency'] ?? 'USD') ?> <?= number_format((float)($b['sell_price'] ?? 0), 2) ?></td>
                        <td class="commission-col"><?= View::e($b['currency'] ?? 'USD') ?> <?= number_format((float)($b['commission'] ?? 0), 2) ?></td>
                        <td><span class="badge badge-<?= View::e($b['status'] ?? 'pending') ?>"><?= View::e(ucfirst($b['status'] ?? 'pending')) ?></span></td>
                        <td><span class="badge badge-<?= View::e($b['payment_status'] ?? 'unpaid') ?>"><?= View::e(ucfirst($b['payment_status'] ?? 'unpaid')) ?></span></td>
                        <td><?= View::date($b['created_at'] ?? date('Y-m-d')) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="agent-pagination">
        <?php
            $queryBase = $filters;
            // Previous
            if ($currentPage > 1) {
                $queryBase['page'] = $currentPage - 1;
                echo '<a href="/agent/bookings?' . http_build_query($queryBase) . '">&laquo; Prev</a>';
            } else {
                echo '<span class="disabled">&laquo; Prev</span>';
            }

            // Page numbers
            $start = max(1, $currentPage - 3);
            $end   = min($totalPages, $currentPage + 3);
            for ($p = $start; $p <= $end; $p++) {
                $queryBase['page'] = $p;
                if ($p === $currentPage) {
                    echo '<span class="active">' . $p . '</span>';
                } else {
                    echo '<a href="/agent/bookings?' . http_build_query($queryBase) . '">' . $p . '</a>';
                }
            }

            // Next
            if ($currentPage < $totalPages) {
                $queryBase['page'] = $currentPage + 1;
                echo '<a href="/agent/bookings?' . http_build_query($queryBase) . '">Next &raquo;</a>';
            } else {
                echo '<span class="disabled">Next &raquo;</span>';
            }
        ?>
    </div>
    <?php endif; ?>

    <div class="export-hint">
        CSV export coming soon.
    </div>
</div>
