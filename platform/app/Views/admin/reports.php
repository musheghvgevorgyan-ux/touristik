<?php use App\Helpers\View; ?>

<style>
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .admin-header h1 { font-size: 1.8rem; color: var(--text-heading, #1a1a2e); margin: 0; }
    .admin-header p { color: #6c757d; margin: 0.3rem 0 0; font-size: 0.95rem; }

    /* Revenue Cards */
    .revenue-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.2rem; margin-bottom: 2rem; }
    .revenue-card { background: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
    .revenue-card .card-label { font-size: 0.82rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.4rem; }
    .revenue-card .card-value { font-size: 1.8rem; font-weight: 700; color: #FF6B35; }
    .revenue-card .card-sub { font-size: 0.82rem; color: #6c757d; margin-top: 0.3rem; }

    /* Booking Status Cards */
    .section-title { font-size: 1.15rem; font-weight: 700; color: var(--text-heading, #1a1a2e); margin: 0 0 1rem; }
    .status-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .status-card { padding: 1.2rem; border-radius: 10px; text-align: center; }
    .status-card .status-count { font-size: 2rem; font-weight: 700; }
    .status-card .status-label { font-size: 0.85rem; font-weight: 600; margin-top: 0.2rem; }
    .status-confirmed { background: #d4edda; }
    .status-confirmed .status-count { color: #155724; }
    .status-confirmed .status-label { color: #155724; }
    .status-pending { background: #fff3cd; }
    .status-pending .status-count { color: #856404; }
    .status-pending .status-label { color: #856404; }
    .status-cancelled { background: #f8d7da; }
    .status-cancelled .status-count { color: #721c24; }
    .status-cancelled .status-label { color: #721c24; }
    .status-completed { background: #d1ecf1; }
    .status-completed .status-count { color: #0c5460; }
    .status-completed .status-label { color: #0c5460; }

    /* Tables and Cards Section */
    .reports-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
    .report-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 1.5rem; }
    .report-card h3 { margin: 0 0 1rem; font-size: 1.05rem; color: var(--text-heading, #1a1a2e); padding-bottom: 0.6rem; border-bottom: 2px solid #f0f0f0; }
    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th { text-align: left; padding: 0.6rem 0.8rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .admin-table td { padding: 0.6rem 0.8rem; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
    .admin-table tr:hover td { background: #f8f9fa; }
    .rank-num { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: #FF6B35; color: #fff; border-radius: 50%; font-size: 0.75rem; font-weight: 700; }

    /* User Stats */
    .user-stat-row { display: flex; justify-content: space-between; padding: 0.55rem 0; border-bottom: 1px solid #f0f0f0; }
    .user-stat-row:last-child { border-bottom: none; }
    .user-stat-label { color: #6c757d; font-size: 0.88rem; font-weight: 600; }
    .user-stat-value { color: #333; font-size: 0.92rem; font-weight: 700; }
    .user-stat-value.highlight { color: #FF6B35; }

    .empty-state { text-align: center; padding: 2rem 1rem; color: #6c757d; font-size: 0.95rem; }

    @media (max-width: 768px) {
        .reports-grid { grid-template-columns: 1fr; }
        .revenue-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 480px) {
        .revenue-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="admin-header">
    <div>
        <h1><?= View::e($title) ?></h1>
        <p>Platform performance overview and analytics.</p>
    </div>
</div>

<!-- Revenue Overview -->
<div class="revenue-grid">
    <div class="revenue-card">
        <div class="card-label">Total Revenue</div>
        <div class="card-value">$<?= number_format($revenue['total'] ?? 0, 2) ?></div>
        <div class="card-sub">All time</div>
    </div>
    <div class="revenue-card">
        <div class="card-label">This Month</div>
        <div class="card-value">$<?= number_format($revenue['this_month'] ?? 0, 2) ?></div>
        <div class="card-sub"><?= date('F Y') ?></div>
    </div>
    <div class="revenue-card">
        <div class="card-label">Last Month</div>
        <div class="card-value">$<?= number_format($revenue['last_month'] ?? 0, 2) ?></div>
        <div class="card-sub"><?= date('F Y', strtotime('first day of last month')) ?></div>
    </div>
    <div class="revenue-card">
        <div class="card-label">Avg Booking Value</div>
        <div class="card-value">$<?= number_format($revenue['average'] ?? 0, 2) ?></div>
        <div class="card-sub">Per booking</div>
    </div>
</div>

<!-- Booking Stats by Status -->
<h2 class="section-title">Bookings by Status</h2>
<div class="status-grid">
    <div class="status-card status-confirmed">
        <div class="status-count"><?= (int)($bookingStats['confirmed'] ?? 0) ?></div>
        <div class="status-label">Confirmed</div>
    </div>
    <div class="status-card status-pending">
        <div class="status-count"><?= (int)($bookingStats['pending'] ?? 0) ?></div>
        <div class="status-label">Pending</div>
    </div>
    <div class="status-card status-cancelled">
        <div class="status-count"><?= (int)($bookingStats['cancelled'] ?? 0) ?></div>
        <div class="status-label">Cancelled</div>
    </div>
    <div class="status-card status-completed">
        <div class="status-count"><?= (int)($bookingStats['completed'] ?? 0) ?></div>
        <div class="status-label">Completed</div>
    </div>
</div>

<!-- Top Destinations & User Stats -->
<div class="reports-grid">
    <!-- Top Destinations -->
    <div class="report-card">
        <h3>Top Destinations</h3>
        <?php if (empty($topDestinations)): ?>
            <div class="empty-state">No booking data available yet.</div>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Destination</th>
                        <th>Bookings</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topDestinations as $i => $dest): ?>
                    <tr>
                        <td><span class="rank-num"><?= $i + 1 ?></span></td>
                        <td><strong><?= View::e($dest['name'] ?? '-') ?></strong></td>
                        <td><?= (int)($dest['booking_count'] ?? 0) ?></td>
                        <td><strong>$<?= number_format($dest['total_revenue'] ?? 0, 2) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- User Stats -->
    <div class="report-card">
        <h3>User Statistics</h3>
        <div class="user-stat-row">
            <span class="user-stat-label">Total Users</span>
            <span class="user-stat-value highlight"><?= (int)($userStats['total'] ?? 0) ?></span>
        </div>
        <div class="user-stat-row">
            <span class="user-stat-label">New This Month</span>
            <span class="user-stat-value"><?= (int)($userStats['new_this_month'] ?? 0) ?></span>
        </div>
        <?php if (!empty($userStats['by_role'])): ?>
            <?php foreach ($userStats['by_role'] as $role => $count): ?>
            <div class="user-stat-row">
                <span class="user-stat-label"><?= View::e(ucfirst($role)) ?>s</span>
                <span class="user-stat-value"><?= (int)$count ?></span>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
