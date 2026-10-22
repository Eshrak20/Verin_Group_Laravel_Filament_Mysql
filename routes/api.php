
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ClientReviewController;
use App\Http\Controllers\Api\FooterController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SavedProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Public footer routes
    Route::get('/footer', [FooterController::class, 'index']);
    Route::get('/footer/{company}/pages', [FooterController::class, 'pages']);
    Route::get('/footer/{company}/pages/{page_type}', [FooterController::class, 'showPage']);

    // Public catalog routes
    Route::get('/categories', [CatalogController::class, 'categories']);
    Route::get('/sub-categories', [CatalogController::class, 'subCategories']);
    Route::get('/brands', [CatalogController::class, 'brands']);

    // Public product routes
    Route::post('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);

    // Public content routes
    Route::get('/banners/{pageName}', [BannerController::class, 'getBanner']);
    Route::get('/client-reviews', [ClientReviewController::class, 'index']);
    Route::get('/clients', [ClientReviewController::class, 'clients']);

    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/blogs/{slug}', [BlogController::class, 'show']);

    // Orders
    Route::post('/orders', [OrderController::class, 'store'])
        ->name('orders.store');

    Route::middleware(['auth:sanctum', 'not.blocked'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/orders/{orderNumber}/status', [OrderController::class, 'status'])
            ->name('orders.status');

        Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])
            ->name('orders.show');
    });

    // Authentication
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

        Route::middleware(['auth:sanctum', 'not.blocked'])->group(function () {
            Route::get('/myprofile', [AuthController::class, 'myprofile']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        });
    });

    // Saved products — authenticated customers only
    Route::prefix('saved-products')
        ->name('saved-products.')
        ->middleware(['auth:sanctum', 'not.blocked'])
        ->controller(SavedProductController::class)
        ->group(function () {
            Route::post('/', 'store')->name('store');
            Route::get('/', 'index')->name('index');

            Route::get('/{productId}', 'show')
                ->whereNumber('productId')
                ->name('show');

            Route::delete('/{productId}', 'destroy')
                ->whereNumber('productId')
                ->name('destroy');
        });
});
