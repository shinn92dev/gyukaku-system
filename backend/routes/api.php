<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\UserController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Resources
    Route::apiResource('/users', UserController::class);
});

// Protected admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/test', TestController::class)->name('test');
// Route::apiResource('test', TestController::class)
