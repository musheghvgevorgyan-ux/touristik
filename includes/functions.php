<?php

// --- Destination functions ---

function getDestinations($pdo) {
    $stmt = $pdo->query("SELECT * FROM destinations ORDER BY id ASC");
    return $stmt->fetchAll();
}

function getDestination($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function addDestination($pdo, $name, $description, $price, $color, $emoji) {
    $stmt = $pdo->prepare("INSERT INTO destinations (name, description, price, color, emoji) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$name, $description, $price, $color, $emoji]);
}

function deleteDestination($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM destinations WHERE id = ?");
    return $stmt->execute([$id]);
}

// --- Contact functions ---

function saveContact($pdo, $name, $email, $message) {
    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
    return $stmt->execute([$name, $email, $message]);
}

function getContacts($pdo) {
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

// --- Booking functions ---

function saveBooking($pdo, $data) {
    $stmt = $pdo->prepare("INSERT INTO bookings (reference, client_reference, hotel_name, guest_name, guest_email, guest_phone, check_in, check_out, rooms, currency, total_price, status, raw_response)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE status = VALUES(status), total_price = VALUES(total_price)");
    return $stmt->execute([
        $data['reference'] ?? '',
        $data['client_reference'] ?? '',
        $data['hotel_name'] ?? '',
        $data['guest_name'] ?? '',
        $data['guest_email'] ?? '',
        $data['guest_phone'] ?? '',
        $data['check_in'] ?? null,
        $data['check_out'] ?? null,
        $data['rooms'] ?? 1,
        $data['currency'] ?? 'EUR',
        $data['total_price'] ?? 0,
        $data['status'] ?? 'CONFIRMED',
        $data['raw_response'] ?? '',
    ]);
}

function getBookings($pdo) {
    $stmt = $pdo->query("SELECT * FROM bookings ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function getBookingByRef($pdo, $ref) {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE reference = ?");
    $stmt->execute([$ref]);
    return $stmt->fetch();
}

// --- Admin functions ---

function loginAdmin($pdo, $username, $password) {
    // Rate limiting
    if (!checkLoginRate()) {
        return false;
    }

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin'] = $admin['username'];
        resetLoginAttempts();
        return true;
    }
    return false;
}

function checkLoginRate() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $key = 'login_attempts_' . md5($ip);
    $attempts = $_SESSION[$key] ?? ['count' => 0, 'time' => time()];

    if (time() - $attempts['time'] > 900) {
        $attempts = ['count' => 0, 'time' => time()];
    }

    if ($attempts['count'] >= 5) {
        return false;
    }

    $attempts['count']++;
    $_SESSION[$key] = $attempts;
    return true;
}

function resetLoginAttempts() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $key = 'login_attempts_' . md5($ip);
    unset($_SESSION[$key]);
}

function isAdmin() {
    return isset($_SESSION['admin']);
}

// --- Settings functions ---

function getSetting($pdo, $key, $default = '') {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

function getAllSettings($pdo) {
    $stmt = $pdo->query("SELECT * FROM settings ORDER BY setting_key ASC");
    return $stmt->fetchAll();
}

function updateSetting($pdo, $key, $value) {
    $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    return $stmt->execute([$value, $key]);
}

// --- CSRF protection ---

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function verifyCsrf() {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        return false;
    }
    return true;
}

// --- URL helper ---

function url($page = 'home', $params = []) {
    $query = http_build_query(array_merge(['page' => $page], $params));
    return 'index.php?' . $query;
}
