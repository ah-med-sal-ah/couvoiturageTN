<?php

use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\UsernameController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Public authentication endpoints.
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// Public so it works during registration, before an account exists.
Route::get('/username-availability', [UsernameController::class, 'check'])->middleware('throttle:20,1');

// Everything else requires a valid Sanctum API token.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);

    Route::get('/publications', [PublicationController::class, 'index']);
    Route::post('/publications', [PublicationController::class, 'store']);
    Route::get('/publications/{publication}', [PublicationController::class, 'show']);
    Route::patch('/publications/{publication}/reservation', [PublicationController::class, 'updateReservation']);

    Route::get('/locations', [LocationController::class, 'index']);

    // Administrator-only. `admin` runs after `auth:sanctum`, so this is a
    // real server-side authorization check - never just a frontend guard.
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
    });
});
