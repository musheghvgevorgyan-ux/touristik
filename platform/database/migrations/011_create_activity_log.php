<?php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS activity_log (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            user_id     INT DEFAULT NULL,
            action      VARCHAR(100) NOT NULL,
            entity_type VARCHAR(50) DEFAULT NULL,
            entity_id   INT DEFAULT NULL,
            details     JSON DEFAULT NULL,
            ip_address  VARCHAR(45) DEFAULT NULL,
            created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user (user_id),
            INDEX idx_entity (entity_type, entity_id),
            INDEX idx_action (action),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS activity_log",
];
