<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\BotAuthMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
            // Permintaan datang via Cloudflare Tunnel (cloudflared di localhost),
            // jadi percayai header X-Forwarded-* agar https terbaca benar.
            $middleware->trustProxies(at: '*');
            $middleware->alias([
                'bot.auth' => BotAuthMiddleware::class,
                'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
