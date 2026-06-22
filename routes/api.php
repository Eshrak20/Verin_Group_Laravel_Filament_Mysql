<?php

use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\FooterController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/footer', [FooterController::class, 'index']);
    Route::get('/categories', [CatalogController::class, 'categories']);
    Route::get('/sub-categories', [CatalogController::class, 'subCategories']);
    Route::get('/brands', [CatalogController::class, 'brands']);



    Route::get('/products', [ProductController::class, 'index']);
});
