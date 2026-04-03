<?php use App\Helpers\View; ?>

<style>
    .dashboard-page { max-width: 1100px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .dashboard-welcome { margin-bottom: 2rem; }
    .dashboard-welcome h1 { font-size: 1.8rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .dashboard-welcome p { color: var(--text-secondary); font-size: 1rem; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem; }
    .stat-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 1.5rem 2rem; display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
    .stat-icon.bookings { background: rgba(255,107,53,0.12); color: #FF6B35; }
    .stat-icon.upcoming { background: rgba(52,168,83,0.12); color: #34a853; }
    .stat-icon.wishlist { background: rgba(66,133,244,0.12); color: #4285f4; }
    .stat-content h3 { font-size: 1.6rem; color: var(--text-heading); margin: 0; line-height: 1.2; }
    .stat-content p { font-size: 0.85rem; color: var(--text-secondary); margin: 0; }
    .dashboard-section { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 2rem; margin-bottom: 2rem; }
    .dashboard-section h2 { font-size: 1.3rem; color: var(--text-heading); margin-bottom: 1.2rem; display: flex; align-items: center; justify-content: space-between; }
    .dashboard-section h2 a { font-size: 0.9rem; color: var(--primary); text-decoration: none; font-weight: 500; }
    .dashboard-section h2 a:hover { text-decoration: underline; }
    .bookings-table { width: 100%; border-collapse: collapse; }
    .bookings-table th { text-align: left; padding: 0.7rem 1rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); }
    .bookings-table td { padding: 0.8rem 1rem; font-size: 0.95rem; color: var(--text-primary); border-bottom: 1px solid var(--border-color); }
    .bookings-table tr:last-child td { border-bottom: none; }
    .bookings-table .status { display: inline-block; padding: 0.2rem 0.7rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .bookings-table .status-confirmed { background: rgba(52,168,83,0.12); color: #34a853; }
    .bookings-table .status-pending { background: rgba(255,152,0,0.12); color: #ff9800; }
    .bookings-table .status-cancelled { background: rgba(234,67,53,0.12); color: #ea4335; }
    .bookings-empty { text-align: center; padding: 2rem 0; color: var(--text-secondary); }
    .bookings-empty p { margin-bottom: 1rem; }
    .bookings-empty a { color: var(--primary); text-decoration: none; font-weight: 600; }
    .quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; }
    .quick-link { display: flex; align-items: center; gap: 0.8rem; padding: 1rem 1.2rem; background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius); text-decoration: none; color: var(--text-heading); font-weight: 600; font-size: 0.95rem; transition: border-color 0.2s, background 0.2s; }
    .quick-link:hover { border-color: var(--primary); background: rgba(255,107,53,0.04); }
    .quick-link .ql-icon { font-size: 1.3rem; }
    @media (max-width: 768px) {
        .bookings-table { font-size: 0.85rem; }
        .bookings-table th, .bookings-table td { padding: 0.5rem; }
        .stats-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 600px) {
        .bookings-table-wrap { overflow-x: auto; }
    }
</style>

<div class="dashboard-page">
    <div class="dashboard-welcome reveal">
        <h1 data-t="dashboard_welcome">Welcome back, <?= View::e($user['first_name'] ?? 'Traveler') ?>!</h1>
        <p data-t="dashboard_subtitle">Here's an overview of your travel activity</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card reveal">
            <div class="stat-icon bookings">&#128203;</div>
            <div class="stat-content">
                <h3><?= (int)($bookingsCount ?? 0) ?></h3>
                <p data-t="total_bookings">Total Bookings</p>
            </div>
        </div>
        <div class="stat-card reveal">
            <div class="stat-icon upcoming">&#9992;</div>
            <div class="stat-content">
                <h3><?php
                    $upcoming = 0;
                    if (!empty($recentBookings)) {
                        foreach ($recentBookings as $b) {
                            if (($b['status'] ?? '') === 'confirmed' && strtotime($b['check_in'] ?? $b['date'] ?? '') > time()) {
                                $upcoming++;
                            }
                        }
                    }
                    echo $upcoming;
                ?></h3>
                <p data-t="upcoming_trips">Upcoming Trips</p>
            </div>
        </div>
        <div class="stat-card reveal">
            <div class="stat-icon wishlist">&#10084;</div>
            <div class="stat-content">
                <h3>0</h3>
                <p data-t="wishlist_items">Wishlist Items</p>
            </div>
        </div>
    </div>

    <div class="dashboard-section reveal">
        <h2>
            <span data-t="recent_bookings">Recent Bookings</span>
            <a href="/account/bookings" data-t="view_all">View All</a>
        </h2>
        <?php if (!empty($recentBookings)): ?>
            <div class="bookings-table-wrap">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th data-t="col_reference">Reference</th>
                            <th data-t="col_type">Type</th>
                            <th data-t="col_name">Name</th>
                            <th data-t="col_dates">Dates</th>
                            <th data-t="col_status">Status</th>
                            <th data-t="col_total">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($recentBookings, 0, 5) as $booking): ?>
                            <tr>
                                <td><a href="/account/bookings/<?= View::e($booking['id'] ?? '') ?>" style="color:var(--primary);text-decoration:none;font-weight:600;"><?= View::e($booking['reference'] ?? '---') ?></a></td>
                                <td><?= View::e(ucfirst($booking['product_type'] ?? 'hotel')) ?></td>
                                <td><?= View::e($booking['name'] ?? $booking['hotel_name'] ?? '---') ?></td>
                                <td><?= !empty($booking['check_in']) ? View::date($booking['check_in'], 'M d') : '---' ?><?= !empty($booking['check_out']) ? ' - ' . View::date($booking['check_out'], 'M d') : '' ?></td>
                                <td>
                                    <?php
                                        $status = $booking['status'] ?? 'pending';
                                        $statusClass = match($status) {
                                            'confirmed' => 'status-confirmed',
                                            'cancelled' => 'status-cancelled',
                                            default => 'status-pending',
                                        };
                                    ?>
                                    <span class="status <?= $statusClass ?>"><?= View::e(ucfirst($status)) ?></span>
                                </td>
                                <td><?= isset($booking['total_price']) ? View::price($booking['total_price'], $booking['currency'] ?? 'USD') : '---' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="bookings-empty">
                <p data-t="no_bookings">You haven't made any bookings yet.</p>
                <a href="/hotels/search" data-t="start_searching">Start searching for hotels</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="dashboard-section reveal">
        <h2 data-t="quick_links">Quick Links</h2>
        <div class="quick-links">
            <a href="/account/profile" class="quick-link">
                <span class="ql-icon">&#128100;</span>
                <span data-t="edit_profile">Edit Profile</span>
            </a>
            <a href="/account/bookings" class="quick-link">
                <span class="ql-icon">&#128203;</span>
                <span data-t="my_bookings">My Bookings</span>
            </a>
            <a href="/hotels/search" class="quick-link">
                <span class="ql-icon">&#127976;</span>
                <span data-t="search_hotels">Search Hotels</span>
            </a>
            <a href="/tours" class="quick-link">
                <span class="ql-icon">&#127759;</span>
                <span data-t="browse_tours">Browse Tours</span>
            </a>
            <a href="/destinations" class="quick-link">
                <span class="ql-icon">&#128205;</span>
                <span data-t="destinations">Destinations</span>
            </a>
            <a href="/contact" class="quick-link">
                <span class="ql-icon">&#128172;</span>
                <span data-t="contact_support">Contact Support</span>
            </a>
        </div>
    </div>
</div>
