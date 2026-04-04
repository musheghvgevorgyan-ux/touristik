<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DestinationController as AdminDestinationController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\PromoController as AdminPromoController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\TourController as AdminTourController;
use App\Http\Controllers\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\Agent\SearchController as AgentSearchController;
use App\Http\Controllers\Agent\BookingController as AgentBookingController;
use App\Http\Controllers\Agent\CommissionController as AgentCommissionController;

// Sitemap
Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
        ['loc' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['loc' => '/contact', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['loc' => '/destinations', 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['loc' => '/tours', 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['loc' => '/tours/ingoing', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['loc' => '/tours/outgoing', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['loc' => '/tours/transfer', 'priority' => '0.7', 'changefreq' => 'weekly'],
        ['loc' => '/hotels/search', 'priority' => '0.8', 'changefreq' => 'daily'],
    ];
    $destinations = \App\Models\Destination::all();
    foreach ($destinations as $d) {
        $urls[] = ['loc' => '/destinations/' . $d->slug, 'priority' => '0.7', 'changefreq' => 'weekly'];
    }
    $tours = \App\Models\Tour::all();
    foreach ($tours as $t) {
        $urls[] = ['loc' => '/tours/' . $t->slug, 'priority' => '0.7', 'changefreq' => 'weekly'];
    }
    $content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $url) {
        $content .= "  <url>\n";
        $content .= "    <loc>https://touristik.am{$url['loc']}</loc>\n";
        $content .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
        $content .= "    <priority>{$url['priority']}</priority>\n";
        $content .= "  </url>\n";
    }
    $content .= '</urlset>';
    return response($content, 200)->header('Content-Type', 'application/xml');
});

// Public pages
Route::get('/', [HomeController::class, 'index']);
Route::get('/about', [HomeController::class, 'about']);
Route::get('/contact', [ContactController::class, 'index']);
Route::post('/contact', [ContactController::class, 'send']);
Route::get('/destinations', [DestinationController::class, 'index']);
Route::get('/destinations/{slug}', [DestinationController::class, 'show']);
Route::get('/hotels/search', [HotelController::class, 'search']);
Route::get('/tours', [TourController::class, 'index']);
Route::get('/tours/ingoing', [TourController::class, 'ingoing']);
Route::get('/tours/outgoing', [TourController::class, 'outgoing']);
Route::get('/tours/transfer', [TourController::class, 'transfer']);
Route::get('/tours/{slug}', [TourController::class, 'show']);

// Auth (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'registerForm']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/register/agency', [AuthController::class, 'agencyRegisterForm']);
    Route::post('/register/agency', [AuthController::class, 'agencyRegister']);
    Route::get('/forgot-password', [AuthController::class, 'forgotForm']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::get('/reset-password/{token}', [AuthController::class, 'resetForm']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});
Route::get('/logout', [AuthController::class, 'logout']);

// Booking (no auth required — guest checkout)
Route::post('/booking/create/{type}/{id}', [BookingController::class, 'create']);
Route::get('/booking/create/{type}/{id}', [BookingController::class, 'create']);
Route::post('/booking/store', [BookingController::class, 'store']);
Route::get('/booking/{reference}', [BookingController::class, 'show']);
Route::post('/booking/{reference}/cancel', [BookingController::class, 'cancel']);

// Payment
Route::middleware('auth')->group(function () {
    Route::get('/payment/{reference}', [PaymentController::class, 'checkout']);
    Route::post('/payment/process', [PaymentController::class, 'process']);
});
Route::match(['get', 'post'], '/payment/callback', [PaymentController::class, 'callback']);

// Customer account (auth required)
Route::middleware('auth')->prefix('account')->group(function () {
    Route::get('/', [AccountController::class, 'dashboard']);
    Route::get('/profile', [AccountController::class, 'profile']);
    Route::post('/profile', [AccountController::class, 'updateProfile']);
    Route::get('/bookings', [AccountController::class, 'bookings']);
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'toggle']);
    Route::get('/reviews', [ReviewController::class, 'myReviews']);
    Route::post('/reviews', [ReviewController::class, 'store']);
});

// Admin panel
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index']);
    Route::get('/bookings', [AdminBookingController::class, 'index']);
    Route::get('/bookings/{id}', [AdminBookingController::class, 'show']);
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{id}', [AdminUserController::class, 'show']);
    Route::post('/users/{id}', [AdminUserController::class, 'update']);
    Route::get('/destinations', [AdminDestinationController::class, 'index']);
    Route::post('/destinations', [AdminDestinationController::class, 'store']);
    Route::get('/settings', [AdminSettingsController::class, 'index']);
    Route::post('/settings', [AdminSettingsController::class, 'update']);
    Route::get('/promos', [AdminPromoController::class, 'index']);
    Route::post('/promos', [AdminPromoController::class, 'store']);
    Route::get('/reviews', [AdminReviewController::class, 'index']);
    Route::post('/reviews/{id}', [AdminReviewController::class, 'moderate']);
    Route::get('/reports', [AdminReportController::class, 'index']);
    Route::get('/tours', [AdminTourController::class, 'index']);
    Route::post('/tours', [AdminTourController::class, 'store']);
    Route::post('/tours/delete', [AdminTourController::class, 'delete']);
});

// B2B Agent portal
Route::middleware(['auth', 'agent'])->prefix('agent')->group(function () {
    Route::get('/', [AgentDashboardController::class, 'index']);
    Route::get('/search', [AgentSearchController::class, 'index']);
    Route::post('/search', [AgentSearchController::class, 'results']);
    Route::get('/bookings', [AgentBookingController::class, 'index']);
    Route::get('/bookings/{id}', [AgentBookingController::class, 'show']);
    Route::get('/commission', [AgentCommissionController::class, 'index']);
});
