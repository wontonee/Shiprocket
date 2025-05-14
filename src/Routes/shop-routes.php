<?php

use Illuminate\Support\Facades\Route;
use Wontonee\Shiprocket\Http\Controllers\Shop\ShiprocketController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency'], 'prefix' => 'shiprocket'], function () {
    Route::get('', [ShiprocketController::class, 'index'])->name('shop.shiprocket.index');
});