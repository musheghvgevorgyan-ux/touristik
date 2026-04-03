<?php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS invoices (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            invoice_no  VARCHAR(20) NOT NULL UNIQUE,
            agency_id   INT NOT NULL,
            booking_id  INT DEFAULT NULL,
            amount      DECIMAL(12,2) NOT NULL,
            currency    VARCHAR(3) DEFAULT 'USD',
            status      ENUM('draft', 'sent', 'paid', 'overdue') DEFAULT 'draft',
            due_date    DATE DEFAULT NULL,
            paid_at     DATETIME DEFAULT NULL,
            created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_agency (agency_id),
            INDEX idx_status (status),
            INDEX idx_invoice_no (invoice_no)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS invoices",
];
