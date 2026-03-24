<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

// Import Controllers
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BannerController;

use App\Http\Controllers\StockInController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products-updated-since', [ProductController::class, 'getUpdatedSince']);
Route::get('/product-new', [ProductController::class, 'product_new']);
Route::get('/category', [CategoryController::class, 'index']);
Route::get('/category/{id}', [CategoryController::class, 'show']);
Route::get('/banner', [BannerController::class, 'index']);
Route::get('/menu', [MenuController::class, 'index']);
Route::get('/posts/detail/{slug}', [PostController::class, 'showBySlug']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::get('/topics', [TopicController::class, 'index']);
Route::post('/topics', [TopicController::class, 'store']);
Route::get('/topics/{id}', [TopicController::class, 'show']);
Route::put('/topics/{id}', [TopicController::class, 'update']);
Route::delete('/topics/{id}', [TopicController::class, 'destroy']);
Route::get('/attributes', [AttributeController::class, 'index']);
Route::post('/contact', [ContactController::class, 'store']);
Route::get('/vnpay-ipn', [OrderController::class, 'vnpayIPN']);
Route::get('/vnpay-return', [OrderController::class, 'vnpayReturn']);
    // Product Management
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::post('/products/{id}/attributes', [AttributeController::class, 'store']);

// Test route để debug post detail

/*
|--------------------------------------------------------------------------
| 2. PROTECTED ROUTES (Đã đăng nhập)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // User cá nhân
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/user-profile', [AuthController::class, 'showProfile']);
    Route::put('/user-profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/change-password', [UserController::class, 'changePassword']);
    Route::put('/user/update-profile', [AuthController::class, 'updateProfile']); // Alternative route
    Route::post('/logout', [AuthController::class, 'logout']);


    // Stockin & Promotion (có thể public hoặc admin tùy frontend)
    Route::get('/stockin', [StockInController::class, 'index']);
    Route::post('/stockin', [StockInController::class, 'store']);
    Route::apiResource('/Promotion', PromotionController::class); // Note: Capital P as per frontend

    // User, Contact & Banner Management
    Route::apiResource('/user', UserController::class);
    Route::get('/contact', [ContactController::class, 'index']);
    Route::get('/contact/{id}', [ContactController::class, 'show']);
    Route::post('/contact/{id}/reply', [ContactController::class, 'reply']);
    Route::delete('/contact/{id}', [ContactController::class, 'destroy']);

    // Settings Management
    Route::get('/setting', [SettingController::class, 'index']);
    Route::put('/setting', [SettingController::class, 'update']);


    // Category Management
    Route::get('/category', [CategoryController::class, 'index']);
    Route::post('/category', [CategoryController::class, 'store']);
    Route::get('/category/{id}', [CategoryController::class, 'show']);
    Route::put('/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']);
    

    // Attribute Management
    Route::apiResource('/attributes', AttributeController::class);

    // Post Management
    Route::apiResource('/post', PostController::class);

    // Menu Management
    Route::apiResource('/menu', MenuController::class);

    /*
    |--------------------------------------------------------------------------
    | 3. ADMIN ROUTES (Prefix: /api/admin/...)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        
        // Categories
        Route::post('/category', [CategoryController::class, 'store']);
        Route::put('/category/{id}', [CategoryController::class, 'update']);
        Route::delete('/category/{id}', [CategoryController::class, 'destroy']);

        // Attributes
        Route::apiResource('/attributes', AttributeController::class)->except(['index']);

        // Orders (Admin quản lý toàn bộ)
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::put('/orders/{id}', [OrderController::class, 'update']);

        // Kho & Khuyến mãi
        Route::get('/stockin', [StockInController::class, 'index']);
        Route::post('/stockin', [StockInController::class, 'store']);
        Route::get('/stock-in/{id}', [StockInController::class, 'show']);
        Route::apiResource('/promotion', PromotionController::class);

        // Nội dung
        Route::apiResource('/topics', TopicController::class);
        Route::apiResource('/post', PostController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('/banner', BannerController::class);
       
        Route::apiResource('/menu', MenuController::class)->only(['store', 'update', 'destroy']);

        // Liên hệ & Cài đặt
        Route::get('/contact', [ContactController::class, 'index']);
        Route::get('/contact/{id}', [ContactController::class, 'show']);
        Route::delete('/contact/{id}', [ContactController::class, 'destroy']);
        
        Route::get('/setting', [SettingController::class, 'index']);
        Route::put('/setting', [SettingController::class, 'update']);
    });
});
