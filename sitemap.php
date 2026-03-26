<?php
header('Content-Type: application/xml; charset=utf-8');
require_once 'includes/db.php';
require_once 'includes/functions.php';

$base = 'https://touristik.am/';

// Static pages
$pages = [
    ['loc' => '', 'priority' => '1.0', 'changefreq' => 'daily'],
    ['loc' => '?page=destinations', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['loc' => '?page=about', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => '?page=contact', 'priority' => '0.7', 'changefreq' => 'monthly'],
];

// Dynamic destination pages
$stmt = $pdo->query("SELECT id FROM destinations ORDER BY id");
$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $p): ?>
    <url>
        <loc><?= htmlspecialchars($base . $p['loc']) ?></loc>
        <changefreq><?= $p['changefreq'] ?></changefreq>
        <priority><?= $p['priority'] ?></priority>
    </url>
<?php endforeach; ?>
<?php foreach ($destinations as $dest): ?>
    <url>
        <loc><?= htmlspecialchars($base . '?page=destination&id=' . $dest['id']) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
<?php endforeach; ?>
</urlset>
