<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;

class IngoingToursSeeder extends Seeder
{
    public function run(): void
    {
        // Remove old ingoing tours
        Tour::where('type', 'ingoing')->delete();

        $tours = [
            [
                'title'       => 'Classic Yerevan City Tour',
                'slug'        => 'classic-yerevan-city',
                'region'      => 'yerevan',
                'description' => 'Walk through the heart of one of the world\'s oldest cities. Visit Republic Square, the Cascade, Vernissage market, and enjoy the stunning views of Mount Ararat.',
                'duration'    => '1 Day',
                'price_from'  => 35.00,
                'image_url'   => 'https://images.unsplash.com/photo-1558972250-100afca53bde?w=600&q=80',
            ],
            [
                'title'       => 'Garni Temple & Geghard Monastery',
                'slug'        => 'garni-geghard',
                'region'      => 'kotayk',
                'description' => 'Visit the only Greco-Roman pagan temple in the Caucasus and the UNESCO-listed Geghard Monastery, partially carved out of rock. Enjoy the scenic Azat River gorge.',
                'duration'    => '1 Day',
                'price_from'  => 40.00,
                'image_url'   => 'https://images.unsplash.com/photo-1600959907703-125ba1374a12?w=600&q=80',
            ],
            [
                'title'       => 'Lake Sevan & Sevanavank',
                'slug'        => 'lake-sevan-sevanavank',
                'region'      => 'gegharkunik',
                'description' => 'Visit the "Pearl of Armenia" — Lake Sevan, one of the largest high-altitude freshwater lakes in the world. Explore the medieval Sevanavank monastery perched on a peninsula.',
                'duration'    => '1 Day',
                'price_from'  => 45.00,
                'image_url'   => 'https://images.unsplash.com/photo-1603921288457-0a30e11e7db8?w=600&q=80',
            ],
            [
                'title'       => 'Khor Virap & Mount Ararat Views',
                'slug'        => 'khor-virap-ararat',
                'region'      => 'ararat',
                'description' => 'See the iconic Khor Virap monastery with Mount Ararat as its breathtaking backdrop. Learn about the origins of Armenian Christianity at this sacred pilgrimage site.',
                'duration'    => '1 Day',
                'price_from'  => 35.00,
                'image_url'   => 'https://images.unsplash.com/photo-1584646098378-0874589d76b1?w=600&q=80',
            ],
            [
                'title'       => 'Noravank Monastery & Areni Winery',
                'slug'        => 'noravank-areni',
                'region'      => 'vayots_dzor',
                'description' => 'Drive through stunning red rock canyons to the 13th-century Noravank monastery. Visit the Areni wine region, home to the world\'s oldest known winery.',
                'duration'    => '1 Day',
                'price_from'  => 50.00,
                'image_url'   => 'https://images.unsplash.com/photo-1569949237615-e1268f0b311b?w=600&q=80',
            ],
            [
                'title'       => 'Tatev Monastery & Wings of Tatev',
                'slug'        => 'tatev-monastery',
                'region'      => 'syunik',
                'description' => 'Ride the world\'s longest reversible aerial tramway to Tatev Monastery, perched on a cliff edge. Explore the medieval monastery complex and enjoy spectacular gorge views.',
                'duration'    => '2 Days',
                'price_from'  => 120.00,
                'image_url'   => 'https://images.unsplash.com/photo-1608746249753-9346e2dca7c0?w=600&q=80',
            ],
            [
                'title'       => 'Haghpat & Sanahin Monasteries',
                'slug'        => 'haghpat-sanahin',
                'region'      => 'lori',
                'description' => 'Explore two UNESCO World Heritage monasteries set in the lush forests of northern Armenia. Marvel at masterpieces of medieval Armenian architecture dating back to the 10th century.',
                'duration'    => '1 Day',
                'price_from'  => 55.00,
                'image_url'   => 'https://images.unsplash.com/photo-1597922187945-f300350e9097?w=600&q=80',
            ],
            [
                'title'       => 'Dilijan National Park & Goshavank',
                'slug'        => 'dilijan-goshavank',
                'region'      => 'tavush',
                'description' => 'Discover the "Armenian Switzerland" — Dilijan\'s stunning forests, crystal-clear lakes, and the beautifully carved Goshavank monastery complex.',
                'duration'    => '1 Day',
                'price_from'  => 50.00,
                'image_url'   => 'https://images.unsplash.com/photo-1580137189272-c9379f8864fd?w=600&q=80',
            ],
            [
                'title'       => 'Mount Aragats & Amberd Fortress',
                'slug'        => 'aragats-amberd',
                'region'      => 'aragatsotn',
                'description' => 'Ascend Armenia\'s highest peak and visit the 7th-century Amberd fortress perched on a cliff between two gorges. Enjoy alpine meadows and breathtaking panoramic views.',
                'duration'    => '1 Day',
                'price_from'  => 45.00,
                'image_url'   => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=600&q=80',
            ],
            [
                'title'       => 'Gyumri Heritage Walk',
                'slug'        => 'gyumri-heritage',
                'region'      => 'shirak',
                'description' => 'Explore Armenia\'s second-largest city with its unique black tufa architecture, vibrant arts scene, and charming old quarter. Visit the Sev Berd fortress and local craft workshops.',
                'duration'    => '1 Day',
                'price_from'  => 40.00,
                'image_url'   => 'https://images.unsplash.com/photo-1595867818082-083862f3d630?w=600&q=80',
            ],
            [
                'title'       => 'Echmiadzin Cathedral & Zvartnots',
                'slug'        => 'echmiadzin-zvartnots',
                'region'      => 'armavir',
                'description' => 'Visit the spiritual heart of Armenia — the Holy See of Echmiadzin, the oldest cathedral in the world. Explore the ruins of the 7th-century Zvartnots Cathedral.',
                'duration'    => '1 Day',
                'price_from'  => 35.00,
                'image_url'   => 'https://images.unsplash.com/photo-1560969184-10fe8719e047?w=600&q=80',
            ],
            [
                'title'       => 'Southern Armenia Adventure',
                'slug'        => 'southern-armenia-adventure',
                'region'      => 'syunik',
                'description' => 'A multi-day journey through Armenia\'s southern highlights: Tatev Monastery, the Wings of Tatev aerial tramway, Jermuk waterfall, and the ancient caves of Khndzoresk.',
                'duration'    => '3 Days',
                'price_from'  => 180.00,
                'image_url'   => 'https://images.unsplash.com/photo-1565073182887-6bcefbe225b1?w=600&q=80',
            ],
            [
                'title'       => 'Grand Tour of Armenia',
                'slug'        => 'grand-tour-armenia',
                'region'      => 'yerevan',
                'description' => 'The ultimate Armenia experience covering all major highlights: Yerevan, Garni, Geghard, Sevan, Dilijan, Tatev, Noravank, wine tasting in Areni, and more.',
                'duration'    => '7 Days',
                'price_from'  => 650.00,
                'image_url'   => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=600&q=80',
            ],
            [
                'title'       => 'Khndzoresk Cave Village',
                'slug'        => 'khndzoresk-caves',
                'region'      => 'syunik',
                'description' => 'Cross the swinging bridge over the dramatic gorge to explore the abandoned cave village of Old Khndzoresk. A surreal landscape of rock pillars and ancient dwellings.',
                'duration'    => '1 Day',
                'price_from'  => 55.00,
                'image_url'   => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&q=80',
            ],
        ];

        foreach ($tours as $tour) {
            Tour::create(array_merge($tour, [
                'type'     => 'ingoing',
                'featured' => true,
                'status'   => 'active',
            ]));
        }

        $this->command->info('Seeded ' . count($tours) . ' ingoing tours with regions.');
    }
}
