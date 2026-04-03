<?php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS agencies (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            name            VARCHAR(255) NOT NULL,
            legal_name      VARCHAR(255) DEFAULT NULL,
            tax_id          VARCHAR(50) DEFAULT NULL,
            email           VARCHAR(255) NOT NULL,
            phone           VARCHAR(30) DEFAULT NULL,
            address         TEXT DEFAULT NULL,
            commission_rate DECIMAL(5,2) DEFAULT 0.00,
            balance         DECIMAL(12,2) DEFAULT 0.00,
            payment_model   ENUM('prepaid', 'credit', 'markup') DEFAULT 'markup',
            status          ENUM('active', 'suspended', 'pending') DEFAULT 'pending',
            created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS agencies",
];
