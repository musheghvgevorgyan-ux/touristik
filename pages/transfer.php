<div class="breadcrumb"><a href="<?= url('home') ?>" data-t="home_link">Home</a> &rsaquo; <span data-t="tour_cat_transfer">Transfer</span></div>

<section class="tour-list-section">
    <h1 data-t="tour_cat_transfer">Transfer</h1>
    <p class="section-subtitle tour-list-subtitle" data-t="transfer_subtitle">Comfortable and reliable transfer services across Armenia</p>

    <div class="tour-list-layout">

        <aside class="tour-filters" id="tourFilters">
            <h3>Filters</h3>

            <div class="filter-group">
                <label>Search</label>
                <input type="text" id="filterSearch" placeholder="Search transfers..." autocomplete="off">
            </div>

            <div class="filter-group">
                <label>Type</label>
                <select id="filterDestination">
                    <option value="">All</option>
                    <option value="airport">Airport</option>
                    <option value="city">City Transfer</option>
                    <option value="tour">Tour Transfer</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Price Range</label>
                <div class="filter-price-range">
                    <input type="range" id="filterPrice" min="0" max="300" value="300" step="10">
                    <div class="filter-price-labels">
                        <span>$0</span>
                        <span id="filterPriceValue">$300</span>
                    </div>
                </div>
            </div>

            <button class="btn-filter-reset" id="filterReset">Reset Filters</button>
        </aside>

        <div class="tour-list-grid">

            <div class="tour-list-card reveal-scale" data-region="airport" data-price="25">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128652;</span>
                        <span>Available 24/7</span>
                    </div>
                    <h3>Airport - Yerevan</h3>
                    <p>Zvartnots International Airport (EVN) to any location in Yerevan. Sedan or minivan.</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$25</strong> /car</span>
                        <span class="tour-list-duration">~30 min</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Book &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="airport" data-price="45">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1570125909232-eb263c188f7e?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128652;</span>
                        <span>Available 24/7</span>
                    </div>
                    <h3>Airport - Yerevan (VIP)</h3>
                    <p>Premium vehicle, meet & greet at arrivals, complimentary water. Mercedes or BMW.</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$45</strong> /car</span>
                        <span class="tour-list-duration">~30 min</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Book &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="tour" data-price="80">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1695571803214-9e6820bffce7?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128652;</span>
                        <span>Daily departures</span>
                    </div>
                    <h3>Yerevan - Garni - Geghard</h3>
                    <p>Round trip transfer to Garni Temple and Geghard Monastery with waiting time included</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$80</strong> /car</span>
                        <span class="tour-list-duration">~4 hours</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Book &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="tour" data-price="120">
                <div class="tour-list-img lazy-bg" data-bg="https://plus.unsplash.com/premium_photo-1670552850982-abe0e83c4391?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128652;</span>
                        <span>Daily departures</span>
                    </div>
                    <h3>Yerevan - Lake Sevan</h3>
                    <p>Round trip transfer to Lake Sevan, Sevanavank Monastery with stops along the way</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$120</strong> /car</span>
                        <span class="tour-list-duration">~6 hours</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Book &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="city" data-price="15">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1668717096562-5a14dbf9454e?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128652;</span>
                        <span>Available 24/7</span>
                    </div>
                    <h3>Yerevan City Transfer</h3>
                    <p>Point-to-point transfer within Yerevan. Hotels, restaurants, malls, any address.</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$15</strong> /car</span>
                        <span class="tour-list-duration">~20 min</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Book &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="tour" data-price="250">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1624357485917-fbb18b951125?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128652;</span>
                        <span>Daily departures</span>
                    </div>
                    <h3>Yerevan - Tatev (Wings of Tatev)</h3>
                    <p>Full day transfer to Tatev Monastery via world's longest cable car, Khor Virap stop</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$250</strong> /car</span>
                        <span class="tour-list-duration">~12 hours</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Book &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-empty" id="tourListEmpty" style="display:none;">
                <p>No transfers match your filters.</p>
            </div>

        </div>

    </div>
</section>

<script>
(function() {
    var search = document.getElementById('filterSearch');
    var dest = document.getElementById('filterDestination');
    var price = document.getElementById('filterPrice');
    var priceLabel = document.getElementById('filterPriceValue');
    var reset = document.getElementById('filterReset');
    var empty = document.getElementById('tourListEmpty');
    var cards = document.querySelectorAll('.tour-list-card');

    function filter() {
        var s = search.value.toLowerCase();
        var d = dest.value;
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
            if (parseInt(card.dataset.price) > maxPrice) show = false;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        empty.style.display = visible === 0 ? '' : 'none';
    }

    search.addEventListener('input', filter);
    dest.addEventListener('change', filter);
    price.addEventListener('input', filter);
    reset.addEventListener('click', function() {
        search.value = ''; dest.value = '';
        price.value = 300; priceLabel.textContent = '$300';
        filter();
    });
})();
</script>
