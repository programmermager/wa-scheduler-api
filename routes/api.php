<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SenderController;
use App\Http\Controllers\ScheduleController;

// 🟢 Public Routes
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// // 🔒 Protected Routes (hanya bisa diakses setelah login)
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);

//     // Endpoint untuk tambah nomor pengirim dan token
//     Route::post('/senders', [SenderController::class, 'store']);

//     // Endpoint untuk menjadwalkan pesan
//     Route::post('/schedule', [ScheduleController::class, 'store']);
// });
