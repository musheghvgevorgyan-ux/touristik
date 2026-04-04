<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;

class OutgoingToursSeeder extends Seeder
{
    public function run(): void
    {
        Tour::where('type', 'outgoing')->delete();

        $defaultIncludes = ['Round-trip flights from Yerevan', 'Hotel accommodation', 'Airport transfers', 'English-speaking guide', 'Sightseeing as per itinerary'];
        $defaultExcludes = ['Travel insurance', 'Personal expenses', 'Optional excursions', 'Meals not mentioned', 'Visa fees (if applicable)'];

        $tours = [
            [
                'title' => 'Greek Islands Explorer',
                'slug' => 'greek-islands-explorer',
                'region' => 'greece',
                'description' => "Island-hop through Santorini, Mykonos, and Athens. Enjoy crystal-clear waters, whitewashed villages, ancient ruins, and vibrant nightlife in the Mediterranean paradise.\n\nThis 7-day package includes visits to the Acropolis, sunset in Oia, beach days in Mykonos, and authentic Greek cuisine.",
                'duration' => '7 Days',
                'price_from' => 890.00,
                'image_url' => 'https://images.unsplash.com/photo-1533105079780-92b9be482077?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1533105079780-92b9be482077?w=800&q=80','https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800&q=80','https://images.unsplash.com/photo-1613395877344-13d4a8e0d49e?w=800&q=80'],
                'itinerary' => [['title'=>'Day 1-2: Athens','description'=>'Acropolis, Parthenon, Plaka neighborhood, Greek cuisine introduction.'],['title'=>'Day 3-4: Santorini','description'=>'Oia sunset, volcanic beaches, wine tasting, caldera views.'],['title'=>'Day 5-6: Mykonos','description'=>'Windmills, Little Venice, beach clubs, Delos island excursion.'],['title'=>'Day 7: Departure','description'=>'Morning free time, transfer to airport.']],
                'includes' => array_merge($defaultIncludes, ['Ferry tickets between islands', 'Breakfast daily']),
                'excludes' => $defaultExcludes,
            ],
            [
                'title' => 'Egypt: Land of Pharaohs',
                'slug' => 'egypt-land-of-pharaohs',
                'region' => 'egypt',
                'description' => "Explore the Pyramids of Giza, cruise the Nile River, visit the Valley of Kings, and discover the wonders of ancient Egyptian civilization.\n\nFrom Cairo's bustling streets to Luxor's ancient temples, this tour covers 5,000 years of history.",
                'duration' => '6 Days',
                'price_from' => 750.00,
                'image_url' => 'https://images.unsplash.com/photo-1539768942893-daf53e736b68?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1539768942893-daf53e736b68?w=800&q=80','https://images.unsplash.com/photo-1553913861-c0fddf2619ee?w=800&q=80'],
                'itinerary' => [['title'=>'Day 1-2: Cairo','description'=>'Pyramids of Giza, Sphinx, Egyptian Museum, Khan el-Khalili bazaar.'],['title'=>'Day 3-4: Luxor','description'=>'Valley of the Kings, Karnak Temple, Hatshepsut Temple, Nile cruise.'],['title'=>'Day 5: Aswan','description'=>'Philae Temple, felucca sailing, Nubian village visit.'],['title'=>'Day 6: Departure','description'=>'Flight back to Cairo, transfer to airport.']],
                'includes' => array_merge($defaultIncludes, ['Nile cruise (1 night)', 'Domestic flights']),
                'excludes' => $defaultExcludes,
            ],
            [
                'title' => 'Dubai & Abu Dhabi',
                'slug' => 'dubai-abu-dhabi',
                'region' => 'uae',
                'description' => "Experience the futuristic skyline of Dubai, desert safari adventures, luxury shopping, and the cultural gems of Abu Dhabi.\n\nFrom the world's tallest building to desert dunes, this tour showcases the best of the UAE.",
                'duration' => '5 Days',
                'price_from' => 680.00,
                'image_url' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&q=80','https://images.unsplash.com/photo-1518684079-3c830dcef090?w=800&q=80'],
                'itinerary' => [['title'=>'Day 1: Arrival & Marina','description'=>'Dubai Marina walk, dinner cruise.'],['title'=>'Day 2: Modern Dubai','description'=>'Burj Khalifa, Dubai Mall, Dubai Fountain show.'],['title'=>'Day 3: Desert Safari','description'=>'Dune bashing, camel rides, BBQ dinner under the stars.'],['title'=>'Day 4: Abu Dhabi','description'=>'Sheikh Zayed Grand Mosque, Louvre Abu Dhabi, Corniche.'],['title'=>'Day 5: Departure','description'=>'Free morning for shopping, airport transfer.']],
                'includes' => array_merge($defaultIncludes, ['Desert safari with BBQ', 'Burj Khalifa ticket']),
                'excludes' => $defaultExcludes,
            ],
            [
                'title' => 'Georgia Highlights',
                'slug' => 'georgia-highlights',
                'region' => 'georgia',
                'description' => "Discover Tbilisi's charming old town, the cave city of Vardzia, the wine region of Kakheti, and the mountain scenery of Kazbegi.\n\nJust a short trip from Armenia, Georgia offers incredible food, wine, and mountain adventures.",
                'duration' => '4 Days',
                'price_from' => 420.00,
                'image_url' => 'https://images.unsplash.com/photo-1573455494060-c5595004fb6c?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1573455494060-c5595004fb6c?w=800&q=80','https://images.unsplash.com/photo-1565008576549-57569a49371d?w=800&q=80'],
                'itinerary' => [['title'=>'Day 1: Tbilisi','description'=>'Old Town, Narikala Fortress, sulfur baths, Georgian feast.'],['title'=>'Day 2: Kakheti Wine Region','description'=>'Wine tasting, Sighnaghi "City of Love", Bodbe Monastery.'],['title'=>'Day 3: Kazbegi','description'=>'Military Highway, Ananuri fortress, Gergeti Trinity Church.'],['title'=>'Day 4: Departure','description'=>'Morning in Tbilisi, transfer to airport/border.']],
                'includes' => ['Transport from Yerevan', 'Hotel accommodation', 'English-speaking guide', 'Wine tasting', 'Breakfast daily'],
                'excludes' => ['Flights', 'Lunch & dinner', 'Personal expenses', 'Travel insurance'],
            ],
            [
                'title' => 'Turkey: East Meets West',
                'slug' => 'turkey-east-meets-west',
                'region' => 'turkey',
                'description' => "From Istanbul's historic mosques and bazaars to Cappadocia's fairy chimneys, Pamukkale's travertines, and the turquoise coast.\n\nA journey through centuries of Ottoman, Byzantine, and ancient history across one of the world's most fascinating countries.",
                'duration' => '7 Days',
                'price_from' => 720.00,
                'image_url' => 'https://images.unsplash.com/photo-1589561253898-768105ca91a8?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1589561253898-768105ca91a8?w=800&q=80','https://images.unsplash.com/photo-1541432901042-2d8bd64b4a9b?w=800&q=80','https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?w=800&q=80'],
                'itinerary' => [['title'=>'Day 1-2: Istanbul','description'=>'Hagia Sophia, Blue Mosque, Grand Bazaar, Bosphorus cruise.'],['title'=>'Day 3-4: Cappadocia','description'=>'Hot air balloon ride, fairy chimneys, underground cities, pottery workshop.'],['title'=>'Day 5: Pamukkale','description'=>'Cotton castle travertines, Hierapolis ancient city, thermal pools.'],['title'=>'Day 6: Antalya','description'=>'Old town, Düden Waterfalls, Mediterranean beaches.'],['title'=>'Day 7: Departure','description'=>'Free morning, transfer to airport.']],
                'includes' => array_merge($defaultIncludes, ['Domestic flights', 'Hot air balloon ride', 'Breakfast daily']),
                'excludes' => $defaultExcludes,
            ],
            [
                'title' => 'Thailand Paradise',
                'slug' => 'thailand-paradise',
                'region' => 'thailand',
                'description' => "Experience Bangkok's vibrant temples and street food, relax on Phuket's stunning beaches, and explore the cultural treasures of Chiang Mai.\n\nThailand offers the perfect blend of culture, cuisine, beaches, and adventure.",
                'duration' => '8 Days',
                'price_from' => 950.00,
                'image_url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80','https://images.unsplash.com/photo-1528181304800-259b08848526?w=800&q=80'],
                'itinerary' => [['title'=>'Day 1-2: Bangkok','description'=>'Grand Palace, Wat Pho, floating market, street food tour.'],['title'=>'Day 3-4: Chiang Mai','description'=>'Doi Suthep temple, elephant sanctuary, night bazaar, cooking class.'],['title'=>'Day 5-7: Phuket','description'=>'Phi Phi Islands, James Bond Island, Patong Beach, Thai massage.'],['title'=>'Day 8: Departure','description'=>'Free morning, transfer to airport.']],
                'includes' => array_merge($defaultIncludes, ['Domestic flights', 'Island hopping boat tour', 'Breakfast daily']),
                'excludes' => $defaultExcludes,
            ],
            [
                'title' => 'Italy: Rome, Florence & Venice',
                'slug' => 'italy-rome-florence-venice',
                'region' => 'italy',
                'description' => "Explore the eternal city of Rome, the Renaissance art of Florence, and the romantic canals of Venice.\n\nThis Italian journey covers the Colosseum, Vatican, Uffizi Gallery, Tuscan countryside, and Venetian gondolas.",
                'duration' => '7 Days',
                'price_from' => 1050.00,
                'image_url' => 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800&q=80','https://images.unsplash.com/photo-1534445867742-43195f401b6c?w=800&q=80'],
                'itinerary' => [['title'=>'Day 1-2: Rome','description'=>'Colosseum, Roman Forum, Vatican Museums, Sistine Chapel, Trevi Fountain.'],['title'=>'Day 3-4: Florence','description'=>'Uffizi Gallery, Duomo, Ponte Vecchio, Tuscan wine tasting.'],['title'=>'Day 5-6: Venice','description'=>'St. Mark\'s Square, gondola ride, Murano glass island, Venetian cuisine.'],['title'=>'Day 7: Departure','description'=>'Morning free time, transfer to airport.']],
                'includes' => array_merge($defaultIncludes, ['High-speed train tickets', 'Museum skip-the-line tickets', 'Breakfast daily']),
                'excludes' => $defaultExcludes,
            ],
            [
                'title' => 'Maldives Beach Escape',
                'slug' => 'maldives-beach-escape',
                'region' => 'maldives',
                'description' => "Crystal-clear turquoise waters, overwater bungalows, and pristine white sand beaches await in this ultimate tropical paradise.\n\nSnorkel with manta rays, enjoy spa treatments, and watch the sunset from your private deck.",
                'duration' => '5 Days',
                'price_from' => 1400.00,
                'image_url' => 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=800&q=80','https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=800&q=80'],
                'itinerary' => [['title'=>'Day 1: Arrival','description'=>'Seaplane transfer to resort, welcome cocktail, settle into overwater villa.'],['title'=>'Day 2-3: Ocean Adventures','description'=>'Snorkeling, diving, dolphin cruise, water sports, spa treatments.'],['title'=>'Day 4: Island Exploration','description'=>'Local island visit, fishing trip, sunset cruise with dinner.'],['title'=>'Day 5: Departure','description'=>'Breakfast, seaplane back to Malé, departure.']],
                'includes' => ['Flights from Yerevan', 'Overwater villa (4 nights)', 'Seaplane transfers', 'Full board meals', 'Snorkeling equipment', 'Sunset cruise'],
                'excludes' => ['Spa treatments', 'Scuba diving', 'Personal expenses', 'Travel insurance'],
            ],
        ];

        foreach ($tours as $tour) {
            Tour::create(array_merge($tour, [
                'type' => 'outgoing',
                'featured' => true,
                'status' => 'active',
            ]));
        }

        $this->command->info('Seeded ' . count($tours) . ' outgoing tours.');
    }
}
