<?php use App\Helpers\View; ?>

<style>
    /* ─── Agent Portal Theme ─── */
    .agent-portal { max-width: 1200px; margin: 0 auto; padding: 1.5rem; }
    .agent-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
    .agent-header h1 { font-size: 1.8rem; color: #1a2332; margin: 0; }
    .agent-header h1 span { font-weight: 400; color: #6c757d; font-size: 1rem; }
    .agent-status { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .agent-status-active { background: #d4edda; color: #155724; }
    .agent-status-pending { background: #fff3cd; color: #856404; }
    .agent-status-suspended { background: #f8d7da; color: #721c24; }

    .agent-info-bar { display: flex; gap: 1.5rem; margin-bottom: 2rem; flex-wrap: wrap; background: linear-gradient(135deg, #2c5364 0%, #203a43 50%, #0f2027 100%); border-radius: 12px; padding: 1.5rem 2rem; color: #fff; }
    .agent-info-item { display: flex; flex-direction: column; }
    .agent-info-item .info-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.8; margin-bottom: 0.2rem; }
    .agent-info-item .info-value { font-size: 1rem; font-weight: 600; }

    .agent-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.2rem; margin-bottom: 2rem; }
    .agent-stat { background: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-left: 4px solid #2c5364; }
    .agent-stat .stat-value { font-size: 2rem; font-weight: 700; color: #2c5364; }
    .agent-stat .stat-label { color: #6c757d; font-size: 0.85rem; margin-top: 0.3rem; }
    .agent-stat.stat-highlight { border-left-color: #28a745; }
    .agent-stat.stat-highlight .stat-value { color: #28a745; }
    .agent-stat.stat-warning { border-left-color: #ffc107; }
    .agent-stat.stat-warning .stat-value { color: #856404; }

    .agent-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; margin-bottom: 2rem; }
    .agent-table-wrap h3 { padding: 1.2rem 1.5rem 0; color: #1a2332; font-size: 1.1rem; }
    .agent-table { width: 100%; border-collapse: collapse; }
    .agent-table th { text-align: left; padding: 0.8rem 1.2rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .agent-table td { padding: 0.8rem 1.2rem; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
    .agent-table tr:hover td { background: #f8f9fa; }
    .agent-table .text-right { text-align: right; }

    .badge { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
    .badge-confirmed { background: #d4edda; color: #155724; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }
    .badge-completed { background: #cce5ff; color: #004085; }
    .badge-failed { background: #f8d7da; color: #721c24; }

    .agent-quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
    .agent-quick-link { display: flex; align-items: center; gap: 0.8rem; background: #fff; padding: 1.2rem 1.5rem; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; color: #1a2332; transition: all 0.2s; border: 2px solid transparent; }
    .agent-quick-link:hover { border-color: #2c5364; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(44,83,100,0.15); }
    .agent-quick-link .link-icon { font-size: 1.5rem; flex-shrink: 0; }
    .agent-quick-link .link-text { font-weight: 600; font-size: 0.95rem; }
    .agent-quick-link .link-desc { font-size: 0.8rem; color: #6c757d; font-weight: 400; }

    .commission-highlight { color: #28a745; font-weight: 600; }

    @media (max-width: 768px) {
        .agent-portal { padding: 1rem; }
        .agent-header h1 { font-size: 1.4rem; }
        .agent-info-bar { flex-direction: column; gap: 0.8rem; }
        .agent-stats { grid-template-columns: repeat(2, 1fr); }
        .agent-quick-links { grid-template-columns: 1fr; }
    }
</style>

<div class="agent-portal">
    <div class="agent-header">
        <h1>
            <?= View::e($agency['name'] ?? 'Agency Dashboard') ?>
            <br><span>B2B Agent Portal</span>
        </h1>
        <span class="agent-status agent-status-<?= View::e($agency['status'] ?? 'pending') ?>">
            <?= View::e(ucfirst($agency['status'] ?? 'pending')) ?>
        </span>
    </div>

    <div class="agent-info-bar">
        <div class="agent-info-item">
            <span class="info-label">Commission Rate</span>
            <span class="info-value"><?= number_format((float)($agency['commission_rate'] ?? 0), 1) ?>%</span>
        </div>
        <div class="agent-info-item">
            <span class="info-label">Payment Model</span>
            <span class="info-value"><?= View::e(ucfirst($agency['payment_model'] ?? 'markup')) ?></span>
        </div>
        <div class="agent-info-item">
            <span class="info-label">Account Balance</span>
            <span class="info-value">$<?= number_format((float)($agency['balance'] ?? 0), 2) ?></span>
        </div>
    </div>

    <div class="agent-stats">
        <div class="agent-stat">
            <div class="stat-value"><?= (int)($stats['total_bookings'] ?? 0) ?></div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="agent-stat">
            <div class="stat-value"><?= (int)($stats['this_month'] ?? 0) ?></div>
            <div class="stat-label">This Month</div>
        </div>
        <div class="agent-stat stat-highlight">
            <div class="stat-value">$<?= number_format((float)($stats['total_commission'] ?? 0), 2) ?></div>
            <div class="stat-label">Commission Earned</div>
        </div>
        <div class="agent-stat stat-warning">
            <div class="stat-value">$<?= number_format((float)($stats['pending_commission'] ?? 0), 2) ?></div>
            <div class="stat-label">Pending Commission</div>
        </div>
    </div>

    <div class="agent-table-wrap">
        <h3>Recent Bookings</h3>
        <table class="agent-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Guest</th>
                    <th>Type</th>
                    <th>Net Price</th>
                    <th>Commission</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentBookings)): ?>
                    <tr><td colspan="7" style="text-align:center;color:#999;padding:2rem;">No bookings yet. Start by searching for hotels.</td></tr>
                <?php else: ?>
                    <?php foreach ($recentBookings as $b): ?>
                    <tr>
                        <td><strong><a href="/agent/bookings/<?= (int)$b['id'] ?>" style="color:#2c5364;text-decoration:none;"><?= View::e($b['reference']) ?></a></strong></td>
                        <td><?= View::e($b['guest_first_name'] . ' ' . $b['guest_last_name']) ?></td>
                        <td><?= View::e(ucfirst($b['product_type'] ?? 'hotel')) ?></td>
                        <td><?= View::e($b['currency'] ?? 'USD') ?> <?= number_format((float)($b['net_price'] ?? 0), 2) ?></td>
                        <td class="commission-highlight">$<?= number_format((float)($b['commission'] ?? 0), 2) ?></td>
                        <td><span class="badge badge-<?= View::e($b['status'] ?? 'pending') ?>"><?= View::e(ucfirst($b['status'] ?? 'pending')) ?></span></td>
                        <td><?= View::date($b['created_at'] ?? date('Y-m-d')) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="agent-quick-links">
        <a href="/agent/search" class="agent-quick-link">
            <span class="link-icon">&#128269;</span>
            <div>
                <div class="link-text">Search Hotels</div>
                <div class="link-desc">Find and book hotels at NET rates</div>
            </div>
        </a>
        <a href="/agent/bookings" class="agent-quick-link">
            <span class="link-icon">&#128203;</span>
            <div>
                <div class="link-text">My Bookings</div>
                <div class="link-desc">View and manage all bookings</div>
            </div>
        </a>
        <a href="/agent/commission" class="agent-quick-link">
            <span class="link-icon">&#128176;</span>
            <div>
                <div class="link-text">Commission Report</div>
                <div class="link-desc">Track earnings and payouts</div>
            </div>
        </a>
    </div>
</div>
