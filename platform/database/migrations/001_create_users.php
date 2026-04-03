<?php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS users (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            email           VARCHAR(255) NOT NULL UNIQUE,
            password        VARCHAR(255) NOT NULL,
            first_name      VARCHAR(100) NOT NULL,
            last_name       VARCHAR(100) NOT NULL,
            phone           VARCHAR(30) DEFAULT NULL,
            role            ENUM('customer', 'agent', 'admin', 'superadmin') DEFAULT 'customer',
            agency_id       INT DEFAULT NULL,
            status          ENUM('active', 'suspended', 'pending') DEFAULT 'active',
            email_verified  BOOLEAN DEFAULT FALSE,
            language        VARCHAR(2) DEFAULT 'en',
            currency        VARCHAR(3) DEFAULT 'USD',
            last_login      DATETIME DEFAULT NULL,
            created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_role (role),
            INDEX idx_agency (agency_id),
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS users",
];
