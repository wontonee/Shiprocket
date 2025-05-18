<?php

use Illuminate\Support\Facades\Route;
use Wontonee\Shiprocket\Http\Controllers\Shop\TrackingController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency'], 'prefix' => 'shiprocket'], function () {
    Route::get('tracking-shipment', [TrackingController::class, 'index'])->name('shiprocket.shop.tracking.index');
    Route::post('tracking-process', [TrackingController::class, 'show'])->name('shiprocket.shop.tracking.show');
});