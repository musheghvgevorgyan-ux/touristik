<div class="breadcrumb"><a href="<?= url('home') ?>" data-t="home_link">Home</a> &rsaquo; <span data-t="tour_cat_outgoing">Outgoing Tours</span></div>

<section class="tour-list-section">
    <h1 data-t="tour_cat_outgoing">Outgoing Tours</h1>
    <p class="section-subtitle tour-list-subtitle" data-t="outgoing_subtitle">Upcoming group tours from Armenia</p>

    <div class="tour-list-layout">

        <aside class="tour-filters" id="tourFilters">
            <h3>Filters</h3>

            <div class="filter-group">
                <label>Search</label>
                <input type="text" id="filterSearch" placeholder="Search tours..." autocomplete="off">
            </div>

            <div class="filter-group">
                <label>Destination</label>
                <select id="filterDestination">
                    <option value="">All</option>
                    <option value="usa">USA</option>
                    <option value="europe">Europe</option>
                    <option value="asia">Asia</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Date</label>
                <input type="date" id="filterDate">
            </div>

            <div class="filter-group">
                <label>Price Range</label>
                <div class="filter-price-range">
                    <input type="range" id="filterPrice" min="0" max="3000" value="3000" step="100">
                    <div class="filter-price-labels">
                        <span>$0</span>
                        <span id="filterPriceValue">$3000</span>
                    </div>
                </div>
            </div>

            <button class="btn-filter-reset" id="filterReset">Reset Filters</button>
        </aside>

        <div class="tour-list-grid">

            <div class="tour-list-card reveal-scale" data-region="usa" data-start="2026-07-08" data-end="2026-07-15" data-price="1850">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1449034446853-66c86144b0ad?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128197;</span>
                        <span>8 - 15 July, 2026</span>
                    </div>
                    <h3>California</h3>
                    <p>Los Angeles, San Francisco, Hollywood, Santa Monica & more</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$1,850</strong> /person</span>
                        <span class="tour-list-duration">8 Days / 7 Nights</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Inquire &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="usa" data-start="2026-07-26" data-end="2026-07-31" data-price="1600">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1534430480872-3498386e7856?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128197;</span>
                        <span>26 - 31 July, 2026</span>
                    </div>
                    <h3>Chicago - New York</h3>
                    <p>Statue of Liberty, Times Square, Millennium Park, Broadway & more</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$1,600</strong> /person</span>
                        <span class="tour-list-duration">6 Days / 5 Nights</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Inquire &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="asia" data-start="2026-08-05" data-end="2026-08-14" data-price="2200">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1508804185872-d7badad00f7d?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128197;</span>
                        <span>5 - 14 August, 2026</span>
                    </div>
                    <h3>China</h3>
                    <p>Beijing, Great Wall, Shanghai, Terracotta Army, Forbidden City & more</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$2,200</strong> /person</span>
                        <span class="tour-list-duration">10 Days / 9 Nights</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Inquire &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="europe" data-start="2026-08-18" data-end="2026-08-25" data-price="1400">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1533105079780-92b9be482077?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128197;</span>
                        <span>18 - 25 August, 2026</span>
                    </div>
                    <h3>Greece</h3>
                    <p>Athens, Santorini, Acropolis, Mykonos, Meteora & Aegean cruise</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$1,400</strong> /person</span>
                        <span class="tour-list-duration">8 Days / 7 Nights</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Inquire &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="europe" data-start="2026-09-01" data-end="2026-09-08" data-price="1100">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1558642452-9d2a7deb7f62?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128197;</span>
                        <span>1 - 8 September, 2026</span>
                    </div>
                    <h3>Cyprus</h3>
                    <p>Limassol, Paphos, Ayia Napa, Troodos Mountains & Mediterranean beaches</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$1,100</strong> /person</span>
                        <span class="tour-list-duration">8 Days / 7 Nights</span>
                        <a href="<?= url('contact') ?>" class="btn btn-sm btn-tour-list">Inquire &#8594;</a>
                    </div>
                </div>
            </div>

            <div class="tour-list-card reveal-scale" data-region="europe" data-start="2026-09-15" data-end="2026-09-23" data-price="1500">
                <div class="tour-list-img lazy-bg" data-bg="https://images.unsplash.com/photo-1543783207-ec64e4d95325?w=600&q=80&fm=webp"></div>
                <div class="tour-list-body">
                    <div class="tour-list-dates">
                        <span class="tour-list-date-icon">&#128197;</span>
                        <span>15 - 23 September, 2026</span>
                    </div>
                    <h3>Spain</h3>
                    <p>Barcelona, Madrid, Sagrada Familia, Alhambra, Flamenco & tapas tour</p>
                    <div class="tour-list-footer">
                        <span class="tour-list-price">From <strong>$1,500</strong> /person</span>
                        <span class="tour-list-duration">9 Days / 8 Nights</span>
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
        price.value = 3000; priceLabel.textContent = '$3,000';
        filter();
    });
})();
</script>
