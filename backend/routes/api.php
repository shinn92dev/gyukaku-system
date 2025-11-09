<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AvailabilitySubmissionController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    /* Auth */
    Route::post('/logout', [AuthController::class, 'logout']);

    /* Resources */
    Route::apiResource('/users', UserController::class);

    /* Availability Submissions */
    Route::get('/availability-submissions', [AvailabilitySubmissionController::class, 'index']);
    Route::get('/availability-submissions/{id}', [AvailabilitySubmissionController::class, 'show']);
    Route::post('/availability-submissions', [AvailabilitySubmissionController::class, 'store']);
    Route::patch('/availability-submissions/{id}', [AvailabilitySubmissionController::class, 'update']);
});

// Protected admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    /* Auth */
    Route::post('/register', [AuthController::class, 'register']);
    
    /* Availability Submissions */
    Route::delete('/availability-submissions/{id}', [AvailabilitySubmissionController::class, 'destroy']);
});

Route::get('/test', TestController::class)->name('test');
// Route::apiResource('test', TestController::class)
