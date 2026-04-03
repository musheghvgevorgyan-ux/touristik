<?php use App\Helpers\View; ?>

<?php
$destination = $searchParams['destination'] ?? '';
$checkIn     = $searchParams['check_in'] ?? '';
$checkOut    = $searchParams['check_out'] ?? '';
$adults      = $searchParams['adults'] ?? 2;
$children    = $searchParams['children'] ?? 0;
?>

<style>
    .agent-portal { max-width: 1200px; margin: 0 auto; padding: 1.5rem; }
    .agent-search-header { margin-bottom: 1.5rem; }
    .agent-search-header h1 { font-size: 1.5rem; color: #1a2332; margin: 0 0 0.3rem; }
    .agent-search-header p { color: #6c757d; margin: 0; font-size: 0.9rem; }

    .agent-search-form { background: linear-gradient(135deg, #2c5364 0%, #203a43 50%, #0f2027 100%); border-radius: 12px; padding: 1.5rem 2rem; margin-bottom: 2rem; }
    .search-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto; gap: 1rem; align-items: end; }
    .search-field label { display: block; color: rgba(255,255,255,0.8); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.3rem; }
    .search-field input, .search-field select { width: 100%; padding: 0.6rem 0.8rem; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(255,255,255,0.95); font-size: 0.9rem; color: #1a2332; }
    .search-field input:focus, .search-field select:focus { outline: none; border-color: #fff; box-shadow: 0 0 0 3px rgba(255,255,255,0.2); }
    .btn-agent-search { padding: 0.6rem 1.5rem; background: #fff; color: #2c5364; border: none; border-radius: 6px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
    .btn-agent-search:hover { background: #e8ecef; }

    .search-results-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .results-count { font-size: 0.9rem; color: #6c757d; }

    .agent-hotel-list { display: flex; flex-direction: column; gap: 1rem; }
    .agent-hotel-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow: hidden; display: flex; transition: box-shadow 0.2s; }
    .agent-hotel-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
    .agent-hotel-image { width: 220px; min-height: 180px; flex-shrink: 0; position: relative; overflow: hidden; background: #e9ecef; }
    .agent-hotel-image img { width: 100%; height: 100%; object-fit: cover; }
    .agent-hotel-image-placeholder { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; font-size: 3rem; color: #adb5bd; }
    .agent-hotel-body { flex: 1; padding: 1.2rem; display: flex; flex-direction: column; justify-content: space-between; }
    .agent-hotel-top h4 { font-size: 1.1rem; color: #1a2332; margin: 0 0 0.3rem; }
    .agent-hotel-stars { color: #ffc107; font-size: 0.9rem; margin-bottom: 0.4rem; }
    .agent-hotel-detail { font-size: 0.85rem; color: #6c757d; display: inline-block; margin-right: 1rem; }
    .agent-hotel-promo { display: inline-block; background: #e8f5e9; color: #2e7d32; font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 4px; margin-top: 0.3rem; }

    .agent-hotel-pricing { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f0f0f0; }
    .price-net { text-align: left; }
    .price-net .price-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; }
    .price-net .price-amount { font-size: 1.4rem; font-weight: 700; color: #2c5364; }
    .price-net .price-per-night { font-size: 0.8rem; color: #6c757d; }
    .price-sell { text-align: center; }
    .price-sell .price-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; }
    .price-sell .price-amount { font-size: 1.1rem; font-weight: 600; color: #28a745; }
    .price-sell .commission-info { font-size: 0.75rem; color: #28a745; }
    .agent-hotel-actions { text-align: right; }
    .btn-book-client { display: inline-block; padding: 0.6rem 1.2rem; background: #2c5364; color: #fff; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s; }
    .btn-book-client:hover { background: #1e3a47; }

    .alert { padding: 1rem 1.2rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; }
    .alert.info { background: #e8f4fd; color: #0c5460; border: 1px solid #bee5eb; }

    @media (max-width: 768px) {
        .agent-portal { padding: 1rem; }
        .search-grid { grid-template-columns: 1fr 1fr; }
        .agent-hotel-card { flex-direction: column; }
        .agent-hotel-image { width: 100%; min-height: 160px; }
        .agent-hotel-pricing { flex-direction: column; gap: 0.8rem; align-items: flex-start; }
    }
</style>

<div class="agent-portal">
    <div class="agent-search-header">
        <h1>Hotel Search</h1>
        <p>Search hotels at NET supplier prices. Commission is calculated automatically based on your agency agreement.</p>
    </div>

    <form method="POST" action="/agent/search" class="agent-search-form">
        <?= View::csrf() ?>
        <div class="search-grid">
            <div class="search-field">
                <label for="destination">Destination</label>
                <input type="text" id="destination" name="destination" value="<?= View::e($destination) ?>" placeholder="City name (e.g. Dubai, Antalya)" required>
            </div>
            <div class="search-field">
                <label for="check_in">Check-in</label>
                <input type="date" id="check_in" name="check_in" value="<?= View::e($checkIn) ?>" min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="search-field">
                <label for="check_out">Check-out</label>
                <input type="date" id="check_out" name="check_out" value="<?= View::e($checkOut) ?>" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
            </div>
            <div class="search-field">
                <label for="adults">Adults</label>
                <select id="adults" name="adults">
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <option value="<?= $i ?>" <?= $adults == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="search-field">
                <label for="children">Children</label>
                <select id="children" name="children">
                    <?php for ($i = 0; $i <= 4; $i++): ?>
                        <option value="<?= $i ?>" <?= $children == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="search-field">
                <button type="submit" class="btn-agent-search">Search</button>
            </div>
        </div>
    </form>

    <?php if ($hotelError): ?>
        <div class="alert info"><?= View::e($hotelError) ?></div>
    <?php elseif (!empty($hotels)): ?>
        <div class="search-results-bar">
            <span class="results-count"><?= count($hotels) ?> hotel<?= count($hotels) !== 1 ? 's' : '' ?> found</span>
        </div>

        <div class="agent-hotel-list">
            <?php foreach ($hotels as $hotel): ?>
            <?php
                $netPrice     = (float)($hotel['net_price'] ?? $hotel['price'] ?? 0);
                $suggestedSell = (float)($hotel['suggested_sell'] ?? $netPrice);
                $commission    = (float)($hotel['commission'] ?? 0);
                $nights        = max((int)($hotel['nights'] ?? 1), 1);
                $currency      = $hotel['currency'] ?? 'EUR';
            ?>
            <div class="agent-hotel-card">
                <div class="agent-hotel-image">
                    <?php if (!empty($hotel['image'])): ?>
                        <img src="<?= View::e($hotel['image']) ?>" alt="<?= View::e($hotel['name'] ?? 'Hotel') ?>" loading="lazy"
                             onerror="this.parentElement.innerHTML='<div class=\'agent-hotel-image-placeholder\'>&#127960;</div>'">
                    <?php else: ?>
                        <div class="agent-hotel-image-placeholder">&#127960;</div>
                    <?php endif; ?>
                </div>
                <div class="agent-hotel-body">
                    <div class="agent-hotel-top">
                        <h4><?= View::e($hotel['name'] ?? 'Hotel') ?></h4>
                        <div class="agent-hotel-stars">
                            <?php
                                $starCount = min((int)substr($hotel['stars_num'] ?? '0', 0, 1), 5);
                                for ($s = 0; $s < $starCount; $s++) echo '&#11088;';
                            ?>
                        </div>
                        <?php if (!empty($hotel['room'])): ?>
                            <span class="agent-hotel-detail">&#128719; <?= View::e($hotel['room']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($hotel['board'])): ?>
                            <span class="agent-hotel-detail">&#127860; <?= View::e($hotel['board']) ?></span>
                        <?php endif; ?>
                        <span class="agent-hotel-detail">&#127769; <?= $nights ?> night<?= $nights > 1 ? 's' : '' ?></span>
                        <?php if (!empty($hotel['promotions'])): ?>
                            <?php foreach ($hotel['promotions'] as $promo): ?>
                                <span class="agent-hotel-promo">&#127873; <?= View::e($promo['name'] ?? '') ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="agent-hotel-pricing">
                        <div class="price-net">
                            <div class="price-label">NET Price (Your Cost)</div>
                            <div class="price-amount"><?= View::e($currency) ?> <?= number_format($netPrice, 2) ?></div>
                            <div class="price-per-night"><?= View::e($currency) ?> <?= number_format(round($netPrice / $nights, 2), 2) ?> / night</div>
                        </div>
                        <div class="price-sell">
                            <div class="price-label">Suggested Sell Price</div>
                            <div class="price-amount"><?= View::e($currency) ?> <?= number_format($suggestedSell, 2) ?></div>
                            <div class="commission-info">+<?= View::e($currency) ?> <?= number_format($commission, 2) ?> commission</div>
                        </div>
                        <div class="agent-hotel-actions">
                            <form method="POST" action="/booking/create/hotel/0?rate_key=<?= urlencode($hotel['rate_key'] ?? '') ?>&rate_type=<?= View::e($hotel['rate_type'] ?? 'BOOKABLE') ?>" style="display:inline">
                                <?= View::csrf() ?>
                                <input type="hidden" name="hotel_data" value="<?= View::e(json_encode($hotel)) ?>">
                                <button type="submit" class="btn-book-client">Book for Client &#8594;</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($destination): ?>
        <div class="alert info">No hotels found for this destination and dates. Try different criteria.</div>
    <?php endif; ?>
</div>
