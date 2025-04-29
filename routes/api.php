<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SenderController;
use App\Http\Controllers\ScheduleController;

// 🟢 Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 🔒 Protected Routes (hanya bisa diakses setelah login)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/senders', [SenderController::class, 'store']);
    Route::post('/schedule', [ScheduleController::class, 'store']);
});
