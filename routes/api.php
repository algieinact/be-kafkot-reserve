<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\TableController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes - No authentication required
Route::prefix('v1')->group(function () {
    
    // Menu endpoints
    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/menus/{id}', [MenuController::class, 'show']);
    
    // Bank accounts (for payment info)
    Route::get('/bank-accounts/active', [BankAccountController::class, 'active']);
    
    // Table availability
    Route::post('/tables/check-availability', [TableController::class, 'checkAvailability']);
    
    // Reservation endpoints
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations/{bookingCode}', [ReservationController::class, 'show']);
    Route::post('/reservations/{id}/upload-payment', [ReservationController::class, 'uploadPaymentProof']);
    
    // Authentication
    Route::post('/auth/login', [AuthController::class, 'login']);
});

// Protected routes - Require authentication
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Auth endpoints
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    
    // Admin routes
    Route::prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard/stats', [\App\Http\Controllers\Api\Admin\DashboardController::class, 'stats']);
        
        // Reservation Management
        Route::get('/reservations', [\App\Http\Controllers\Api\Admin\ReservationManagementController::class, 'index']);
        Route::get('/reservations/{id}', [\App\Http\Controllers\Api\Admin\ReservationManagementController::class, 'show']);
        Route::post('/reservations/{id}/verify', [\App\Http\Controllers\Api\Admin\ReservationManagementController::class, 'verifyPayment']);
        Route::post('/reservations/{id}/reject', [\App\Http\Controllers\Api\Admin\ReservationManagementController::class, 'rejectPayment']);
        Route::patch('/reservations/{id}/complete', [\App\Http\Controllers\Api\Admin\ReservationManagementController::class, 'complete']);
        Route::delete('/reservations/{id}', [\App\Http\Controllers\Api\Admin\ReservationManagementController::class, 'cancel']);
    });
});
