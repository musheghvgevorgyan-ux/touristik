<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Top 10 Places to Visit in Armenia',
                'slug' => 'top-10-places-to-visit-in-armenia',
                'excerpt' => 'From ancient monasteries perched on cliffs to the stunning shores of Lake Sevan, discover the must-see destinations that make Armenia one of the most underrated travel gems in the world.',
                'image_url' => 'https://images.unsplash.com/photo-1668717096562-5a14dbf9454e?w=1200&q=80',
                'status' => 'published',
                'published_at' => now(),
                'user_id' => 1,
                'body' => <<<'HTML'
<p>Armenia is a land of ancient history, breathtaking landscapes, and warm hospitality. Whether you're a history buff, a nature lover, or a foodie, this small but mighty country has something extraordinary to offer. Here are the top 10 places you absolutely must visit.</p>

<h2>1. Garni Temple</h2>
<p>The only standing Greco-Roman colonnaded building in Armenia, Garni Temple dates back to the 1st century AD. Perched on the edge of a dramatic gorge, it offers stunning views of the surrounding mountains and the "Symphony of Stones" — a natural formation of hexagonal basalt columns that looks like a giant organ.</p>

<h2>2. Geghard Monastery</h2>
<p>A UNESCO World Heritage Site, Geghard Monastery is partially carved out of the adjacent mountain. Founded in the 4th century, it's one of the most atmospheric religious sites in the country. The acoustics inside the rock-hewn chambers are extraordinary — if you're lucky, you might hear monks chanting.</p>

<h2>3. Lake Sevan</h2>
<p>Known as the "Pearl of Armenia," Lake Sevan is one of the largest freshwater high-altitude lakes in the world. At 1,900 meters above sea level, its turquoise waters are surrounded by mountains and dotted with ancient monasteries. Visit the Sevanavank Monastery on the peninsula for panoramic views.</p>

<h2>4. Tatev Monastery & Wings of Tatev</h2>
<p>Ride the world's longest non-stop double-track cable car — the Wings of Tatev — for 12 breathtaking minutes over the Vorotan River gorge. At the other end awaits Tatev Monastery, a 9th-century masterpiece perched on the edge of a cliff in southern Armenia.</p>

<h2>5. Dilijan National Park</h2>
<p>Often called the "Armenian Switzerland," Dilijan is a lush, forested town surrounded by hiking trails, mineral springs, and charming architecture. Don't miss Haghartsin Monastery hidden in the forest, and take a stroll down the reconstructed Old Dilijan street.</p>

<h2>6. Noravank Monastery</h2>
<p>Nestled in a narrow gorge with red cliff walls, Noravank is one of the most photographed sites in Armenia. The 13th-century monastery is a masterpiece of medieval Armenian architecture, and the drive through the canyon to reach it is an adventure in itself.</p>

<h2>7. Yerevan — The Pink City</h2>
<p>Armenia's vibrant capital is built from pink volcanic tuff stone, giving it a warm, rosy glow. Explore the Cascade complex for contemporary art and city views, wander through the lively Vernissage market, visit the Matenadaran manuscript repository, and end your evening at a rooftop restaurant overlooking Mount Ararat.</p>

<h2>8. Khor Virap</h2>
<p>No image captures Armenia quite like Khor Virap Monastery with Mount Ararat rising behind it. This iconic monastery sits at the closest point to the Turkish border and marks the spot where Gregory the Illuminator was imprisoned for 13 years before converting Armenia to Christianity in 301 AD.</p>

<h2>9. Jermuk</h2>
<p>Armenia's premier spa town, Jermuk is famous for its hot mineral springs and the dramatic Jermuk Waterfall. The town sits at 2,000 meters altitude, surrounded by forests and mountains. It's the perfect place to relax, drink healing mineral water straight from the source, and enjoy nature walks.</p>

<h2>10. Amberd Fortress</h2>
<p>Perched at 2,300 meters on the slopes of Mount Aragats (Armenia's highest peak), Amberd is a 7th-century fortress that feels like it's floating in the clouds. On a clear day, the views are endless. Combine your visit with a drive to the summit of Aragats for an unforgettable day trip from Yerevan.</p>

<h2>Plan Your Trip</h2>
<p>Ready to explore Armenia? At Touristik Travel Club, we offer guided tours to all these destinations and more. Whether you want a private tour, a group excursion, or a custom itinerary, our team will make sure your Armenian adventure is unforgettable. <a href="/contact">Contact us</a> to start planning your trip today.</p>
HTML
            ],
            [
                'title' => 'How to Get a Visa to Armenia: Complete Guide 2026',
                'slug' => 'how-to-get-visa-to-armenia-2026',
                'excerpt' => 'Everything you need to know about visiting Armenia — visa-free countries, e-visa process, invitation letters, and how Touristik can handle all the paperwork for you.',
                'image_url' => 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=1200&q=80',
                'status' => 'published',
                'published_at' => now()->subHours(2),
                'user_id' => 1,
                'body' => <<<'HTML'
<p>Planning a trip to Armenia? Great news — Armenia has one of the most welcoming visa policies in the world. Here's everything you need to know about entry requirements for 2026.</p>

<h2>Visa-Free Entry (60+ Countries)</h2>
<p>Citizens of over 60 countries can enter Armenia <strong>without a visa</strong> for stays up to 180 days. This includes:</p>
<ul>
<li><strong>European Union</strong> — all 27 member states</li>
<li><strong>CIS countries</strong> — Russia, Georgia, Belarus, Kazakhstan, etc.</li>
<li><strong>Americas</strong> — USA, Canada, Brazil, Argentina, etc.</li>
<li><strong>Asia-Pacific</strong> — Japan, South Korea, Australia, etc.</li>
<li><strong>Middle East</strong> — UAE, Qatar, Kuwait, etc.</li>
</ul>
<p>If your country is on this list, you just need a valid passport and you're good to go!</p>

<h2>E-Visa (Online Application)</h2>
<p>If your country is not on the visa-free list, you can apply for an <strong>e-visa</strong> online. Here's how:</p>
<ol>
<li>Visit the official Armenian e-visa portal</li>
<li>Fill in the application form with your personal details</li>
<li>Upload a photo and passport scan</li>
<li>Pay the visa fee (approximately $6 for a 21-day visa or $31 for a 120-day visa)</li>
<li>Receive your e-visa by email within 2-3 business days</li>
</ol>
<p>Print the e-visa and present it at the border along with your passport.</p>

<h2>Visa on Arrival</h2>
<p>Most nationalities can also get a <strong>visa on arrival</strong> at Yerevan's Zvartnots International Airport. However, we recommend getting an e-visa in advance to avoid queues and potential issues.</p>

<h2>Invitation Letters</h2>
<p>Some countries require an <strong>official invitation letter</strong> from Armenia to apply for a visa at the embassy. At Touristik, we provide invitation letters for:</p>
<ul>
<li>Tourist visits</li>
<li>Business trips</li>
<li>Medical treatment</li>
<li>Family visits</li>
</ul>
<p>Our standard processing time is <strong>5-7 business days</strong>, with an express option available in <strong>2-3 days</strong>.</p>

<h2>Required Documents</h2>
<p>Depending on your nationality and visa type, you may need:</p>
<ul>
<li>Valid passport (at least 6 months validity remaining)</li>
<li>Completed visa application form</li>
<li>Passport-sized photo</li>
<li>Travel insurance</li>
<li>Hotel reservation or invitation letter</li>
<li>Proof of sufficient funds</li>
<li>Return flight ticket</li>
</ul>

<h2>Tips for a Smooth Entry</h2>
<ul>
<li><strong>Check your passport expiry</strong> — it should be valid for at least 6 months beyond your planned stay</li>
<li><strong>Keep a copy</strong> of your hotel booking and return ticket on your phone</li>
<li><strong>Currency</strong> — ATMs are available at the airport, but bringing some USD or EUR cash is a good idea</li>
<li><strong>Travel insurance</strong> — not mandatory but highly recommended</li>
</ul>

<h2>Let Touristik Handle Your Visa</h2>
<p>Not sure about your country's requirements? Don't want to deal with paperwork? Our visa department handles hundreds of applications every year. We'll take care of everything — from invitation letters to embassy submissions. <a href="/contact">Contact us</a> for a free consultation.</p>
HTML
            ],
            [
                'title' => 'Best Time to Visit Yerevan: A Seasonal Guide',
                'slug' => 'best-time-to-visit-yerevan',
                'excerpt' => 'Yerevan is beautiful year-round, but each season offers a different experience. Here is when to visit based on weather, events, prices, and what you want to do.',
                'image_url' => 'https://images.unsplash.com/photo-1603483080228-04f2313d9f10?w=1200&q=80',
                'status' => 'published',
                'published_at' => now()->subHours(4),
                'user_id' => 1,
                'body' => <<<'HTML'
<p>Yerevan, one of the world's oldest continuously inhabited cities, is a destination that rewards visitors in every season. But when is the <em>best</em> time to go? It depends on what you're looking for.</p>

<h2>Spring (April - May) — Best Overall</h2>
<p><strong>Temperature:</strong> 12-25°C</p>
<p>Spring is arguably the best time to visit Yerevan. The weather is warm but not hot, the apricot trees are in bloom, and Mount Ararat is still snow-capped — creating the iconic postcard view. The city comes alive with outdoor cafes, street musicians, and festivals.</p>
<p><strong>Why visit in spring:</strong></p>
<ul>
<li>Perfect weather for sightseeing and day trips</li>
<li>Apricot blossom season (April) — Armenia's national symbol</li>
<li>Fewer tourists than summer, better hotel rates</li>
<li>Easter celebrations with unique Armenian traditions</li>
</ul>

<h2>Summer (June - August) — Peak Season</h2>
<p><strong>Temperature:</strong> 25-40°C</p>
<p>Summer in Yerevan is hot — really hot. But this is also when the city is at its most vibrant. Outdoor concerts, wine festivals, and long warm evenings on Republic Square make summer unforgettable. Escape the heat with day trips to Lake Sevan or the cool forests of Dilijan.</p>
<p><strong>Why visit in summer:</strong></p>
<ul>
<li>Lake Sevan swimming and water sports</li>
<li>Yerevan Wine Days festival (usually June)</li>
<li>Longest daylight hours — more time for exploration</li>
<li>All mountain roads and hiking trails are accessible</li>
</ul>
<p><strong>Tip:</strong> Book accommodation early and bring sunscreen. Temperatures can exceed 40°C in July.</p>

<h2>Autumn (September - October) — Hidden Gem</h2>
<p><strong>Temperature:</strong> 15-28°C</p>
<p>Autumn might be the most underrated season. The scorching heat fades, the vineyards are heavy with grapes, and the mountains turn gold and red. September and October offer the perfect balance of warm days, cool evenings, and significantly fewer tourists.</p>
<p><strong>Why visit in autumn:</strong></p>
<ul>
<li>Wine harvest season — visit wineries and taste fresh wine</li>
<li>Areni Wine Festival (October)</li>
<li>Stunning fall foliage, especially in Dilijan and Tatev</li>
<li>Best prices on hotels and flights</li>
<li>Pomegranate season — you'll see them everywhere</li>
</ul>

<h2>Winter (November - March) — Budget Friendly</h2>
<p><strong>Temperature:</strong> -5 to 5°C</p>
<p>Winter transforms Yerevan into a cozy, atmospheric city. Christmas and New Year celebrations blend Armenian and European traditions. Snow-covered mountains create dramatic landscapes, and the ski resort of Tsaghkadzor is just an hour away.</p>
<p><strong>Why visit in winter:</strong></p>
<ul>
<li>Lowest prices of the year on everything</li>
<li>Skiing at Tsaghkadzor (December - March)</li>
<li>New Year celebrations are spectacular in Yerevan</li>
<li>Hot springs in Jermuk</li>
<li>Cozy wine bars and restaurants with hearty Armenian cuisine</li>
</ul>

<h2>Quick Comparison</h2>
<table style="width:100%;border-collapse:collapse;margin:1.5rem 0;">
<thead><tr style="background:#f8f9fa;"><th style="padding:0.8rem;border:1px solid #ddd;text-align:left;">Season</th><th style="padding:0.8rem;border:1px solid #ddd;">Temp</th><th style="padding:0.8rem;border:1px solid #ddd;">Crowds</th><th style="padding:0.8rem;border:1px solid #ddd;">Prices</th><th style="padding:0.8rem;border:1px solid #ddd;">Best For</th></tr></thead>
<tbody>
<tr><td style="padding:0.8rem;border:1px solid #ddd;"><strong>Spring</strong></td><td style="padding:0.8rem;border:1px solid #ddd;text-align:center;">12-25°C</td><td style="padding:0.8rem;border:1px solid #ddd;text-align:center;">Medium</td><td style="padding:0.8rem;border:1px solid #ddd;text-align:center;">Medium</td><td style="padding:0.8rem;border:1px solid #ddd;">Sightseeing, Photography</td></tr>
<tr><td style="padding:0.8rem;border:1px solid #ddd;"><strong>Summer</strong></td><td style="padding:0.8rem;border:1px solid #ddd;text-align:center;">25-40°C</td><td style="padding:0.8rem;border:1px solid #ddd;text-align:center;">High</td><td style="padding:0.8rem;border:1px solid #ddd;text-align:center;">High</td><td style="padding:0.8rem;border:1px solid #ddd;">Lake Sevan, Festivals</td></tr>
<tr><td style="padding:0.8rem;border:1px solid #ddd;"><strong>Autumn</strong></td><td style="padding:0.8rem;border:1px solid #ddd;text-align:center;">15-28°C</td><td style="padding:0.8rem;border:1px solid #ddd;text-align:center;">Low</td><td style="padding:0.8rem;border:1px solid #ddd;text-align:center;">Low</td><td style="padding:0.8rem;border:1px solid #ddd;">Wine, Hiking, Value</td></tr>
<tr><td style="padding:0.8rem;border:1px solid #ddd;"><strong>Winter</strong></td><td style="padding:0.8rem;border:1px solid #ddd;text-align:center;">-5 to 5°C</td><td style="padding:0.8rem;border:1px solid #ddd;text-align:center;">Low</td><td style="padding:0.8rem;border:1px solid #ddd;text-align:center;">Lowest</td><td style="padding:0.8rem;border:1px solid #ddd;">Skiing, Budget Travel</td></tr>
</tbody>
</table>

<h2>Ready to Visit?</h2>
<p>No matter when you come, Yerevan will surprise you with its warmth — both the weather and the people. At Touristik Travel Club, we plan trips year-round and know the best experiences for every season. <a href="/contact">Get in touch</a> and let us help you plan the perfect Yerevan getaway.</p>
HTML
            ],
        ];

        foreach ($posts as $data) {
            Post::updateOrCreate(['slug' => $data['slug']], $data);
        }

        echo "Seeded 3 blog posts.\n";
    }
}
