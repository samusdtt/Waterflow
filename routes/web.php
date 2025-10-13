<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Client routes
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
    Route::get('/products', [ClientController::class, 'products'])->name('products');
    Route::get('/cart', [ClientController::class, 'cart'])->name('cart');
    Route::post('/cart/add', [ClientController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update', [ClientController::class, 'updateCart'])->name('cart.update');
    Route::post('/cart/remove', [ClientController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/checkout', [ClientController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [ClientController::class, 'processOrder'])->name('checkout.process');
    Route::get('/orders', [ClientController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [ClientController::class, 'orderDetails'])->name('orders.show');
    Route::get('/profile', [ClientController::class, 'profile'])->name('profile');
    Route::post('/profile', [ClientController::class, 'updateProfile'])->name('profile.update');
});

// Staff routes
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
    Route::get('/weekly-deliveries', [StaffController::class, 'weeklyDeliveries'])->name('weekly-deliveries');
    Route::get('/orders', [StaffController::class, 'orders'])->name('orders');
    Route::post('/orders/{order}/delivered', [StaffController::class, 'markDelivered'])->name('orders.delivered');
    Route::post('/orders/{order}/payment-request', [StaffController::class, 'requestPaymentVerification'])->name('orders.payment-request');
    Route::get('/login-hours', [StaffController::class, 'loginHours'])->name('login-hours');
    Route::post('/clock-in', [StaffController::class, 'clockIn'])->name('clock-in');
    Route::post('/clock-out', [StaffController::class, 'clockOut'])->name('clock-out');
    Route::get('/profile', [StaffController::class, 'profile'])->name('profile');
    Route::post('/profile', [StaffController::class, 'updateProfile'])->name('profile.update');
});

// Admin routes
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/daily-orders', [AdminController::class, 'dailyOrders'])->name('daily-orders');
    Route::get('/client-data', [AdminController::class, 'clientData'])->name('client-data');
    Route::get('/staff-management', [AdminController::class, 'staffManagement'])->name('staff-management');
    Route::get('/daily-accounts', [AdminController::class, 'dailyAccounts'])->name('daily-accounts');
    Route::post('/daily-accounts', [AdminController::class, 'updateDailyAccount'])->name('daily-accounts.update');
    Route::get('/jar-records', [AdminController::class, 'jarRecords'])->name('jar-records');
    Route::post('/jar-records', [AdminController::class, 'updateJarRecord'])->name('jar-records.update');
    Route::post('/orders/{order}/payment-received', [AdminController::class, 'markPaymentReceived'])->name('orders.payment-received');
});

// Supplier Admin routes
Route::middleware(['auth', 'role:supplier_admin'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/daily-orders', [AdminController::class, 'dailyOrders'])->name('daily-orders');
    Route::get('/client-data', [AdminController::class, 'clientData'])->name('client-data');
    Route::get('/staff-management', [AdminController::class, 'staffManagement'])->name('staff-management');
    Route::get('/daily-accounts', [AdminController::class, 'dailyAccounts'])->name('daily-accounts');
    Route::post('/daily-accounts', [AdminController::class, 'updateDailyAccount'])->name('daily-accounts.update');
    Route::get('/jar-records', [AdminController::class, 'jarRecords'])->name('jar-records');
    Route::post('/jar-records', [AdminController::class, 'updateJarRecord'])->name('jar-records.update');
    Route::post('/orders/{order}/payment-received', [AdminController::class, 'markPaymentReceived'])->name('orders.payment-received');
});