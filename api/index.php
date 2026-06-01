<?php

// 1. Paksa PHP untuk menampilkan error langsung ke layar browser
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Jalankan Laravel secara normal
require __DIR__ . '/../public/index.php';