<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CurrencyApiController;
use App\Http\Controllers\Api\HotelApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\WishlistApiController;

Route::get('/currency/rates', [CurrencyApiController::class, 'rates']);
Route::get('/hotels/search', [HotelApiController::class, 'search']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationApiController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationApiController::class, 'markRead']);
    Route::post('/wishlist/toggle', [WishlistApiController::class, 'toggle']);
});
