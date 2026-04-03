<?php

/**
 * Application Routes
 *
 * Available middleware:
 *   \App\Middleware\AuthMiddleware::class    — must be logged in
 *   \App\Middleware\RoleMiddleware::class    — requires specific role (set via param)
 *   \App\Middleware\CsrfMiddleware::class    — validates CSRF token on POST
 */

$router = $app->router();

// ─── Public Pages ───────────────────────────────────────────
$router->get('/',              \App\Controllers\HomeController::class,        'index');
$router->get('/about',         \App\Controllers\HomeController::class,        'about');
$router->get('/contact',       \App\Controllers\ContactController::class,     'index');
$router->post('/contact',      \App\Controllers\ContactController::class,     'send',    [\App\Middleware\CsrfMiddleware::class]);

// ─── Destinations ───────────────────────────────────────────
$router->get('/destinations',       \App\Controllers\DestinationController::class, 'index');
$router->get('/destinations/{slug}', \App\Controllers\DestinationController::class, 'show');

// ─── Hotels ─────────────────────────────────────────────────
$router->get('/hotels/search',   \App\Controllers\HotelController::class,  'search');
$router->post('/hotels/results', \App\Controllers\HotelController::class,  'results', [\App\Middleware\CsrfMiddleware::class]);
$router->get('/hotels/{code}',   \App\Controllers\HotelController::class,  'detail');

// ─── Tours ──────────────────────────────────────────────────
$router->get('/tours',           \App\Controllers\TourController::class,   'index');
$router->get('/tours/ingoing',   \App\Controllers\TourController::class,   'ingoing');
$router->get('/tours/outgoing',  \App\Controllers\TourController::class,   'outgoing');
$router->get('/tours/transfer',  \App\Controllers\TourController::class,   'transfer');
$router->get('/tours/{slug}',    \App\Controllers\TourController::class,   'show');

// ─── Auth ───────────────────────────────────────────────────
$router->get('/login',            \App\Controllers\AuthController::class,  'loginForm');
$router->post('/login',           \App\Controllers\AuthController::class,  'login',          [\App\Middleware\CsrfMiddleware::class]);
$router->get('/register',         \App\Controllers\AuthController::class,  'registerForm');
$router->post('/register',        \App\Controllers\AuthController::class,  'register',       [\App\Middleware\CsrfMiddleware::class]);
$router->get('/register/agency',  \App\Controllers\AuthController::class,  'agencyRegisterForm');
$router->post('/register/agency', \App\Controllers\AuthController::class,  'agencyRegister', [\App\Middleware\CsrfMiddleware::class]);
$router->get('/forgot-password',  \App\Controllers\AuthController::class,  'forgotForm');
$router->post('/forgot-password', \App\Controllers\AuthController::class,  'forgotPassword', [\App\Middleware\CsrfMiddleware::class]);
$router->get('/reset-password/{token}',  \App\Controllers\AuthController::class, 'resetForm');
$router->post('/reset-password',  \App\Controllers\AuthController::class,  'resetPassword',  [\App\Middleware\CsrfMiddleware::class]);
$router->get('/logout',           \App\Controllers\AuthController::class,  'logout');

// ─── Booking ────────────────────────────────────────────────
// POST create accepts hotel selection from search (no auth required to start)
$router->post('/booking/create/{type}/{id}', \App\Controllers\BookingController::class, 'create', [\App\Middleware\CsrfMiddleware::class]);
$router->get('/booking/create/{type}/{id}',  \App\Controllers\BookingController::class, 'create');
$router->post('/booking/store',              \App\Controllers\BookingController::class, 'store',  [\App\Middleware\CsrfMiddleware::class]);
$router->get('/booking/{reference}',         \App\Controllers\BookingController::class, 'show');
$router->post('/booking/{reference}/cancel', \App\Controllers\BookingController::class, 'cancel', [\App\Middleware\CsrfMiddleware::class]);

// ─── Payment ────────────────────────────────────────────────
$router->group(['prefix' => '/payment', 'middleware' => [\App\Middleware\AuthMiddleware::class]], function ($router) {
    $router->get('/{reference}',      \App\Controllers\PaymentController::class,   'checkout');
    $router->post('/process',         \App\Controllers\PaymentController::class,   'process',  [\App\Middleware\CsrfMiddleware::class]);
});
// Payment callback — no auth middleware (gateway calls this)
$router->get('/payment/callback',  \App\Controllers\PaymentController::class, 'callback');
$router->post('/payment/callback', \App\Controllers\PaymentController::class, 'callback');

// ─── Customer Account ───────────────────────────────────────
$router->get('/account', \App\Controllers\AccountController::class, 'dashboard', [\App\Middleware\AuthMiddleware::class]);
$router->group(['prefix' => '/account', 'middleware' => [\App\Middleware\AuthMiddleware::class]], function ($router) {
    $router->get('/profile',      \App\Controllers\AccountController::class,    'profile');
    $router->post('/profile',     \App\Controllers\AccountController::class,    'updateProfile', [\App\Middleware\CsrfMiddleware::class]);
    $router->get('/bookings',     \App\Controllers\AccountController::class,    'bookings');
    $router->get('/wishlist',     \App\Controllers\WishlistController::class,   'index');
    $router->post('/wishlist',    \App\Controllers\WishlistController::class,   'toggle',        [\App\Middleware\CsrfMiddleware::class]);
    $router->get('/reviews',      \App\Controllers\ReviewController::class,     'myReviews');
    $router->post('/reviews',     \App\Controllers\ReviewController::class,     'store',         [\App\Middleware\CsrfMiddleware::class]);
});

// ─── Admin Panel ────────────────────────────────────────────
$router->get('/admin', \App\Controllers\Admin\DashboardController::class, 'index', [\App\Middleware\AuthMiddleware::class, \App\Middleware\AdminMiddleware::class]);
$router->group(['prefix' => '/admin', 'middleware' => [\App\Middleware\AuthMiddleware::class, \App\Middleware\AdminMiddleware::class]], function ($router) {
    $router->get('/bookings',         \App\Controllers\Admin\BookingController::class,     'index');
    $router->get('/bookings/{id}',    \App\Controllers\Admin\BookingController::class,     'show');
    $router->get('/users',            \App\Controllers\Admin\UserController::class,        'index');
    $router->get('/users/{id}',       \App\Controllers\Admin\UserController::class,        'show');
    $router->post('/users/{id}',      \App\Controllers\Admin\UserController::class,        'update', [\App\Middleware\CsrfMiddleware::class]);
    $router->get('/destinations',     \App\Controllers\Admin\DestinationController::class, 'index');
    $router->post('/destinations',    \App\Controllers\Admin\DestinationController::class, 'store',  [\App\Middleware\CsrfMiddleware::class]);
    $router->get('/settings',         \App\Controllers\Admin\SettingsController::class,    'index');
    $router->post('/settings',        \App\Controllers\Admin\SettingsController::class,    'update', [\App\Middleware\CsrfMiddleware::class]);
    $router->get('/promos',           \App\Controllers\Admin\PromoController::class,       'index');
    $router->post('/promos',          \App\Controllers\Admin\PromoController::class,       'store',  [\App\Middleware\CsrfMiddleware::class]);
    $router->get('/reviews',          \App\Controllers\Admin\ReviewController::class,      'index');
    $router->post('/reviews/{id}',    \App\Controllers\Admin\ReviewController::class,      'moderate', [\App\Middleware\CsrfMiddleware::class]);
    $router->get('/reports',          \App\Controllers\Admin\ReportController::class,      'index');
    $router->get('/tours',            \App\Controllers\Admin\TourController::class,        'index');
    $router->post('/tours',           \App\Controllers\Admin\TourController::class,        'store',    [\App\Middleware\CsrfMiddleware::class]);
    $router->post('/tours/delete',    \App\Controllers\Admin\TourController::class,        'delete',   [\App\Middleware\CsrfMiddleware::class]);
});

// ─── B2B Agent Portal ───────────────────────────────────────
$router->get('/agent', \App\Controllers\Agent\DashboardController::class, 'index', [\App\Middleware\AuthMiddleware::class, \App\Middleware\AgentMiddleware::class]);
$router->group(['prefix' => '/agent', 'middleware' => [\App\Middleware\AuthMiddleware::class, \App\Middleware\AgentMiddleware::class]], function ($router) {
    $router->get('/search',       \App\Controllers\Agent\SearchController::class,    'index');
    $router->post('/search',      \App\Controllers\Agent\SearchController::class,    'results',  [\App\Middleware\CsrfMiddleware::class]);
    $router->get('/bookings',     \App\Controllers\Agent\BookingController::class,   'index');
    $router->get('/bookings/{id}',\App\Controllers\Agent\BookingController::class,   'show');
    $router->get('/commission',   \App\Controllers\Agent\CommissionController::class,'index');
});

// ─── API Endpoints ──────────────────────────────────────────
$router->group(['prefix' => '/api'], function ($router) {
    $router->get('/currency/rates',          \App\Controllers\Api\CurrencyApiController::class,      'rates');
    $router->get('/hotels/search',           \App\Controllers\Api\HotelApiController::class,         'search');
    $router->get('/notifications',           \App\Controllers\Api\NotificationApiController::class,  'index',    [\App\Middleware\AuthMiddleware::class]);
    $router->post('/notifications/{id}/read',\App\Controllers\Api\NotificationApiController::class,  'markRead', [\App\Middleware\AuthMiddleware::class]);
    $router->post('/wishlist/toggle',        \App\Controllers\Api\WishlistApiController::class,      'toggle',   [\App\Middleware\AuthMiddleware::class]);
});
