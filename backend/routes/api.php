<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AvailabilitySubmissionController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    /* Auth */
    Route::post('/logout', [AuthController::class, 'logout']);

    /* Users */
    Route::apiResource('/users', UserController::class);

    /* Availability Submissions */
    Route::get('/availability-submissions', [AvailabilitySubmissionController::class, 'index']);
    Route::get('/availability-submissions/{id}', [AvailabilitySubmissionController::class, 'show']);
    Route::post('/availability-submissions', [AvailabilitySubmissionController::class, 'store']);
    Route::patch('/availability-submissions/{id}', [AvailabilitySubmissionController::class, 'update']);

    /* Schedule Periods */
    Route::get('/schedule-periods', [ScheduleController::class, 'schedulePeriods']);
    Route::get('/schedules/current', [ScheduleController::class, 'currentPeriod']);

    /* Schedule */
    Route::get('/schedules/today', [ScheduleController::class, 'today']);
    Route::get('/schedules/range', [ScheduleController::class, 'getByDateRange']);
    Route::get('/schedules/{id}', [ScheduleController::class, 'show']);

    /* Shifts */
    Route::post('/shifts/clock/{action}', [ShiftController::class, 'clockAction'])
        ->whereIn('action', ['clock-in', 'clock-out', 'break-start', 'break-end']);
});

// Protected admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    /* Auth */
    Route::post('/register', [AuthController::class, 'register']);

    /* Availability Submissions */
    Route::delete('/availability-submissions/{id}', [AvailabilitySubmissionController::class, 'destroy']);

    /* Schedule Periods */
    Route::post('/schedules/new-period', [ScheduleController::class, 'createNewPeriod']);

    /* Schedules */
    Route::get('/schedules', [ScheduleController::class, 'index']);
    Route::post('/schedules', [ScheduleController::class, 'store']);
    Route::patch('/schedules/{id}', [ScheduleController::class, 'update']);
    Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);

    /* Shifts */
    Route::post('/shifts/{userId}/clock/{action}', [ShiftController::class, 'clockActionForUser'])
        ->whereIn('action', ['clock-in', 'clock-out', 'break-start', 'break-end'])
        ->whereNumber('userId');
    Route::delete('/shifts/{id}', [ShiftController::class, 'destroy']);
});

Route::get('/test', TestController::class)->name('test');
// Route::apiResource('test', TestController::class)
