<?php

return [
    'up' => [
        // Users → Agencies
        "ALTER TABLE users ADD CONSTRAINT fk_users_agency FOREIGN KEY (agency_id) REFERENCES agencies(id) ON DELETE SET NULL",

        // Bookings → Users, Agencies
        "ALTER TABLE bookings ADD CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL",
        "ALTER TABLE bookings ADD CONSTRAINT fk_bookings_agency FOREIGN KEY (agency_id) REFERENCES agencies(id) ON DELETE SET NULL",
        "ALTER TABLE bookings ADD CONSTRAINT fk_bookings_agent FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE SET NULL",
        "ALTER TABLE bookings ADD CONSTRAINT fk_bookings_promo FOREIGN KEY (promo_code_id) REFERENCES promo_codes(id) ON DELETE SET NULL",

        // Payments → Bookings
        "ALTER TABLE payments ADD CONSTRAINT fk_payments_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE",

        // Contacts → Users
        "ALTER TABLE contacts ADD CONSTRAINT fk_contacts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL",

        // Tours → Destinations
        "ALTER TABLE tours ADD CONSTRAINT fk_tours_destination FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE SET NULL",

        // Activity Log → Users
        "ALTER TABLE activity_log ADD CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL",

        // Notifications → Users
        "ALTER TABLE notifications ADD CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE",

        // Invoices → Agencies, Bookings
        "ALTER TABLE invoices ADD CONSTRAINT fk_invoices_agency FOREIGN KEY (agency_id) REFERENCES agencies(id) ON DELETE CASCADE",
        "ALTER TABLE invoices ADD CONSTRAINT fk_invoices_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL",

        // Promo Codes → Agencies
        "ALTER TABLE promo_codes ADD CONSTRAINT fk_promos_agency FOREIGN KEY (agency_id) REFERENCES agencies(id) ON DELETE SET NULL",

        // Promo Usage → Promo Codes, Users, Bookings
        "ALTER TABLE promo_usage ADD CONSTRAINT fk_promo_usage_promo FOREIGN KEY (promo_id) REFERENCES promo_codes(id) ON DELETE CASCADE",
        "ALTER TABLE promo_usage ADD CONSTRAINT fk_promo_usage_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE",
        "ALTER TABLE promo_usage ADD CONSTRAINT fk_promo_usage_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE",

        // Wishlists → Users
        "ALTER TABLE wishlists ADD CONSTRAINT fk_wishlists_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE",

        // Reviews → Users, Bookings
        "ALTER TABLE reviews ADD CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE",
        "ALTER TABLE reviews ADD CONSTRAINT fk_reviews_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE",
    ],
    'down' => [
        "ALTER TABLE reviews DROP FOREIGN KEY fk_reviews_booking",
        "ALTER TABLE reviews DROP FOREIGN KEY fk_reviews_user",
        "ALTER TABLE wishlists DROP FOREIGN KEY fk_wishlists_user",
        "ALTER TABLE promo_usage DROP FOREIGN KEY fk_promo_usage_booking",
        "ALTER TABLE promo_usage DROP FOREIGN KEY fk_promo_usage_user",
        "ALTER TABLE promo_usage DROP FOREIGN KEY fk_promo_usage_promo",
        "ALTER TABLE promo_codes DROP FOREIGN KEY fk_promos_agency",
        "ALTER TABLE invoices DROP FOREIGN KEY fk_invoices_booking",
        "ALTER TABLE invoices DROP FOREIGN KEY fk_invoices_agency",
        "ALTER TABLE notifications DROP FOREIGN KEY fk_notifications_user",
        "ALTER TABLE activity_log DROP FOREIGN KEY fk_activity_user",
        "ALTER TABLE tours DROP FOREIGN KEY fk_tours_destination",
        "ALTER TABLE contacts DROP FOREIGN KEY fk_contacts_user",
        "ALTER TABLE payments DROP FOREIGN KEY fk_payments_booking",
        "ALTER TABLE bookings DROP FOREIGN KEY fk_bookings_promo",
        "ALTER TABLE bookings DROP FOREIGN KEY fk_bookings_agent",
        "ALTER TABLE bookings DROP FOREIGN KEY fk_bookings_agency",
        "ALTER TABLE bookings DROP FOREIGN KEY fk_bookings_user",
        "ALTER TABLE users DROP FOREIGN KEY fk_users_agency",
    ],
];
