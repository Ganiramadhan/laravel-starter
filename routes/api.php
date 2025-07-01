<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

// Public routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/example', fn () => response()->json(['message' => 'CORS Test']));

// Public Product routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Public Shop routes
Route::get('/shops', [ShopController::class, 'index']);
Route::get('/shops/{id}', [ShopController::class, 'show']);

// Protected routes
Route::middleware('jwt.auth')->group(function () {
    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::post('/logout', [UserController::class, 'logout']);
        Route::get('/profile', fn () => response()->json(JWTAuth::user()));
    });

    // Product routes (create/update/delete)
    Route::prefix('products')->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });

    // Shop routes (create/update/delete)
    Route::prefix('shops')->group(function () {
        Route::post('/', [ShopController::class, 'store']);
        Route::put('/{id}', [ShopController::class, 'update']);
        Route::delete('/{id}', [ShopController::class, 'destroy']);
    });
});
