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
?>

<section class="admin-section">
    <h2 data-t="admin_dashboard">Admin Dashboard</h2>
    <?= $adminMessage ?>

    <div class="admin-tabs">
        <a href="<?= url('admin', ['tab' => 'destinations']) ?>" class="tab <?= $activeTab === 'destinations' ? 'active' : '' ?>" data-t="destinations">Destinations</a>
        <a href="<?= url('admin', ['tab' => 'contacts']) ?>" class="tab <?= $activeTab === 'contacts' ? 'active' : '' ?>" data-t="messages">Messages</a>
        <a href="<?= url('admin', ['tab' => 'settings']) ?>" class="tab <?= $activeTab === 'settings' ? 'active' : '' ?>" data-t="settings">Settings</a>
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
                            <button type="submit" name="delete_destination" class="btn btn-danger" data-t="delete_btn" onclick="return confirm('Delete this destination?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
    <?php endif; ?>
</section>
