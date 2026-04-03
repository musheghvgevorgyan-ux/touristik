<?php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS flight_prices (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            from_city   VARCHAR(100) NOT NULL,
            to_city     VARCHAR(100) NOT NULL,
            price       DECIMAL(10,2) NOT NULL,
            trip_type   ENUM('oneway', 'roundtrip') NOT NULL,
            updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_route (from_city, to_city, trip_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS flight_prices",
];
