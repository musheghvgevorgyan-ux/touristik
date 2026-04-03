<?php

namespace Database\Seeds;

use Core\Database;

class TourSeeder
{
    public function run(): void
    {
        $db = Database::getInstance();

        $exists = $db->query("SELECT COUNT(*) as cnt FROM tours")->fetch();
        if ($exists['cnt'] > 0) {
            echo "  Tours already seeded, skipping.\n";
            return;
        }

        $now = date('Y-m-d H:i:s');

        $tours = [
            // ── Ingoing Tours (Armenia) ─────────────────────
            [
                'title'       => 'Classic Yerevan',
                'slug'        => 'classic-yerevan',
                'type'        => 'ingoing',
                'description' => 'Discover the heart of Armenia with a guided 2-day tour through Yerevan. Visit the Cascade Complex, Republic Square, Vernissage market, the Matenadaran manuscript museum, and the Yerevan Brandy Company. Enjoy traditional Armenian cuisine at local restaurants.',
                'duration'    => '2 days',
                'price_from'  => 149.00,
                'image_url'   => 'https://images.unsplash.com/photo-1603289847962-1d148a89e787?w=800',
                'featured'    => 1,
                'itinerary'   => json_encode([
                    ['day' => 1, 'description' => 'Arrival, Republic Square walking tour, Cascade Complex, welcome dinner'],
                    ['day' => 2, 'description' => 'Matenadaran, Vernissage market, Brandy Company visit, departure'],
                ]),
            ],
            [
                'title'       => 'Lake Sevan & Dilijan',
                'slug'        => 'lake-sevan-dilijan',
                'type'        => 'ingoing',
                'description' => 'Explore the "Armenian Sea" — Lake Sevan — and the lush forests of Dilijan National Park. Visit Sevanavank monastery perched above the lake and stroll through Dilijan\'s Old Town with its artisan workshops.',
                'duration'    => '3 days',
                'price_from'  => 249.00,
                'image_url'   => 'https://images.unsplash.com/photo-1589308078059-be1415eab4c3?w=800',
                'featured'    => 0,
                'itinerary'   => json_encode([
                    ['day' => 1, 'description' => 'Drive to Lake Sevan, visit Sevanavank Monastery, lakeside lunch'],
                    ['day' => 2, 'description' => 'Transfer to Dilijan, explore Dilijan National Park, Old Town walk'],
                    ['day' => 3, 'description' => 'Haghartsin Monastery, local craft workshop, return to Yerevan'],
                ]),
            ],
            [
                'title'       => 'Southern Armenia',
                'slug'        => 'southern-armenia',
                'type'        => 'ingoing',
                'description' => 'Journey through southern Armenia visiting the ancient Tatev Monastery via the Wings of Tatev aerial tramway, the mysterious Karahunj observatory, and the cave city of Khndzoresk. Perfect for history and nature lovers.',
                'duration'    => '4 days',
                'price_from'  => 349.00,
                'image_url'   => 'https://images.unsplash.com/photo-1600959907703-125ba1374a12?w=800',
                'featured'    => 0,
                'itinerary'   => json_encode([
                    ['day' => 1, 'description' => 'Yerevan to Khor Virap Monastery with Ararat views, continue to Areni wine region'],
                    ['day' => 2, 'description' => 'Noravank Monastery, Karahunj (Stonehenge of Armenia), overnight in Goris'],
                    ['day' => 3, 'description' => 'Wings of Tatev tramway, Tatev Monastery, Khndzoresk swing bridge'],
                    ['day' => 4, 'description' => 'Shaki Waterfall, Jermuk mineral springs, return to Yerevan'],
                ]),
            ],

            // ── Outgoing Tours ──────────────────────────────
            [
                'title'       => 'Greece Islands',
                'slug'        => 'greece-islands',
                'type'        => 'outgoing',
                'description' => 'Island-hop through the stunning Greek Cyclades. Visit Santorini\'s iconic blue-domed churches, swim in the volcanic hot springs, explore Mykonos\'s vibrant nightlife and charming Chora streets, and relax on Naxos beaches.',
                'duration'    => '7 days',
                'price_from'  => 899.00,
                'image_url'   => 'https://images.unsplash.com/photo-1613395877344-13d4a8e0d49e?w=800',
                'featured'    => 1,
                'itinerary'   => json_encode([
                    ['day' => 1, 'description' => 'Fly to Athens, transfer to Piraeus port, ferry to Santorini'],
                    ['day' => 2, 'description' => 'Santorini: Oia sunset walk, wine tasting, volcanic hot springs'],
                    ['day' => 3, 'description' => 'Santorini: Red Beach, Akrotiri archaeological site, free evening'],
                    ['day' => 4, 'description' => 'Ferry to Mykonos, Chora walking tour, Little Venice sunset'],
                    ['day' => 5, 'description' => 'Mykonos: Delos island excursion, beach afternoon'],
                    ['day' => 6, 'description' => 'Ferry to Naxos, Portara temple, Old Town exploration'],
                    ['day' => 7, 'description' => 'Morning at leisure, ferry to Piraeus, flight home'],
                ]),
            ],
            [
                'title'       => 'Dubai Explorer',
                'slug'        => 'dubai-explorer',
                'type'        => 'outgoing',
                'description' => 'Experience the dazzling city of Dubai from towering skyscrapers to golden deserts. Visit the Burj Khalifa observation deck, explore the Dubai Mall, cruise Dubai Marina, and enjoy an unforgettable desert safari at sunset.',
                'duration'    => '5 days',
                'price_from'  => 699.00,
                'image_url'   => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800',
                'featured'    => 0,
                'itinerary'   => json_encode([
                    ['day' => 1, 'description' => 'Arrival, hotel check-in, Dubai Marina evening cruise'],
                    ['day' => 2, 'description' => 'Burj Khalifa, Dubai Mall, Dubai Fountain show'],
                    ['day' => 3, 'description' => 'Old Dubai: Gold & Spice Souks, Abra ride, Dubai Museum'],
                    ['day' => 4, 'description' => 'Desert safari: dune bashing, camel ride, BBQ dinner under the stars'],
                    ['day' => 5, 'description' => 'Palm Jumeirah, Atlantis visit, departure'],
                ]),
            ],
            [
                'title'       => 'Egyptian Wonders',
                'slug'        => 'egyptian-wonders',
                'type'        => 'outgoing',
                'description' => 'Walk in the footsteps of pharaohs on this 6-day journey through Egypt. Witness the Great Pyramids and Sphinx, cruise the Nile to Luxor\'s Valley of the Kings, and explore the colorful bazaars of Cairo.',
                'duration'    => '6 days',
                'price_from'  => 799.00,
                'image_url'   => 'https://images.unsplash.com/photo-1539650116574-8efeb43e2750?w=800',
                'featured'    => 0,
                'itinerary'   => json_encode([
                    ['day' => 1, 'description' => 'Arrival in Cairo, Egyptian Museum, Khan el-Khalili bazaar'],
                    ['day' => 2, 'description' => 'Pyramids of Giza, Great Sphinx, Sound & Light show'],
                    ['day' => 3, 'description' => 'Fly to Luxor, Karnak Temple, Luxor Temple at sunset'],
                    ['day' => 4, 'description' => 'Valley of the Kings, Hatshepsut Temple, Colossi of Memnon'],
                    ['day' => 5, 'description' => 'Nile cruise, Edfu & Kom Ombo temples'],
                    ['day' => 6, 'description' => 'Return to Cairo, Citadel of Saladin, departure'],
                ]),
            ],

            // ── Transfer Services ───────────────────────────
            [
                'title'       => 'Airport Pickup',
                'slug'        => 'airport-pickup',
                'type'        => 'transfer',
                'description' => 'Comfortable meet-and-greet service at Zvartnots International Airport. Our driver holds a name sign at arrivals and assists with luggage. Available 24/7 with flight tracking for delayed arrivals.',
                'duration'    => '1 day',
                'price_from'  => 25.00,
                'image_url'   => 'https://images.unsplash.com/photo-1556388158-158ea5ccacbd?w=800',
                'featured'    => 1,
                'itinerary'   => json_encode([
                    ['day' => 1, 'description' => 'Airport meet & greet, luggage assistance, private transfer to your hotel in Yerevan'],
                ]),
            ],
            [
                'title'       => 'City Transfer',
                'slug'        => 'city-transfer',
                'type'        => 'transfer',
                'description' => 'Point-to-point private transfers within Yerevan. Ideal for getting between your hotel, meetings, restaurants, or attractions. Clean, air-conditioned vehicles with English-speaking drivers.',
                'duration'    => '1 day',
                'price_from'  => 15.00,
                'image_url'   => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0afa?w=800',
                'featured'    => 0,
                'itinerary'   => json_encode([
                    ['day' => 1, 'description' => 'Private car pickup from your location and drop-off at your destination within Yerevan'],
                ]),
            ],
            [
                'title'       => 'Intercity Transfer',
                'slug'        => 'intercity-transfer',
                'type'        => 'transfer',
                'description' => 'Travel comfortably between Armenian cities. Popular routes include Yerevan to Gyumri, Yerevan to Dilijan, and Yerevan to Tatev. Scenic stops along the way on request.',
                'duration'    => '1 day',
                'price_from'  => 60.00,
                'image_url'   => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=800',
                'featured'    => 0,
                'itinerary'   => json_encode([
                    ['day' => 1, 'description' => 'Hotel pickup, scenic intercity drive with optional photo stops, drop-off at destination'],
                ]),
            ],
        ];

        foreach ($tours as $tour) {
            $db->query(
                "INSERT INTO tours (title, slug, type, description, duration, price_from, image_url, featured, itinerary, status, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, ?)",
                [
                    $tour['title'],
                    $tour['slug'],
                    $tour['type'],
                    $tour['description'],
                    $tour['duration'],
                    $tour['price_from'],
                    $tour['image_url'],
                    $tour['featured'],
                    $tour['itinerary'],
                    $now,
                    $now,
                ]
            );
        }

        echo "  Seeded 9 tours (3 ingoing, 3 outgoing, 3 transfers).\n";
    }
}
