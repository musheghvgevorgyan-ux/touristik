<div class="breadcrumb"><a href="<?= url('home') ?>" data-t="home_link">Home</a> &rsaquo; <span data-t="tour_cat_ingoing">Ingoing Tours</span></div>

<section class="tour-list-section">
    <h1 data-t="tour_cat_ingoing">Ingoing Tours</h1>
    <p class="section-subtitle tour-list-subtitle" data-t="ingoing_subtitle">Discover the beauty of Armenia with our curated tour packages</p>

    <div class="tour-list-layout">

        <aside class="tour-filters" id="tourFilters">
            <h3>Filters</h3>

            <div class="filter-group">
                <label>Search</label>
                <input type="text" id="filterSearch" placeholder="Search tours..." autocomplete="off">
            </div>

            <div class="filter-group">
                <label>Region</label>
                <select id="filterDestination">
                    <option value="">All</option>
                    <option value="yerevan">Yerevan</option>
                    <option value="cultural">Cultural</option>
                    <option value="nature">Nature</option>
                    <option value="adventure">Adventure</option>
                    <option value="gastronomy">Gastronomy</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Date</label>
                <input type="date" id="filterDate">
            </div>

            <div class="filter-group">
                <label>Price Range</label>
                <div class="filter-price-range">
                    <input type="range" id="filterPrice" min="0" max="1000" value="1000" step="50">
                    <div class="filter-price-labels">
                        <span>$0</span>
                        <span id="filterPriceValue">$1000</span>
                    </div>
                </div>
            </div>

            <button class="btn-filter-reset" id="filterReset">Reset Filters</button>
        </aside>

        <div class="tour-list-grid">

            <div class="tour-list-card reveal-scale" data-region="yerevan" data-start="2026-04-01" data-end="2026-10-31" data-price="199">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1668717096562-5a14dbf9454e?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128197;</span>
                        <span>April - October, 2026</span>
                    </div>
                    <h3>Classic Yerevan</h3>
                    <p>Republic Square, Cascade Complex, Matenadaran, Northern Avenue & vibrant nightlife</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$199</strong> /person</span>
                        <span class="tour-list-duration">3 Days / 2 Nights</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Inquire &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="cultural" data-start="2026-04-01" data-end="2026-11-30" data-price="349">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1695571803214-9e6820bffce7?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128197;</span>
                        <span>April - November, 2026</span>
                    </div>
                    <h3>Ancient Temples & Monasteries</h3>
                    <p>Garni Temple, Geghard Monastery, Tatev, Noravank & Khor Virap with views of Mount Ararat</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$349</strong> /person</span>
                        <span class="tour-list-duration">5 Days / 4 Nights</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Inquire &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="nature" data-start="2026-05-01" data-end="2026-10-31" data-price="599">
                <div class="tour-list-img lazy-bg" data-bg="https://plus.unsplash.com/premium_photo-1670552850982-abe0e83c4391?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128197;</span>
                        <span>May - October, 2026</span>
                    </div>
                    <h3>Grand Armenia Tour</h3>
                    <p>Lake Sevan, Dilijan, Jermuk, wine tasting in Areni & the Silk Road trails</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$599</strong> /person</span>
                        <span class="tour-list-duration">7 Days / 6 Nights</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Inquire &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="adventure" data-start="2026-05-15" data-end="2026-09-30" data-price="279">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1624357485917-fbb18b951125?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128197;</span>
                        <span>May - September, 2026</span>
                    </div>
                    <h3>Adventure & Hiking</h3>
                    <p>Dilijan National Park, Aragats summit, Lastiver caves & mountain trails</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$279</strong> /person</span>
                        <span class="tour-list-duration">4 Days / 3 Nights</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Inquire &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="gastronomy" data-start="2026-04-01" data-end="2026-11-30" data-price="449">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1743366500405-6689bb916fd2?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128197;</span>
                        <span>April - November, 2026</span>
                    </div>
                    <h3>Wine & Gastronomy Trail</h3>
                    <p>Areni winery, Armenian BBQ, lavash baking, brandy tasting at Ararat factory</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$449</strong> /person</span>
                        <span class="tour-list-duration">6 Days / 5 Nights</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Inquire &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="cultural" data-start="2026-04-01" data-end="2026-12-31" data-price="150">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1558185348-fe8fa4cf631f?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128197;</span>
                        <span>Year-round, 2026</span>
                    </div>
                    <h3>Echmiadzin & Zvartnots</h3>
                    <p>Mother Cathedral, Holy See, Zvartnots ruins & Armenian Apostolic Church history</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$150</strong> /person</span>
                        <span class="tour-list-duration">1 Day</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Inquire &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-empty" id="tourListEmpty" style="display:none;">
                <p>No tours match your filters.</p>
            </div>

        </div>

    </div>
</section>

<script>
(function() {
    var search = document.getElementById('filterSearch');
    var dest = document.getElementById('filterDestination');
    var dateFilter = document.getElementById('filterDate');
    var price = document.getElementById('filterPrice');
    var priceLabel = document.getElementById('filterPriceValue');
    var reset = document.getElementById('filterReset');
    var empty = document.getElementById('tourListEmpty');
    var cards = document.querySelectorAll('.tour-list-card');

    function filter() {
        var s = search.value.toLowerCase();
        var d = dest.value;
        var selectedDate = dateFilter.value;
        var maxPrice = parseInt(price.value);
        priceLabel.textContent = '$' + maxPrice.toLocaleString();
        var visible = 0;
        cards.forEach(function(card) {
            var show = true;
            if (s) {
                var text = card.textContent.toLowerCase();
                if (text.indexOf(s) === -1) show = false;
            }
            if (d && card.dataset.region !== d) show = false;
            if (selectedDate) {
                var start = card.dataset.start;
                var end = card.dataset.end;
                if (selectedDate < start || selectedDate > end) show = false;
            }
            if (parseInt(card.dataset.price) > maxPrice) show = false;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        empty.style.display = visible === 0 ? '' : 'none';
    }

    search.addEventListener('input', filter);
    dest.addEventListener('change', filter);
    dateFilter.addEventListener('change', filter);
    price.addEventListener('input', filter);
    reset.addEventListener('click', function() {
        search.value = ''; dest.value = ''; dateFilter.value = '';
        price.value = 1000; priceLabel.textContent = '$1,000';
        filter();
    });
})();
</script>
