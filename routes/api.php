<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\Admin\VariationGroupController;
use App\Http\Controllers\Api\Admin\VariationOptionController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes - No authentication required
// Menu endpoints
Route::get('/menus', [MenuController::class, 'index']);
Route::get('/menus/{id}', [MenuController::class, 'show']);

// Bank accounts (for payment info)
Route::get('/bank-accounts/active', [BankAccountController::class, 'active']);

// Banners (public - active only)
Route::get('/banners', [BannerController::class, 'index']);

// Categories (public)
Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);


// Table availability
Route::post('/tables/check-availability', [TableController::class, 'checkAvailability']);
Route::post('/tables/availability-status', [TableController::class, 'getTablesWithAvailability']);

// Table types (public - for reservation form)
Route::get('/table-types', [\App\Http\Controllers\Api\Admin\TableManagementController::class, 'getTableTypes']);


// Reservation endpoints
Route::post('/reservations', [ReservationController::class, 'store']);
Route::get('/reservations/{bookingCode}', [ReservationController::class, 'show']);
Route::post('/reservations/{id}/upload-payment', [ReservationController::class, 'uploadPaymentProof']);

// Authentication
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes - Require authentication
Route::middleware('auth:sanctum')->group(function () {

    // Auth endpoints
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Admin routes
    Route::prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard/stats', [\App\Http\Controllers\Api\Admin\DashboardController::class, 'stats']);

        // Menu Management
        Route::get('/menus', [\App\Http\Controllers\Api\Admin\MenuManagementController::class, 'index']);
        Route::get('/menus/{id}', [\App\Http\Controllers\Api\Admin\MenuManagementController::class, 'show']);
        Route::post('/menus', [\App\Http\Controllers\Api\Admin\MenuManagementController::class, 'store']);
        Route::put('/menus/{id}', [\App\Http\Controllers\Api\Admin\MenuManagementController::class, 'update']);
        Route::delete('/menus/{id}', [\App\Http\Controllers\Api\Admin\MenuManagementController::class, 'destroy']);
        Route::patch('/menus/{id}/toggle-availability', [\App\Http\Controllers\Api\Admin\MenuManagementController::class, 'toggleAvailability']);

        // Table Management
        Route::get('/tables', [TableController::class, 'index']);
        Route::post('/tables', [TableController::class, 'store']);
        Route::put('/tables/{id}', [TableController::class, 'update']);
        Route::delete('/tables/{id}', [TableController::class, 'destroy']);
        Route::put('/tables/{id}/position', [TableController::class, 'updatePosition']);

        // Table Types
        Route::get('/table-types', [\App\Http\Controllers\Api\Admin\TableManagementController::class, 'getTableTypes']);

        Route::patch('/tables/{id}/status', [\App\Http\Controllers\Api\Admin\TableManagementController::class, 'updateStatus']);
        Route::get('/table-types', [\App\Http\Controllers\Api\Admin\TableManagementController::class, 'getTableTypes']);

        // Reservation Management
        Route::get('/reservations', [\App\Http\Controllers\Api\Admin\ReservationManagementController::class, 'index']);
        Route::get('/reservations/{id}', [\App\Http\Controllers\Api\Admin\ReservationManagementController::class, 'show']);
        Route::post('/reservations/{id}/verify', [\App\Http\Controllers\Api\Admin\ReservationManagementController::class, 'verifyPayment']);
        Route::post('/reservations/{id}/reject', [\App\Http\Controllers\Api\Admin\ReservationManagementController::class, 'rejectPayment']);
        Route::patch('/reservations/{id}/complete', [\App\Http\Controllers\Api\Admin\ReservationManagementController::class, 'complete']);
        Route::delete('/reservations/{id}', [\App\Http\Controllers\Api\Admin\ReservationManagementController::class, 'cancel']);

        // Banner Management (Admin only)
        Route::get('/banners', [BannerController::class, 'adminIndex']);
        Route::post('/banners', [BannerController::class, 'store']);
        Route::get('/banners/{id}', [BannerController::class, 'show']);
        Route::put('/banners/{id}', [BannerController::class, 'update']);
        Route::delete('/banners/{id}', [BannerController::class, 'destroy']);

        // Category Management (Admin only)
        Route::get('/categories', [\App\Http\Controllers\Api\Admin\CategoryManagementController::class, 'index']);
        Route::post('/categories', [\App\Http\Controllers\Api\Admin\CategoryManagementController::class, 'store']);
        Route::put('/categories/{id}', [\App\Http\Controllers\Api\Admin\CategoryManagementController::class, 'update']);
        Route::delete('/categories/{id}', [\App\Http\Controllers\Api\Admin\CategoryManagementController::class, 'destroy']);


        // Variation Group Management (Admin only)
        Route::get('/variation-groups', [VariationGroupController::class, 'index']);
        Route::post('/variation-groups', [VariationGroupController::class, 'store']);
        Route::get('/variation-groups/{id}', [VariationGroupController::class, 'show']);
        Route::put('/variation-groups/{id}', [VariationGroupController::class, 'update']);
        Route::delete('/variation-groups/{id}', [VariationGroupController::class, 'destroy']);

        // Variation Option Management (Admin only)
        Route::post('/variation-options', [VariationOptionController::class, 'store']);
        Route::put('/variation-options/{id}', [VariationOptionController::class, 'update']);
        Route::delete('/variation-options/{id}', [VariationOptionController::class, 'destroy']);
        Route::post('/variation-options/reorder', [VariationOptionController::class, 'reorder']);

        // Menu Variation Assignment (Admin only)
        Route::post('/menus/{id}/variations', [MenuController::class, 'assignVariations']);
        Route::delete('/menus/{menuId}/variations/{groupId}', [MenuController::class, 'removeVariation']);

        // User Management (Admin only)
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::get('/users/{id}', [UserManagementController::class, 'show']);
        Route::post('/users', [UserManagementController::class, 'store']);
        Route::put('/users/{id}', [UserManagementController::class, 'update']);
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);
    });
});
