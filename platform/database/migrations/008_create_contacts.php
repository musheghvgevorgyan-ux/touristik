<?php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS contacts (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            user_id    INT DEFAULT NULL,
            name       VARCHAR(255) NOT NULL,
            email      VARCHAR(255) NOT NULL,
            subject    VARCHAR(255) DEFAULT NULL,
            message    TEXT NOT NULL,
            status     ENUM('new', 'read', 'replied') DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS contacts",
];
