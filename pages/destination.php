<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$dest = getDestination($pdo, $id);

if (!$dest) {
    header('Location: ' . url('404'));
    exit;
}

// Get live flight price
$livePrice = getDestinationFlightPrice($dest['name']);
if ($livePrice !== null) {
    $dest['price'] = $livePrice;
}
?>

<section class="destination-detail">
    <?php if (!empty($dest['image_url'])): ?>
    <div class="detail-hero" style="background-image: url('<?= htmlspecialchars($dest['image_url']) ?>');"></div>
    <?php else: ?>
    <div class="detail-hero" style="background-color: <?= htmlspecialchars($dest['color']) ?>;">
        <span class="detail-emoji"><?= htmlspecialchars($dest['emoji']) ?></span>
    </div>
    <?php endif; ?>
    <div class="detail-content">
        <h1 data-t="dest_name_<?= $dest['id'] ?>"><?= htmlspecialchars($dest['name']) ?></h1>
        <p class="detail-description" data-t="dest_desc_<?= $dest['id'] ?>"><?= htmlspecialchars($dest['description']) ?></p>
        <div class="detail-price">
            <span class="price" data-base-price="<?= $dest['price'] ?>">From $<?= number_format($dest['price'], 0) ?></span>
        </div>
        <?php
            $destCity = trim(explode(',', $dest['name'])[0]);
        ?>
        <button class="btn book-trigger" data-t="book_now">Book Now</button>
        <a href="<?= url('destinations') ?>" class="btn btn-outline" data-t="back_dest">Back to Destinations</a>
    </div>
</section>
