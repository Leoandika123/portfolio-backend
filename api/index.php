<?php

// 1. Paksa Laravel memakai folder /tmp bawaan Vercel untuk cache & views
$storagePath = '/tmp/storage/framework';
foreach (['/views', '/cache', '/sessions'] as $path) {
    if (!is_dir($storagePath . $path)) {
        mkdir($storagePath . $path, 0755, true);
    }
}

// 2. Masukkan Environment Variables langsung lewat kode PHP
$_ENV['APP_DEBUG'] = 'true';
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = ':memory:';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// 3. Jalankan Laravel secara normal (LARAVEL_START didefinisikan di dalam file ini)
require __DIR__ . '/../public/index.php';