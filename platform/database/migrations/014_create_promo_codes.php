<?php

return [
    'up' => [
        "CREATE TABLE IF NOT EXISTS promo_codes (
            id             INT AUTO_INCREMENT PRIMARY KEY,
            code           VARCHAR(50) NOT NULL UNIQUE,
            type           ENUM('percentage', 'fixed') NOT NULL,
            value          DECIMAL(10,2) NOT NULL,
            currency       VARCHAR(3) DEFAULT 'USD',
            min_order      DECIMAL(10,2) DEFAULT 0.00,
            max_discount   DECIMAL(10,2) DEFAULT NULL,
            product_types  JSON DEFAULT NULL,
            usage_limit    INT DEFAULT NULL,
            usage_count    INT DEFAULT 0,
            per_user_limit INT DEFAULT 1,
            agency_id      INT DEFAULT NULL,
            starts_at      DATETIME DEFAULT NULL,
            expires_at     DATETIME DEFAULT NULL,
            status         ENUM('active', 'inactive') DEFAULT 'active',
            created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_code (code),
            INDEX idx_status (status),
            INDEX idx_dates (starts_at, expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS promo_usage (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            promo_id        INT NOT NULL,
            user_id         INT NOT NULL,
            booking_id      INT NOT NULL,
            discount_amount DECIMAL(10,2) NOT NULL,
            created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_promo (promo_id),
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ],
    'down' => [
        "DROP TABLE IF EXISTS promo_usage",
        "DROP TABLE IF EXISTS promo_codes",
    ],
];
