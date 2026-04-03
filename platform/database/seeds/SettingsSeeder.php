<?php

namespace Database\Seeds;

use Core\Database;

class SettingsSeeder
{
    public function run(): void
    {
        $db = Database::getInstance();

        $exists = $db->query("SELECT COUNT(*) as cnt FROM settings")->fetch();
        if ($exists['cnt'] > 0) {
            echo "  Settings already seeded, skipping.\n";
            return;
        }

        $settings = [
            ['site_name',         'Touristik',                                    'Site name displayed in header and title'],
            ['site_tagline',      'Your Journey, Our Passion',                    'Site tagline / slogan'],
            ['hero_title',        'Discover Your Next Adventure',                 'Homepage hero section title'],
            ['hero_subtitle',     'Explore the world with Touristik — flights, hotels, tours, and more.', 'Homepage hero subtitle'],
            ['contact_email',     'info@touristik.am',                            'Main contact email'],
            ['contact_phone',     '+374 10 123456',                               'Main contact phone'],
            ['footer_text',       '© 2026 Touristik LLC. All rights reserved.',   'Footer copyright text'],
            ['items_per_page',    '12',                                            'Default pagination items per page'],
            ['maintenance_mode',  '0',                                             'Enable maintenance mode (1/0)'],
            ['ga_measurement_id', '',                                              'Google Analytics 4 Measurement ID'],
            ['currency_default',  'USD',                                           'Default display currency'],
            ['language_default',  'en',                                            'Default site language'],
        ];

        foreach ($settings as [$key, $value, $desc]) {
            $db->query(
                "INSERT INTO settings (setting_key, setting_value, description) VALUES (?, ?, ?)",
                [$key, $value, $desc]
            );
        }

        echo "  Seeded " . count($settings) . " settings.\n";
    }
}
