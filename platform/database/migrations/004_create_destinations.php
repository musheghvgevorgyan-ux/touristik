<?php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS destinations (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            name        VARCHAR(255) NOT NULL,
            slug        VARCHAR(255) NOT NULL UNIQUE,
            description TEXT DEFAULT NULL,
            country     VARCHAR(100) DEFAULT NULL,
            price_from  DECIMAL(10,2) DEFAULT NULL,
            image_url   VARCHAR(500) DEFAULT NULL,
            color       VARCHAR(7) DEFAULT NULL,
            emoji       VARCHAR(10) DEFAULT NULL,
            featured    BOOLEAN DEFAULT FALSE,
            status      ENUM('active', 'inactive') DEFAULT 'active',
            created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_featured (featured),
            INDEX idx_status (status),
            INDEX idx_slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS destinations",
];
