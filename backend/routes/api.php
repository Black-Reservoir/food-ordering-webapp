<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware('throttle:5,1')
    ->name('password.forgot');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->name('password.reset');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
        ->middleware(['throttle:6,1'])->name('verification.send');;
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->middleware(['signed'])->name('verification.verify');

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware(['verified'])->group(function () {
        // Admin routes
        Route::middleware('role:admin')->group(function () {
            // Route::get('/admin/dashboard', [AdminController::class, 'index']);
        });

        // Customer routes
        Route::middleware('role:customer')->group(function () {
            // Route::get('/customer/orders', [CustomerController::class, 'orders']);
        });

        // Restaurant routes
        Route::middleware('role:restaurant')->group(function () {
            // Route::get('/restaurant/menu', [RestaurantController::class, 'menu']);
        });
    });
});
