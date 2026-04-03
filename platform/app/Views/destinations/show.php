<?php use App\Helpers\View; ?>

<style>
    .dest-hero { position: relative; height: 400px; display: flex; align-items: flex-end; overflow: hidden; }
    .dest-hero .dest-hero-bg { position: absolute; inset: 0; background-size: cover; background-position: center; }
    .dest-hero .dest-hero-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.1) 60%); }
    .dest-hero .dest-hero-content { position: relative; z-index: 2; padding: 2rem 2rem 2.5rem; max-width: 1200px; margin: 0 auto; width: 100%; }
    .dest-hero .dest-hero-content h1 { font-size: 2.5rem; color: #fff; margin-bottom: 0.3rem; text-shadow: 0 2px 8px rgba(0,0,0,0.3); }
    .dest-hero .dest-hero-content .dest-country { font-size: 1.1rem; color: rgba(255,255,255,0.85); }
    .dest-detail { max-width: 900px; margin: 0 auto; padding: 2.5rem 2rem 4rem; }
    .dest-detail .dest-description { font-size: 1.05rem; line-height: 1.8; color: var(--text-secondary); margin-bottom: 2rem; }
    .dest-detail .detail-price { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 1.5rem 2rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; box-shadow: var(--shadow); }
    .dest-detail .detail-price .price { font-size: 1.8rem; }
    .dest-detail .detail-price .price-label { font-size: 0.85rem; color: var(--text-secondary); display: block; margin-bottom: 0.2rem; }
    .dest-actions { display: flex; gap: 1rem; flex-wrap: wrap; }
    .dest-actions .btn-book { display: inline-block; padding: 0.9rem 2.5rem; background: var(--primary); color: #fff; text-decoration: none; border-radius: var(--radius); font-weight: 600; font-size: 1rem; transition: background 0.2s; border: none; cursor: pointer; }
    .dest-actions .btn-book:hover { background: var(--primary-dark); }
    .dest-actions .btn-back { display: inline-block; padding: 0.9rem 2.5rem; background: transparent; color: var(--text-heading); text-decoration: none; border-radius: var(--radius); font-weight: 600; font-size: 1rem; border: 2px solid var(--border-color); transition: background 0.2s, border-color 0.2s; }
    .dest-actions .btn-back:hover { border-color: var(--primary); color: var(--primary); }
    @media (max-width: 768px) {
        .dest-hero { height: 280px; }
        .dest-hero .dest-hero-content h1 { font-size: 1.8rem; }
        .dest-detail .detail-price { flex-direction: column; gap: 1rem; text-align: center; }
        .dest-actions { justify-content: center; }
    }
</style>

<div class="dest-hero">
    <div class="dest-hero-bg lazy-bg" data-bg="<?= View::e($destination['image'] ?? '') ?>"></div>
    <div class="dest-hero-overlay"></div>
    <div class="dest-hero-content reveal">
        <h1><?= View::e($destination['name'] ?? '') ?></h1>
        <span class="dest-country"><?= View::e($destination['country'] ?? '') ?></span>
    </div>
</div>

<div class="dest-detail">
    <div class="dest-description reveal">
        <?= nl2br(View::e($destination['description'] ?? '')) ?>
    </div>

    <?php if (!empty($destination['price_from'])): ?>
        <div class="detail-price reveal">
            <div>
                <span class="price-label" data-t="starting_from">Starting from</span>
                <span class="price"><?= View::price($destination['price_from']) ?></span>
                <span class="price-label" data-t="per_person">per person</span>
            </div>
            <div class="dest-actions">
                <a href="/contact" class="btn-book" data-t="book_now">Book Now</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="dest-actions reveal" style="margin-top: 1.5rem;">
        <a href="/destinations" class="btn-back" data-t="back_to_destinations">&larr; Back to Destinations</a>
    </div>
</div>
