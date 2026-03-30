<?php
// Session security
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', '1');
}
session_start();

// Security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/router.php';
require_once 'includes/currency.php';
require_once 'includes/flight_prices.php';
require_once 'includes/hotelbeds.php';

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

// Handle redirects before any output
if ($currentPage === 'logout') {
    session_destroy();
    header('Location: ' . url('home'));
    exit;
}
if ($currentPage === 'login' && isAdmin()) {
    header('Location: ' . url('admin'));
    exit;
}
if ($currentPage === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit']) && verifyCsrf()) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    if (checkLoginRate() && loginAdmin($pdo, $username, $password)) {
        header('Location: ' . url('admin'));
        exit;
    }
}

// Render the page
require_once 'templates/header.php';
require $route['file'];
require_once 'templates/footer.php';
