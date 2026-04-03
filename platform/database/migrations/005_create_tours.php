<?php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS tours (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            title           VARCHAR(255) NOT NULL,
            slug            VARCHAR(255) NOT NULL UNIQUE,
            type            ENUM('ingoing', 'outgoing', 'transfer') NOT NULL,
            description     TEXT DEFAULT NULL,
            itinerary       JSON DEFAULT NULL,
            duration        VARCHAR(50) DEFAULT NULL,
            price_from      DECIMAL(10,2) DEFAULT NULL,
            image_url       VARCHAR(500) DEFAULT NULL,
            destination_id  INT DEFAULT NULL,
            featured        BOOLEAN DEFAULT FALSE,
            status          ENUM('active', 'inactive') DEFAULT 'active',
            created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_type (type),
            INDEX idx_featured (featured),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS tours",
];
