<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ClientReviewController;
use App\Http\Controllers\Api\FooterController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/footer', [FooterController::class, 'index']);
    Route::get('/footer/{company}/pages', [FooterController::class, 'pages']);
    Route::get('/footer/{company}/pages/{page_type}', [FooterController::class, 'showPage']);

    Route::get('/categories', [CatalogController::class, 'categories']);
    Route::get('/sub-categories', [CatalogController::class, 'subCategories']);
    Route::get('/brands', [CatalogController::class, 'brands']);



    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/banners/{pageName}', [BannerController::class, 'getBanner']);
    Route::get('/client-reviews', [ClientReviewController::class, 'index']);
    Route::get('/clients', [ClientReviewController::class, 'clients']);
    Route::post('/orders', [OrderController::class, 'store'])
        ->name('api.v1.orders.store');

    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/blogs/{slug}', [BlogController::class, 'show']);


    Route::prefix('auth')->group(function () {
        Route::post('/signup', [AuthController::class, 'signup']);

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:auth-sensitive');

        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
            ->middleware('throttle:auth-sensitive');

        Route::post('/reset-password', [AuthController::class, 'resetPassword']);

        Route::post('/send-otp', [AuthController::class, 'sendOtp'])
            ->middleware('throttle:auth-sensitive');

        Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])
            ->middleware('throttle:auth-sensitive');

        Route::post('/resend-otp', [AuthController::class, 'resendOtp'])
            ->middleware('throttle:auth-sensitive');

        Route::post('/verify-email', [AuthController::class, 'verifyEmail'])
            ->middleware('throttle:auth-sensitive');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/myprofile', [AuthController::class, 'myprofile']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        });
    });
});
