<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API routes for mobile app
Route::middleware('auth:sanctum')->group(function () {
    // Profile routes
    Route::get('/profile', [ApiController::class, 'profile']);
    Route::post('/profile', [ApiController::class, 'updateProfile']);
    
    // Product routes
    Route::get('/products', [ApiController::class, 'products']);
    
    // Order routes
    Route::post('/orders', [ApiController::class, 'createOrder']);
    Route::get('/orders', [ApiController::class, 'orders']);
    Route::get('/orders/{order}', [ApiController::class, 'orderDetails']);
    
    // Staff routes
    Route::get('/staff/orders', [ApiController::class, 'staffOrders']);
    Route::post('/staff/orders/{order}/delivered', [ApiController::class, 'markDelivered']);
    Route::post('/staff/clock-in', [ApiController::class, 'clockIn']);
    Route::post('/staff/clock-out', [ApiController::class, 'clockOut']);
    
    // Notification routes
    Route::get('/notifications', [ApiController::class, 'notifications']);
    Route::post('/notifications/{notification}/read', [ApiController::class, 'markNotificationRead']);
});