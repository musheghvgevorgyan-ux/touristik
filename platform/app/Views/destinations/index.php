<?php use App\Helpers\View; ?>

<style>
    .destinations-page { max-width: 1200px; margin: 0 auto; padding: 2rem 2rem 4rem; }
    .destinations-page .section-header { text-align: center; margin-bottom: 2.5rem; }
    .destinations-page .section-header h1 { font-size: 2.2rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .destinations-page .section-header p { color: var(--text-secondary); font-size: 1.1rem; }
    .destinations-page .card-image { position: relative; }
    .destinations-page .card-body h3 a { color: var(--text-heading); text-decoration: none; }
    .destinations-page .card-body h3 a:hover { color: var(--primary); }
    .destinations-page .dest-country { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.3rem; }
    .destinations-page .dest-desc { color: var(--text-secondary); font-size: 0.95rem; line-height: 1.5; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .destinations-page .card-footer { display: flex; justify-content: space-between; align-items: center; }
    .destinations-page .btn-explore { display: inline-block; padding: 0.5rem 1.2rem; background: var(--primary); color: #fff; text-decoration: none; border-radius: var(--radius); font-weight: 600; font-size: 0.9rem; transition: background 0.2s; }
    .destinations-page .btn-explore:hover { background: var(--primary-dark); }
    .destinations-page .empty-state { text-align: center; padding: 4rem 0; color: var(--text-secondary); }
    .destinations-page .empty-state h2 { color: var(--text-heading); margin-bottom: 0.5rem; }
</style>

<div class="destinations-page">
    <div class="section-header reveal">
        <h1 data-t="destinations_title">Popular Destinations</h1>
        <p data-t="destinations_subtitle">Explore the world's most breathtaking places with Touristik</p>
    </div>

    <?php if (!empty($destinations)): ?>
        <div class="card-grid">
            <?php foreach ($destinations as $i => $dest): ?>
                <div class="card reveal" style="transition-delay: <?= $i * 0.1 ?>s">
                    <div class="card-image lazy-bg" data-bg="<?= View::e($dest['image'] ?? '') ?>">
                    </div>
                    <div class="card-body">
                        <span class="dest-country"><?= View::e($dest['country'] ?? '') ?></span>
                        <h3><a href="/destinations/<?= View::e($dest['slug'] ?? '') ?>"><?= View::e($dest['name'] ?? '') ?></a></h3>
                        <p class="dest-desc"><?= View::e($dest['description'] ?? '') ?></p>
                        <div class="card-footer">
                            <span class="price" data-t="from_price">From <?= View::price($dest['price_from'] ?? 0) ?></span>
                            <a href="/destinations/<?= View::e($dest['slug'] ?? '') ?>" class="btn-explore" data-t="explore">Explore</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state reveal">
            <h2 data-t="no_destinations_title">Coming Soon</h2>
            <p data-t="no_destinations_text">We're curating amazing destinations for you. Check back soon!</p>
        </div>
    <?php endif; ?>
</div>
