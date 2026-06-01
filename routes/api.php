<?php

use App\Http\Controllers\AIChatController;
use Illuminate\Support\Facades\Route;

// ✨ PASTIKAN DITULIS DALAM ARRAY BERSAMA NAMA FUNGSI 'chat'
Route::post('/ai-chat', [AIChatController::class, 'chat']);