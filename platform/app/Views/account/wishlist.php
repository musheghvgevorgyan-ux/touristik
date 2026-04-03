<?php use App\Helpers\View; ?>

<style>
    .wishlist-page { max-width: 1100px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .wishlist-page h1 { font-size: 1.8rem; color: var(--text-heading); margin-bottom: 0.3rem; }
    .wishlist-page > p { color: var(--text-secondary); margin-bottom: 2rem; }
    .wishlist-count { font-size: 0.95rem; color: var(--text-secondary); margin-bottom: 1.5rem; }
    .wishlist-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
    .wishlist-card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; display: flex; flex-direction: column; }
    .wishlist-card:hover { transform: translateY(-3px); box-shadow: var(--shadow), 0 8px 24px rgba(0,0,0,0.08); }
    .wishlist-card-img { position: relative; width: 100%; height: 200px; overflow: hidden; background: var(--bg-body); }
    .wishlist-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
    .wishlist-card:hover .wishlist-card-img img { transform: scale(1.05); }
    .wishlist-card-img .type-badge { position: absolute; top: 12px; left: 12px; padding: 0.2rem 0.7rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .type-badge.hotel { background: rgba(255,107,53,0.9); color: #fff; }
    .type-badge.tour { background: rgba(52,168,83,0.9); color: #fff; }
    .type-badge.destination { background: rgba(66,133,244,0.9); color: #fff; }
    .type-badge.transfer { background: rgba(156,39,176,0.9); color: #fff; }
    .type-badge.package { background: rgba(255,152,0,0.9); color: #fff; }
    .wishlist-card-body { padding: 1.2rem 1.4rem; flex: 1; display: flex; flex-direction: column; }
    .wishlist-card-body h3 { font-size: 1.1rem; color: var(--text-heading); margin-bottom: 0.4rem; line-height: 1.3; }
    .wishlist-card-body .wishlist-meta { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.8rem; display: flex; align-items: center; gap: 0.4rem; }
    .wishlist-card-body .wishlist-price { font-size: 1.15rem; font-weight: 700; color: var(--primary); margin-bottom: 1rem; }
    .wishlist-card-body .wishlist-price .price-label { font-size: 0.8rem; font-weight: 400; color: var(--text-secondary); }
    .wishlist-card-actions { display: flex; gap: 0.8rem; margin-top: auto; }
    .wishlist-card-actions a,
    .wishlist-card-actions button { flex: 1; padding: 0.6rem 1rem; border-radius: var(--radius); font-size: 0.9rem; font-weight: 600; text-align: center; text-decoration: none; cursor: pointer; transition: background 0.2s, color 0.2s; border: none; }
    .btn-view { background: var(--primary); color: #fff; }
    .btn-view:hover { background: var(--primary-dark); }
    .btn-remove { background: transparent; border: 1px solid var(--border-color) !important; color: var(--text-secondary); }
    .btn-remove:hover { background: rgba(234,67,53,0.08); color: #ea4335; border-color: #ea4335 !important; }
    .wishlist-card-img .no-image { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; font-size: 3rem; color: var(--text-secondary); opacity: 0.3; background: var(--bg-body); }
    .wishlist-empty { text-align: center; padding: 5rem 1rem; }
    .wishlist-empty .empty-icon { font-size: 4rem; margin-bottom: 1rem; opacity: 0.4; }
    .wishlist-empty h2 { color: var(--text-heading); font-size: 1.4rem; margin-bottom: 0.5rem; }
    .wishlist-empty p { color: var(--text-secondary); margin-bottom: 1.5rem; max-width: 400px; margin-left: auto; margin-right: auto; }
    .wishlist-empty .btn-explore { display: inline-block; padding: 0.8rem 2rem; background: var(--primary); color: #fff; text-decoration: none; border-radius: var(--radius); font-weight: 600; transition: background 0.2s; }
    .wishlist-empty .btn-explore:hover { background: var(--primary-dark); }
    .wishlist-back { margin-top: 2rem; }
    .wishlist-back a { color: var(--primary); text-decoration: none; font-weight: 600; }
    .wishlist-back a:hover { text-decoration: underline; }
    @media (max-width: 600px) {
        .wishlist-grid { grid-template-columns: 1fr; }
        .wishlist-card-img { height: 180px; }
    }
</style>

<div class="wishlist-page">
    <h1 data-t="wishlist_title">My Wishlist</h1>
    <p data-t="wishlist_subtitle">Save your favorite hotels, tours, and destinations for later</p>

    <?php if (!empty($wishlists)): ?>
        <div class="wishlist-count">
            <?= count($wishlists) ?> item<?= count($wishlists) !== 1 ? 's' : '' ?> saved
        </div>

        <div class="wishlist-grid">
            <?php foreach ($wishlists as $item): ?>
                <?php
                    $data     = $item['item_data'] ?? [];
                    $name     = $data['name'] ?? 'Saved Item';
                    $image    = $data['image'] ?? '';
                    $price    = $data['price'] ?? null;
                    $currency = $data['currency'] ?? 'USD';
                    $location = $data['location'] ?? $data['destination'] ?? '';
                    $type     = $item['item_type'] ?? 'hotel';
                    $itemId   = $item['item_id'] ?? '';
                    $savedAt  = $item['created_at'] ?? '';

                    // Build the "View" link based on item type
                    $viewUrl = match ($type) {
                        'hotel'       => '/hotels/search?id=' . urlencode($itemId),
                        'tour'        => '/tours/' . urlencode($itemId),
                        'destination' => '/destinations/' . urlencode($itemId),
                        'transfer'    => '/transfers/' . urlencode($itemId),
                        default       => '#',
                    };

                    $typeIcon = match ($type) {
                        'tour'        => '&#127759;',
                        'transfer'    => '&#128663;',
                        'destination' => '&#128205;',
                        'package'     => '&#127873;',
                        default       => '&#127976;',
                    };
                ?>
                <div class="wishlist-card reveal">
                    <div class="wishlist-card-img">
                        <?php if ($image): ?>
                            <img src="<?= View::e($image) ?>" alt="<?= View::e($name) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="no-image"><?= $typeIcon ?></div>
                        <?php endif; ?>
                        <span class="type-badge <?= View::e($type) ?>"><?= View::e(ucfirst($type)) ?></span>
                    </div>
                    <div class="wishlist-card-body">
                        <h3><?= View::e($name) ?></h3>
                        <?php if ($location): ?>
                            <div class="wishlist-meta">
                                <span>&#128205;</span>
                                <span><?= View::e($location) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($price !== null): ?>
                            <div class="wishlist-price">
                                <?= View::price((float) $price, $currency) ?>
                                <span class="price-label"> / night</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($savedAt): ?>
                            <div class="wishlist-meta">Saved <?= View::date($savedAt, 'M j, Y') ?></div>
                        <?php endif; ?>
                        <div class="wishlist-card-actions">
                            <a href="<?= View::e($viewUrl) ?>" class="btn-view" data-t="view_details">View</a>
                            <form method="POST" action="/account/wishlist" style="flex:1;display:flex;">
                                <?= View::csrf() ?>
                                <input type="hidden" name="item_type" value="<?= View::e($type) ?>">
                                <input type="hidden" name="item_id" value="<?= View::e($itemId) ?>">
                                <button type="submit" class="btn-remove" style="width:100%;" data-t="remove_item">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="wishlist-empty reveal">
            <div class="empty-icon">&#10084;</div>
            <h2 data-t="wishlist_empty_title">Your Wishlist is Empty</h2>
            <p data-t="wishlist_empty_text">Start exploring and save your favorite hotels, tours, and destinations to plan your perfect trip.</p>
            <a href="/hotels/search" class="btn-explore" data-t="search_hotels">Search Hotels</a>
        </div>
    <?php endif; ?>

    <div class="wishlist-back reveal">
        <a href="/account" data-t="back_to_dashboard">&larr; Back to Dashboard</a>
    </div>
</div>
