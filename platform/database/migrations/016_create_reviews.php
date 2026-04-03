<?php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS reviews (
            id           INT AUTO_INCREMENT PRIMARY KEY,
            user_id      INT NOT NULL,
            booking_id   INT NOT NULL,
            product_type ENUM('hotel', 'tour', 'transfer') NOT NULL,
            product_id   VARCHAR(100) NOT NULL,
            rating       TINYINT NOT NULL,
            title        VARCHAR(255) DEFAULT NULL,
            comment      TEXT DEFAULT NULL,
            status       ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            admin_reply  TEXT DEFAULT NULL,
            created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY one_review_per_booking (booking_id),
            INDEX idx_product (product_type, product_id),
            INDEX idx_status (status),
            INDEX idx_rating (rating)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS reviews",
];
