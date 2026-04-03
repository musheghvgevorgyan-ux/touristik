<?php

namespace Database\Seeds;

use Core\Database;

class AdminSeeder
{
    public function run(): void
    {
        $db = Database::getInstance();

        // Check if admin already exists
        $exists = $db->query("SELECT COUNT(*) as cnt FROM users WHERE role = 'superadmin'")->fetch();
        if ($exists['cnt'] > 0) {
            echo "  Admin already exists, skipping.\n";
            return;
        }

        $db->query(
            "INSERT INTO users (email, password, first_name, last_name, role, status, email_verified) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                'admin@touristik.am',
                password_hash('admin123', PASSWORD_BCRYPT),
                'Admin',
                'Touristik',
                'superadmin',
                'active',
                true,
            ]
        );

        echo "  Created superadmin: admin@touristik.am / admin123\n";
    }
}
