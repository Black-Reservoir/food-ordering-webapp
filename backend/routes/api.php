<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware('throttle:5,1')
    ->name('password.forgot');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->middleware('throttle:5,1')
    ->name('password.reset');

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->middleware(['signed'])->name('verification.verify');

Route::middleware(['auth:sanctum'])->group(function () {

    // Authentication routes
    Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
        ->middleware(['throttle:6,1'])->name('verification.send');;

    Route::post('/logout', [AuthController::class, 'logout']);

    // Protected routes for authenticated users
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

    // Profile routes for authenticated users
    Route::get('/me', [ProfileController::class, 'show']);
    Route::patch('/me', [ProfileController::class, 'update']);
    Route::post('/me/profile-picture', [ProfileController::class, 'uploadProfilePicture']);
});
