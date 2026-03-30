<?php
$dbConfig = json_decode(file_get_contents(__DIR__ . '/../config/database.json'), true);

if (!$dbConfig) {
    die("Database configuration file is missing or invalid.");
}

try {
    $dsn = "mysql:host={$dbConfig['host']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create database if not exists
    $dbname = $dbConfig['dbname'];
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo->exec("USE `$dbname`");

    // Create tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS destinations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        color VARCHAR(20) DEFAULT '#2e86ab',
        emoji VARCHAR(20) DEFAULT '&#127757;',
        image_url VARCHAR(500) DEFAULT '',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Add image_url column if missing (for existing databases)
    try {
        $pdo->exec("ALTER TABLE destinations ADD COLUMN image_url VARCHAR(500) DEFAULT '' AFTER emoji");
    } catch (PDOException $e) {
        // Column already exists
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )");

    // Flight prices table
    $pdo->exec("CREATE TABLE IF NOT EXISTS flight_prices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        from_city VARCHAR(100) NOT NULL,
        to_city VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        trip_type ENUM('oneway','roundtrip') DEFAULT 'roundtrip',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY route (from_city, to_city, trip_type)
    )");

    // Seed flight prices
    $fpCount = $pdo->query("SELECT COUNT(*) FROM flight_prices")->fetchColumn();
    if ($fpCount == 0) {
        $pdo->exec("INSERT INTO flight_prices (from_city, to_city, price, trip_type) VALUES
            ('Yerevan', 'Moscow', 150, 'oneway'), ('Yerevan', 'Moscow', 260, 'roundtrip'),
            ('Yerevan', 'Sochi', 130, 'oneway'), ('Yerevan', 'Sochi', 220, 'roundtrip'),
            ('Yerevan', 'Dubai', 220, 'oneway'), ('Yerevan', 'Dubai', 380, 'roundtrip'),
            ('Yerevan', 'Istanbul', 100, 'oneway'), ('Yerevan', 'Istanbul', 170, 'roundtrip'),
            ('Yerevan', 'Antalya', 120, 'oneway'), ('Yerevan', 'Antalya', 200, 'roundtrip'),
            ('Yerevan', 'Paris', 250, 'oneway'), ('Yerevan', 'Paris', 420, 'roundtrip'),
            ('Yerevan', 'London', 280, 'oneway'), ('Yerevan', 'London', 470, 'roundtrip'),
            ('Yerevan', 'Berlin', 220, 'oneway'), ('Yerevan', 'Berlin', 370, 'roundtrip'),
            ('Yerevan', 'Frankfurt', 230, 'oneway'), ('Yerevan', 'Frankfurt', 390, 'roundtrip'),
            ('Yerevan', 'Munich', 240, 'oneway'), ('Yerevan', 'Munich', 400, 'roundtrip'),
            ('Yerevan', 'Rome', 210, 'oneway'), ('Yerevan', 'Rome', 360, 'roundtrip'),
            ('Yerevan', 'Milan', 220, 'oneway'), ('Yerevan', 'Milan', 370, 'roundtrip'),
            ('Yerevan', 'Athens', 180, 'oneway'), ('Yerevan', 'Athens', 310, 'roundtrip'),
            ('Yerevan', 'Halkidiki', 200, 'oneway'), ('Yerevan', 'Halkidiki', 340, 'roundtrip'),
            ('Yerevan', 'Crete', 210, 'oneway'), ('Yerevan', 'Crete', 350, 'roundtrip'),
            ('Yerevan', 'Tivat', 190, 'oneway'), ('Yerevan', 'Tivat', 320, 'roundtrip'),
            ('Yerevan', 'Tbilisi', 60, 'oneway'), ('Yerevan', 'Tbilisi', 100, 'roundtrip'),
            ('Yerevan', 'Cairo', 200, 'oneway'), ('Yerevan', 'Cairo', 340, 'roundtrip'),
            ('Yerevan', 'El Alamein', 220, 'oneway'), ('Yerevan', 'El Alamein', 370, 'roundtrip'),
            ('Yerevan', 'Sharm El Sheikh', 210, 'oneway'), ('Yerevan', 'Sharm El Sheikh', 350, 'roundtrip'),
            ('Yerevan', 'Hurghada', 210, 'oneway'), ('Yerevan', 'Hurghada', 350, 'roundtrip'),
            ('Yerevan', 'Barcelona', 260, 'oneway'), ('Yerevan', 'Barcelona', 440, 'roundtrip'),
            ('Yerevan', 'Madrid', 270, 'oneway'), ('Yerevan', 'Madrid', 450, 'roundtrip'),
            ('Yerevan', 'Bangkok', 380, 'oneway'), ('Yerevan', 'Bangkok', 650, 'roundtrip'),
            ('Yerevan', 'Phuket', 400, 'oneway'), ('Yerevan', 'Phuket', 680, 'roundtrip'),
            ('Yerevan', 'New York', 450, 'oneway'), ('Yerevan', 'New York', 760, 'roundtrip'),
            ('Yerevan', 'Los Angeles', 500, 'oneway'), ('Yerevan', 'Los Angeles', 850, 'roundtrip'),
            ('Yerevan', 'Miami', 470, 'oneway'), ('Yerevan', 'Miami', 800, 'roundtrip'),
            ('Moscow', 'Dubai', 250, 'oneway'), ('Moscow', 'Dubai', 420, 'roundtrip'),
            ('Moscow', 'Istanbul', 150, 'oneway'), ('Moscow', 'Istanbul', 260, 'roundtrip'),
            ('Moscow', 'Antalya', 170, 'oneway'), ('Moscow', 'Antalya', 290, 'roundtrip'),
            ('Moscow', 'Paris', 200, 'oneway'), ('Moscow', 'Paris', 340, 'roundtrip'),
            ('Moscow', 'London', 220, 'oneway'), ('Moscow', 'London', 380, 'roundtrip'),
            ('Moscow', 'Bangkok', 350, 'oneway'), ('Moscow', 'Bangkok', 590, 'roundtrip'),
            ('Moscow', 'Sochi', 80, 'oneway'), ('Moscow', 'Sochi', 140, 'roundtrip'),
            ('Istanbul', 'London', 130, 'oneway'), ('Istanbul', 'London', 220, 'roundtrip'),
            ('Istanbul', 'Paris', 140, 'oneway'), ('Istanbul', 'Paris', 240, 'roundtrip'),
            ('Dubai', 'London', 300, 'oneway'), ('Dubai', 'London', 510, 'roundtrip'),
            ('Dubai', 'Bangkok', 250, 'oneway'), ('Dubai', 'Bangkok', 420, 'roundtrip')
        ");
    }

    // Bookings table for tracking hotel reservations
    $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        reference VARCHAR(100) NOT NULL,
        client_reference VARCHAR(100) DEFAULT '',
        hotel_name VARCHAR(255) NOT NULL,
        guest_name VARCHAR(200) NOT NULL,
        guest_email VARCHAR(200) DEFAULT '',
        guest_phone VARCHAR(50) DEFAULT '',
        check_in DATE,
        check_out DATE,
        rooms INT DEFAULT 1,
        currency VARCHAR(10) DEFAULT 'EUR',
        total_price DECIMAL(10,2) DEFAULT 0,
        status VARCHAR(50) DEFAULT 'CONFIRMED',
        raw_response TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY booking_ref (reference)
    )");

    // Settings table for site-wide configuration
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        description VARCHAR(255) DEFAULT '',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Seed default destinations
    $count = $pdo->query("SELECT COUNT(*) FROM destinations")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO destinations (name, description, price, color, emoji) VALUES
            ('Paris, France', 'The City of Light awaits with iconic landmarks, world-class cuisine, and timeless romance.', 899, '#2e86ab', '&#9968;'),
            ('Tokyo, Japan', 'Experience the perfect blend of ancient tradition and cutting-edge modernity.', 1199, '#a23b72', '&#9961;'),
            ('Bali, Indonesia', 'Tropical paradise with stunning temples, lush rice terraces, and pristine beaches.', 749, '#f18f01', '&#127796;'),
            ('Rome, Italy', 'Walk through millennia of history among ancient ruins, fountains, and piazzas.', 949, '#c73e1d', '&#127963;'),
            ('New York, USA', 'The city that never sleeps offers endless entertainment, dining, and culture.', 699, '#3b1f2b', '&#127747;'),
            ('Maldives', 'Crystal-clear waters, overwater villas, and unforgettable sunsets await you.', 1499, '#119da4', '&#9978;')
        ");
    }

    // Seed default admin
    $adminCount = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
    if ($adminCount == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hashedPassword]);
    }

    // Seed default settings
    $settingsCount = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
    if ($settingsCount == 0) {
        $pdo->exec("INSERT INTO settings (setting_key, setting_value, description) VALUES
            ('site_name', 'Wanderlust', 'Website name displayed in header and title'),
            ('site_tagline', 'Discover the World', 'Tagline shown on the homepage'),
            ('hero_title', 'Explore the World with Touristik', 'Main hero section heading'),
            ('hero_subtitle', 'Discover breathtaking destinations and create unforgettable memories', 'Hero section subheading'),
            ('contact_email', 'info@wanderlust.com', 'Contact email address'),
            ('footer_text', '© 2026 Wanderlust Tourism. All rights reserved.', 'Footer copyright text'),
            ('items_per_page', '12', 'Number of items per page'),
            ('maintenance_mode', '0', 'Enable maintenance mode (1=on, 0=off)'),
            ('ga_measurement_id', '', 'Google Analytics Measurement ID (e.g. G-XXXXXXXXXX)')
        ");
    }

    // Ensure ga_measurement_id exists (for databases seeded before this was added)
    $gaExists = $pdo->query("SELECT COUNT(*) FROM settings WHERE setting_key = 'ga_measurement_id'")->fetchColumn();
    if ($gaExists == 0) {
        $pdo->exec("INSERT INTO settings (setting_key, setting_value, description) VALUES ('ga_measurement_id', '', 'Google Analytics Measurement ID (e.g. G-XXXXXXXXXX)')");
    }

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
