<?php
$destinations = getDestinationsWithLivePrices($pdo);
?>

<nav class="breadcrumbs" aria-label="Breadcrumb">
    <a href="<?= url('home') ?>" data-t="breadcrumb_home">Home</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <span class="breadcrumb-current" data-t="all_destinations">All Destinations</span>
</nav>

<section class="destinations">
    <h2 data-t="all_destinations">All Destinations</h2>
    <div class="card-grid">
        <?php foreach ($destinations as $dest): ?>
        <div class="card">
            <a href="<?= url('destination', ['id' => $dest['id']]) ?>">
                <?php if (!empty($dest['image_url'])): ?>
                <div class="card-image lazy-bg" data-bg="<?= htmlspecialchars($dest['image_url']) ?>"></div>
                <?php else: ?>
                <div class="card-image" style="background-color: <?= htmlspecialchars($dest['color']) ?>;">
                    <span class="card-emoji"><?= htmlspecialchars($dest['emoji']) ?></span>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <h3 data-t="dest_name_<?= $dest['id'] ?>"><?= htmlspecialchars($dest['name']) ?></h3>
                    <p data-t="dest_desc_<?= $dest['id'] ?>"><?= htmlspecialchars($dest['description']) ?></p>
                    <span class="price" data-base-price="<?= $dest['price'] ?>">From $<?= number_format($dest['price'], 0) ?></span>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="recently-viewed" id="recentlyViewed" style="display:none;">
        <h3 data-t="recently_viewed">&#128065; Recently Viewed</h3>
        <div class="rv-strip" id="rvStrip"></div>
    </div>
    <?php if (empty($destinations)): ?>
        <p style="text-align:center; color:#666; margin-top:2rem;" data-t="no_destinations">No destinations available yet.</p>
    <?php endif; ?>
</section>
