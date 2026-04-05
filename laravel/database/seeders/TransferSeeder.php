<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;

class TransferSeeder extends Seeder
{
    public function run(): void
    {
        Tour::where('type', 'transfer')->delete();

        $tours = [
            [
                'title' => 'Airport Pickup — Zvartnots to Yerevan',
                'slug' => 'airport-pickup-zvartnots',
                'description' => "Comfortable, reliable airport transfer from Zvartnots International Airport (EVN) to your hotel in Yerevan. Our driver will meet you at arrivals with a name sign, help with your luggage, and take you directly to your accommodation.\n\nAvailable 24/7, including late-night and early-morning flights. Free waiting time up to 60 minutes after landing.",
                'duration' => '30-40 min',
                'price_from' => 15.00,
                'image_url' => 'https://images.unsplash.com/photo-1556388158-158ea5ccacbd?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1556388158-158ea5ccacbd?w=800&q=80','https://images.unsplash.com/photo-1549317661-bd32c8ce0afa?w=800&q=80'],
                'itinerary' => [['title'=>'Meet at Arrivals','description'=>'Driver with name sign waits at arrivals exit. Help with luggage.'],['title'=>'Direct Transfer','description'=>'Comfortable sedan or minivan to your hotel in Yerevan center (25-40 min).']],
                'includes' => ['Meet & greet at arrivals', 'Free 60-min waiting time', 'Luggage assistance', 'Bottled water', 'WiFi in vehicle'],
                'excludes' => ['Return transfer (book separately)', 'Tips'],
            ],
            [
                'title' => 'Airport Drop-off — Yerevan to Zvartnots',
                'slug' => 'airport-dropoff-zvartnots',
                'description' => "Stress-free transfer from your Yerevan hotel to Zvartnots International Airport. We pick you up on time so you never miss a flight.\n\nBook your departure transfer along with your trip for complete peace of mind.",
                'duration' => '30-40 min',
                'price_from' => 15.00,
                'image_url' => 'https://images.unsplash.com/photo-1436491865332-7a61a109db05?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1436491865332-7a61a109db05?w=800&q=80'],
                'itinerary' => [['title'=>'Hotel Pickup','description'=>'Driver arrives at your hotel lobby at the scheduled time.'],['title'=>'Airport Drop-off','description'=>'Direct drive to Zvartnots Airport departure terminal.']],
                'includes' => ['Hotel pickup', 'Luggage assistance', 'Bottled water'],
                'excludes' => ['Tips'],
            ],
            [
                'title' => 'Yerevan City Transfer',
                'slug' => 'yerevan-city-transfer',
                'description' => "Private car service within Yerevan for hotel changes, restaurant visits, business meetings, or sightseeing stops. Available by the hour or per trip.\n\nComfortable sedan for 1-3 passengers or spacious minivan for groups up to 7.",
                'duration' => 'Per trip',
                'price_from' => 8.00,
                'image_url' => 'https://images.unsplash.com/photo-1558972250-100afca53bde?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1558972250-100afca53bde?w=800&q=80'],
                'itinerary' => [['title'=>'Pickup','description'=>'Driver picks you up at your location within Yerevan.'],['title'=>'Transfer','description'=>'Direct transfer to your destination anywhere in Yerevan.']],
                'includes' => ['Door-to-door service', 'Professional driver', 'Air conditioning'],
                'excludes' => ['Waiting time over 15 min', 'Tips'],
            ],
            [
                'title' => 'Yerevan to Tbilisi Transfer',
                'slug' => 'yerevan-tbilisi-transfer',
                'description' => "Private transfer from Yerevan to Tbilisi, Georgia (or reverse). A scenic 5-hour drive through the Armenian and Georgian highlands with a stop at the border.\n\nOptional stops at Haghpat Monastery (UNESCO) or Sadakhlo duty-free.",
                'duration' => '5-6 hours',
                'price_from' => 120.00,
                'image_url' => 'https://images.unsplash.com/photo-1573455494060-c5595004fb6c?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1573455494060-c5595004fb6c?w=800&q=80'],
                'itinerary' => [['title'=>'Depart Yerevan','description'=>'Hotel pickup in Yerevan, drive north through the Debed Canyon.'],['title'=>'Optional Stop: Haghpat','description'=>'Visit the UNESCO-listed Haghpat Monastery (30-min stop, optional).'],['title'=>'Border Crossing','description'=>'Bagratashen-Sadakhlo border crossing. Driver assists with formalities.'],['title'=>'Arrive Tbilisi','description'=>'Drop-off at your hotel in Tbilisi center.']],
                'includes' => ['Hotel pickup & drop-off', 'Comfortable minivan', 'Border assistance', 'Bottled water'],
                'excludes' => ['Meals', 'Border fees (if any)', 'Optional sightseeing entry fees', 'Tips'],
            ],
            [
                'title' => 'Yerevan to Gyumri Transfer',
                'slug' => 'yerevan-gyumri-transfer',
                'description' => "Private transfer between Yerevan and Gyumri, Armenia's second-largest city. A comfortable 2-hour drive through the Armenian highlands.\n\nPerfect for travelers heading to Gyumri for sightseeing or continuing to the Georgian border.",
                'duration' => '2 hours',
                'price_from' => 55.00,
                'image_url' => 'https://images.unsplash.com/photo-1595867818082-083862f3d630?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1595867818082-083862f3d630?w=800&q=80'],
                'itinerary' => [['title'=>'Depart Yerevan','description'=>'Pickup from your hotel in Yerevan.'],['title'=>'Arrive Gyumri','description'=>'Drop-off at your hotel or desired location in Gyumri.']],
                'includes' => ['Door-to-door service', 'Professional driver', 'Air conditioning', 'Bottled water'],
                'excludes' => ['Meals', 'Tips'],
            ],
            [
                'title' => 'Yerevan to Sevan Transfer',
                'slug' => 'yerevan-sevan-transfer',
                'description' => "Private transfer from Yerevan to Lake Sevan area hotels and resorts. A scenic 1-hour drive through mountain passes with stunning views.\n\nIdeal for travelers heading to lakeside hotels, Sevanavank Monastery, or Dilijan.",
                'duration' => '1-1.5 hours',
                'price_from' => 35.00,
                'image_url' => 'https://images.unsplash.com/photo-1603921288457-0a30e11e7db8?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1603921288457-0a30e11e7db8?w=800&q=80'],
                'itinerary' => [['title'=>'Depart Yerevan','description'=>'Pickup from your hotel in Yerevan.'],['title'=>'Arrive Lake Sevan','description'=>'Drop-off at your lakeside hotel, Sevanavank, or Dilijan.']],
                'includes' => ['Door-to-door service', 'Professional driver', 'Air conditioning', 'Bottled water'],
                'excludes' => ['Return transfer (book separately)', 'Tips'],
            ],
            [
                'title' => 'VIP Chauffeur Service',
                'slug' => 'vip-chauffeur-service',
                'description' => "Premium chauffeur service with a luxury vehicle for business travelers, VIPs, or special occasions. Mercedes E-Class or similar.\n\nAvailable by the hour or full day. Perfect for business meetings, weddings, or luxury sightseeing.",
                'duration' => 'Hourly / Full Day',
                'price_from' => 25.00,
                'image_url' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0afa?w=600&q=80',
                'gallery' => ['https://images.unsplash.com/photo-1549317661-bd32c8ce0afa?w=800&q=80'],
                'itinerary' => [['title'=>'Hourly Rate','description'=>'$25/hour with professional driver. Minimum 3 hours.'],['title'=>'Full Day Rate','description'=>'$150 for up to 10 hours with Mercedes E-Class or similar.']],
                'includes' => ['Luxury vehicle', 'Professional uniformed driver', 'WiFi & bottled water', 'Flexible schedule'],
                'excludes' => ['Fuel surcharge for trips over 200km', 'Parking fees', 'Tips'],
            ],
        ];

        foreach ($tours as $tour) {
            Tour::create(array_merge($tour, [
                'type' => 'transfer',
                'featured' => true,
                'status' => 'active',
            ]));
        }

        $this->command->info('Seeded ' . count($tours) . ' transfer services.');
    }
}
