<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin
        if (!User::where('email', 'admin@touristik.am')->exists()) {
            User::create([
                'name'              => 'Admin Touristik',
                'first_name'        => 'Admin',
                'last_name'         => 'Touristik',
                'email'             => 'admin@touristik.am',
                'password'          => bcrypt('admin123'),
                'role'              => 'superadmin',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);
            $this->command->info('Created superadmin: admin@touristik.am / admin123');
        }

        // Destinations
        if (Destination::count() === 0) {
            foreach ([
                ['Paris', 'paris', 'The city of lights, love, and timeless elegance.', 'France', 299.00, 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800', '#E8336D'],
                ['Tokyo', 'tokyo', 'Where ancient tradition meets futuristic innovation.', 'Japan', 599.00, 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', '#FF6B35'],
                ['Bali', 'bali', 'Tropical paradise with stunning temples and beaches.', 'Indonesia', 449.00, 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800', '#28A745'],
                ['Rome', 'rome', 'Eternal city with millennia of history and culture.', 'Italy', 349.00, 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800', '#8B4513'],
                ['New York', 'new-york', 'The city that never sleeps.', 'USA', 399.00, 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800', '#1E3A5F'],
                ['Maldives', 'maldives', 'Crystal-clear waters and overwater luxury villas.', 'Maldives', 899.00, 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=800', '#00BCD4'],
            ] as [$name, $slug, $desc, $country, $price, $img, $color]) {
                Destination::create([
                    'name' => $name, 'slug' => $slug, 'description' => $desc,
                    'country' => $country, 'price_from' => $price, 'image_url' => $img,
                    'color' => $color, 'featured' => true, 'status' => 'active',
                ]);
            }
            $this->command->info('Seeded 6 destinations.');
        }

        // Tours
        if (Tour::count() === 0) {
            foreach ([
                ['Classic Yerevan City Tour', 'classic-yerevan', 'ingoing', 'Explore the heart of Armenia.', '2 days', 149.00, true],
                ['Lake Sevan & Dilijan', 'lake-sevan-dilijan', 'ingoing', 'Mountain lakes and forest trails.', '3 days', 249.00, false],
                ['Southern Armenia Adventure', 'southern-armenia', 'ingoing', 'Monasteries, wine, and mountain passes.', '4 days', 349.00, false],
                ['Greece Islands Hopping', 'greece-islands', 'outgoing', 'Santorini, Mykonos, and more.', '7 days', 899.00, true],
                ['Dubai Explorer', 'dubai-explorer', 'outgoing', 'Luxury, adventure, and desert safari.', '5 days', 699.00, false],
                ['Egyptian Wonders', 'egyptian-wonders', 'outgoing', 'Pyramids, Nile cruise, and temples.', '6 days', 799.00, false],
                ['Airport Pickup', 'airport-pickup', 'transfer', 'Zvartnots Airport to Yerevan.', '1 day', 25.00, true],
                ['City Transfer', 'city-transfer', 'transfer', 'Within Yerevan.', '1 day', 15.00, false],
                ['Intercity Transfer', 'intercity-transfer', 'transfer', 'Between Armenian cities.', '1 day', 60.00, false],
            ] as [$title, $slug, $type, $desc, $duration, $price, $featured]) {
                Tour::create([
                    'title' => $title, 'slug' => $slug, 'type' => $type,
                    'description' => $desc, 'duration' => $duration,
                    'price_from' => $price, 'featured' => $featured, 'status' => 'active',
                ]);
            }
            $this->command->info('Seeded 9 tours.');
        }

        // Settings
        if (Setting::count() === 0) {
            foreach ([
                ['site_name', 'Touristik'], ['site_tagline', 'Your Journey, Our Passion'],
                ['hero_title', 'Explore the World with Touristik'],
                ['hero_subtitle', 'Discover breathtaking destinations and create unforgettable memories'],
                ['contact_email', 'info@touristik.am'], ['contact_phone', '+374 10 123456'],
                ['footer_text', '© 2026 Touristik LLC. All rights reserved.'],
                ['items_per_page', '12'], ['maintenance_mode', '0'],
                ['ga_measurement_id', 'G-JHCDZH0E3T'],
                ['currency_default', 'USD'], ['language_default', 'en'],
            ] as [$key, $value]) {
                Setting::create(['setting_key' => $key, 'setting_value' => $value]);
            }
            $this->command->info('Seeded 12 settings.');
        }
    }
}
