<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            ['Paris', 'paris', 'The city of lights, love, and timeless elegance. From the Eiffel Tower to Montmartre, Paris captivates with its art, cuisine, and romantic boulevards. Stroll along the Seine, explore the Louvre, and savor croissants at a sidewalk cafe.', 'France', 299.00, 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800', '#E8336D', 48.8566, 2.3522],
            ['Tokyo', 'tokyo', 'Where ancient tradition meets futuristic innovation. Experience neon-lit Shibuya, serene temples in Asakusa, world-class sushi, and the iconic Mount Fuji views. Tokyo is a city of endless discovery.', 'Japan', 599.00, 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', '#FF6B35', 35.6762, 139.6503],
            ['Bali', 'bali', 'Tropical paradise where emerald rice terraces meet volcanic beaches. Visit ancient Hindu temples, surf world-class waves, enjoy Ubud\'s arts scene, and unwind with a Balinese massage at sunset.', 'Indonesia', 449.00, 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800', '#28A745', -8.3405, 115.0920],
            ['Rome', 'rome', 'Eternal city with millennia of history layered beneath every street. Walk the Colosseum floor, toss a coin in the Trevi Fountain, marvel at the Sistine Chapel, and feast on authentic Roman pasta.', 'Italy', 349.00, 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800', '#8B4513', 41.9028, 12.4964],
            ['Dubai', 'dubai', 'A futuristic metropolis rising from the desert. Ascend the Burj Khalifa, shop at massive malls, ride dunes in a desert safari, and dine at underwater restaurants. Dubai redefines luxury travel.', 'UAE', 499.00, 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800', '#C4A35A', 25.2048, 55.2708],
            ['Maldives', 'maldives', 'Crystal-clear turquoise waters and overwater luxury villas. Snorkel with manta rays, enjoy spa treatments above the Indian Ocean, and watch bioluminescent beaches glow at night. Pure paradise.', 'Maldives', 899.00, 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=800', '#00BCD4', 3.2028, 73.2207],
            ['Istanbul', 'istanbul', 'Where Europe meets Asia across the Bosphorus strait. Explore the Blue Mosque, Hagia Sophia, and Grand Bazaar. Cruise the Bosphorus at sunset and feast on Turkish kebabs and baklava.', 'Turkey', 350.00, 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?w=800', '#DC3545', 41.0082, 28.9784],
            ['Tbilisi', 'tbilisi', 'Georgia\'s charming capital perched on the banks of the Kura River. Wander the cobbled streets of the Old Town, soak in sulfur baths, taste legendary Georgian wine, and take the funicular to Narikala Fortress.', 'Georgia', 220.00, 'https://images.unsplash.com/photo-1573455494060-c5595004fb6c?w=800', '#6F42C1', 41.7151, 44.8271],
            ['Bangkok', 'bangkok', 'A city of golden temples, floating markets, and explosive street food. Visit the Grand Palace, ride a longtail boat through canals, and experience the electric energy of Khao San Road after dark.', 'Thailand', 550.00, 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=800', '#FD7E14', 13.7563, 100.5018],
            ['Santorini', 'santorini', 'The crown jewel of the Greek islands. Whitewashed buildings cascading down volcanic cliffs, sunset views from Oia, black sand beaches, and the finest Mediterranean cuisine. Unforgettable romance.', 'Greece', 650.00, 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800', '#0D6EFD', 36.3932, 25.4615],
        ];

        foreach ($destinations as [$name, $slug, $desc, $country, $price, $img, $color, $lat, $lng]) {
            Destination::updateOrCreate(['slug' => $slug], [
                'name' => $name, 'slug' => $slug, 'description' => $desc,
                'country' => $country, 'price_from' => $price, 'image_url' => $img,
                'color' => $color, 'latitude' => $lat, 'longitude' => $lng,
                'featured' => true, 'status' => 'active',
            ]);
        }

        $this->command->info('Seeded ' . count($destinations) . ' destinations.');
    }
}
