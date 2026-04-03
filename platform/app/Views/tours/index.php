<?php use App\Helpers\View; ?>

<div class="tours-page">
    <h2 class="reveal" data-t="tours_title">Explore Our Tours</h2>
    <p class="section-subtitle reveal" data-t="tours_subtitle">Choose your adventure - from discovering Armenia to exploring the world</p>

    <div class="tours-grid">
        <a href="/tours/ingoing" class="tour-category-card reveal">
            <div class="tour-category-img lazy-bg" data-bg="https://images.unsplash.com/photo-1695571803214-9e6820bffce7?w=600&q=80"></div>
            <div class="tour-category-body">
                <h3 data-t="tour_cat_ingoing">Ingoing Tours</h3>
                <p data-t="tour_cat_ingoing_desc">Discover Armenia's ancient monasteries, stunning landscapes, and rich culture</p>
            </div>
        </a>

        <a href="/tours/outgoing" class="tour-category-card reveal">
            <div class="tour-category-img lazy-bg" data-bg="https://images.unsplash.com/photo-1530841377377-3ff06c0ca713?w=600&q=80"></div>
            <div class="tour-category-body">
                <h3 data-t="tour_cat_outgoing">Outgoing Tours</h3>
                <p data-t="tour_cat_outgoing_desc">Explore the world's most popular destinations with our curated packages</p>
            </div>
        </a>

        <a href="/tours/transfer" class="tour-category-card reveal">
            <div class="tour-category-img lazy-bg" data-bg="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&q=80"></div>
            <div class="tour-category-body">
                <h3 data-t="tour_cat_transfer">Transfer Services</h3>
                <p data-t="tour_cat_transfer_desc">Airport pickups, city transfers, and intercity transportation</p>
            </div>
        </a>
    </div>
</div>
