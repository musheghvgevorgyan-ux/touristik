<?php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS wishlists (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            user_id    INT NOT NULL,
            item_type  ENUM('hotel', 'tour', 'destination', 'transfer') NOT NULL,
            item_id    VARCHAR(100) NOT NULL,
            item_data  JSON DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_wish (user_id, item_type, item_id),
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS wishlists",
];
