<?php
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'destinations';
$adminMessage = '';

// Handle destination actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf()) {
    if (isset($_POST['add_destination'])) {
        $name = htmlspecialchars(trim($_POST['name']));
        $description = htmlspecialchars(trim($_POST['description']));
        $price = floatval($_POST['price']);
        $color = htmlspecialchars(trim($_POST['color']));
        $emoji = htmlspecialchars(trim($_POST['emoji']));

        if ($name && $description && $price > 0) {
            addDestination($pdo, $name, $description, $price, $color, $emoji);
            $adminMessage = '<div class="alert success" data-t="dest_added">Destination added successfully.</div>';
        } else {
            $adminMessage = '<div class="alert error" data-t="fill_fields">Please fill in all fields correctly.</div>';
        }
    }

    if (isset($_POST['delete_destination'])) {
        $id = (int)$_POST['destination_id'];
        deleteDestination($pdo, $id);
        $adminMessage = '<div class="alert success" data-t="dest_deleted">Destination deleted.</div>';
    }

    if (isset($_POST['cancel_booking'])) {
        $ref = trim($_POST['booking_ref'] ?? '');
        if ($ref) {
            require_once __DIR__ . '/../includes/hotelbeds.php';
            $cancelResult = hbCancelBooking($ref);
            if ($cancelResult['success']) {
                // Update status in database
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'CANCELLED' WHERE reference = ?");
                $stmt->execute([$ref]);

                // Send cancellation email to guest
                $booking = getBookingByRef($pdo, $ref);
                if ($booking && $booking['guest_email']) {
                    $cancelHtml = '<p style="margin:0 0 15px;">Dear <strong>' . htmlspecialchars($booking['guest_name']) . '</strong>,</p>'
                        . '<p style="margin:0 0 20px;">Your booking has been cancelled. Details below:</p>'
                        . '<table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e0e0e0;border-radius:6px;border-collapse:collapse;margin-bottom:20px;">'
                        . '<tr style="background:#f8f9fa;"><td style="border-bottom:1px solid #e0e0e0;font-weight:600;width:140px;color:#203a43;">Reference</td><td style="border-bottom:1px solid #e0e0e0;">' . htmlspecialchars($ref) . '</td></tr>'
                        . '<tr><td style="border-bottom:1px solid #e0e0e0;font-weight:600;color:#203a43;">Hotel</td><td style="border-bottom:1px solid #e0e0e0;">' . htmlspecialchars($booking['hotel_name']) . '</td></tr>'
                        . '<tr style="background:#f8f9fa;"><td style="border-bottom:1px solid #e0e0e0;font-weight:600;color:#203a43;">Check-in</td><td style="border-bottom:1px solid #e0e0e0;">' . htmlspecialchars($booking['check_in']) . '</td></tr>'
                        . '<tr><td style="border-bottom:1px solid #e0e0e0;font-weight:600;color:#203a43;">Check-out</td><td style="border-bottom:1px solid #e0e0e0;">' . htmlspecialchars($booking['check_out']) . '</td></tr>'
                        . '<tr style="background:#f8f9fa;"><td style="font-weight:600;color:#203a43;">Status</td><td><span style="background:#dc3545;color:#fff;padding:3px 10px;border-radius:4px;font-size:13px;">CANCELLED</span></td></tr>'
                        . '</table>'
                        . '<p style="margin:0;">If you have any questions, please do not hesitate to contact us.</p>';
                    sendHtmlEmail($booking['guest_email'], "Booking Cancelled - $ref | Touristik", emailTemplate('Booking Cancelled', $cancelHtml));
                }

                $adminMessage = '<div class="alert success">Booking ' . htmlspecialchars($ref) . ' cancelled successfully.</div>';
            } else {
                $adminMessage = '<div class="alert error">Cancellation failed: ' . htmlspecialchars($cancelResult['error']) . '</div>';
            }
        }
    }

    if (isset($_POST['save_settings'])) {
        $allowedKeys = ['site_name', 'site_tagline', 'hero_title', 'hero_subtitle', 'contact_email', 'footer_text', 'items_per_page', 'maintenance_mode', 'ga_measurement_id'];
        foreach ($_POST['settings'] as $key => $value) {
            if (in_array($key, $allowedKeys)) {
                updateSetting($pdo, htmlspecialchars($key), htmlspecialchars($value));
            }
        }
        $adminMessage = '<div class="alert success" data-t="settings_saved">Settings updated successfully.</div>';
    }
}

$destinations = getDestinations($pdo);
$contacts = getContacts($pdo);
$settings = getAllSettings($pdo);
$bookings = ($activeTab === 'bookings') ? getBookings($pdo) : [];
?>

<section class="admin-section">
    <h2 data-t="admin_dashboard">Admin Dashboard</h2>
    <?= $adminMessage ?>

    <div class="admin-tabs">
        <a href="<?= url('admin', ['tab' => 'destinations']) ?>" class="tab <?= $activeTab === 'destinations' ? 'active' : '' ?>" data-t="destinations">Destinations</a>
        <a href="<?= url('admin', ['tab' => 'bookings']) ?>" class="tab <?= $activeTab === 'bookings' ? 'active' : '' ?>" data-t="tab_bookings">Bookings</a>
        <a href="<?= url('admin', ['tab' => 'contacts']) ?>" class="tab <?= $activeTab === 'contacts' ? 'active' : '' ?>" data-t="messages">Messages</a>
        <a href="<?= url('admin', ['tab' => 'settings']) ?>" class="tab <?= $activeTab === 'settings' ? 'active' : '' ?>" data-t="settings">Settings</a>
        <a href="<?= url('admin', ['tab' => 'performance']) ?>" class="tab <?= $activeTab === 'performance' ? 'active' : '' ?>" data-t="tab_performance">Performance</a>
    </div>

    <?php if ($activeTab === 'destinations'): ?>
    <div class="admin-panel">
        <h3 data-t="add_new_dest">Add New Destination</h3>
        <form class="admin-form" method="POST" action="<?= url('admin', ['tab' => 'destinations']) ?>">
            <?= csrfField() ?>
            <input type="text" name="name" data-tp="dest_name_ph" placeholder="Destination Name" required>
            <textarea name="description" data-tp="description_ph" placeholder="Description" rows="3" required></textarea>
            <input type="number" name="price" data-tp="price_ph" placeholder="Price" step="0.01" min="0" required>
            <input type="color" name="color" value="#2e86ab">
            <input type="text" name="emoji" placeholder="Emoji (e.g. &#127757;)" value="&#127757;">
            <button type="submit" name="add_destination" class="btn" data-t="add_dest_btn">Add Destination</button>
        </form>

        <h3 data-t="existing_dest">Existing Destinations</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th data-t="th_name">Name</th>
                    <th data-t="th_price">Price</th>
                    <th data-t="th_actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($destinations as $dest): ?>
                <tr>
                    <td><?= $dest['id'] ?></td>
                    <td><?= htmlspecialchars($dest['name']) ?></td>
                    <td>$<?= number_format($dest['price'], 2) ?></td>
                    <td>
                        <form method="POST" action="<?= url('admin', ['tab' => 'destinations']) ?>" style="display:inline">
                            <?= csrfField() ?>
                            <input type="hidden" name="destination_id" value="<?= $dest['id'] ?>">
                            <button type="submit" name="delete_destination" class="btn btn-danger" data-t="delete_btn" onclick="return confirm((translations[localStorage.getItem('lang')||'en']||translations.en).confirm_delete)">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php elseif ($activeTab === 'bookings'): ?>
    <div class="admin-panel">
        <h3 data-t="booking_title">Hotel Bookings</h3>
        <?php if (empty($bookings)): ?>
            <p data-t="booking_no_bookings">No bookings yet.</p>
        <?php else: ?>
        <div class="bookings-stats">
            <div class="stat-card">
                <span class="stat-number"><?= count($bookings) ?></span>
                <span class="stat-label" data-t="booking_total">Total Bookings</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= count(array_filter($bookings, function($b) { return $b['status'] === 'CONFIRMED'; })) ?></span>
                <span class="stat-label" data-t="booking_confirmed">Confirmed</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= count(array_filter($bookings, function($b) { return strtotime($b['check_in']) >= strtotime('today'); })) ?></span>
                <span class="stat-label" data-t="booking_upcoming">Upcoming</span>
            </div>
        </div>
        <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th data-t="booking_reference">Reference</th>
                    <th data-t="booking_hotel">Hotel</th>
                    <th data-t="booking_guest">Guest</th>
                    <th data-t="booking_checkin">Check-in</th>
                    <th data-t="booking_checkout">Check-out</th>
                    <th data-t="th_price">Price</th>
                    <th data-t="booking_status">Status</th>
                    <th data-t="booking_booked">Booked</th>
                    <th data-t="th_actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($booking['reference']) ?></strong></td>
                    <td><?= htmlspecialchars($booking['hotel_name']) ?></td>
                    <td>
                        <?= htmlspecialchars($booking['guest_name']) ?>
                        <?php if ($booking['guest_email']): ?>
                            <br><small><?= htmlspecialchars($booking['guest_email']) ?></small>
                        <?php endif; ?>
                        <?php if ($booking['guest_phone']): ?>
                            <br><small><?= htmlspecialchars($booking['guest_phone']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= $booking['check_in'] ? date('M d, Y', strtotime($booking['check_in'])) : '-' ?></td>
                    <td><?= $booking['check_out'] ? date('M d, Y', strtotime($booking['check_out'])) : '-' ?></td>
                    <td><?= htmlspecialchars($booking['currency']) ?> <?= number_format($booking['total_price'], 2) ?></td>
                    <td><span class="booking-status status-<?= strtolower($booking['status']) ?>"><?= htmlspecialchars($booking['status']) ?></span></td>
                    <td><?= date('M d, Y H:i', strtotime($booking['created_at'])) ?></td>
                    <td>
                        <?php if (strtoupper($booking['status']) === 'CONFIRMED'): ?>
                        <form method="POST" action="<?= url('admin', ['tab' => 'bookings']) ?>" style="display:inline" onsubmit="return confirm((translations[localStorage.getItem('lang')||'en']||translations.en).confirm_cancel_booking)">
                            <?= csrfField() ?>
                            <input type="hidden" name="booking_ref" value="<?= htmlspecialchars($booking['reference']) ?>">
                            <button type="submit" name="cancel_booking" class="btn btn-danger" data-t="cancel_btn">Cancel</button>
                        </form>
                        <?php else: ?>
                        <span style="color:#888">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

    <?php elseif ($activeTab === 'contacts'): ?>
    <div class="admin-panel">
        <h3 data-t="contact_messages">Contact Messages</h3>
        <?php if (empty($contacts)): ?>
            <p data-t="no_messages">No messages yet.</p>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th data-t="th_name">Name</th>
                    <th data-t="th_email">Email</th>
                    <th data-t="th_message">Message</th>
                    <th data-t="th_date">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact): ?>
                <tr>
                    <td><?= htmlspecialchars($contact['name']) ?></td>
                    <td><?= htmlspecialchars($contact['email']) ?></td>
                    <td><?= htmlspecialchars($contact['message']) ?></td>
                    <td><?= htmlspecialchars($contact['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <?php elseif ($activeTab === 'settings'): ?>
    <div class="admin-panel">
        <h3 data-t="site_settings">Site Settings</h3>
        <form class="admin-form" method="POST" action="<?= url('admin', ['tab' => 'settings']) ?>">
            <?= csrfField() ?>
            <?php foreach ($settings as $setting): ?>
            <div class="setting-row">
                <label for="setting_<?= htmlspecialchars($setting['setting_key']) ?>">
                    <?= htmlspecialchars($setting['setting_key']) ?>
                    <?php if ($setting['description']): ?>
                        <small><?= htmlspecialchars($setting['description']) ?></small>
                    <?php endif; ?>
                </label>
                <input type="text" id="setting_<?= htmlspecialchars($setting['setting_key']) ?>"
                       name="settings[<?= htmlspecialchars($setting['setting_key']) ?>]"
                       value="<?= htmlspecialchars($setting['setting_value']) ?>">
            </div>
            <?php endforeach; ?>
            <button type="submit" name="save_settings" class="btn" data-t="save_settings_btn">Save Settings</button>
        </form>
    </div>
    <?php elseif ($activeTab === 'performance'): ?>
    <div class="admin-panel">
        <h3>Site Performance & Health</h3>

        <?php
        $baseDir = __DIR__ . '/..';

        // Server info
        $phpVersion = phpversion();
        $mysqlVersion = $pdo->query("SELECT VERSION()")->fetchColumn();

        // File sizes
        $cssFile = $baseDir . '/css/styles.css';
        $cssMinFile = $baseDir . '/css/styles.min.css';
        $jsFile = $baseDir . '/js/script.js';
        $jsMinFile = $baseDir . '/js/script.min.js';
        $cssSize = file_exists($cssFile) ? filesize($cssFile) : 0;
        $cssMinSize = file_exists($cssMinFile) ? filesize($cssMinFile) : 0;
        $jsSize = file_exists($jsFile) ? filesize($jsFile) : 0;
        $jsMinSize = file_exists($jsMinFile) ? filesize($jsMinFile) : 0;
        $heroImg = $baseDir . '/img/hero-bg.jpg';
        $heroSize = file_exists($heroImg) ? filesize($heroImg) : 0;

        // Cache status
        $currencyCache = $baseDir . '/cache/currency_rates.json';
        $currencyCacheOk = file_exists($currencyCache);
        $currencyCacheAge = $currencyCacheOk ? (time() - filemtime($currencyCache)) : 0;

        // Database stats
        $destCount = $pdo->query("SELECT COUNT(*) FROM destinations")->fetchColumn();
        $contactCount = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
        $bookingCount = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
        $flightCount = $pdo->query("SELECT COUNT(*) FROM flight_prices")->fetchColumn();

        // SEO checks
        $sitemapExists = file_exists($baseDir . '/sitemap.php');
        $robotsExists = file_exists($baseDir . '/robots.txt');
        $manifestExists = file_exists($baseDir . '/manifest.json');
        $swExists = file_exists($baseDir . '/sw.js');
        $htaccessExists = file_exists($baseDir . '/.htaccess');
        $gaId = getSetting($pdo, 'ga_measurement_id', '');

        // Format bytes
        function formatBytes($bytes) {
            if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
            if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
            return $bytes . ' B';
        }

        // Score calculation
        $score = 0;
        $maxScore = 0;
        $checks = [];

        // Minification
        $maxScore += 2;
        if ($cssMinSize > 0) { $score++; $checks[] = ['pass', 'CSS minified (' . formatBytes($cssMinSize) . ')']; }
        else { $checks[] = ['fail', 'CSS not minified — run terser/csso to create styles.min.css']; }
        if ($jsMinSize > 0) { $score++; $checks[] = ['pass', 'JS minified (' . formatBytes($jsMinSize) . ')']; }
        else { $checks[] = ['fail', 'JS not minified — run terser to create script.min.js']; }

        // Minification savings
        $maxScore += 2;
        if ($cssMinSize > 0 && $cssSize > 0) {
            $cssSaving = round((1 - $cssMinSize / $cssSize) * 100);
            if ($cssSaving >= 20) { $score++; $checks[] = ['pass', "CSS savings: {$cssSaving}% smaller"]; }
            else { $checks[] = ['warn', "CSS savings only {$cssSaving}% — consider optimizing"]; }
        }
        if ($jsMinSize > 0 && $jsSize > 0) {
            $jsSaving = round((1 - $jsMinSize / $jsSize) * 100);
            if ($jsSaving >= 20) { $score++; $checks[] = ['pass', "JS savings: {$jsSaving}% smaller"]; }
            else { $checks[] = ['warn', "JS savings only {$jsSaving}% — consider optimizing"]; }
        }

        // Hero image
        $maxScore++;
        if ($heroSize > 0 && $heroSize < 500000) { $score++; $checks[] = ['pass', 'Hero image: ' . formatBytes($heroSize)]; }
        elseif ($heroSize >= 500000) { $checks[] = ['warn', 'Hero image is large: ' . formatBytes($heroSize) . ' — consider compressing']; }
        else { $checks[] = ['pass', 'No local hero image']; $score++; }

        // Cache
        $maxScore++;
        if ($currencyCacheOk && $currencyCacheAge < 43200) {
            $ageHours = round($currencyCacheAge / 3600, 1);
            $score++; $checks[] = ['pass', "Currency cache active ({$ageHours}h old)"];
        } elseif ($currencyCacheOk) {
            $checks[] = ['warn', 'Currency cache stale (' . round($currencyCacheAge / 3600) . 'h old)'];
        } else {
            $checks[] = ['fail', 'No currency cache — rates fetched every request'];
        }

        // SEO
        $maxScore += 4;
        if ($sitemapExists) { $score++; $checks[] = ['pass', 'Sitemap exists (sitemap.php)']; }
        else { $checks[] = ['fail', 'Missing sitemap.php']; }
        if ($robotsExists) { $score++; $checks[] = ['pass', 'robots.txt exists']; }
        else { $checks[] = ['fail', 'Missing robots.txt']; }
        if ($gaId) { $score++; $checks[] = ['pass', 'Google Analytics connected (' . htmlspecialchars($gaId) . ')']; }
        else { $checks[] = ['fail', 'Google Analytics not configured']; }
        if ($htaccessExists) { $score++; $checks[] = ['pass', 'Security headers (.htaccess)']; }
        else { $checks[] = ['fail', 'Missing .htaccess']; }

        // GZIP & Caching
        $maxScore += 2;
        $htContent = $htaccessExists ? file_get_contents($baseDir . '/.htaccess') : '';
        if (strpos($htContent, 'mod_deflate') !== false) { $score++; $checks[] = ['pass', 'GZIP compression enabled']; }
        else { $checks[] = ['fail', 'GZIP compression not configured in .htaccess']; }
        if (strpos($htContent, 'mod_expires') !== false) { $score++; $checks[] = ['pass', 'Browser caching headers configured']; }
        else { $checks[] = ['fail', 'Browser caching not configured in .htaccess']; }

        // PWA
        $maxScore += 2;
        if ($manifestExists) { $score++; $checks[] = ['pass', 'PWA manifest exists']; }
        else { $checks[] = ['fail', 'Missing manifest.json']; }
        if ($swExists) { $score++; $checks[] = ['pass', 'Service worker exists']; }
        else { $checks[] = ['fail', 'Missing sw.js']; }

        $scorePercent = $maxScore > 0 ? round(($score / $maxScore) * 100) : 0;
        $scoreColor = $scorePercent >= 80 ? '#28a745' : ($scorePercent >= 60 ? '#f18f01' : '#dc3545');
        ?>

        <!-- Health Score -->
        <div class="perf-score-section">
            <div class="perf-score-circle" style="border-color: <?= $scoreColor ?>">
                <span class="perf-score-number" style="color: <?= $scoreColor ?>"><?= $scorePercent ?></span>
                <span class="perf-score-label">/ 100</span>
            </div>
            <div class="perf-score-text">
                <h4>Health Score</h4>
                <p><?= $score ?> / <?= $maxScore ?> checks passed</p>
            </div>
        </div>

        <!-- Server Info -->
        <div class="perf-section">
            <h4>Server Information</h4>
            <div class="perf-grid">
                <div class="perf-item"><span class="perf-label">PHP Version</span><span class="perf-value"><?= $phpVersion ?></span></div>
                <div class="perf-item"><span class="perf-label">MySQL Version</span><span class="perf-value"><?= $mysqlVersion ?></span></div>
                <div class="perf-item"><span class="perf-label">Server Software</span><span class="perf-value"><?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') ?></span></div>
            </div>
        </div>

        <!-- File Sizes -->
        <div class="perf-section">
            <h4>File Sizes</h4>
            <div class="perf-grid">
                <div class="perf-item">
                    <span class="perf-label">CSS</span>
                    <span class="perf-value"><?= formatBytes($cssSize) ?><?= $cssMinSize ? ' (min: ' . formatBytes($cssMinSize) . ')' : '' ?></span>
                </div>
                <div class="perf-item">
                    <span class="perf-label">JavaScript</span>
                    <span class="perf-value"><?= formatBytes($jsSize) ?><?= $jsMinSize ? ' (min: ' . formatBytes($jsMinSize) . ')' : '' ?></span>
                </div>
                <div class="perf-item">
                    <span class="perf-label">Hero Image</span>
                    <span class="perf-value"><?= $heroSize > 0 ? formatBytes($heroSize) : 'None' ?></span>
                </div>
            </div>
        </div>

        <!-- Database -->
        <div class="perf-section">
            <h4>Database</h4>
            <div class="perf-grid">
                <div class="perf-item"><span class="perf-label">Destinations</span><span class="perf-value"><?= $destCount ?> records</span></div>
                <div class="perf-item"><span class="perf-label">Bookings</span><span class="perf-value"><?= $bookingCount ?> records</span></div>
                <div class="perf-item"><span class="perf-label">Contact Messages</span><span class="perf-value"><?= $contactCount ?> records</span></div>
                <div class="perf-item"><span class="perf-label">Flight Routes</span><span class="perf-value"><?= $flightCount ?> records</span></div>
            </div>
        </div>

        <!-- Checks -->
        <div class="perf-section">
            <h4>Health Checks</h4>
            <div class="perf-checks">
                <?php foreach ($checks as $check): ?>
                <div class="perf-check perf-check-<?= $check[0] ?>">
                    <span class="perf-check-icon"><?= $check[0] === 'pass' ? '&#10003;' : ($check[0] === 'warn' ? '&#9888;' : '&#10007;') ?></span>
                    <span><?= $check[1] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php endif; ?>
</section>
