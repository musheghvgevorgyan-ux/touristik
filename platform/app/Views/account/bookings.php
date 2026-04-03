<?php use App\Helpers\View; ?>

<style>
    .bookings-page { max-width: 1100px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .bookings-page h1 { font-size: 1.8rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .bookings-page > p { color: var(--text-secondary); margin-bottom: 2rem; }
    .bookings-list { display: flex; flex-direction: column; gap: 1rem; }
    .booking-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 1.5rem 2rem; display: grid; grid-template-columns: auto 1fr auto; gap: 1.5rem; align-items: center; transition: transform 0.2s; }
    .booking-card:hover { transform: translateY(-2px); }
    .booking-type-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
    .booking-type-icon.hotel { background: rgba(255,107,53,0.12); color: #FF6B35; }
    .booking-type-icon.tour { background: rgba(52,168,83,0.12); color: #34a853; }
    .booking-type-icon.transfer { background: rgba(66,133,244,0.12); color: #4285f4; }
    .booking-type-icon.flight { background: rgba(156,39,176,0.12); color: #9c27b0; }
    .booking-details h3 { font-size: 1.1rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .booking-details h3 a { color: var(--text-heading); text-decoration: none; }
    .booking-details h3 a:hover { color: var(--primary); }
    .booking-meta { display: flex; flex-wrap: wrap; gap: 1rem; color: var(--text-secondary); font-size: 0.85rem; }
    .booking-meta span { display: flex; align-items: center; gap: 0.3rem; }
    .booking-right { text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem; }
    .booking-right .price { font-size: 1.2rem; }
    .booking-right .status { display: inline-block; padding: 0.25rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .status-confirmed { background: rgba(52,168,83,0.12); color: #34a853; }
    .status-pending { background: rgba(255,152,0,0.12); color: #ff9800; }
    .status-cancelled { background: rgba(234,67,53,0.12); color: #ea4335; }
    .bookings-empty-state { text-align: center; padding: 4rem 0; }
    .bookings-empty-state .empty-icon { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }
    .bookings-empty-state h2 { color: var(--text-heading); font-size: 1.4rem; margin-bottom: 0.5rem; }
    .bookings-empty-state p { color: var(--text-secondary); margin-bottom: 1.5rem; max-width: 400px; margin-left: auto; margin-right: auto; }
    .bookings-empty-state .btn-explore { display: inline-block; padding: 0.8rem 2rem; background: var(--primary); color: #fff; text-decoration: none; border-radius: var(--radius); font-weight: 600; transition: background 0.2s; }
    .bookings-empty-state .btn-explore:hover { background: var(--primary-dark); }
    .bookings-back { margin-top: 2rem; }
    .bookings-back a { color: var(--primary); text-decoration: none; font-weight: 600; }
    .bookings-back a:hover { text-decoration: underline; }
    @media (max-width: 768px) {
        .booking-card { grid-template-columns: 1fr; gap: 1rem; }
        .booking-right { align-items: flex-start; flex-direction: row; gap: 1rem; }
        .booking-type-icon { display: none; }
    }
</style>

<div class="bookings-page">
    <h1 data-t="bookings_title">My Bookings</h1>
    <p data-t="bookings_subtitle">View and manage all your travel bookings</p>

    <?php if (!empty($bookings)): ?>
        <div class="bookings-list">
            <?php foreach ($bookings as $booking): ?>
                <?php
                    $type = $booking['product_type'] ?? 'hotel';
                    $status = $booking['status'] ?? 'pending';
                    $statusClass = match($status) {
                        'confirmed' => 'status-confirmed',
                        'cancelled' => 'status-cancelled',
                        default => 'status-pending',
                    };
                    $typeIcon = match($type) {
                        'tour' => '&#127759;',
                        'transfer' => '&#128663;',
                        'flight' => '&#9992;',
                        default => '&#127976;',
                    };
                    $typeClass = match($type) {
                        'tour' => 'tour',
                        'transfer' => 'transfer',
                        'flight' => 'flight',
                        default => 'hotel',
                    };
                ?>
                <div class="booking-card reveal">
                    <div class="booking-type-icon <?= $typeClass ?>"><?= $typeIcon ?></div>
                    <div class="booking-details">
                        <h3>
                            <a href="/account/bookings/<?= View::e($booking['id'] ?? '') ?>">
                                <?= View::e($booking['name'] ?? $booking['hotel_name'] ?? 'Booking') ?>
                            </a>
                        </h3>
                        <div class="booking-meta">
                            <span data-t="booking_ref">Ref: <?= View::e($booking['reference'] ?? '---') ?></span>
                            <span><?= View::e(ucfirst($type)) ?></span>
                            <?php if (!empty($booking['check_in'])): ?>
                                <span><?= View::date($booking['check_in']) ?><?= !empty($booking['check_out']) ? ' - ' . View::date($booking['check_out']) : '' ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="booking-right">
                        <span class="status <?= $statusClass ?>"><?= View::e(ucfirst($status)) ?></span>
                        <?php if (isset($booking['total_price'])): ?>
                            <span class="price"><?= View::price($booking['total_price'], $booking['currency'] ?? 'USD') ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bookings-empty-state reveal">
            <div class="empty-icon">&#128218;</div>
            <h2 data-t="no_bookings_title">No Bookings Yet</h2>
            <p data-t="no_bookings_text">You haven't made any bookings yet. Start exploring our hotels, tours, and destinations to plan your next adventure.</p>
            <a href="/hotels/search" class="btn-explore" data-t="search_hotels">Search Hotels</a>
        </div>
    <?php endif; ?>

    <div class="bookings-back reveal">
        <a href="/account" data-t="back_to_dashboard">&larr; Back to Dashboard</a>
    </div>
</div>
