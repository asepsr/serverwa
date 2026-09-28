<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\BotSettingController;

Route::middleware('bot.auth')->group(function () {
    Route::get('customers/{waId}/orders', [OrderController::class, 'myOrders']);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('payments', \App\Http\Controllers\Api\PaymentController::class);
    Route::post('webhook', [WebhookController::class, 'handle']);
    Route::get('bot-settings', [BotSettingController::class, 'index']);
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
});

// ------------------------------------------------------------------
// Mobile API v1 (Flutter) — token-based via Sanctum.
// Pintu masuk terpisah dari bot: TIDAK memakai X-Bot-API-Key,
// melainkan header "Authorization: Bearer <token>".
// Logika bisnis (produk/order/payment) memakai model & service yang sama.
// ------------------------------------------------------------------
Route::prefix('v1')->group(function () {
    Route::post('auth/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);
    Route::post('auth/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login']);
    Route::get('products', [\App\Http\Controllers\Api\V1\ProductController::class, 'index']);
    Route::get('products/{id}', [\App\Http\Controllers\Api\V1\ProductController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
        Route::get('me', [\App\Http\Controllers\Api\V1\AuthController::class, 'me']);
        Route::get('orders', [\App\Http\Controllers\Api\V1\OrderController::class, 'index']);
        Route::post('orders', [\App\Http\Controllers\Api\V1\OrderController::class, 'store']);
        Route::get('orders/{id}', [\App\Http\Controllers\Api\V1\OrderController::class, 'show']);
        Route::get('payments', [\App\Http\Controllers\Api\V1\PaymentController::class, 'index']);
        Route::post('payments', [\App\Http\Controllers\Api\V1\PaymentController::class, 'store']);
        Route::get('payments/{id}', [\App\Http\Controllers\Api\V1\PaymentController::class, 'show']);
    });
});
