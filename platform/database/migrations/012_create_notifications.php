<?php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS notifications (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            user_id    INT NOT NULL,
            type       VARCHAR(50) NOT NULL,
            title      VARCHAR(255) NOT NULL,
            message    TEXT DEFAULT NULL,
            link       VARCHAR(255) DEFAULT NULL,
            is_read    BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_read (user_id, is_read),
            INDEX idx_type (type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS notifications",
];
