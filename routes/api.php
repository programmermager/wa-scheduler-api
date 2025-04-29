<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
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

    Route::controller(ContactController::class)->group(function () {
        Route::get('/contact', 'index');
        Route::get('/contact/{id}', 'show');
        Route::post('/contact/store', 'store');
        Route::post('/contact/update/{id}', 'update');
        Route::delete('/contact/delete/{id}', 'destroy');
    });

    Route::controller(SenderController::class)->group(function () {
        Route::get('/sender', 'index');
        Route::get('/sender/{id}', 'show');
        Route::post('/sender/store', 'store');
        Route::post('/sender/update/{id}', 'update');
        Route::delete('/sender/delete/{id}', 'destroy');
    });
});
