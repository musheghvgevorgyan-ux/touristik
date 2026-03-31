<nav class="breadcrumbs" aria-label="Breadcrumb">
    <a href="<?= url('home') ?>" data-t="breadcrumb_home">Home</a>
    <span class="breadcrumb-sep">&#8250;</span>
    <span class="breadcrumb-current" data-t="tours_title">Tours</span>
</nav>

<section class="tours-page">
    <h2 data-t="tours_title">Tours</h2>
    <p class="section-subtitle tours-subtitle" data-t="tours_page_subtitle">Discover Armenia, explore the world, or book a comfortable transfer</p>

    <div class="tours-grid">
        <a href="<?= url('ingoing-tours') ?>" class="tour-category-card reveal-scale">
            <div class="tour-category-img lazy-bg" data-bg="https://images.unsplash.com/photo-1668717096562-5a14dbf9454e?w=600&q=80&fm=webp"></div>
            <div class="tour-category-body">
                <h3 data-t="tour_cat_ingoing">Ingoing Tours</h3>
                <p data-t="tours_ingoing_desc">Explore the beauty of Armenia with our guided tours — from ancient temples and monasteries to scenic hikes and wine trails.</p>
            </div>
        </a>

        <a href="<?= url('outgoing-tours') ?>" class="tour-category-card reveal-scale">
            <div class="tour-category-img lazy-bg" data-bg="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80&fm=webp"></div>
            <div class="tour-category-body">
                <h3 data-t="tour_cat_outgoing">Outgoing Tours</h3>
                <p data-t="tours_outgoing_desc">Travel the world with Touristik — curated packages to Europe, USA, Asia and beyond with flights and accommodation included.</p>
            </div>
        </a>

        <a href="<?= url('transfer') ?>" class="tour-category-card reveal-scale">
            <div class="tour-category-img lazy-bg" data-bg="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=600&q=80&fm=webp"></div>
            <div class="tour-category-body">
                <h3 data-t="tour_cat_transfer">Transfer</h3>
                <p data-t="tours_transfer_desc">Comfortable airport pickups, VIP transfers, and day-trip transportation across Armenia at fixed prices.</p>
            </div>
        </a>
    </div>

    <div class="tours-featured">
        <h3 data-t="tours_featured">Featured Tours</h3>
        <div class="featured-tours-grid">
            <div class="tour-list-card reveal-scale">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1668717096562-5a14dbf9454e?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates"><span>&#128197; Apr – Oct 2026</span></div>
                    <h3 data-t="tour_classic_yerevan">Classic Yerevan City Tour</h3>
                    <p data-t="tour_classic_yerevan_desc">Walk through the Pink City — Republic Square, Cascade Complex, Vernissage market, Matenadaran, and the brandy factory.</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price" data-t="tours_from">From <strong>$199</strong> /person</span>
                        <span class="tour-list-duration">3 Days / 2 Nights</span>
                        <a href="<?= url('ingoing-tours') ?>" class="btn btn-sm btn-tour-list" data-t="tours_view_all">View All &#10132;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1533105079780-92b9be482077?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates"><span>&#128197; 18 – 25 Aug 2026</span></div>
                    <h3 data-t="tour_greece">Greece</h3>
                    <p data-t="tour_greece_desc">Athens, Santorini and Mykonos — ancient ruins, blue-domed churches, crystal waters and unforgettable sunsets.</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price" data-t="tours_from">From <strong>$1,400</strong> /person</span>
                        <span class="tour-list-duration">8 Days / 7 Nights</span>
                        <a href="<?= url('outgoing-tours') ?>" class="btn btn-sm btn-tour-list" data-t="tours_view_all">View All &#10132;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates"><span>&#128663; ~30 min</span></div>
                    <h3 data-t="tour_airport_transfer">Airport Transfer</h3>
                    <p data-t="tour_airport_desc">Comfortable pickup from Zvartnots Airport to anywhere in Yerevan. Available 24/7 with meet & greet service.</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price" data-t="tours_from">From <strong>$25</strong> /car</span>
                        <span class="tour-list-duration">24/7</span>
                        <a href="<?= url('transfer') ?>" class="btn btn-sm btn-tour-list" data-t="tours_view_all">View All &#10132;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
