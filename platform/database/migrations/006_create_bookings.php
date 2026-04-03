<?php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS bookings (
            id                INT AUTO_INCREMENT PRIMARY KEY,
            reference         VARCHAR(20) NOT NULL UNIQUE,
            supplier_ref      VARCHAR(100) DEFAULT NULL,

            user_id           INT DEFAULT NULL,
            agency_id         INT DEFAULT NULL,
            agent_id          INT DEFAULT NULL,

            product_type      ENUM('hotel', 'flight', 'tour', 'transfer', 'package') NOT NULL,
            supplier          VARCHAR(50) NOT NULL,

            guest_first_name  VARCHAR(100) NOT NULL,
            guest_last_name   VARCHAR(100) NOT NULL,
            guest_email       VARCHAR(255) NOT NULL,
            guest_phone       VARCHAR(30) DEFAULT NULL,

            product_data      JSON NOT NULL,

            net_price         DECIMAL(12,2) NOT NULL,
            sell_price        DECIMAL(12,2) NOT NULL,
            commission        DECIMAL(12,2) DEFAULT 0.00,
            currency          VARCHAR(3) DEFAULT 'USD',

            promo_code_id     INT DEFAULT NULL,
            discount_amount   DECIMAL(12,2) DEFAULT 0.00,

            status            ENUM('pending', 'confirmed', 'cancelled', 'completed', 'failed', 'refunded') DEFAULT 'pending',
            payment_status    ENUM('unpaid', 'paid', 'refunded', 'partial_refund') DEFAULT 'unpaid',

            supplier_request  JSON DEFAULT NULL,
            supplier_response JSON DEFAULT NULL,

            created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_user (user_id),
            INDEX idx_agency (agency_id),
            INDEX idx_status (status),
            INDEX idx_payment (payment_status),
            INDEX idx_created (created_at),
            INDEX idx_reference (reference)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS bookings",
];
