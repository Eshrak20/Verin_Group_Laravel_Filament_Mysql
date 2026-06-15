<?php

use App\Http\Controllers\Api\FooterController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/footer', [FooterController::class, 'index']);
});
