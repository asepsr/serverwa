<?php
return [
    'webhook_url' => env('BOT_WEBHOOK_URL', 'http://localhost:8000/bot/webhook'),
    'service_url' => env('BOT_SERVICE_URL', 'http://localhost:3000'),
    'api_base_url' => env('APP_URL', 'http://localhost:8000'),
];
