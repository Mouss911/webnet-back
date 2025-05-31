<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="E-commerce API Documentation",
 *     description="Documentation de l'API E-commerce",
 *     @OA\Contact(
 *         email="votre@email.com",
 *         name="Support API"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 * 
 * @OA\Server(
 *     description="Local Environment",
 *     url="http://localhost:8000/api"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CouponController;

// Auth routes
Route::post('/register', [ApiController::class, 'register']);
Route::post('/login', [ApiController::class, 'login']);

// Public routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/products/category/{category}', [ProductController::class, 'getByCategory']);
Route::get('/products/search/{query}', [ProductController::class, 'search']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User profile routes
    Route::get('profile', [ApiController::class, 'profile']);
    Route::get('logout', [ApiController::class, 'logout']);
    Route::get('refresh-token', [ApiController::class, 'refreshToken']);
    Route::put('/profile/update', [ApiController::class, 'updateProfile']);
    Route::put('/profile/password', [ApiController::class, 'changePassword']);
    Route::post('/profile/avatar', [ApiController::class, 'updateAvatar']);

    // Cart routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'addItem']);
    Route::put('/cart/update/{cartItem}', [CartController::class, 'updateItem']);
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'removeItem']);
    Route::delete('/cart/clear', [CartController::class, 'clearCart']);
    Route::get('/cart/count', [CartController::class, 'getCartCount']);
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon']);

    // Order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/cancel/{order}', [OrderController::class, 'cancelOrder']);
    Route::get('/orders/history', [OrderController::class, 'orderHistory']);
    Route::post('/orders/{order}/review', [OrderController::class, 'addReview']);

    // Admin routes (vous devrez ajouter un middleware admin plus tard)
    Route::middleware(['admin'])->group(function () {
        // Product management
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        // Category management
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // Order management
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);

        // Dashboard stats
        Route::get('/admin/dashboard/stats', [AdminController::class, 'getDashboardStats']);
        Route::get('/admin/orders/stats', [AdminController::class, 'getOrderStats']);
        
        // Gestion des utilisateurs
        Route::get('/admin/users', [AdminController::class, 'getAllUsers']);
        Route::put('/admin/users/{user}/role', [AdminController::class, 'updateUserRole']);
        
        // Gestion des coupons
        Route::resource('/admin/coupons', CouponController::class);
    });
});