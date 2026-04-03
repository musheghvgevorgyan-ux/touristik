<?php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS payments (
            id               INT AUTO_INCREMENT PRIMARY KEY,
            booking_id       INT NOT NULL,
            transaction_id   VARCHAR(255) DEFAULT NULL,
            gateway          VARCHAR(50) NOT NULL,

            amount           DECIMAL(12,2) NOT NULL,
            currency         VARCHAR(3) DEFAULT 'USD',

            method           ENUM('card', 'bank_transfer', 'cash', 'balance') NOT NULL,
            status           ENUM('pending', 'completed', 'failed', 'refunded', 'partial_refund') DEFAULT 'pending',

            gateway_response JSON DEFAULT NULL,
            refund_amount    DECIMAL(12,2) DEFAULT 0.00,
            refund_reason    TEXT DEFAULT NULL,

            paid_at          DATETIME DEFAULT NULL,
            created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            INDEX idx_booking (booking_id),
            INDEX idx_transaction (transaction_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS payments",
];
