<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/router.php';
require_once 'includes/currency.php';
require_once 'includes/flight_prices.php';

// Route the request
$router = new Router();
$page = isset($_GET['page']) ? $_GET['page'] : '';
$route = $router->resolve($page);

$pageTitle = $route['title'];
$currentPage = $route['page'];

// Load site name into title from settings
$siteName = getSetting($pdo, 'site_name', 'Wanderlust');
if ($currentPage === 'home') {
    $pageTitle = $siteName . ' - ' . getSetting($pdo, 'site_tagline', 'Discover the World');
}

// Render the page
require_once 'templates/header.php';
require $route['file'];
require_once 'templates/footer.php';
