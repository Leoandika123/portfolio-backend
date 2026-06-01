<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// 1. Inisialisasi konfigurasi aplikasi seperti biasa
$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// 2. PINDAHKAN KE SINI: Paksa storage path setelah objek $app resmi dibuat
if (isset($_SERVER['VERCEL_URL']) || env('APP_ENV') === 'production') {
    $app->useStoragePath('/tmp');
}

// 3. Kembalikan instansiasi aplikasi
return $app;