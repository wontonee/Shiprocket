<?php

use Illuminate\Support\Facades\Route;
use Wontonee\ShipRocket\Http\Controllers\Shop\ShipRocketController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency'], 'prefix' => 'shiprocket'], function () {
    Route::get('', [ShipRocketController::class, 'index'])->name('shop.shiprocket.index');
});