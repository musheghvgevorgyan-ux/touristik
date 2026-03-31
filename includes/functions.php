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

// --- HTML Email ---

function emailTemplate($title, $contentHtml, $footerExtra = '') {
    $extra = $footerExtra ? '<tr><td style="padding:0 40px 20px;text-align:center;">' . $footerExtra . '</td></tr>' : '';
    return '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>' . htmlspecialchars($title) . '</title></head>'
        . '<body style="margin:0;padding:0;background:#f4f4f4;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . '<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;"><tr><td align="center" style="padding:20px 10px;">'
        . '<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">'
        // Header
        . '<tr><td style="background:linear-gradient(135deg,#0f2027 0%,#203a43 50%,#2c5364 100%);padding:30px 40px;text-align:center;">'
        . '<h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:1px;">&#9992; Touristik Travel Club</h1>'
        . '</td></tr>'
        // Title bar
        . '<tr><td style="background:#f18f01;padding:12px 40px;text-align:center;">'
        . '<h2 style="margin:0;color:#ffffff;font-size:18px;font-weight:600;">' . htmlspecialchars($title) . '</h2>'
        . '</td></tr>'
        // Content
        . '<tr><td style="padding:30px 40px;color:#333333;font-size:15px;line-height:1.7;">'
        . $contentHtml
        . '</td></tr>'
        . $extra
        // Divider
        . '<tr><td style="padding:0 40px;"><hr style="border:none;border-top:1px solid #e0e0e0;"></td></tr>'
        // Footer
        . '<tr><td style="padding:20px 40px 30px;color:#888888;font-size:13px;line-height:1.8;">'
        . '<p style="margin:0 0 8px;font-weight:600;color:#203a43;">Touristik Travel Club</p>'
        . '<p style="margin:0;">Phone: +374 33 060 609 | +374 55 060 609</p>'
        . '<p style="margin:0;">Email: info@touristik.am</p>'
        . '<p style="margin:0;">Website: <a href="https://touristik.am" style="color:#f18f01;text-decoration:none;">touristik.am</a></p>'
        . '<p style="margin:10px 0 0;font-size:12px;color:#aaa;">Branches: Komitas 38 &bull; Mashtots 7/6 &bull; Arshakunyats 34 (Yerevan Mall, 2nd floor)</p>'
        . '</td></tr>'
        . '</table></td></tr></table></body></html>';
}

function sendHtmlEmail($to, $subject, $htmlBody, $replyTo = '') {
    $from = 'info@touristik.am';
    $headers  = "From: $from\r\n";
    $headers .= "Reply-To: " . ($replyTo ?: $from) . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    return @mail($to, $subject, $htmlBody, $headers);
}
