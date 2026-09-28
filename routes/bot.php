<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebhookController;

Route::post('/webhook', [WebhookController::class, 'handle']);
