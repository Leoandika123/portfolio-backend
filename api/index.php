<?php

// 1. Paksa Laravel memakai folder /tmp bawaan Vercel untuk cache & views
$storagePath = '/tmp/storage/framework';
foreach (['/views', '/cache', '/sessions'] as $path) {
    if (!is_dir($storagePath . $path)) {
        mkdir($storagePath . $path, 0755, true);
    }
}

// 2. Daftarkan path baru ke environment server sebelum Laravel di-load
define('LARAVEL_START', microtime(true));
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// 3. Jalankan Laravel secara normal
require __DIR__ . '/../public/index.php';