<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminController::class, 'login'])->name('login');
    Route::post('/admin/login', [AdminController::class, 'authenticate'])->name('login.authenticate');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::get('/whatsapp', [AdminController::class, 'whatsapp'])->name('whatsapp');
    Route::post('/whatsapp/reset', [AdminController::class, 'resetWhatsAppSession'])->name('whatsapp.reset');
    Route::post('/whatsapp/logout', [AdminController::class, 'logoutWhatsApp'])->name('whatsapp.logout');
    Route::get('/qris', [AdminController::class, 'qris'])->name('qris');
    Route::post('/qris', [AdminController::class, 'uploadQris'])->name('qris.upload');
    Route::get('/bot-messages', [AdminController::class, 'botMessages'])->name('bot-messages');
    Route::put('/bot-messages', [AdminController::class, 'saveBotMessages'])->name('bot-messages.update');
    Route::get('/store-profile', [AdminController::class, 'storeProfile'])->name('store-profile');
    Route::put('/store-profile', [AdminController::class, 'saveStoreProfile'])->name('store-profile.update');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/products', [AdminController::class, 'saveProduct'])->name('products.store');
    Route::put('/products/{product}', [AdminController::class, 'saveProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.destroy');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'saveCategory'])->name('categories.store');
    Route::put('/categories/{category}', [AdminController::class, 'saveCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('categories.destroy');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::put('/orders/{order}', [AdminController::class, 'updateOrder'])->name('orders.update');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'saveUser'])->name('users.store');
});

Route::prefix('bot')->group(function () {
    require base_path('routes/bot.php');
});
