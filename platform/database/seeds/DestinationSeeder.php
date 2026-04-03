<?php

namespace Database\Seeds;

use Core\Database;

class DestinationSeeder
{
    public function run(): void
    {
        $db = Database::getInstance();

        $exists = $db->query("SELECT COUNT(*) as cnt FROM destinations")->fetch();
        if ($exists['cnt'] > 0) {
            echo "  Destinations already seeded, skipping.\n";
            return;
        }

        $destinations = [
            ['Paris', 'paris', 'The city of lights, love, and timeless elegance.', 'France', 299.00, 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800', '#E8336D', '🇫🇷', true],
            ['Tokyo', 'tokyo', 'Where ancient tradition meets futuristic innovation.', 'Japan', 599.00, 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', '#FF6B35', '🇯🇵', true],
            ['Bali', 'bali', 'Tropical paradise with stunning temples and beaches.', 'Indonesia', 449.00, 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800', '#28A745', '🇮🇩', true],
            ['Rome', 'rome', 'Eternal city with millennia of history and culture.', 'Italy', 349.00, 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800', '#8B4513', '🇮🇹', true],
            ['New York', 'new-york', 'The city that never sleeps — iconic skyline and energy.', 'USA', 399.00, 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800', '#1E3A5F', '🇺🇸', true],
            ['Maldives', 'maldives', 'Crystal-clear waters and overwater luxury villas.', 'Maldives', 899.00, 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=800', '#00BCD4', '🇲🇻', true],
        ];

        foreach ($destinations as [$name, $slug, $desc, $country, $price, $img, $color, $emoji, $featured]) {
            $db->query(
                "INSERT INTO destinations (name, slug, description, country, price_from, image_url, color, emoji, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$name, $slug, $desc, $country, $price, $img, $color, $emoji, $featured]
            );
        }

        echo "  Seeded 6 destinations.\n";
    }
}
