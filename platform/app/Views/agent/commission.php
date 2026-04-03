<?php use App\Helpers\View; ?>

<style>
    .agent-portal { max-width: 1200px; margin: 0 auto; padding: 1.5rem; }
    .agent-page-header { margin-bottom: 1.5rem; }
    .agent-page-header h1 { font-size: 1.5rem; color: #1a2332; margin: 0; }
    .agent-breadcrumb { font-size: 0.85rem; color: #6c757d; margin-bottom: 0.3rem; }
    .agent-breadcrumb a { color: #2c5364; text-decoration: none; }

    .commission-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.2rem; margin-bottom: 2rem; }
    .commission-card { background: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-left: 4px solid #2c5364; }
    .commission-card.earned { border-left-color: #28a745; }
    .commission-card.earned .card-value { color: #28a745; }
    .commission-card.pending { border-left-color: #ffc107; }
    .commission-card.pending .card-value { color: #856404; }
    .commission-card.this-month { border-left-color: #2c5364; }
    .commission-card.this-month .card-value { color: #2c5364; }
    .commission-card .card-value { font-size: 2rem; font-weight: 700; }
    .commission-card .card-label { color: #6c757d; font-size: 0.85rem; margin-top: 0.3rem; }

    .agency-info-box { background: linear-gradient(135deg, #2c5364 0%, #203a43 50%, #0f2027 100%); border-radius: 12px; padding: 1.2rem 2rem; color: #fff; margin-bottom: 2rem; display: flex; gap: 2rem; flex-wrap: wrap; }
    .agency-info-box .info-item { display: flex; flex-direction: column; }
    .agency-info-box .info-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.8; margin-bottom: 0.2rem; }
    .agency-info-box .info-value { font-size: 0.95rem; font-weight: 600; }

    .section-title { font-size: 1.1rem; color: #1a2332; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #eee; }

    .agent-table-wrap { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto; margin-bottom: 2rem; }
    .agent-table { width: 100%; border-collapse: collapse; }
    .agent-table th { text-align: left; padding: 0.8rem 1rem; border-bottom: 2px solid #eee; color: #6c757d; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .agent-table td { padding: 0.8rem 1rem; border-bottom: 1px solid #f0f0f0; font-size: 0.85rem; }
    .agent-table tr:hover td { background: #f8f9fa; }
    .agent-table .text-right { text-align: right; }
    .agent-table .commission-col { color: #28a745; font-weight: 600; }
    .agent-table .total-row td { font-weight: 700; background: #f8f9fa; border-top: 2px solid #dee2e6; }

    .badge { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
    .badge-confirmed { background: #d4edda; color: #155724; }
    .badge-completed { background: #cce5ff; color: #004085; }
    .badge-pending { background: #fff3cd; color: #856404; }

    .month-name { font-weight: 600; color: #1a2332; }

    @media (max-width: 768px) {
        .agent-portal { padding: 1rem; }
        .commission-summary { grid-template-columns: 1fr; }
        .agency-info-box { flex-direction: column; gap: 0.8rem; }
    }
</style>

<div class="agent-portal">
    <div class="agent-page-header">
        <div class="agent-breadcrumb"><a href="/agent">Dashboard</a> &rsaquo; Commission Report</div>
        <h1>Commission Report</h1>
    </div>

    <div class="commission-summary">
        <div class="commission-card earned">
            <div class="card-value">$<?= number_format((float)$totalEarned, 2) ?></div>
            <div class="card-label">Total Earned (Confirmed)</div>
        </div>
        <div class="commission-card pending">
            <div class="card-value">$<?= number_format((float)$pendingCommission, 2) ?></div>
            <div class="card-label">Pending Commission</div>
        </div>
        <div class="commission-card this-month">
            <div class="card-value">$<?= number_format((float)($thisMonth ?? 0), 2) ?></div>
            <div class="card-label">This Month</div>
        </div>
    </div>

    <?php if ($agency): ?>
    <div class="agency-info-box">
        <div class="info-item">
            <span class="info-label">Agency</span>
            <span class="info-value"><?= View::e($agency['name'] ?? '') ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Commission Rate</span>
            <span class="info-value"><?= number_format((float)($agency['commission_rate'] ?? 0), 1) ?>%</span>
        </div>
        <div class="info-item">
            <span class="info-label">Payment Model</span>
            <span class="info-value"><?= View::e(ucfirst($agency['payment_model'] ?? 'markup')) ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Account Balance</span>
            <span class="info-value">$<?= number_format((float)($agency['balance'] ?? 0), 2) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <h3 class="section-title">Monthly Breakdown</h3>
    <div class="agent-table-wrap">
        <table class="agent-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-right">Bookings</th>
                    <th class="text-right">Total Net</th>
                    <th class="text-right">Total Commission</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($monthlyBreakdown)): ?>
                    <tr><td colspan="4" style="text-align:center;color:#999;padding:2rem;">No commission data yet.</td></tr>
                <?php else: ?>
                    <?php
                        $grandNet = 0;
                        $grandCommission = 0;
                        $grandBookings = 0;
                    ?>
                    <?php foreach ($monthlyBreakdown as $row): ?>
                    <?php
                        $grandNet += (float)($row['total_net'] ?? 0);
                        $grandCommission += (float)($row['total_commission'] ?? 0);
                        $grandBookings += (int)($row['bookings_count'] ?? 0);
                    ?>
                    <tr>
                        <td class="month-name"><?= date('F Y', strtotime($row['month'] . '-01')) ?></td>
                        <td class="text-right"><?= (int)($row['bookings_count'] ?? 0) ?></td>
                        <td class="text-right">$<?= number_format((float)($row['total_net'] ?? 0), 2) ?></td>
                        <td class="text-right commission-col">$<?= number_format((float)($row['total_commission'] ?? 0), 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td>Total</td>
                        <td class="text-right"><?= $grandBookings ?></td>
                        <td class="text-right">$<?= number_format($grandNet, 2) ?></td>
                        <td class="text-right commission-col">$<?= number_format($grandCommission, 2) ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <h3 class="section-title">Per-Booking Commission Detail</h3>
    <div class="agent-table-wrap">
        <table class="agent-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th class="text-right">Net Price</th>
                    <th class="text-right">Sell Price</th>
                    <th class="text-right">Commission</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr><td colspan="8" style="text-align:center;color:#999;padding:2rem;">No bookings with commission yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><strong><?= View::e($b['reference']) ?></strong></td>
                        <td><?= View::e($b['guest_first_name'] . ' ' . $b['guest_last_name']) ?></td>
                        <td><?= View::e(ucfirst($b['product_type'] ?? 'hotel')) ?></td>
                        <td class="text-right"><?= View::e($b['currency'] ?? 'USD') ?> <?= number_format((float)($b['net_price'] ?? 0), 2) ?></td>
                        <td class="text-right"><?= View::e($b['currency'] ?? 'USD') ?> <?= number_format((float)($b['sell_price'] ?? 0), 2) ?></td>
                        <td class="text-right commission-col"><?= View::e($b['currency'] ?? 'USD') ?> <?= number_format((float)($b['commission'] ?? 0), 2) ?></td>
                        <td><span class="badge badge-<?= View::e($b['status'] ?? 'pending') ?>"><?= View::e(ucfirst($b['status'] ?? 'pending')) ?></span></td>
                        <td><?= View::date($b['created_at'] ?? date('Y-m-d')) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
