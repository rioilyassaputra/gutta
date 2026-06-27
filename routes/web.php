<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Api\RegionController;
use Illuminate\Support\Facades\Route;

// ── PUBLIK ───────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('shop.products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('shop.products.show');
Route::get('/collections/{category:slug}', [ProductController::class, 'byCategory'])->name('shop.collections.show');

// Internal API proxy untuk RajaOngkir (tanpa auth, tapi API key aman di server)
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/regions/provinces', [RegionController::class, 'provinces'])->name('provinces');
    Route::get('/regions/cities', [RegionController::class, 'cities'])->name('cities');
    Route::post('/regions/cost', [RegionController::class, 'cost'])->name('cost');
});

// ── AUTH REQUIRED ────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('pages.dashboard.index');
    })->name('dashboard');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/orders/{order:order_number}', [OrderController::class, 'show'])->name('orders.show');
});

// Profile route dari Breeze
Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

// ── ADMIN ─────────────────────────────────────────────────────────────────────
Route::prefix('gutta-manage')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', Admin\ProductController::class);
    Route::get('categories', [Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}', [Admin\OrderController::class, 'update'])->name('orders.update');
    Route::post('orders/{order}/tracking', [Admin\OrderController::class, 'updateTracking'])->name('orders.tracking');
    Route::get('customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/export', [Admin\CustomerController::class, 'export'])->name('customers.export');
});

// ── WEBHOOK (publik tapi diverifikasi signature) ───────────────────────────
Route::post('/webhooks/midtrans', [WebhookController::class, 'midtrans'])
    ->name('webhooks.midtrans')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

require __DIR__.'/auth.php';
