<?php

use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ClientReviewController;
use App\Http\Controllers\Api\FooterController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/footer', [FooterController::class, 'index']);
    Route::get('/categories', [CatalogController::class, 'categories']);
    Route::get('/sub-categories', [CatalogController::class, 'subCategories']);
    Route::get('/brands', [CatalogController::class, 'brands']);



    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/banners/{pageName}', [BannerController::class, 'getBanner']);
    Route::get('/client-reviews', [ClientReviewController::class, 'index']);
    Route::get('/clients', [ClientReviewController::class, 'clients']);

    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/blogs/{slug}', [BlogController::class, 'show']);
});
